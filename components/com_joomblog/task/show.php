<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: show.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage show.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'show.base.php' );
require_once( JB_COM_PATH . DS . 'libraries' . DS . 'plugins.php' );

class JbblogShowTask extends JbblogShowBase
{
	var $_plugins	= null;
	var $row = null;
	var $uid = null;
	var $prev = null;
	var $next = null;
	
	function JbblogShowTask()
	{	
		$this->_plugins	= new JBPlugins();
		$this->toolbar = JB_TOOLBAR_HOME;
		
		$db	=& JFactory::getDBO();
		$show	= JRequest::getVar( 'show' , '' , 'GET' );
		$id	= JRequest::getVar( 'id' , '' , 'GET' );
		$this->uid	= (!empty( $show ) ) ? $show : $id;
		$uid	= $this->uid;

		if (is_numeric($uid)){
			$row	=& JTable::getInstance( 'Blogs' , 'Table' );
			if (!$row->load( $uid )) {return;}
			
			if (!empty($_SESSION['entrySession']))
			{
				$key = array_search($uid, $_SESSION['entrySession']);
				$this->prev = ($key) ? $_SESSION['entrySession'][$key - 1] : '';
				$this->next = ($key < count($_SESSION['entrySession']) - 1) ? $_SESSION['entrySession'][$key + 1] : '';
			}
			
		}
		
		$this->row = &$row;
	}
	
	function _header()
	{
		echo parent::_header();
		
		if($this->isJbEntry()){
      $doc = JFactory::getDocument();
      $doc->addScript(rtrim( JURI::root() , '/' ).'/components/com_joomblog/js/joomblog.js');
		}
	}
	
	function isJbEntry()
	{
		$my		=& JFactory::getUser();
		if (isset($this->row->created_by))
		return( $this->row->created_by == $my->id ); 
		else return false; 
	}

