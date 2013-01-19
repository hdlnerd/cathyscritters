<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: browse.base.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage browse.base.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH.DS.'task'.DS.'base.php');
require_once( JB_LIBRARY_PATH.DS.'avatar.php');
require_once( JB_LIBRARY_PATH.DS.'plugins.php');
require_once( JB_COM_PATH.DS.'tables'.DS.'blogs.php');

class JbblogBrowseBase extends JbblogBaseController
{
	var $entries;
	var $totalEntries;
	var $filters;
	var $html;
	var $limit;
	var $limitstart;
	var $_plugins	= null;
	var $_headerHTML = '';
	
	function JbblogBrowseBase()
	{
		global $_JB_CONFIGURATION;
				
		parent::JbblogBaseController();

		$this->_plugins	= new JBPlugins();
		$this->toolbar = JB_TOOLBAR_HOME;
		$this->limit = JRequest::getVar( 'limit' , $_JB_CONFIGURATION->get('numEntry') , 'GET');
		$this->limitstart = JRequest::getVar( 'limitstart' , 0 , 'GET' );
	}
	
	function _header()
	{
		return parent::_header();
	}
	
	function display()
	{
		global $_JB_CONFIGURATION, $JBBLOG_LANG, $Itemid;
		
		$db	=& JFactory::getDBO();

		if(!jbAllowedGuestView('intro'))
		{
			$template		= new JoomblogTemplate();
			$content		= $template->fetch($this->_getTemplateName('permissions'));
			return $content;
		}
		
		$blogger = JRequest::getVar( 'user' , '' , 'GET' );
		
		if( !empty( $blogger ) ){
			$title = jbGetAuthorTitle($blogger);
			if(!$title){
				$title = $blogger."'s blog";
			}
			
			jbAddPathway(JText::_('COM_JOOMBLOG_ALL_BLOGS_TITLE'),JURI::base().JRoute::_('index.php?option=com_joomblog&user='.$blogger));
			jbAddPathway($title);
		}
		
		$jb	=& JFactory::getUser();		

		$this->setData();
		$this->_getEntries($this->filters);
	
		$tpl = new JoomblogCachedTemplate(time() . $jb->usertype . $_JB_CONFIGURATION->get('template'));
		$html = '';	
	
		if(!is_array($this->entries)){
			$this->entries = array();
		}

		array_walk($this->entries, array($this, '_prepareData') );
		
		$content_modules = array();
		if($_JB_CONFIGURATION->get('modulesDisplay')){
        
      $query = "SELECT m.module, m.params FROM #__modules AS m WHERE m.module LIKE ('mod_jb_%') GROUP BY (m.module) ";
      $db->setQuery($query);
      $modules = $db->loadObjectList('module');
      
      $query = "SELECT m.* FROM #__joomblog_modules AS m ";
      $db->setQuery($query);
      $joomblog_modules = $db->loadObjectList('name');
      
      $query = "SELECT MAX(m.ordering) FROM #__joomblog_modules AS m ";
      $db->setQuery($query);
      $next_ordering = $db->loadResult();
      
      $next_ordering++;

      foreach($modules as $key => $value){
        if(!isset($joomblog_modules[$key])){
          $db->setQuery("INSERT INTO #__joomblog_modules SET published='1',	name = '$key', params='".$value->params."', ordering = ".$next_ordering." ");
          $db->query();
          $next_ordering++;
        }
      }
        
      $query = "SELECT m.* FROM #__joomblog_modules AS jm, #__modules AS m WHERE jm.name = m.module AND jm.published = 1 GROUP BY (m.module)  ";
      $db->setQuery($query);
      $rows = $db->loadObjectList();
      $document	= JFactory::getDocument();
      $renderer	= $document->loadRenderer('module');
      foreach ($rows as $module) {
        $module->user = null;
        $modules	= JModuleHelper::getModule($module->module);
        $params		= array();
        ob_start();
        echo $renderer->render($module, $params);//ob_get_contents()
        $content_modules[] = array('text' => ob_get_clean(), 'name' => $module->module, 'title' => $module->title);
      }
		}
		
		$entryArray = $tpl->object_to_array($this->entries);
		
		$entrySession = array(); $_SESSION['entrySession'] = array();
		foreach($entryArray as $entry){
				array_push($entrySession, $entry['id']);
		}
		$_SESSION['entrySession'] = (!empty($entrySession)) ? $entrySession : array();
	
		$tpl->set('entry', $entryArray);
		$tpl->set('categoryDisplay' , $_JB_CONFIGURATION->get('categoryDisplay') );
		$tpl->set('showAnchor', $_JB_CONFIGURATION->get('anchorReadmore') ? '#readmore' : '');
		$tpl->set('useDraganddrop', $_JB_CONFIGURATION->get('useDraganddrop'));
		$tpl->set('modulesDisplay', $_JB_CONFIGURATION->get('modulesDisplay'));
		$tpl->set('modules', $content_modules);
		$tpl->set('headerHTML', $this->_headerHTML);
		
		$get = JRequest::get('GET');

		if( 
		(isset($get['option']) && isset($get['Itemid']) && count($get)==2) ||
        (isset($get['option']) && count($get)==1 ) || 
        (isset($get['option']) && isset($get['Itemid']) && isset($get['view']) && $get['view'] == 'default' && count($get)==3 ) ||
        (isset($get['option']) && isset($get['view']) && $get['view'] == 'default' && count($get)==2 ) ||
        (isset($get['option']) && isset($get['Itemid']) && isset($get['view']) && isset($get['language']) && isset($get['lang']) && $get['view'] == 'default' && count($get)==5 ) || 
        (isset($get['option']) && isset($get['Itemid']) && isset($get['language']) && isset($get['lang']) && count($get)==4 )){
			$isHome = 1;
		}else{
			$isHome = 0;
		}
		
		$tpl->set('isHome',$isHome);
		
		unset($entryArray);
				
		$template = $this->_getTemplateName('index');
		$html = $tpl->fetch_cache($template);

		if ( !isset($_SERVER['REQUEST_URI']) )
		{

			$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'],1 );
		
			if (isset($_SERVER['QUERY_STRING']))
			{
				$_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING'];
			}
		}

		if(!isset($_SERVER['QUERY_STRING']))
		{
			$_SERVER['QUERY_STRING'] = ''; 
		
			foreach($_GET as $key => $val){
				$_SERVER['QUERY_STRING'] .= $key . '=' . $val . '&';
			}
			$_SERVER['QUERY_STRING'] = rtrim($_SERVER['QUERY_STRING'] , '&');
		}
		

		$queryString = $_SERVER['QUERY_STRING'];
		$queryString = preg_replace("/\&limit=[0-9]*/i", "", $queryString);
		$queryString = preg_replace("/\&limitstart=[0-9]*/i", "", $queryString);
		
		$pageNavLink = $_SERVER['REQUEST_URI'];
		$pageNavLink = preg_replace("/\&limit=[0-9]*/i", "", $pageNavLink);
		$pageNavLink = preg_replace("/\&limitstart=[0-9]*/i", "", $pageNavLink);

		if (!$_JB_CONFIGURATION->get('modulesDisplay') && $this->totalEntries > $this->limit)
		{
			jimport( 'joomla.html.pagination' );
			$pageNav	= new JPagination( $this->totalEntries , $this->limitstart , $this->limit );
			
			$html .= '<div class="jb-pagenav">' . $pageNav->getPagesLinks('index.php?' . $queryString) . '</div>';
		}
		
		return $html;	
	}
	
