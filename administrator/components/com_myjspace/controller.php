<?php
/**
* @version $Id: controller.php $
* @version		2.0.3 25/10/2012
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
// Displays a view
	function display($cachable = false, $urlparams = false)
	{
		// Load & add the menu
		require_once JPATH_COMPONENT.'/helpers/myjspace.php';
		MyJspaceHelper::addSubmenu(JRequest::getCmd('view', 'myjspace'));
		
		switch ($this->getTask())
		{
			case 'edit'    :
			{
				JRequest::setVar( 'view', 'page' );
			} break;
			case 'remove'    :
			{
				JRequest::setVar( 'task', 'remove' );
			} break;
			case 'add'    :
			{
				JRequest::setVar( 'view', 'createpage' );
				// If no root pages forder existing & root page folder supposed to be used
				// Config
				$pparams = JComponentHelper::getParams('com_myjspace');
				require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
				$foldername = BSHelperUser::getFoldername();
				$link_folder = $pparams->get('link_folder', 1);
				// Test itself
				if (!BSHelperUser::ifExistFoldername($foldername) && $link_folder == 1) {
					$this->setRedirect(JRoute::_('index.php?option=com_myjspace&view=url', false), JText::_('COM_MYJSPACE_ALERTYOURADMIN'), 'error');
					return;
				}
				
			} break;
		}
	
		parent::display();
	}
	
// Create an empty page or a page with a model
	function adm_create_page()
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
		
		$pagename = JRequest::getVar('mjs_pagename', '');
		$user_name = JRequest::getVar('mjs_username', '');
		$user_id = JRequest::getInt('mjs_userid', 0);
		$mjs_model_page = JRequest::getInt('mjs_model_page', 0);

		$pparams = JComponentHelper::getParams('com_myjspace');
		$nb_max_page = $pparams->get('nb_max_page', 1);

		if ($user_id > 0)
			$user = JFactory::getuser($user_id); // J1.6+
		else
			$user = JFactory::getuser($user_name); // J!1.5

		$user_page = New BSHelperUser();

		if ($pagename == '' && $nb_max_page <= 1)
			$pagename =  $user_page->only_valid_char($user->username);
		else if ($pagename == '' && $nb_max_page > 1 && $user->username != '') // Create an 'automatic' name => username+suffix
			$pagename = $user_page->GetPagenameFree($user_page->only_valid_char($user->username));
		
		if (($user) && $pagename != '') {
			$list_page_tab = $user_page->GetListPageId($user->id);
			if (count($list_page_tab) >= $pparams->get('nb_max_page',1))
				$this->setRedirect(JRoute::_('index.php?option=com_myjspace&view=pages', false), JText::_('COM_MYJSPACE_USERPAGEMAXREACH'), 'error');			
			else {
				$id = BSUserEvent::Adm_save_page_conf(0, $user->id, $pagename, 1, 0, '', '', '', '', $mjs_model_page, null, null, null, 'admin');

				if ($id > 0)
					$this->setRedirect(JRoute::_('index.php?option=com_myjspace&view=page&task=edit&id='.$id, false));

				return;
			}
		} else // user do no exist
			$this->setRedirect(JRoute::_('index.php?option=com_myjspace&view=pages', false), JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
	}
	
// Remove the personal page record from the database and forder & files from disk
	function remove()
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
		$pageid_tab = JRequest::getVar('cid', array(0));
		
		BSUserEvent::adm_page_remove(intval($pageid_tab[0]), JRoute::_('index.php?option=com_myjspace&view=pages', false) , 'admin');
	}

// Save (update) page details 'only'
	function adm_save_page($url = '')
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';

		$pparams = JComponentHelper::getParams('com_myjspace');

		$id = JRequest::getInt('id', 0);
		$pagename = JRequest::getVar('mjs_pagename', '');
		$blockview = JRequest::getVar('mjs_mode_view', 0);
		$blockedit = JRequest::getVar('mjs_mode_edit', 0);
		$resethits =  JRequest::getVar('resethits', 'no');
		$publish_up = JRequest::getVar('publish_up');
		$publish_down = JRequest::getVar('publish_down');
		$metakey = JRequest::getVar('mjs_metakey', '');
		$mjs_template = JRequest::getVar('mjs_template', '');
		$mjs_categories = JRequest::getInt('mjs_categories', 0);
		$user_id = JRequest::getInt('mjs_userid', 0);

		if ($pparams->get('share_page', 0) != 0)
			$mjs_share = JRequest::getInt('mjs_share', 0);
		else
			$mjs_share = null;

		if (version_compare(JVERSION, '1.6.0', 'lt')) { // J!1.5
			$user_name = JRequest::getVar('mjs_username', '');
			$user = JFactory::getuser($user_name);
			if ($user)
				$user_id = $user->id;
			else
				$user_id = 0; // No change
		}

		if ($url == '')
			$url = 'index.php?option=com_myjspace&view=page&id='.$id;

		if ($resethits != 'yes') {
			BSUserEvent::Adm_save_page_conf($id, $user_id, $pagename, $blockview, $blockedit, $publish_up, $publish_down, $metakey, $mjs_template, 0, $mjs_categories, $mjs_share, JRoute::_($url, false), 'admin2');
		} else {
			BSUserEvent::Adm_reset_page_access($id, JRoute::_($url, false), 'admin');
		}
	}

// Save (update) page details 'only' & exit to page list
	function adm_save_page_exit()
	{
		$this->adm_save_page('index.php?option=com_myjspace&view=pages');
	}  

// Upload file for user page
	function upload_file()
	{
		$Itemid = JRequest::getInt('Itemid' , 0);
		$id = JRequest::getInt('id', 0);

		require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
		
		if (!isset($_FILES['upload_file']))
			return;
		$FileObject = $_FILES['upload_file'];

		BSUserEvent::Adm_upload_file($id, $FileObject, JRoute::_('index.php?option=com_myjspace&view=page&id='.$id, false), 'admin');
	}
	
// Delete file from user page
	function delete_file()
	{
		$Itemid = JRequest::getInt('Itemid' , 0);
		$id = JRequest::getInt('id', 0);

		require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
		
		$file_name = JRequest::getVar('delete_file');
		BSUserEvent::Adm_delete_file($id, $file_name, JRoute::_('index.php?option=com_myjspace&view=page&id='.$id, false), 'admin');
	}	

// Save(update) page content 'only' & exit to page list
	function adm_save_page_content_exit()
	{
		$this->adm_save_page_content('index.php?option=com_myjspace&view=pages');
	}	
	
	
// Save(update) page content 'only'
	function adm_save_page_content($url = '')
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';

		$id = JRequest::getInt('id', 0);
		
		$content = JRequest::getVar('mjs_content', '@@vide@@', 'POST', 'STRING', JREQUEST_ALLOWRAW);
		if ($content == '@@vide@@') { // To allow really empty page
			$this->setRedirect(JRoute::_('index.php'), JText::_('COM_MYJSPACE_ERRUPDATINGPAGE'), 'error');
			return;
		}		

		$pparams = JComponentHelper::getParams('com_myjspace');
		if ($pparams->get('editor_bbcode', 1) == 1)
			$content = bs_bbcode($content, $pparams->get('editor_bbcode_width', 800), $pparams->get('editor_bbcode_height'));

		if ($url == '')
			$url = 'index.php?option=com_myjspace&view=page&id='.$id;
			
		BSUserEvent::Adm_save_page_content($id, $content, $pparams->get('name_page_max_size', 92160), JRoute::_($url, false), 'admin');
	}
	
// Rename/create/move the personnal Root pages folder or subfolders
	function adm_ren_folder () 
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
		
		$foldername_new = JRequest::getVar('mjs_foldername');
		$keep = JRequest::getInt('keep', 0);
		
		BSUserEvent::Adm_ren_folder($foldername_new, $keep, JRoute::_('index.php?option=com_myjspace&view=url', false));
	}

// Create folders and link pages for all personal pages
	function adm_create_folder()
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
		
		list($retour, $msg) = BSUserEvent::Adm_create_folder();
		$url = JRoute::_('index.php?option=com_myjspace&view=tools', false);
		if ($retour == 0)
			$this->setRedirect($url, JText::_($msg), 'error');
		else
			$this->setRedirect($url, JText::_($msg), 'message');
	}

// Delete folders and link pages for all personal pages
	function adm_delete_folder()
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
		
		list($retour, $msg) = BSUserEvent::Adm_delete_folder();
		$url = JRoute::_('index.php?option=com_myjspace&view=tools', false);
		if ($retour > 0)
			$this->setRedirect($url, JText::_($msg), 'error');
		else
			$this->setRedirect($url, JText::_($msg), 'message');
	}
	
// Delete all empy pages (= content + folder empty)
	function adm_delete_empty_pages()
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
		
		$msg = BSUserEvent::adm_delete_empty_pages();
		$this->setRedirect(JRoute::_('index.php?option=com_myjspace&view=tools', false), JText::_($msg), 'message');
	}
	
}
?>
