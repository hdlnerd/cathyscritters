<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @package JoomBlog
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.utilities.date' );
jimport( 'joomla.filesystem.file' );

class JbblogBaseController
{
	var $toolbar = JB_TOOLBAR_HOME;
	var $pageTitle = "";
	var $category = "";

	function execute(){
		$content = $this->display();
		echo $this->_header();
		echo $content;
		echo $this->_footer();
	}
	
	function JbblogBaseController()
	{		
		$db	=& JFactory::getDBO();
		
		$this->category = JRequest::getVar( 'tag' , '' , 'REQUEST' );
		if( !empty( $this->category ) )
		{
			$this->category = strval(urldecode( $this->category ));
			$this->category = str_replace("+", " ", $this->category);
		}
		
		$db->setQuery("SELECT rules FROM #__assets WHERE name = 'com_joomblog' ");
		if($db->loadResult() == "{}" || !$db->loadResult()){
			$db->setQuery('UPDATE #__assets SET rules=\'{"core.admin":{"1":0,"6":0,"7":0,"2":0,"3":0,"4":0,"5":0,"10":0,"12":0,"8":0},"core.manage":{"1":0,"6":0,"7":0,"2":0,"3":0,"4":0,"5":0,"10":0,"12":0,"8":0},"core.create":{"1":1,"6":0,"7":0,"2":1,"3":0,"4":1,"5":1,"10":1,"12":0,"8":0},"core.delete":{"1":1,"6":1,"7":1,"2":1,"3":1,"4":1,"5":1,"10":1,"12":1,"8":0},"core.edit":{"1":1,"6":1,"7":1,"2":1,"3":1,"4":1,"5":1,"10":1,"12":1,"8":0},"core.edit.state":{"1":1,"6":1,"7":1,"2":1,"3":1,"4":1,"5":1,"10":1,"12":1,"8":0},"core.edit.own":{"1":1,"6":1,"7":1,"2":1,"3":1,"4":1,"5":1,"10":1,"12":1,"8":0}}\' WHERE name = "com_joomblog" ');
			$db->query();
		}
	}

