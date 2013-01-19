<?php
/**
* @version $Id: controller.php $
* @version		2.0.3 20/10/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'legacy.php';
		
jimport('joomla.application.component.controller');

class MyjspaceController extends JControllerLegacy
{
	function display($cachable = false, $urlparams = false)
	{
	  	$user = JFactory::getuser();
		$pparams = JComponentHelper::getParams('com_myjspace');
		$acces_ok = true;
		$get_view = JRequest::getCmd('view', '');
		
		// J! >= 1.6 ACL
		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			// View
			if ($get_view != '' && !JFactory::getUser()->authorise('user.'.$get_view, 'com_myjspace') )
				$acces_ok = false;
		}

		// If not connected => redirection to login page for 'admin' & 'delete', 'edit'
		if (!isset($user->username) && ( $get_view == 'config' || $get_view == 'delete' || $get_view == 'edit' || ( $get_view == 'see' && JRequest::getInt( 'id' , 0) == 0 && JRequest::getVar('pagename' , '') == '' ) ) ) {
			$acces_ok = false; // Login redirection
		}

		if ($acces_ok == false && !isset($user->username) ) { // Redirect to login page
			$uri = JFactory::getURI();
			$return = $uri->toString();
			
			if ($pparams->get('url_login_redirect', '')) 
				$url = $pparams->get('url_login_redirect', '');
			else {
				if (version_compare(JVERSION, '1.6.0', 'ge'))
					$url = 'index.php?option=com_users&view=login';
				else
					$url = 'index.php?option=com_user&view=login';
				$url .= '&return='.base64_encode($return); // to redirect to the originaly call page
				$url = JRoute::_($url, false);
			}

			$this->setRedirect($url);
			return;
		} else if ($acces_ok == false && isset($user->username) ) { // Not allowed
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED') , 'error');		
		}

		parent::display();
	}

// Compatibility <= 1.2
	function view_page()
	{
		$id = JRequest::getInt('id');
		$Itemid = JRequest::getInt('Itemid', 0);
		$return	=  JRoute::_('index.php?option=com_myjspace&view=see&id='.$id.'&Itemid='.$Itemid, false);
		$this->setRedirect( $return );
	}

// Save page content
	function save()
	{
		if (version_compare(JVERSION, '1.6.0', 'ge') && !JFactory::getUser()->authorise('user.config', 'com_myjspace') ) {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED') , 'error');			
			return;
		}
		
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
		
		$pparams = JComponentHelper::getParams('com_myjspace');

		$Itemid = JRequest::getInt('Itemid', 0);
		$Itemid_see = get_menu_itemid('index.php?option=com_myjspace&view=see', $Itemid); // Compatibility old install
		$Itemid_see = get_menu_itemid('index.php?option=com_myjspace&view=see&id=&pagename=', $Itemid_see);
		$id = JRequest::getInt('id', 0);
	
		$user = JFactory::getuser();
		$user_page = New BSHelperUser();
		if ($pparams->get('share_page', 0) != 0)
			$access = $user->getAuthorisedViewLevels();
		else
			$access = null;
		$list_page_tab = $user_page->GetListPageId($user->id, $id, $access);
		if (count($list_page_tab) != 1 ) { // For 'my' page
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
			return;
		}
		
		$content = JRequest::getVar('mjs_content', '@@vide@@', 'POST', 'STRING', JREQUEST_ALLOWRAW);
		if ($content == '@@vide@@') { // To allow really empty page
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_ERRUPDATINGPAGE'), 'error');
			return;
		}

		$pparams = JComponentHelper::getParams('com_myjspace');
		
		if ($pparams->get('editor_bbcode', 1) == 1)
			$content = bs_bbcode($content, $pparams->get('editor_bbcode_width', 800), $pparams->get('editor_bbcode_height'));
			
		BSUserEvent::Adm_save_page_content($id, $content, $pparams->get('name_page_max_size', 92160), JRoute::_('index.php?option=com_myjspace&view=see&id='.$id.'&Itemid='.$Itemid_see, false), 'site');
	}
	
// Save page config (& create page if no exist)
	function save_config()
	{
		$Itemid = JRequest::getInt('Itemid', 0);
		$id = JRequest::getInt('id', 0);

		if (version_compare(JVERSION, '1.6.0', 'ge') && !JFactory::getUser()->authorise('user.config', 'com_myjspace') ) {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED') , 'error');			
			return;
		}
		
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';

		$pparams = JComponentHelper::getParams('com_myjspace');
		
		$user = JFactory::getuser(); 
		$user_page = New BSHelperUser();
		$list_page_tab = $user_page->GetListPageId($user->id, $id);
		if (count($list_page_tab) != 1 ) // For 'my' page
			$id = 0;

		$pagename = JRequest::getVar('mjs_pagename', '');
		$resethits = JRequest::getVar('resethits', 'no');
		$publish_up = JRequest::getVar('publish_up', '0000-00-00');
		$publish_down = JRequest::getVar('publish_down', '0000-00-00');
		$metakey = JRequest::getVar('mjs_metakey', '');
		$mjs_template = JRequest::getVar('mjs_template', '');
		$mjs_model_page = JRequest::getInt('mjs_model_page', 0);
		$mjs_categories = JRequest::getInt('mjs_categories', 0);

		if ($pparams->get('share_page', 0) == 2)
			$mjs_share = JRequest::getInt('mjs_share', 0);
		else
			$mjs_share = null;
		
		if ($resethits == 'yes' && $id != 0) {
			BSUserEvent::Adm_reset_page_access($id, JRoute::_('index.php?option=com_myjspace&view=config&id='.$id.'&Itemid='.$Itemid, false), 'site');		
		} else if ($resethits == 'yes' && $id == 0) {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED') , 'error');			
		} else {
			if ($pparams->get('user_mode_view', 1) == 0)
				$blockview = $pparams->get('user_mode_view_default', 1); // Do do take param in this case (safety)
			else
				$blockview = JRequest::getVar('mjs_mode_view', 0);
				
			BSUserEvent::Adm_save_page_conf($id, $user->id, $pagename, $blockview, 0, $publish_up, $publish_down, $metakey, $mjs_template, $mjs_model_page, $mjs_categories, $mjs_share, JRoute::_('index.php?option=com_myjspace&view=config&id='.$id.'&Itemid='.$Itemid, false), 'site');
		}
	}

// Delete page
	function del_page()
	{
		if (version_compare(JVERSION, '1.6.0', 'ge') && !JFactory::getUser()->authorise('user.delete', 'com_myjspace') ) {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED') , 'error');			
			return;
		}

        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';

		$Itemid = JRequest::getInt('Itemid', 0);
		$Itemid_see = get_menu_itemid('index.php?option=com_myjspace&view=see', $Itemid); // Compatibility old install
		$Itemid_see = get_menu_itemid('index.php?option=com_myjspace&view=see&id=&pagename=', $Itemid_see);		
		$Itemid_config = get_menu_itemid('index.php?option=com_myjspace&view=config', $Itemid);

		$user = JFactory::getuser(); 
		$user_page = New BSHelperUser();
		$list_page_tab = $user_page->GetListPageId($user->id, JRequest::getInt('id', 0)); 
		
		if (count($list_page_tab) == 1 ) // For 'my' page
			$pageid = $list_page_tab[0]['id'];
		else {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
			return;
		}
		
		$pparams = JComponentHelper::getParams('com_myjspace');
		$auto_create_page = $pparams->get('auto_create_page',3);
	
		if ($auto_create_page != 3 && $auto_create_page != 1)
			BSUserEvent::Adm_page_remove($pageid, JRoute::_('index.php?option=com_myjspace&view=config&Itemid='.$Itemid_config, false) );
		else
			BSUserEvent::Adm_page_remove($pageid, JRoute::_('index.php?option=com_myjspace&view=see&Itemid='.$Itemid_see, false) );
	}


// Upload file for user page
	function upload_file()
	{
		if (version_compare(JVERSION, '1.6.0', 'ge') && !JFactory::getUser()->authorise('user.config', 'com_myjspace') ) {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED') , 'error');			
			return;
		}
	
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';

		$Itemid = JRequest::getInt('Itemid', 0);
		
		$user = JFactory::getuser(); 
		$user_page = New BSHelperUser();
		$list_page_tab = $user_page->GetListPageId($user->id, JRequest::getInt('id', 0)); 
		
		if (count($list_page_tab) == 1 ) // For 'my' page
			$pageid = $list_page_tab[0]['id'];
		else {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
			return;
		}

		if (!isset($_FILES['upload_file']))
			return;
		$FileObject = $_FILES['upload_file'];

		BSUserEvent::Adm_upload_file($pageid, $FileObject, JRoute::_('index.php?option=com_myjspace&view=config&id='.$pageid.'&Itemid='.$Itemid, false), 'site');
	}
	
// Delete file from user page
	function delete_file()
	{
		if (version_compare(JVERSION, '1.6.0', 'ge') && !JFactory::getUser()->authorise('user.config', 'com_myjspace') ) {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED') , 'error');			
			return;
		}

        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';

		$Itemid = JRequest::getInt('Itemid', 0);

		$user = JFactory::getuser(); 
		$user_page = New BSHelperUser();
		$list_page_tab = $user_page->GetListPageId($user->id, JRequest::getInt('id', 0)); 
		
		if (count($list_page_tab) == 1 ) // For 'my' page
			$pageid = $list_page_tab[0]['id'];
		else {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
			return;
		}

		$file_name = JRequest::getVar('delete_file');
		BSUserEvent::Adm_delete_file($pageid, $file_name, JRoute::_('index.php?option=com_myjspace&view=config&id='.$pageid.'&Itemid='.$Itemid, false), 'site');
	}
	
}
?>
