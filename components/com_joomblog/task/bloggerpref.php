<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: bloggerpref.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage bloggerpref.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'base.php' );

class JbblogBloggerprefTask extends JbblogBaseController
{

	function JbblogBloggerprefTask()
	{
		$this->toolbar	= JB_TOOLBAR_BLOGGER;
	}
	
	function display()
	{
		global $_JB_CONFIGURATION;

		$my	=& JFactory::getUser();
		$mainframe =& JFactory::getApplication();
		$pathway =& $mainframe->getPathway();

		$user =& JTable::getInstance( 'BlogUsers' , 'Table' );
		$user->load( $my->id );
		
		if( JRequest::getMethod() == 'POST' ){
			$profile = JRequest::getVar('blog-subtitle','', 'POST');
			$feedburner	= JRequest::getVar('feedburnerURL','','POST');
			$title = JRequest::getVar('blog-title', '', 'POST');
			$about = JRequest::getVar('blog-aboutme', '', 'POST');
			$twitter = JRequest::getVar('blog-twitter', '', 'POST');
			$site = JRequest::getVar('blog-site', '', 'POST');
			$day = JRequest::getInt('blog-day', 0);
			$month = JRequest::getInt('blog-month', 0);
			$year = JRequest::getInt('blog-year', 0);
			$remove_avatar = JRequest::getVar('remove_avatar', '', 'POST');
			
			$date_birthday = sprintf("%02d",$year)."-".sprintf("%02d",$month)."-".sprintf("%02d",$day);
							
			$user->description	= strip_tags($profile);
			$user->feedburner = $feedburner;
			$user->title = strip_tags( $title );
			$user->site = strip_tags( $site );
			$user->about = strip_tags( $about );
			$user->twitter = strip_tags( $twitter );
			$user->birthday	= $date_birthday;
			
			if( $user->store() ){
				$mainframe->enqueueMessage( JText::_('COM_JOOMBLOG_BLOG_ADMIN_PROFILE_UPDATED') ); 
			}
			
			JbblogBloggerprefTask::uploadAvatar($user->user_id);
			
		}

		$pathway->addItem(JText::_('COM_JOOMBLOG_ADMIN_MENU_PREFERENCES'),'');

		$showFeedburner	=$_JB_CONFIGURATION->get('userUseFeedBurner') ? true : false;
		
		jbAddEditorHeader();
		
		$tpl = new JoomblogTemplate();

		$user->name = $my->get('name');
		$user->day = date("d",strtotime($user->birthday." 00:00:00"));
		$user->month = date("n",strtotime($user->birthday." 00:00:00"));
		$user->year = date("Y",strtotime($user->birthday." 00:00:00"));

		$tpl->set('showFeedburner', $showFeedburner);
		$tpl->set('user' , array( $user ) );		
		$tpl->set('Jbitemid', JbGetItemId());
		$tpl->set('postingRights', JbGetUserCanPost());
		$tpl->set('description', stripslashes($user->description));
		$tpl->set('descColor', $user->getStyle('blog-subtitle-color'));
		$tpl->set('title', stripslashes($user->title));
		$tpl->set('titleColor', $user->getStyle('blog-title-color'));
		
		$html = $tpl->fetch(JB_TEMPLATE_PATH."/admin/blogger_profile.html");

		return $html;
	}
	