	function _header()
	{
		global $_JB_CONFIGURATION, $JOOMBLOG_LANG;;
		
		$mainframe	=& JFactory::getApplication();
		$doc = JFactory::getDocument();
		$db	=& JFactory::getDBO();
		
		$doc->addScript(rtrim( JURI::base() , '/' ).'/components/com_joomblog/js/jquery-1.8.3.min.js');
		$doc->addScript(rtrim( JURI::base() , '/' ).'/components/com_joomblog/js/joomblog.js');
		$doc->addScript('https://apis.google.com/js/plusone.js');
		$doc->addScriptDeclaration("var baseurl = '".JURI::base()."'");
		
		/*** SMT SOCIAL ADDED ***/
		$usetwitter = $_JB_CONFIGURATION->get('usetwitter');
		$usefacebook = $_JB_CONFIGURATION->get('usefacebook');
		$usegp = $_JB_CONFIGURATION->get('usegp');
		$useln = $_JB_CONFIGURATION->get('useln');
		$useat = $_JB_CONFIGURATION->get('useAddThis');
        $usepi = $_JB_CONFIGURATION->get('usepi');
						
		if ($usetwitter) {
            $doc->addScript('http://platform.twitter.com/widgets.js');
        }
		//if ($usefacebook) { $doc->addScript('http://connect.facebook.net/en_US/all.js#appId=259018057462154&amp;xfbml=1'); }
		if ($usegp) {
            $doc->addScript('https://apis.google.com/js/plusone.js');
            $gpLang 	= $_JB_CONFIGURATION->get('gp_language');
            if (isset($gpLang)) $doc->addScriptDeclaration("{lang: '" . $gpLang . "'}");
        }
		if ($useln) {
            $doc->addScript('http://platform.linkedin.com/in.js');
        }
		if ($useat) {
				$lang = $_JB_CONFIGURATION->get('addthis_language') ? $_JB_CONFIGURATION->get('addthis_language') : 'en';
				$doc->addScriptDeclaration('var addthis_config = {"ui_language":\'' . $lang . '\'};');
				$doc->addScript('http://s7.addthis.com/js/250/addthis_widget.js#pubid=' . $_JB_CONFIGURATION->get('addThisName'));
		}
        if ($usepi) {

        }
		
		/***/
		

			if ($_JB_CONFIGURATION->get('overrideTemplate'))
			{
				$jbCustomTplStyle = JPATH_base . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'html' . DS . 'com_joomblog' . DS . 'css' . DS . 'template_style.css';
				
				if( JFile::exists($jbCustomTplStyle) )
				{
					$style = '<link rel="stylesheet" type="text/css" href="' . rtrim( JURI::base() , '/' ) . '/templates/' .$mainframe->getTemplate() .'/html/com_joomblog/css/template_style.css" />';
					$doc->addStyleSheet(rtrim( JURI::base() , '/' ) . '/templates/' .$mainframe->getTemplate() ."/html/com_joomblog/css/template_style.css");
				}
				else
				{
					if( JFile::exists(JB_TEMPLATE_PATH . "/" . $_JB_CONFIGURATION->get('template') . "/css/template_style.css"))
					{
						$style = '<link rel="stylesheet" type="text/css" href="' . rtrim( JURI::base() , '/' ) . '/components/com_joomblog/templates/' . $_JB_CONFIGURATION->get('template') . "/css/template_style.css" . '"/>';
						$doc->addStyleSheet(rtrim( JURI::base() , '/' ) . '/components/com_joomblog/templates/' . $_JB_CONFIGURATION->get('template') . "/css/template_style.css");
					}	
				}
			}
			else
			{
				if(JFile::exists(JB_TEMPLATE_PATH . "/" . $_JB_CONFIGURATION->get('template') . "/css/template_style.css"))
				{
					$style = '<link rel="stylesheet" type="text/css" href="' . rtrim( JURI::base() , '/' ) . '/components/com_joomblog/templates/' . $_JB_CONFIGURATION->get('template') . "/css/template_style.css" . '"/>';
					$doc->addStyleSheet(rtrim( JURI::base() , '/' ) . '/components/com_joomblog/templates/' . $_JB_CONFIGURATION->get('template') . "/css/template_style.css");
				}
			}
	
		
		if ( JFile::exists(JB_TEMPLATE_PATH . "/" . $_JB_CONFIGURATION->get('template') . "/css/IE6.css" ) )
	       {
	                       $style  = "\r\n";
	                       $style .= '<!--[if lte IE 6]>'."\r\n";                            
	                       $style .= '<link rel="stylesheet" type="text/css" href="' . rtrim( JURI::base() , '/' ) . '/components/com_joomblog/templates/' . $_JB_CONFIGURATION->get('template') . "/css/IE6.css" . '"/>'."\r\n";
	                       $style .= '<![endif]-->'."\r\n";                                   
	                       $doc->addCustomTag($style);
	       }

		if ( JFile::exists(JB_TEMPLATE_PATH . "/" . $_JB_CONFIGURATION->get('template') . "/css/IE7.css" ) )
		{
			$style  = "\r\n";
			$style  .= '<!--[if IE 7]>'."\r\n"; 
			$style .= '<link rel="stylesheet" type="text/css" href="' . rtrim( JURI::base() , '/' ) . '/components/com_joomblog/templates/' . $_JB_CONFIGURATION->get('template') . "/css/IE7.css" . '"/>'."\r\n"; 
			$style .= '<![endif]-->'."\r\n"; 	
			$doc->addCustomTag($style);
		}
		
		if ($_JB_CONFIGURATION->get('useNewYearStyleheader'))
		{
			$footer_class = 'class="footer-new-year"';
		}
		$html  = '<div id="joomBlog-wrap" '.$footer_class.'>';
		
		if ($_JB_CONFIGURATION->get('useRSSFeed'))
		{
			
			$rssLink = "index.php?option=com_joomblog";
			if (isset ($_REQUEST['blogger']) and $_REQUEST['blogger'] != "")
				$rssLink .= "&user=" . htmlspecialchars($_REQUEST['blogger']);
			
			if (isset ($_REQUEST['tag']) and isset($_REQUEST['category']) && $_REQUEST['category'] != "")
				$rssLink .= "&tag=" . htmlspecialchars($_REQUEST['category']);
			
			if (isset ($_REQUEST['keyword']) and $_REQUEST['keyword'] != "")
				$rssLink .= "&keyword=" . htmlspecialchars($_REQUEST['keyword']);
			
			if (isset ($_REQUEST['archive']) and $_REQUEST['archive'] != "")
				$rssLink .= "&archive=" . htmlspecialchars($_REQUEST['archive']);
			
			if (isset ($_REQUEST['Itemid']) and $_REQUEST['Itemid'] != "" and $_REQUEST['Itemid'] != "0")
				$rssLink .= "&Itemid=" . intval($_REQUEST['Itemid']);
			else
			{

				$query = "SELECT id FROM #__menu  WHERE type='components' "
				        ."AND link='index.php?option=com_joomblog' "
				        ."AND published='1'";
	
				$db->setQuery($query);
	
				$jbItemid = $db->loadResult();
				if (!$jbItemid)
					$jbItemid = 1;
				$Itemid = $jbItemid;
			}
			$rssLink	.= "&task=rss";
            
            if ($_JB_CONFIGURATION->get('useFeedBurnerIntegration') == "0") {
                $rssLink	= JRoute::_($rssLink);
            } else {
                $rssLink = $_JB_CONFIGURATION->get('rssFeedBurner');
            }
            
			$blogger	= JRequest::getVar( 'user' , '' , 'GET' );
			if(isset($blogger) && !empty($blogger))
			{

				if($_JB_CONFIGURATION->get('userUseFeedBurner'))
				{

					$user		=& JTable::getInstance('BlogUsers','Table');
					$user->load(JbGetAuthorId($blogger));

					if($user->feedburner == '' && $_JB_CONFIGURATION->get('useFeedBurner'))
					{
						$rssLink	= $_JB_CONFIGURATION->get('useFeedBurnerURL');
					}
					else
					{
						$rssLink	= $user->feedburner;
					}
				}
			}
			

			if($_JB_CONFIGURATION->get('useFeedBurner') && empty($blogger))
			{
				$rssLink	= $_JB_CONFIGURATION->get('useFeedBurnerURL');
			}
			
			$rss = '<div class="topFeed">' .
						'<a href="'.$rssLink.'">' .
							'<span>'.$_JB_CONFIGURATION->get('titleFeed').'</span>' .
						'</a>' .
					'</div>';
			$html .= $rss;
		}
		
				
		$tmpl = JRequest::getVar('tmpl');
		if (!$tmpl || $tmpl!='component') 
		{
			$html .= $this->_showToolbar($this->toolbar);
		}
		
		return $html;
	}
	