	function setData()
	{		
		$searchby = array();
		
		$category	= JRequest::getVar( 'category' , '' , 'REQUEST' );
		
		if( !empty( $category ) )
		{
			if(is_numeric($category))
			{
				$category = strval( urldecode( $category ) );
				$category = str_replace("+", " ", $category);
				$searchby['jcategory'] = $category;
			}
			else
			{
				$category = strval( urldecode( $category ) );
				$category = str_replace("+", " ", $category);
				$searchby['category'] = $category;
			}
		}

		$archive	= JRequest::getVar( 'archive' , '' , 'REQUEST' );
		if ( !empty( $archive ) )
		{
			$archive = urldecode( $archive );
			$archive = str_replace(':', '-', $archive);
			$archive = date("Y-m-d 00:00:00", strtotime($archive."-01"));
			$searchby['archive']	= $archive;
		}
		
		$this->filters = $searchby;
	}
	

	function _prepareData(&$row, $key)
	{
		global $_JB_CONFIGURATION, $Itemid , $JBBLOG_LANG;
		
		$mainframe	=& JFactory::getApplication();
		$tmpl = JRequest::getVar('tmpl');
		if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';

		$this->_plugins->load();


		$blogger = JRequest::getVar( 'user' , '' , 'GET' );
		
		
		if( !empty( $blogger ) ){
			$title = jbGetAuthorTitle($blogger);
			if(!$title){
				$title = $blogger."'s blog";
			}

			jbAddPageTitle( $title );
		}

		
		$row->permalink = jbGetPermalinkUrl($row->id);
		
		$row->introtext = str_replace('src="images', 'src="'. rtrim( JURI::base() , '/' ) .'/images', $row->introtext);
		$row->fulltext  = str_replace('src="images', 'src="'. rtrim( JURI::base() , '/' ) .'/images',  $row->fulltext);
		$row->introtext = str_replace('{social}', '', $row->introtext);
		$row->fulltext = str_replace('{social}', '', $row->fulltext);
		$row->author		= jbGetAuthorName($row->created_by, $_JB_CONFIGURATION->get('useFullName'));
		$row->authorLink	= JRoute::_("index.php?option=com_joomblog&task=profile&id=".$row->created_by."&Itemid=$Itemid");
		$row->blogsLink		= JRoute::_("index.php?option=com_joomblog&task=blogs&Itemid=".$Itemid.$tmpl);
		$row->categories	= jbCategoriesURLGet($row->id, true);
		
		$row->jcategory = null;
		$row->multicats = false;
		$cats = jbGetMultiCats($row->id);
		if (sizeof($cats))
		{
			$jcategories = array();
			foreach ( $cats as $cat ) 
			{
				$catlink = JRoute::_('index.php?option=com_joomblog&task=tag&category='.$cat.'&Itemid='.$Itemid.$tmpl );
				$jcategories []= ' <a class="category" href="' .$catlink. '">' . jbGetJoomlaCategoryName($cat).'</a> ';	
			}
			if (sizeof($jcategories)>1) $row->multicats = true;
			if (sizeof($jcategories)) $row->jcategory = implode(',', $jcategories);
			
		}else $row->jcategory	= '<a class="category" href="' . JRoute::_('index.php?option=com_joomblog&task=tag&category=' . $row->catid.'&Itemid='.$Itemid.$tmpl ). '">' . jbGetJoomlaCategoryName( $row->catid ) . '</a>';
		
		$date = new JDate( $row->created );
		
		$row->createdFormatted	= $date->toFormat( $_JB_CONFIGURATION->get( 'dateFormat' ) );
		$row->created = $date->toFormat();

		$row->readmore	= ($_JB_CONFIGURATION->get('useIntrotext') == '1') ? '1' : '0';
		
		$registry = new JRegistry;
		$registry->loadJSON($row->attribs);
		$attribs = $registry->toArray();
		
		$row->readmorelink	= @$attribs['alternative_readmore']?$attribs['alternative_readmore']:$_JB_CONFIGURATION->get('readMoreLink');
		
		//SMT social int
		
		/*** Twitter ***/
		$usetwitter = $_JB_CONFIGURATION->get('usetwitter');
		$row->twitter_button = null;
		$row->twposition = $_JB_CONFIGURATION->get('positiontwitterInList');
		
		if ($usetwitter)
		{
			$showTwInList = $_JB_CONFIGURATION->get('showtwitterInList');
			
			
			if ($showTwInList)
			{
				$twitStyle 	= $_JB_CONFIGURATION->get('twitterliststyle');
				$twitFlSt 	= $_JB_CONFIGURATION->get('twitterfollowliststyle');
				$twitLang 	= $_JB_CONFIGURATION->get('twitterlang');
				$twitName 	= $_JB_CONFIGURATION->get('twitterName');
				$twitflName = $twitName;
				$twitUrl 	= $row->permalink;	
				
				if (isset($twitLang)) $twitLang='data-lang="'.$twitLang.'"'; else $twitLang='';
				if (isset($twitName)) $twitName='data-via="'.$twitName.'"'; else $twitName='';
				if (isset($twitUrl)) $twitUrl='data-url="'.$twitUrl.'"'; else $twitUrl='';
				if (isset($row->title)) $twitText='data-text="'.$row->title.'"'; else $twitText='';
					switch ( $twitStyle ) 
					{
						case 'none': 
							$row->twitter_button='<a href="http://twitter.com/share" class="twitter-share-button" '.$twitUrl.' data-count="none" '.$twitText.' '.$twitName.' '.$twitLang.'>Tweet</a>';
						break;
						case 'horizontal': 
							$row->twitter_button='<a href="http://twitter.com/share" class="twitter-share-button" '.$twitUrl.' data-count="horizontal" '.$twitText.' '.$twitName.' '.$twitLang.'>Tweet</a>';
						break;
						case 'vertical': 
							$row->twitter_button='<a href="http://twitter.com/share" class="twitter-share-button" '.$twitUrl.' data-count="vertical" '.$twitText.' '.$twitName.' '.$twitLang.'>Tweet</a>';
						break;
					}
										
					switch ( $twitFlSt ) 
					{
						case 'twf1': 
							$row->twitter_button.='<a href="http://twitter.com/'.$twitflName.'" class="twitter-follow-button" data-show-count="false" '.$twitLang.' >@'.$twitflName.'</a>';
						break;
						case 'twf2': 
							$row->twitter_button.='<a href="http://twitter.com/'.$twitflName.'" class="twitter-follow-button" data-show-count="true" '.$twitLang.' >'.$twitflName.'</a>';
						break;
					}
					
					
					
			}
								
		}
		
		/*** Facebook ***/
		$usefacebook = $_JB_CONFIGURATION->get('usefacebook');
		$row->fb_button = null;
		$row->fbposition = $_JB_CONFIGURATION->get('positionfbInList');
		
		if ($usefacebook)
		{
			$showFbInList = $_JB_CONFIGURATION->get('showfbInList');
			
			if ($showFbInList)
			{
				$fbStyle 		= $_JB_CONFIGURATION->get('fb_style_list');
				$fbSendButton 	= $_JB_CONFIGURATION->get('fb`_sendbutton');
				$fbwidth		= (int)$_JB_CONFIGURATION->get('fbwidth',400);
				$fbUrl 	= $row->permalink;	
				
				$row->fb_button='<script src="http://connect.facebook.net/en_US/all.js#appId=259018057462154&amp;xfbml=1"></script>';

					switch ( $fbStyle ) 
					{
						case 'none': 
							$row->fb_button.='<div id="fb-root"></div><fb:like href="'.$fbUrl.'" send="'.($fbSendButton?true:false).'" width="'.$fbwidth.'" show_faces="false" font=""></fb:like>';
						break;
						case 'horizontal': 
							$row->fb_button.='<div id="fb-root"></div><fb:like layout="button_count" href="'.$fbUrl.'" send="'.($fbSendButton?true:false).'" width="'.$fbwidth.'" show_faces="false" font=""></fb:like>';
						break;
						case 'vertical': 
							$row->fb_button.='<div id="fb-root"></div><fb:like layout="box_count" href="'.$fbUrl.'" send="'.($fbSendButton?true:false).'" width="'.$fbwidth.'" show_faces="false" font=""></fb:like>';
						break;
					}					
									
			}
								
		}
		
		/*** Google + ***/
		$usegp = $_JB_CONFIGURATION->get('usegp');
		$row->gp_button = null;
		$row->gpposition = $_JB_CONFIGURATION->get('positiongpInList');
		
		if ($usegp)
		{
			$showGpInList = $_JB_CONFIGURATION->get('showgpInList');
			$gpLang 	= $_JB_CONFIGURATION->get('gp_language');
			if (isset($gpLang)) $gpLang="{lang: '".$gpLang."'}"; else $gpLang='';
			
			if ($showGpInList)
			{
				$gpStyle 		= $_JB_CONFIGURATION->get('gp_style_list');
				$gpUrl 	= $row->permalink;	
				
				

					switch ( $gpStyle ) 
					{
						case 'none': 
							$row->gp_button.='<g:plusone size="small" href="'.urlencode($gpUrl).'"></g:plusone>';
						break;
						case 'horizontal': 
							$row->gp_button.='<g:plusone count="false" href="'.urlencode($gpUrl).'"></g:plusone>';
						break;
						case 'vertical': 
							$row->gp_button.='<g:plusone size="tall" href="'.urlencode($gpUrl).'"></g:plusone>';
						break;
					}					
									
			}
								
		}
		
		/*** Linkedin ***/
		$useln = $_JB_CONFIGURATION->get('useln');
		$row->ln_button = null;
		$row->lnposition = $_JB_CONFIGURATION->get('positionlnInList');
		
		if ($useln)
		{
			$showLnInList = $_JB_CONFIGURATION->get('showlnInList');
			
			if ($showLnInList)
			{
				$lnStyle 		= $_JB_CONFIGURATION->get('ln_style_list');
				$lnUrl 	= $row->permalink;	
				
				
				
					switch ( $lnStyle ) 
					{
						case 'none': 
							$row->ln_button.='<script type="IN/Share" data-url="'.$lnUrl.'"></script>';
						break;
						case 'horizontal': 
							$row->ln_button.='<script type="IN/Share" data-url="'.$lnUrl.'" data-counter="right"></script>';
						break;
						case 'vertical': 
							$row->ln_button.='<script type="IN/Share" data-url="'.$lnUrl.'" data-counter="top"></script>';
						break;
					}					
									
			}
								
		}
		/*** Pinterest ***/

        $usepi = $_JB_CONFIGURATION->get('usepi');
        $row->pi_button = null;
        $row->piposition = $_JB_CONFIGURATION->get('positionpiInList');

        if ($usepi) {
            $showPiInList = $_JB_CONFIGURATION->get('showpiInList');
            $piLang 	= $_JB_CONFIGURATION->get('pi_language');
            $piLang = (isset($piLang)) ? "{lang: '" . $piLang . "'}" : '';

            if ($showPiInList)
            {
                $piStyle 		= $_JB_CONFIGURATION->get('pi_style_list');

                $piUrl 	= $row->permalink;

                $piDescription = $row->blogtitle;

                preg_match_all('#<img.*?src=["\']*([\S]+)["\'].*?>#', $row->introtext . $row->fulltext, $piImageTemp);
                $piImage        = ($piImageTemp == '') ? 'none' : '&media=' . @$piImageTemp[1][0];
                
                $pi_url_text    = '<a href="http://pinterest.com/pin/create/button/?url=' . $piUrl . $piImage . '&description=' . $piDescription . '" class="pin-it-button" count-layout="' . $piStyle . '"><img border="0" src="http://assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>';
                $row->pi_button .= $pi_url_text;
                $row->pi_button .= '<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js">' . $piLang . '</script>';
                
                
               
            }
        }

        

		/*** AddThis ***/
		$useat = $_JB_CONFIGURATION->get('useAddThis');
		$row->at_button = null;
		$row->atposition = $_JB_CONFIGURATION->get('addThisListPosition');
		
			if ($useat)
				if ($_JB_CONFIGURATION->get('showAddThisInList') == 1)
				{						
				$sefUrl = $row->permalink;
				$host = $_JB_CONFIGURATION->get('addThisName');
				$button_style = $_JB_CONFIGURATION->get('addthis_list_button_style');

					$services = array();
					
					if ($button_style == 'style2'){	$add = '<!-- AddThis Button BEGIN --><div class="addthis_toolbox addthis_default_style addthis_32x32_style">';} 
					else if ($button_style == 'style9' || $button_style == 'style10'){ $add = '';} else {$add = '<!-- AddThis Button BEGIN --><div class="addthis_toolbox addthis_default_style ">';}
					
					$add .= socialDefaultAdd($button_style, $sefUrl, $host);
					$row->at_button = $add;
				}
		
		
		
		if($_JB_CONFIGURATION->get('necessaryReadmore') == '1' && $row->readmore == '1')
		{
			if($row->introtext && empty($row->fulltext) )
			{

				$count = TableBlogs::getParagraphCount($row->introtext);
				if( $count <= $_JB_CONFIGURATION->get('autoReadmorePCount') )
				{
					$row->readmore = '0';
				}
			}
			else if( empty($row->introtext) && $row->fulltext )
			{

				$count = TableBlogs::getParagraphCount($row->fulltext);
				
				if( $count <= $_JB_CONFIGURATION->get('autoReadmorePCount') )
				{
					$row->readmore = '0';
				}
			}
		}				
		
		TableBlogs::getBrowseText($row);
		
		$row->comments = ($_JB_CONFIGURATION->get('useComment') == "1" && $_JB_CONFIGURATION->get('useDisqus') == "0") ? jbCommentsURLGet($row->id, true) : jbGetDisqusComments($row->id);

		$avatar	= 'Jb' . ucfirst($_JB_CONFIGURATION->get('avatar')) . 'Avatar';
		$avatar	= new $avatar($row->created_by);
		
		$row->avatar	= $avatar->get();
		$params			= $this->_buildParams();
		
		if ($_JB_CONFIGURATION->get('mambotFrontpage')=="1")
		{
			$row->beforeContent = $this->_plugins->trigger('onBeforeDisplayContent', $row, $this->_buildParams(), 0);
			$this->_plugins->trigger('onPrepareContent', $row, $params, 0);			
			$row->afterContent	= @$this->_plugins->trigger('onAfterDisplayContent', $row, $this->_buildParams(), 0);
			if ($row->afterContent != "") $row->afterContent = "<br/>" . $row->afterContent;
		}


		$row->text 	= str_replace(array('{mosimage}', 
			'{mospagebreak}', 
			'{readmore}',
			'{jomcomment}',
			'{!jomcomment}'), '', $row->text);

	}
		