	function uploadAvatar($cid)
{		
		global $_JB_CONFIGURATION;
		
		$db = JFactory::getDBO();
		jimport('joomla.filesystem.file');
		jimport('joomla.utilities.utility');

		include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_joomblog'.DS.'helpers'.DS.'image.php');
		$user			= JFactory::getUser($cid);			
		
		$mainframe =& JFactory::getApplication();		
		$file		= JRequest::getVar( 'Filedata' , '' , 'FILES' , 'array' );
		$userid		= $user->id;
		$url		= 'index.php?option=com_joomblog&task=bloggerpref';
		
		if( !isset( $file['tmp_name'] ) || empty( $file['tmp_name'] ) )
		{	
			return true;
		}
		else
		{
				
			$uploadLimit	= (double) $_JB_CONFIGURATION->get('maxFileSize');
			$uploadLimit	= ( $uploadLimit * 1024 * 1024 );

			// @rule: Limit image size based on the maximum upload allowed.
			if( filesize( $file['tmp_name'] ) > $uploadLimit && $uploadLimit != 0 )
			{
				$mainframe->enqueueMessage( JText::_('COM_JOOMBLOG_IMAGEFILESIZEEXCEEDED') , 'error' );

				if(isset($url)){
					$mainframe->redirect($url);
				}
			}
			
            if( !JBImageHelper::isValidType( $file['type'] ) )
			{
				$mainframe->enqueueMessage( JText::_('COM_JOOMBLOG_IMAGEFILENOTSUPPORTED') , 'error' );

				if(isset($url))
				{
					$mainframe->redirect($url);
				}

				$mainframe->redirect( CRoute::_('index.php?option=com_joomblog&task=bloggerpref') );
            }
				
			if( !JBImageHelper::isValid($file['tmp_name'] ) )
			{
				$mainframe->enqueueMessage(JText::_('COM_JOOMBLOG_IMAGEFILENOTSUPPORTED'), 'error');

				if(isset($url)){
					$mainframe->redirect($url);
				}
			}
			else
			{
				
				// @todo: configurable width?
				$imageMaxWidth	= $_JB_CONFIGURATION->get('avatarWidth');

				// Get a hash for the file name.
				
				$fileName		= JUtility::getHash( $file['tmp_name'] . time() );
				$hashFileName	= JString::substr( $fileName , 0 , 24 );
					
				//@todo: configurable path for avatar storage?

				$storage			= JPATH_ROOT . DS . 'images'. DS . 'joomblog' . DS . 'avatar';
				$storageImage		= $storage . DS . $hashFileName . JBImageHelper::getExtension( $file['type'] );
				$storageThumbnail	= $storage . DS . 'thumb_' . $hashFileName . JBImageHelper::getExtension( $file['type'] );
				$image				= 'images/joomblog/avatar/' . $hashFileName . JBImageHelper::getExtension( $file['type'] );
				$thumbnail			= 'images/joomblog/avatar/' . 'thumb_' . $hashFileName . JBImageHelper::getExtension( $file['type'] );
						
					
				// Only resize when the width exceeds the max.
				if( !JBImageHelper::resizeProportional( $file['tmp_name'] , $storageImage , $file['type'] , $imageMaxWidth ) )
				{
					$mainframe->enqueueMessage(JText::sprintf('COM_JOOMBLOG_ERROR_MOVING_UPLOADED_FILE' , $storageImage), 'error');

					if(isset($url)){
						$mainframe->redirect($url);
					}
				}

				// Generate thumbnail
				if(!JBImageHelper::createThumb( $file['tmp_name'] , $storageThumbnail , $file['type'] ))
				{
					$mainframe->enqueueMessage(JText::sprintf('COM_JOOMBLOG_ERROR_MOVING_UPLOADED_FILE' , $storageThumbnail), 'error');

					if(isset($url)){
						$mainframe->redirect($url);
					}
				}			
				
				$db->setQuery("UPDATE #__joomblog_user SET avatar = '".$hashFileName . JBImageHelper::getExtension( $file['type'] )."' WHERE user_id =".$cid);
				$db->query();
				
				return true;
			}
		}
	}
	
	function removeAvatar($cid)
	{
		$db = JFactory::getDBO();
		jimport('joomla.filesystem.file');
		jimport('joomla.utilities.utility');
		
		$mainframe =& JFactory::getApplication();
		$db->setQuery("SELECT avatar FROM #__joomblog_user WHERE user_id=".$cid);
		$avatar = $db->loadResult();
		$filename = JPATH_ROOT.DS.'images'.DS.'joomblog'.DS.'avatar'.DS.$avatar;
		$ThumbName = JPATH_ROOT.DS.'images'.DS.'joomblog'.DS.'avatar'.DS.'thumb_'.$avatar;
		$url		= 'index.php?option=com_joomblog&task=bloggerpref';
		
		if ($avatar){
			if (JFile::exists($filename) && JFile::exists($ThumbName)){
				if (JFile::delete($filename) && JFile::delete($ThumbName)){
						$db->setQuery("UPDATE #__joomblog_user SET avatar='' WHERE user_id=".$cid);
						$db->query();
						
						return true;
				} else {
					$mainframe->redirect($url, 'Can not remove avatar');
				}
			} else {
				$mainframe->redirect($url, 'Avatar is not exists');
			}
		}
	}
}