	function display($styleid = '', $wrapTag = 'div')
	{
		global $JBBLOG_LANG, $_JB_CONFIGURATION;
		
		$mainframe	=& JFactory::getApplication();
		$doc = JFactory::getDocument();
		$my	=& JFactory::getUser();
		
		if(!jbAllowedGuestView('entry'))
		{
			$template		= new JoomblogTemplate();
			$content		= $template->fetch($this->_getTemplateName('permissions'));
			return $content;
		}
		
		$Itemid		= jbGetItemId();
		$row		= null;
		$task		= '';
		$task_url	= "";

		if ($task!="")
		{
			$task_url = "&task=$task";
		}
		
		// Load plugins
		$this->_plugins->load();
		
		if (isset($this->row)) $row = &$this->row; else $row=null;
		if (!isset($row->id)) 
		{
			return JText::_('COM_JOOMBLOG_BLOG_ADMIN_NO_ENTRIES');
		}
		
		$row->permalink = jbGetPermalinkUrl($row->id);
		$row->comments = "";
		
		$row->addPosition = $_JB_CONFIGURATION->get('addThisPosition');
		
		$date			=& JFactory::getDate( $row->created );
		//$date->setOffSet( $mainframe->getCfg( 'offset' ) );

		$row->createdFormatted	= $date->toFormat( $_JB_CONFIGURATION->get('dateFormat') );
		$row->created			= $date->toFormat();

		$row->title = jbUnhtmlspecialchars($row->title);
		$row->title = htmlspecialchars($row->title); 
		
		$db			=& JFactory::getDBO();
		$date		=& JFactory::getDate();

		if ( $row->state != 1 || $row->publish_up > $date->toMySQL() )
		{	
			if($row->created_by != $my->get('id')){
				echo JText::_('COM_JOOMBLOG_CANNOT_FIND_THE_ENTRY');
				return;
			}
		}

		$query	= "UPDATE #__joomblog_posts SET hits=hits+1 WHERE id=$row->id";
		$db->setQuery( $query );
		$db->query();

		jbAddPageTitle(jbUnhtmlspecialchars($row->title));
		jbAddPathway($row->title);
		$tags	= jbGetTags($row->id);
		//looking for old metadata
		$metadata = json_decode ($row->metadata);
		$keywords	= ''.$metadata->metakey;
		if (empty( $keywords ))
		{
			foreach($tags as $tag)
			{
				if ( !empty ($tag->name))
				{
					if ( empty( $keywords ) )
					{
						$keywords	.= $tag->name ;
					}
					else $keywords	.= ', '.$tag->name ;
				}
			}
		}
		$metadescription = $row->metadesc.' '.$metadata->metadesc;
		if($doc->getDescription() == '' || $row->metadesc != '')
			$doc->setDescription( $metadescription );
		if( !empty( $row->metakey ) )
		{
			$keywords	.= ' ' . $row->metakey;
		}
		$doc->setMetaData( 'keywords' , $doc->getMetaData('keywords').' '.$keywords );

		$tpl	= new JoomblogCachedTemplate(serialize($row) . $my->usertype . $_JB_CONFIGURATION->get('template') . $task);


		if (!$tpl->is_cached())
		{
			if ($_JB_CONFIGURATION->get('useAddThis')){
				if ($_JB_CONFIGURATION->get('addThisName')!=''){
					$row->introtext = preg_replace('/{social}/', jbGetAddThis(), $row->introtext);
					$row->fulltext = preg_replace('/{social}/', jbGetAddThis(), $row->fulltext);
				} else {
					global $raiseDisqusNotice;
					if(!$raiseDisqusNotice){
						$raiseDisqusNotice=1;
						JError::raiseNotice('',JText::_('COM_JOOMBLOG_PLEASE_ENTER_YOUR_ADDTHIS_PROFILE'));
					}
					$row->introtext = str_replace('{social}', '', $row->introtext);
					$row->fulltext = str_replace('{social}', '', $row->fulltext);
				}
			} else {
					$row->introtext = str_replace('{social}', '', $row->introtext);
					$row->fulltext = str_replace('{social}', '', $row->fulltext);
			}
			
			$row->text	= '';
			
			if($row->introtext && trim($row->introtext) != '')
			{
				$row->text	.= $row->introtext;
			}
			
			if($row->fulltext && trim($row->fulltext) != '')
			{
				if($_JB_CONFIGURATION->get('anchorReadmore'))
				{
					$row->text	.= '<a name="readmore"></a>'; 
				}
				$row->text	.= $row->fulltext;
			}

		$enablecomments=true;
		$privecycomments = $row->comm;
		$usr	=& JFactory::getUser();
		switch ( $privecycomments ) {
			case 0:
				$enablecomments=true;
				break;
			case 1:	
				if (!$usr->id) 
				{
					$enablecomments=false;
				}
			break;
			case 2:	
				if (!$usr->id) 
				{
					$enablecomments=false;
				}else
				{
					if (!$this->isFriends($usr->id, $row->created_by) && $usr->id!=$row->created_by)
					{
						$enablecomments=false;
					}	
				}						
			break;
			case 3:	
				if (!$usr->id) 
				{
					$enablecomments=false;
				}else
				{
					if ($usr->id!=$row->created_by)
					{
						$enablecomments=false;
					}	
				}						
			break;
		}

			if ($enablecomments)
			{
			if ($_JB_CONFIGURATION->get('useComment') && $_JB_CONFIGURATION->get('useJomComment'))
			{
				jimport( 'joomla.filesystem.file');
				$file	= JPATH_PLUGINS . DS . 'content' . DS . 'jom_comment_bot.php';
				if (JFile::exists( $file ) )
				{
					require_once( $file );
					
 					if($_JB_CONFIGURATION->get('enableJCDashboard'))
					{
 						if(eregi('\{!jomcomment\}',$row->text))
						{
 							$row->text	= str_replace('{!jomcomment}','',$row->text);
 						}
						else if(eregi('\{jomcomment\}',$row->text))
						{
 							$row->text	= str_replace('{jomcomment}','',$row->text);
 							$row->comments	= "";
 							$row->comments 	= jomcomment($row->id, "com_joomblog");
 						}
 						else if(eregi('\{jomcomment lock\}', $row->text) )
 						{
 							$row->text	= str_replace('{jomcomment lock}','',$row->text);
 							$row->comments	= "";
 							$row->comments 	= jomcomment($row->id, "com_joomblog" , '' , '' , true );
						}
						else
						{
 							$row->comments	= "";
 							$row->comments 	= jomcomment($row->id, "com_joomblog");
 						}
 					}
					else
					{
 						$row->comments	= "";
 						$row->comments 	= jomcomment($row->id, "com_joomblog");
 					}
				}
			}elseif($_JB_CONFIGURATION->get('useComment') && !$_JB_CONFIGURATION->get('useDisqus')){
				$row->comments = jbGetComments($row->id);
			} else if ($_JB_CONFIGURATION->get('useComment') && $_JB_CONFIGURATION->get('useDisqus')){
				$row->comments = jbGetDisqusComments($row->id);
			}
			}else $row->comments ="";
						
			
			$row->author = jbUserGetName($row->created_by, $_JB_CONFIGURATION->get('useFullName'));
			//$row->authorLink = JRoute::_("index.php?option=com_joomblog$task_url&user=" . urlencode(jbGetAuthorName($row->created_by , $_JB_CONFIGURATION->get('useFullName'))) . "&Itemid=$Itemid");
			$row->authorLink = JRoute::_("index.php?option=com_joomblog$task_url&task=profile&id=" . $row->created_by . "&Itemid=$Itemid");
			$row->categories = jbCategoriesURLGet($row->id, true, $task);
			$row->jcategory	= '<a href="' . JRoute::_('index.php?option=com_joomblog&task=tag&category=' . $row->catid.'&Itemid='.$Itemid ) . '">' . jbGetJoomlaCategoryName( $row->catid ) . '</a>';
			$row->emailLink = JRoute::_("index.php?option=com_content&task=emailform&id={$this->uid}");

			$avatar	= 'Jb' . ucfirst($_JB_CONFIGURATION->get('avatar')) . 'Avatar';
			$avatar	= new $avatar($row->created_by);	
			$row->avatar	= $avatar->get();

			$row->afterContent = '';
			$row->beforeContent = '';
			
			
			$params	= $this->_buildParams();
			$row->beforeContent		= @$this->_plugins->trigger('onBeforeDisplayContent', $row, $params, 0);
			$row->onPrepareContent	= @$this->_plugins->trigger('onPrepareContent', $row, $params, 0);
			//$row->onContentPrepare  = $this->_plugins->trigger('onContentPrepare', $row, $params, 0);
			$row->afterContent		= "<br />". @$this->_plugins->trigger('onAfterDisplayContent', $row, $params, 0);

			$row->editLink = '<span class="editLink"><a href="index.php?option=com_joomblog&task=write&id='.$row->id.'&Itemid='.$Itemid.'"><img border="0" src="'.JURI::base().'media/system/images/edit.png" alt="Edit" name="Edit"></a></span>';

			if($_JB_CONFIGURATION->get('enableBackLink'))
				$row->afterContent .= jbGetBackLink();

			$enablePdf		= ( boolean ) $_JB_CONFIGURATION->get('enablePdfLink');
			$enablePrint	= ( boolean ) $_JB_CONFIGURATION->get( 'enablePrintLink' );
			
			$tpl->set( 'enablePdfLink' 	, $enablePdf );
			$tpl->set( 'enablePrintLink', $enablePrint );

			unset($row->_table);
			unset($row->_key);

			$my		=& JFactory::getUser();
			
			$tpl->set('userId', $my->id);
			$tpl->set('categoryDisplay' , $_JB_CONFIGURATION->get('categoryDisplay') );
			
			
			
	//SMT social int
		
		/*** Twitter ***/
		$usetwitter = $_JB_CONFIGURATION->get('usetwitter');
		$row->twitter_button = null;
		$row->twposition = $_JB_CONFIGURATION->get('positiontwitterInPost');
		
		if ($usetwitter)
		{
			$showTwInPost = $_JB_CONFIGURATION->get('showtwitterInPost');
			
			
			if ($showTwInPost)
			{
				$twitStyle 	= $_JB_CONFIGURATION->get('twitterpoststyle');
				$twitFlSt 	= $_JB_CONFIGURATION->get('twitterfollowpoststyle');
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
					
					//$row->twitter_button.='<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
					
			}
								
		}
		
		/*** Facebook ***/
		$usefacebook = $_JB_CONFIGURATION->get('usefacebook');
		$row->fb_button = null;
		$row->fbposition = $_JB_CONFIGURATION->get('positionfbInPost');
		
		if ($usefacebook)
		{
			$showFbInPost = $_JB_CONFIGURATION->get('showfbInPost');
			
			if ($showFbInPost)
			{
				$fbStyle 		= $_JB_CONFIGURATION->get('fb_style_post');
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
		$row->gpposition = $_JB_CONFIGURATION->get('positiongpInPost');
		
		if ($usegp)
		{
			$showGpInPost = $_JB_CONFIGURATION->get('showgpInPost');
			$gpLang 	= $_JB_CONFIGURATION->get('gp_language');
			if (isset($gpLang)) $gpLang="{lang: '" . $gpLang . "'}"; else $gpLang='';
			
			if ($showGpInPost)
			{
				$gpStyle 		= $_JB_CONFIGURATION->get('gp_style_post');
				$gpUrl 	= $row->permalink;	
				
				//$row->gp_button='<script type="text/javascript" src="https://apis.google.com/js/plusone.js">'.$gpLang.'</script>';

					switch ( $gpStyle ) 
					{
						case 'none': 
							$row->gp_button.='<g:plusone size="small" href="'.$gpUrl.'"></g:plusone>';
						break;
						case 'horizontal': 
							$row->gp_button.='<g:plusone count="false" href="'.$gpUrl.'"></g:plusone>';
						break;
						case 'vertical': 
							$row->gp_button.='<g:plusone size="tall" href="'.$gpUrl.'"></g:plusone>';
						break;
					}					
									
			}
								
		}

        /*** Pinterest ***/

        $usepi = $_JB_CONFIGURATION->get('usepi');
        $row->pi_button = null;
        $row->piposition = $_JB_CONFIGURATION->get('positionpiInPost');

        if ($usepi) {
            $showPiInPost = $_JB_CONFIGURATION->get('showpiInPost');
            $piLang 	= $_JB_CONFIGURATION->get('pi_language');
            $piLang = (isset($piLang)) ? "{lang: '" . $piLang . "'}" : '';

            if ($showPiInPost)
            {
                $piStyle        = $_JB_CONFIGURATION->get('pi_style_post');
                $piUrl          = $row->permalink;
                $piDescription  = $row->title;
                preg_match_all('#<img.*?src=["\']*([\S]+)["\'].*?>#', $row->introtext . $row->fulltext, $piImageTemp);
                /*'#<img.*?src=["\']*([\S]+)["\'].*?>#si'*/
                $piImage        = ($piImageTemp == '') ? 'none' : '&media=' . @$piImageTemp[1][0];
                $pi_url_text    = '<a href="http://pinterest.com/pin/create/button/?url=' . $piUrl . $piImage . '&description=' . $piDescription . '" class="pin-it-button" count-layout="' . $piStyle . '"><img border="0" src="http://assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>';
                $row->pi_button .= $pi_url_text;
                $row->pi_button .= '<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js">' . $piLang . '</script>';
            }
        }

        //

		/*** Linkedin ***/
		$useln = $_JB_CONFIGURATION->get('useln');
		$row->ln_button = null;
		$row->lnposition = $_JB_CONFIGURATION->get('positionlnInPost');
		
		if ($useln)
		{
			$showLnInPost = $_JB_CONFIGURATION->get('showlnInPost');
			
			if ($showLnInPost)
			{
				$lnStyle 		= $_JB_CONFIGURATION->get('ln_style_post');
				$lnUrl 	= $row->permalink;	
				
				//$row->ln_button='<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>';
				
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
		
		/*** AddThis ***/
		$useat = $_JB_CONFIGURATION->get('useAddThis');
		$row->at_button = null;
		$row->atposition = $_JB_CONFIGURATION->get('addThisPostPosition');
		
			if ($useat)
				if ($_JB_CONFIGURATION->get('showAddThisInPost') == 1)
				{						
				$sefUrl = $row->permalink;
				$host = $_JB_CONFIGURATION->get('addThisName');
				$button_style = $_JB_CONFIGURATION->get('addthis_post_button_style');

					$services = array();
					
					if ($button_style == 'style2'){	$add = '<!-- AddThis Button BEGIN --><div class="addthis_toolbox addthis_default_style addthis_32x32_style">';} 
					else if ($button_style == 'style9' || $button_style == 'style10'){ $add = '';} else {$add = '<!-- AddThis Button BEGIN --><div class="addthis_toolbox addthis_default_style ">';}
					
					$add .= socialDefaultAdd($button_style, $sefUrl, $host);
					$row->at_button = $add;
				}
	//
			
			
			
			$tpl->set('entry', $tpl->object_to_array($row));
			
			$prevHTML = ''; $nextHTML = '';
			if ($this->prev)
			{
				$temp	=& JTable::getInstance( 'Blogs' , 'Table' );
				$temp->load( $this->prev );
				
				$prevHTML .= '<a href="'.JRoute::_('index.php?option=com_joomblog&show='.$this->prev.'&Itemid='.$Itemid).'" title="View previous post: '.$temp->title.'">'.$temp->title.'</a>';
			}
			
			$tpl->set( 'prev' 	, $prevHTML );
			unset($temp);
			
			if ($this->next)
			{
				$temp	=& JTable::getInstance( 'Blogs' , 'Table' );
				$temp->load( $this->next );
				
				$nextHTML .= '<a href="'.JRoute::_('index.php?option=com_joomblog&show='.$this->next.'&Itemid='.$Itemid).'" title="View next post: '.$temp->title.'">'.$temp->title.'</a>';
			}
			
			$tpl->set( 'next' 	, $nextHTML );
		}
		
		$content	= '';
		
		$path	= $this->_getTemplateName( 'entry' );
		
		$content .= $tpl->fetch_cache( $path );
		return $content;		
	}
	
	protected function isFriends($id1=0,$id2=0)
	{
		$db	=& JFactory::getDBO();
		$db->setQuery(	" SELECT `connection_id` FROM `#__community_connection` " .
						" WHERE connect_from=".(int)$id1." AND connect_to=".(int)$id2." AND `status`=1 ");
		$frindic = $db->loadResult();
		if ($frindic) return true; else return false;				
	}
}