	function _getEntries(&$searchby)
	{
		global $_JB_CONFIGURATION, $Itemid;
		
		$doc        =  JFactory::getDocument();
		$mainframe	=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$user	    =& JFactory::getUser();
		
		$limit 		= isset($searchby['limit']) 	 ? intval($searchby['limit']): $this->limit;
		$limitstart = isset($searchby['limitstart']) ? intval($searchby['limitstart']): $this->limitstart;
		$jcategory 	= isset($searchby['jcategory'])  ? intval($searchby['jcategory']): 0;
		
		$authorid 	= isset($searchby['authorid']) 	 ? $db->getEscaped($searchby['authorid']): "";
		$category 	= isset($searchby['category']) 	 ? $db->getEscaped($searchby['category']): "";
		$search 	= isset($searchby['search']) 	 ? $db->getEscaped($searchby['search']): "";
		$archive 	= isset($searchby['archive']) 	 ? $db->getEscaped($searchby['archive']): "";
		
		$sections = implode(',',jbGetCategoryArray($_JB_CONFIGURATION->get('managedSections')));
		
		$selectMore		= "";
		$searchWhere	= "";
		$primaryOrder	= "";
		$use_tables		= "";
		
		$tmpl = JRequest::getVar('tmpl');
		if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
				
		if (!empty ($category) && empty($jcategory))
		{
			$categoriesArray = explode(",", $category);
			$categoriesList = "0";
			foreach ($categoriesArray as $jbcat)
			{
				$jbcat = $db->getEscaped(trim($jbcat));
				

				$jbcat = str_replace(' ', '%', $jbcat);
				$db->setQuery("SELECT id FROM #__joomblog_tags WHERE name LIKE '$jbcat' ");
				$searchCategoryId = $db->loadResult();
				
				if ($searchCategoryId)
				{
					$categoriesList .= ",";
					$categoriesList .= "$searchCategoryId";
				}
			}
			
			$use_tables .= ",#__joomblog_tags as b,#__joomblog_content_tags as c ";
			$searchWhere .= " AND (b.id=c.tag AND c.contentid=a.id AND b.id IN ($categoriesList)) ";			
		}

		if (!empty ($jcategory) && $jcategory > 0)
		{		
			
			$searchWhere .= " AND (mc.cid='$jcategory') ";
			$query = "SELECT c.* , COUNT(mc.cid) as count  
			FROM #__categories AS c 
			LEFT JOIN `#__joomblog_multicats` AS `mc` ON `mc`.`cid`=`c`.`id` 
			WHERE c.published = 1 AND c.id='$jcategory'";
			$db->setQuery($query);
			$categories = $db->loadObjectList();
			$category = $categories[0];
						
			$category->link = JRoute::_('index.php?option=com_joomblog&task=tag&category='.$category->id.'&Itemid='.$Itemid.$tmpl);

			$cat = array();
			
		}
		
		$view	= JRequest::getVar( 'view' , '' , 'GET' );
			if ($view == 'blogger'){
				
				$blogid = JRequest::getInt('blogid', 0);
				if (!$blogid)
				{
					$menu =& JSite::getMenu();
					$item   = $menu->getActive();
					$params   =& $menu->getParams($item->id);
					$blogid = $params->get('blogid', 0);
				}
				$accesden=false;
				if ($blogid)
				{
					$query = 'SELECT content_id FROM #__joomblog_blogs WHERE blog_id = '.$blogid;
					$db->setQuery($query);
					$articles = $db->loadResultArray();
					$artcle_ids = implode(',', $articles);
					$searchWhere .= " AND a.id IN ($artcle_ids)";
					
					$db->setQuery("SELECT metadesc, metakey FROM #__joomblog_list_blogs WHERE id=".$blogid);
					$meta = $db->loadObjectList();
					

					if($doc->getDescription() == '' || $meta[0]->metadata != '')
						if (isset($meta[0]->metadesc))	$doc->setDescription( $meta[0]->metadesc );
					
					$keywords	= '';
					if( !empty( $meta[0]->metakey ) )
					{
						$keywords	.= ' ' . $meta[0]->metakey;
					}

					$doc->setMetaData( 'keywords' , $doc->getMetaData('keywords').' '.$keywords );

					$db->setQuery("SELECT distinct(u.id) as user_id, mu.description,u.username, u.name, mu.title, mu.id, ub.avatar, p.posts, p.comments,p.jsviewgroup  "
						." FROM #__users u, #__joomblog_user as ub, #__joomblog_list_blogs mu " .
						" LEFT JOIN `#__joomblog_privacy` AS `p` ON `p`.`postid`=`mu`.`id` AND `p`.`isblog`=1 "
						." WHERE mu.user_id=u.id AND ub.user_id = u.id AND mu.published = 1 AND mu.id=".$blogid);
						$blogs = $db->loadObjectList();
						if (isset($blogs[0]))
						{
						 $blog = $blogs[0];
						
						
						switch ( $blog->posts ) 
							{
								case 0:	break;
								case 1:	
									if (!$user->id) 
									{
										$accesden=true;
									}
								break;
								case 2:	
									if (!$user->id) 
									{
										$accesden=true;
									}else
									{
										if (!$this->isFriends($user->id, $blog->user_id) && $user->id!=$blog->user_id)
										{
											$accesden=true;
										}	
									}
								break;
								case 3:	
									if (!$user->id) 
									{
										$accesden=true;
									}else
									{
										if ($user->id!=$blog->user_id)
										{
											$accesden=true;
										}	
									}						
								break;
								case 4:	
										if (!$user->id) 
									{
										$accesden=true;
									}else
									{
										if (!$this->inJSgroup($user->id, $blog->jsviewgroup))
											{
												$accesden=true;
											}	
										}
									break;
							}
							
						
						
						$blog->title = $blog->title?$blog->title:$blog->username."'s blog";
								
						$blog->numEntries	= jbCountUserEntry($blog->user_id, "1");
						$blog->numHits		= jbCountUserHits($blog->user_id);
						$blog->blogLink		= JRoute::_("index.php?option=com_joomblog&blogid=" . $blog->id . "&view=blogger&Itemid=".$Itemid.$tmpl);
				
						if($_JB_CONFIGURATION->get('avatar') == 'jomsocial'){
					
							$db->setQuery("SELECT thumb FROM #__community_users WHERE userid = ".$blog->user_id);
							$thumb = $db->loadResult();
							$blog->src			= (!$thumb) ? JUri::root().'components/com_joomblog/images/user_thumb.png' : JUri::root().$thumb;
	
						} else {
							$blog->src			= (!$blog->avatar) ? JUri::root().'components/com_joomblog/images/user_thumb.png' : JUri::root().'images/joomblog/avatar/thumb_'.$blog->avatar;
						}
						
						$blog->authorLink	= JRoute::_("index.php?option=com_joomblog&task=profile&id=" . $blog->user_id . "&Itemid=".$Itemid.$tmpl);
						$blog->description	= $blog->description;
							}else $blog=null;
						}
						
						$blogs = array();
						$blogs[0]		= $blog;
						if ($accesden) 
						{
							$blogs = array();
							$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&view=default'.$tmpl.'&Itemid='.$Itemid,false),JText::_('COM_JOOMBLOG_BLOG_ADMIN_NO_PERMISSIONS_TO_VIEW'),'notice');
							
						}
						$template		= new JoomblogTemplate();
						$template->set( 'blogs' , $blogs );
						$template->set( 'Itemid' , $Itemid );
				
						$this->_headerHTML	= $template->fetch( $this->_getTemplateName('blog_header') );
						
			}
			
		if (!empty ($authorid) or $authorid == "0")
		{
			$searchWhere .= " AND a.created_by IN ($authorid)";
			$db->setQuery("SELECT u.username, u.name, ub.* FROM #__joomblog_user as ub LEFT JOIN #__users as u ON u.id = ub.user_id WHERE u.block = 0 AND ub.user_id = ".$authorid);
			$users = $db->loadObjectList();
			$user = $users[0];
			
			if ($_JB_CONFIGURATION->get('avatar') == 'jomsocial'){
				$db->setQuery("SELECT thumb FROM #__community_users WHERE userid = ".$user->user_id);
				$avatar = $db->loadResult();
				$user->src			= (!$avatar) ? JUri::root()."components/com_joomblog/images/user_thumb.png" : JUri::root().$avatar;
			} else if ($_JB_CONFIGURATION->get('avatar') == 'juser'){
				if ($user->avatar)
				{
					$user->src = JURI::root()."images/joomblog/avatar/thumb_".$user->avatar;
				} else {
					$user->src = JURI::root()."components/com_joomblog/images/user_thumb.png";
				}
			}
			$user->link = JRoute::_("index.php?option=com_joomblog&task=profile&id={$user->user_id}&Itemid=$Itemid.$tmpl");
			
			$man = array();
			$man[0] = $user;
			
			$template		= new JoomblogTemplate();
			$template->set( 'man' ,  $man);
			$this->_headerHTML = $template->fetch( $this->_getTemplateName('users_header') );
		}
		

		if (!empty ($search))
		{
			$searchWhere .= " AND match (a.title,a.fulltext,a.introtext) against ('$search' in BOOLEAN MODE) ";
		}
		

		if (!empty ($archive))
		{
			$searchWhere .= " AND a.created BETWEEN '$archive' AND date_add('$archive', INTERVAL 1 MONTH) ";
		}
		
		$lang = JFactory::getLanguage();

		$searchWhere .= " AND a.language IN ('*','".$lang->get('tag')."') ";
		
		$date =& JFactory::getDate();
		
		$query = " SELECT COUNT(*) " .
				" FROM #__joomblog_posts as a $use_tables " ;
				if (!empty ($jcategory) && $jcategory > 0) $query .=" LEFT JOIN `#__joomblog_multicats` AS `mc` ON `mc`.`aid`=`a`.`id` ";
				$query .=" WHERE a.state=1 and a.publish_up <= '" .$date->toMySQL() . "' " .
				" and a.catid in ($sections) $searchWhere ";
				
		$db->setQuery($query);
		$total = $db->loadResult();
		$searchby['total'] = $total;
		$this->totalEntries = $total;

		$vote = " (( SELECT COUNT(v2.vote) FROM #__joomblog_votes as v2 WHERE v2.vote = 1 AND v2.contentid = a.id ) - ( SELECT COUNT(v1.vote) FROM #__joomblog_votes as v1 WHERE v1.vote = -1 AND v1.contentid = a.id )) as sumvote "; 

		$date =& JFactory::getDate();
        $publish_up = $date->toFormat();
		$query = " SELECT a.*, $vote , round(r.rating_sum/r.rating_count) as rating, r.rating_count, p.posts, p.comments,p.jsviewgroup  $selectMore 
			FROM (#__joomblog_posts as a $use_tables ) 
				left outer join #__joomblog_posts_rating as r 
					on (r.content_id=a.id) " .
			" LEFT JOIN `#__joomblog_privacy` AS `p` ON `p`.`postid`=`a`.`id` AND `p`.`isblog`=0 ".
			" LEFT JOIN `#__joomblog_multicats` AS `mc` ON `mc`.`aid`=`a`.`id` ".
			" WHERE a.state=1 and a.publish_up <= '" . $publish_up . "' 
				and a.catid in ($sections) 
				$searchWhere GROUP BY a.id ORDER BY $primaryOrder a.created DESC,a.id DESC LIMIT $limitstart,$limit";
	
		
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		
		JbblogBrowseBase::_getBlogs($rows);
		$rows = $this->getRowsByPrivacyFilter($rows);
		$this->entries = $rows;

		if (!empty ($jcategory) && $jcategory > 0)
		{

			$category->count = $this->totalEntries;
			$cat[0] = $category;
			$template		= new JoomblogTemplate();
			$template->set( 'category' , $cat );
			$this->_headerHTML = $template->fetch( $this->_getTemplateName('category_header') );
		}
				
		unset($rows);
	}
	
	protected function getRowsByPrivacyFilter($rows=null)
	{
		$user	=& JFactory::getUser();
		if (sizeof($rows))
		{
			for ( $i = 0, $n = sizeof( $rows ); $i < $n; $i++ ) 
			{
				$post = &$rows[$i];
				$post->posts?$post->posts:$post->posts=0;
				if (!isset($post->blogtitle)) {unset($rows[$i]); $this->totalEntries--;continue;}
				switch ( $post->posts ) 
				{
					case 0:	break;
					case 1:	
						if (!$user->id) 
						{
							$rows[$i]=null;
							unset($rows[$i]);
							$this->totalEntries--;
						}
					break;
					case 2:	
						if (!$user->id) 
						{
							unset($rows[$i]);
							$this->totalEntries--;
						}else
						{
							if (!$this->isFriends($user->id, $post->created_by) && $user->id!=$post->created_by)
							{
								unset($rows[$i]);
								$this->totalEntries--;
							}	
						}						
					break;
					case 3:	
						if (!$user->id) 
						{
							unset($rows[$i]);
							$this->totalEntries--;
						}else
						{
							if ($user->id!=$post->created_by)
							{
								unset($rows[$i]);
								$this->totalEntries--;
							}	
						}						
					break;
					case 4:	
								unset($rows[$i]);
								$this->totalEntries--;												
					break;
				}
			}
			$rows = array_values($rows);
		}
		return $rows;
	}
	
	protected function isFriends($id1=0,$id2=0)
	{
		$db	=& JFactory::getDBO();
		$db->setQuery(	" SELECT `connection_id` FROM `#__community_connection` " .
						" WHERE connect_from=".(int)$id1." AND connect_to=".(int)$id2." AND `status`=1 ");
		$frindic = $db->loadResult();
		if ($frindic) return true; else return false;				
	}
	
	protected function inJSgroup($id=0,$gid=0)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('groupid');
		$query->from('#__community_groups_members');
		$query->where('groupid='.(int)$gid);
		$query->where('memberid='.(int)$id);
		$db->setQuery($query);
		if ($db->loadResult()) return true; else return false;
	}
	
