<?php
/**
* @version $Id: view.html.php $
* @version		2.0.3 22/10/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'legacy.php';

jimport('joomla.application.component.view');

class MyjspaceViewEdit extends JViewLegacy
{
	function display($tpl = null)
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
	    require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';

		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();

		// Params
		$layout = JRequest::getVar('layout', '');
		
		if ($layout != 'tags') {
			$Itemid = JRequest::getInt('Itemid', 0);
			$Itemid_pages = get_menu_itemid('index.php?option=com_myjspace&view=pages', $Itemid);
			$Itemid_config = get_menu_itemid('index.php?option=com_myjspace&view=config', $Itemid);

			$id = JRequest::getInt('id', 0);

			if ($id == 0) {
				$pageid_tab = JRequest::getVar('cid', array(0));
				$id = intval($pageid_tab[0]);
			}

			// User info
			$user = JFactory::getuser();
			$user_page = New BSHelperUser();

			// Page id - check
			if ($pparams->get('share_page', 0) != 0)
				$access = $user->getAuthorisedViewLevels();
			else
				$access = null;
			$list_page_tab = $user_page->GetListPageId($user->id, $id, $access);
			$nb_page = count($list_page_tab);			

			if ($id <= 0 || $nb_page != 1) {
				if ($id < 0 && $nb_page >= $nb_max_page) { // New page KO
					$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages&lview=edit&Itemid='.$Itemid_pages, false), JText::_('COM_MYJSPACE_MAXREACHED'), 'error');	
					return;
				} else if ($id < 0 || $nb_page == 0) { // New page
					$id = 0;
				} else if ($nb_page == 1) { // id= 0 => Display the page
					$id = $list_page_tab[0]['id'];
				} else { // Display Pages list
					$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages&lview=edit&Itemid='.$Itemid_pages, false));
					return;
				}
			} else if ($nb_page > 1) { // Error 
				$app->redirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
				return;
			}
				
			// Personnal page info
			$user_page->id = $id;		
			$user_page->loadPageInfo();
			
			// Test if foldername exist => Admin
			$link_folder = $pparams->get('link_folder', 1);
			if (!BSHelperUser::ifExistFoldername($user_page->foldername) && $link_folder == 1) {
				$app->redirect(JRoute::_('index.php?option=com_myjspace&view=config&Itemid='.$Itemid_config, false));
				return;
			}

			// Create automaticaly page if none, if option 'auto_create_page' is activated & max 1 model
			$auto_create_page = $pparams->get('auto_create_page', 3);
			$model_page_list = BSUserEvent::model_pagename_list();  // Model page list
			if ($user_page->pagename == '' && ($auto_create_page == 2 || $auto_create_page == 3) && count($model_page_list) < 2) {
				if ($pparams->get('user_mode_view', 1) == 0)
					$blockview = $pparams->get('user_mode_view_default', 1); // Do do take param in this case (safety)
				else
					$blockview = JRequest::getVar('mjs_mode_view', 0);
				$id = BSUserEvent::Adm_save_page_conf(0, $user->id, $user->username, $blockview, 0, '', '', '', '', 0, null, null, null, 'admin');
				
				$user_page->id = $id;
				$user_page->loadPageInfoOnly(); // Reload the user data
			}

			JRequest::setVar('id', $id, 'GET'); // value used for upload from editor

			if ($user_page->pagename == '' && $layout != 'tags') { // Page not found => Go to create it
				$app->redirect(JRoute::_('index.php?option=com_myjspace&view=config', false));
				return;
			}
			
			$this->assignRef('content', $user_page->content);
			$msgvide = null;
			$this->assignRef('msg', $msgvide );
			if ($user_page->blockView == null) // Test Not necessary any more, since redirect if no page ?
				$this->assignRef('msg', JText::_('COM_MYJSPACE_PAGENOTFOUND'));
			else if ($user_page->blockEdit == 1)
				$this->assignRef('msg', JText::_('COM_MYJSPACE_EDITBLOCKED'));
			else if ($user_page->blockEdit == 2)
				$this->assignRef('msg', JText::_('COM_MYJSPACE_EDITLOCKED'));

			// Links
			$link_folder = $pparams->get('link_folder', 1);
		
			// Editor selection
			$editor_selection = $pparams->get('editor_selection', 'myjsp');
			if (check_editor_selection($editor_selection) == false || $editor_selection == '-') // Use the Joomla default editor
				$editor_selection = null;

			// Editor button
			if ($pparams->get('allow_editor_button', 1) == 1)
				$editor_button = array('readmore', 'article');
			else
				$editor_button = false;

			// Editor 'windows' size
			$edit_x = $pparams->get('user_edit_x', '100%');
			$edit_y = $pparams->get('user_edit_y', '600px');
			
			// Upload images 
			$uploadimg = $pparams->get('uploadimg', 1);
			$uploadmedia = $pparams->get('uploadmedia', 1);
			$downloadimg = $pparams->get('downloadimg', 1);
			if ($link_folder == 0) { // Automatic configuration :-)
				$uploadimg = 0;
				$uploadmedia = 0;
				$downloadimg = 0;
			}

			// Web page title
			if ($pparams->get('pagetitle', 1) == 1) {
				$title = $user_page->pagename;
				if (empty($title)) {
					$title = $app->getCfg('sitename');
				} elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
					$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
				} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
					$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
				}
				if ($title)
					$document->setTitle($title);
			}

			// Breadcrumbs
			$pathway = $app->getPathway();
			if (($pathid = count($pathway->getPathwayNames())) > 1)
				$pathway->setItemName($pathid-2, JText::_('COM_MYJSPACE_TITLEEDIT'));
			$pathway->addItem($user_page->pagename, '');

			// Vars Assign
			$this->assignRef('Itemid', $Itemid);
			$this->assignRef('id', $user_page->id);
			$this->assignRef('uploadimg', $uploadimg);
			$this->assignRef('uploadmedia', $uploadmedia);
			$this->assignRef('downloadimg', $downloadimg);
			$this->assignRef('editor_selection', $editor_selection);
			$this->assignRef('edit_x', $edit_x);
			$this->assignRef('edit_y', $edit_y);		
			$this->assignRef('foldername', $user_page->foldername);
			$this->assignRef('pagename', $user_page->pagename);
			$this->assignRef('template', $user_page->template);
			$this->assignRef('editor_button', $editor_button);
		} else {
			// Tags buttons
			$e_name = JRequest::getVar('e_name', 'mjs_editable');
			$allow_tag_myjsp_iframe = $pparams->get('allow_tag_myjsp_iframe', 1);
			$allow_tag_myjsp_include = $pparams->get('allow_tag_myjsp_include', 1);
			
			$this->assignRef('e_name', $e_name);
			$this->assignRef('allow_tag_myjsp_iframe', $allow_tag_myjsp_iframe);
			$this->assignRef('allow_tag_myjsp_include', $allow_tag_myjsp_include);
		}
		
		parent::display($tpl);
	}
}
?>