	function _footer()
	{
		$html  = getPoweredByLink();
		$html .= '</div>'; 
		return $html;
	}

	function _buildParams()
	{
		$mainframe	=& JFactory::getApplication();
		
		$mosParams = new JParameter('');
		$mosParams->def('show_title', "");
		$mosParams->def('link_titles', "");
		$mosParams->def('show_intro', "");
		$mosParams->def('show_category', "");
		$mosParams->def('link_category', "");
		$mosParams->def('show_parent_category', "");
		$mosParams->def('link_parent_category', "");
		$mosParams->def('show_author', "");
		$mosParams->def('link_author', "");
		$mosParams->def('show_create_date', "");
		$mosParams->def('show_modify_date', "");
		$mosParams->def('show_publish_date', "");
		$mosParams->def('show_item_navigation', "");
		$mosParams->def('show_icons', "");
		$mosParams->def('show_print_icon', "");
		$mosParams->def('show_email_icon', "");
		$mosParams->def('show_vote', "");
		$mosParams->def('show_hits', "");
		$mosParams->def('show_noauth', "");
		$mosParams->def('alternative_readmore', "");
		$mosParams->def('article_layout', "");

		return $mosParams;
	}

	function _showToolbar($op = "")
	{
		global $JOOMBLOG_LANG, $Itemid , $_JB_CONFIGURATION;

		$mainframe	=& JFactory::getApplication();		
		$show		= array();
		$category	= JRequest::getVar( 'category' , '' , 'GET' );
		$search		= JRequest::getVar( 'search' , '' , 'GET' );
		$db	=& JFactory::getDBO();
		$document	=& JFactory::getDocument();
		
		$blogger	= JRequest::getVar( 'user' , '' , 'GET' );

		$isBlogger	= jbGetUserCanPost();
		
		if( $isBlogger )
		{
			jbAddEditorHeader();
		}
		
		$show['feed'] = $_JB_CONFIGURATION->get('useRSSFeed');

		$rssLink	 = '';
		if($show['feed'])
		{
			$rssLink = "index.php?option=com_joomblog";
			if (isset ($_REQUEST['blogger']) and $_REQUEST['blogger'] != "")
				$rssLink .= "&user=" . htmlspecialchars($_REQUEST['blogger']);
			
			if (isset ($_REQUEST['tag']) and isset($_REQUEST['category']) && $_REQUEST['category'] != "")
				$rssLink .= "&tag=" . htmlspecialchars($_REQUEST['category']);
			
			if (isset ($_REQUEST['keyword']) and $_REQUEST['keyword'] != "")
				$rssLink .= "&keyword=" . htmlspecialchars($_REQUEST['keyword']);
			
			if (isset ($_REQUEST['archive']) and $_REQUEST['archive'] != "")
				$rssLink .= "&archive=" . htmlspecialchars($_REQUEST['archive']);
			
			if (isset ($_REQUEST['Itemid']) and $_REQUEST['Itemid'] != "" and $_REQUEST['Itemid'] != "0")
				$rssLink .= "&Itemid=" . intval($_REQUEST['Itemid']);
			else
			{

				$query = "SELECT id FROM #__menu  WHERE type='components' "
				        ."AND link='index.php?option=com_joomblog' "
				        ."AND published='1'";
	
				$db->setQuery($query);
	
				$jbItemid = $db->loadResult();
				if (!$jbItemid)
					$jbItemid = 1;
				$Itemid = $jbItemid;
			}
			$rssLink	.= "&task=rss";
			$rssLink	= JRoute::_($rssLink);

			if(isset($blogger) && !empty($blogger))
			{

				if($_JB_CONFIGURATION->get('userUseFeedBurner'))
				{

					$user		=& JTable::getInstance('BlogUsers','Table');
					$user->load(JbGetAuthorId($blogger));

					if($user->feedburner == '' && $_JB_CONFIGURATION->get('useFeedBurner'))
					{
						$rssLink	= $_JB_CONFIGURATION->get('useFeedBurnerURL');
					}
					else
					{
						$rssLink	= $user->feedburner;
					}
				}
			}
			

			if($_JB_CONFIGURATION->get('useFeedBurner') && empty($blogger))
			{
				$rssLink	= $_JB_CONFIGURATION->get('useFeedBurnerURL');
			}

			$rssTitle = $JOOMBLOG_LANG['_JB_RSS_BLOG_ENTRIES'];
			
			if ($blogger && $blogger != "")
			{
				$rssTitle .= $JOOMBLOG_LANG['_JB_RSS_BLOG_FOR'] . ' ' . $blogger;
			}
			
			
			if ($category && $category != "")
			{
				$rssTitle .= ' ' . $JOOMBLOG_LANG['_JB_RSS_BLOG_TAGGED'] . ' \'' . htmlspecialchars($category) . "'";
			}
			
			
			if ($search && $search != "")
			{
				$rssTitle .= "," . $JOOMBLOG_LANG['_JB_RSS_BLOG_KEYWORD'] . "'" . htmlspecialchars($search) ."'";
			}
			
			$rssLinkHeader = "\r\n".'<link rel="alternate" type="application/rss+xml" title="' . $rssTitle . '" href="' . $rssLink . '" />'."\r\n";
			                              
	        $document->addCustomTag($rssLinkHeader);
		}
		
			
		if($_JB_CONFIGURATION->get('frontpageToolbar'))
		{
			$Itemid	= jbGetDefaultItemId();
			$dashboardItemid = jbGetAdminItemId();
			$task = JRequest::getCmd('task');
			$view = JRequest::getVar( 'view' , '' , 'GET' );
			$user = JRequest::getVar( 'user' , '' , 'GET' );
			
			$homeLink = JRoute::_("index.php?option=com_joomblog&view=default&Itemid=$Itemid");
			$blogsLink = JRoute::_("index.php?option=com_joomblog&task=blogs&Itemid=$Itemid");
			$tagsLink = JRoute::_("index.php?option=com_joomblog&task=viewtags&Itemid=$Itemid");
			$searchLink = JRoute::_("index.php?option=com_joomblog&task=search&Itemid=$Itemid");
			$bloggersLink = JRoute::_("index.php?option=com_joomblog&task=users&Itemid=$Itemid");
			$accountLink = JRoute::_("index.php?option=com_joomblog&task=adminhome&Itemid=$dashboardItemid");
			$categoriesLink = JRoute::_("index.php?option=com_joomblog&task=categories&Itemid=$Itemid");
			$archiveLink = JRoute::_("index.php?option=com_joomblog&task=archive&Itemid=$Itemid");
			$addBlogLink = JRoute::_("index.php?option=com_joomblog&Itemid=$Itemid&task=newblog");					
			$dashboardClass = "thickbox";
			$thickboxScript="";
			
			if(!class_exists('JoomblogTemplate'))
			    include_once( JB_COM_PATH.DS.'template.php' );
	
			$tpl = new JoomblogTemplate();
			$toolbar = array();
			$active	= array();

			$toolbar['op'] = $op;	
			$toolbar['homeLink'] = $homeLink;
			$toolbar['blogsLink'] = $blogsLink;
			$toolbar['bloggersLink'] = $bloggersLink;
			$toolbar['tagsLink'] = $tagsLink;
			$toolbar['searchLink'] = $searchLink;
			$toolbar['accountLink'] = $accountLink;
			$toolbar['categoriesLink'] = $categoriesLink;
			$toolbar['archiveLink'] = $archiveLink;			
			$toolbar['write'] = JRoute::_('index.php?option=com_joomblog&task=write&id=0&Itemid='.jbGetItemId());
			$toolbar['addBlogLink'] = $addBlogLink;

			$active['home']	= '';
			$active['category'] = '';
			$active['search'] ='';
			$active['blogger'] ='';
			$active['account']= '';
			$active['archive'] = '';
			$active['tags'] ='';
			$active[$op] = ' blogActive';
	  
	  if ($task == "categories"){
			$active['blogger'] ='';
			$active['category'] = ' blogActive';
	  }
	  
	  if ($task == "archive" || $view == 'archives'){
			$active['blogger'] ='';
			$active['archive'] = ' blogActive';
	  }
	  
	  if ($task == "viewtags" || $view == 'tags'){
			$active['blogger'] ='';
			$active['tags'] = ' blogActive';
	  }
	  
	   if ($view == 'user' || $user != ''){
			$active['blogger'] ='';
			$active['home'] = ' blogActive';
	  }
	  
      if($task == "bloggerstats" || $task == "adminhome" || $task == "bloggerpref" || $task == "showcomments"){
        $active['account']= ' blogActive';
      }elseif($op == "blogger" && $view != 'user' && $user == ''){
				$active['home']= '';
			}

			if ($op == "userblog")
			{
				$homeLink				= JRoute::_("index.php?option=com_joomblog&Itemid=$Itemid&task=userblog");
				$manageBlogLink 		= JRoute::_("index2.php?option=com_joomblog&admin=1&task=adminhome&Itemid=$Itemid&keepThis=true&TB_iframe=true&height=600&width=850");
				$toolbar['homeLink'] 	= $homeLink;
				$toolbar['manageBlogLink'] = $manageBlogLink;
			}
			else 
			{
				$toolbar['rssFeedLink'] = $rssLink;
			}
			
			$title 	= '';
			$desc	= '';
			
			if( $task == 'userblog' )
			{
				$jb			=& JFactory::getUser();
				$blogger	= $jb->username;
			}
			
			if( !empty( $blogger ) )
			{
				$title	= stripslashes(JbGetAuthorTitle(JbGetAuthorId($blogger)));
				$desc	= stripslashes(JbGetAuthorDescription(JbGetAuthorId($blogger)));	
			}
			
			if ($_JB_CONFIGURATION->get('showPrimaryTitles'))
			{
			$title 	= empty($title) ? stripslashes($_JB_CONFIGURATION->get('mainBlogTitle'))	: $title;
			$desc	= empty($desc)  ? stripslashes($_JB_CONFIGURATION->get('mainBlogDesc'))	: $desc;
			}			
			
			$tpl->set('toolbar', $toolbar);
			$tpl->set('show', $show);
			$tpl->set('active', $active);
			$tpl->set('title', $title);
			$tpl->set('summary', $desc);
			
			$templateFile	= $this->_getTemplateName( 'toolbar' );
	
			$toolbar_output = $tpl->fetch($templateFile);
			return $toolbar_output;
		}else
		if ($_JB_CONFIGURATION->get('showPrimaryTitles'))
		{
			$task = JRequest::getCmd('task');
			$blogger	= JRequest::getVar( 'user' , '' , 'GET' );
			
			if(!class_exists('JoomblogTemplate'))
			    include_once( JB_COM_PATH.DS.'template.php' );
	
			$tpl = new JoomblogTemplate();
			
			$title 	= '';
			$desc	= '';
			
			if( $task == 'userblog' )
			{
				$jb			=& JFactory::getUser();
				$blogger	= $jb->username;
			}
			if( !empty( $blogger ) )
			{
				$title	= stripslashes(JbGetAuthorTitle(JbGetAuthorId($blogger)));
				$desc	= stripslashes(JbGetAuthorDescription(JbGetAuthorId($blogger)));	
			}
			
			$title 	= empty($title) ? stripslashes($_JB_CONFIGURATION->get('mainBlogTitle'))	: $title;
			$desc	= empty($desc)  ? stripslashes($_JB_CONFIGURATION->get('mainBlogDesc'))	: $desc;
			
			$tpl->set('title', $title);
			$tpl->set('summary', $desc);
			
			$templateFile	= $this->_getTemplateName( 'onlyprimarybar' );
	
			$head_output = $tpl->fetch($templateFile);
			return $head_output;
		}		
	}

	function _getTemplateName($templateType)
	{
		global $_JB_CONFIGURATION;
		
		$mainframe	=& JFactory::getApplication();

		$template	= JB_TEMPLATE_PATH . DS . 'default' . DS . $templateType . '.tmpl.html';

		if ($_JB_CONFIGURATION->get('overrideTemplate'))
		{
			$path		= JPATH_base . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'html' . DS . 'com_joomblog' . DS . $templateType . '.tmpl.html';
			
			$template	= JFile::exists( $path ) ? $path : $template;
		}
		else
		{
			$path		= JB_TEMPLATE_PATH . DS . $_JB_CONFIGURATION->get('template') . DS . $templateType . '.tmpl.html';
			$template	= JFile::exists( $path ) ? $path : $template;
		}
		
		return $template;
	}
	
	function _checkViewPermissions($context){

	}
}