	function _getBlogs(&$rows){
		
		$db			=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		
		if(count($rows)){
			for($i =0; $i < count($rows); $i++){
				$row    =& $rows[$i];
				$row->notjsgroup=false;
				$db->setQuery(" SELECT b.blog_id, lb.title,p.posts, p.comments, p.jsviewgroup  " .
						      " FROM #__joomblog_blogs as b, #__joomblog_list_blogs as lb " .
						      " LEFT JOIN `#__joomblog_privacy` AS `p` ON `p`.`postid`=`lb`.`id` AND `p`.`isblog`=1 ".
						      " WHERE b.content_id=".$row->id." AND lb.id = b.blog_id AND lb.approved=1 AND lb.published=1 ");
				$blogs = $db->loadObjectList();

				if (sizeof($blogs)) 
				{
				switch ( $blogs[0]->posts ) 
				{
					case 0:	break;
					case 1:	
						if (!$user->id) 
						{
							$row->posts = $blogs[0]->posts;
						}
					break;
					case 2:	
						if (!$user->id) 
						{
							$row->posts = $blogs[0]->posts;
						}else
						{
							if (!$this->isFriends($user->id, $row->created_by) && $user->id!=$row->created_by)
							{
								$row->posts = $blogs[0]->posts;
							}	
						}
					break;
					case 3:	
						if (!$user->id) 
						{
							$row->posts = $blogs[0]->posts;
						}else
						{
							if ($user->id!=$row->created_by)
							{
								$row->posts = $blogs[0]->posts;
							}	
						}						
					break;
					case 4:	
						if (!$user->id) 
						{
							$row->posts = $blogs[0]->posts;
						}else
						{
							if (!$this->inJSgroup($user->id, $blogs[0]->jsviewgroup))
							{
								$row->posts = $blogs[0]->posts;
							}	else $row->posts = 0;
						}
					break;
				}					
				$row->blogid = $blogs[0]->blog_id;
				$row->blogtitle = $blogs[0]->title;
				}
			}
		}

	}
}
