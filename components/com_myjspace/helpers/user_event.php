<?php
/**
* @version $Id: user_event.php $
* @version		2.0.3 25/10/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// Component Helper
jimport('joomla.application.component.helper');

// -----------------------------------------------------------------------------

// Theses function are here because they can be call from user or admin interface

class BSUserEvent
{
// Constructeur
	function bshelperuserevent() {}

// Rename personal page folder or create	
	public static function Adm_ren_folder($foldername_new = '', $keep = 0, $url_redirect = 'index.php') 
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
		
		$app = JFactory::getApplication();
		$user_page = New BSHelperUser();
		$user_page->foldername = BSHelperUser::getFoldername(); // Get the actual folder
		$foldername_old = $user_page->foldername;

		$pparams = JComponentHelper::getParams('com_myjspace');		
		$link_folder = $pparams->get('link_folder', 1);
		$uploadadmin = $pparams->get('uploadadmin', 1);
		$uploadimg = $pparams->get('uploadimg', 1);		

		$foldername_new = trim($foldername_new); // Whitespace stripped from the beginning and end
		$foldername_new = trim($foldername_new, '/'); // '/' stripped from the beginning and end

		if (BSHelperUser::checkFoldername($foldername_new)) { // Test if characters allowed
			if ($user_page->updateFoldername( $foldername_new, $link_folder, $keep )) {
				if ($uploadadmin == 1 || $uploadimg == 1) // Rename folder inside all pages content !
					BSUserEvent::adm_rename_folder_in_pages($foldername_old, $foldername_new);
			
				$app->redirect($url_redirect, JText::_('COM_MYJSPACE_FOLDERNAMEUPDATED'), 'message');
			} else
				$app->redirect($url_redirect, JText::_('COM_MYJSPACE_ERRUPDATINGFOLDERNAMEFILE'), 'error');
		} else
			$app->redirect($url_redirect, JText::_('COM_MYJSPACE_NOTVALIDFOLDERNAME'), 'error');
	}
	
// Removes the personal page record from the database and files
	public static function Adm_page_remove($id = 0, $url_redirect = 'index.php', $caller = 'site')
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';
		
		$app = JFactory::getApplication();
		$user_page = New BSHelperUser();
		$user_page->id = $id; // To set page id
		$user_page->loadPageInfoOnly(); // To get pagename & foldername
	
		$Itemid_config = get_menu_itemid('index.php?option=com_myjspace&view=config');

		// If page locked (admin & edit | edit)
		if ($user_page->blockEdit != 0 && $caller == 'site') {
			$this->redirect(JRoute::_('index.php?option=com_myjspace&view=config&Itemid='.$Itemid_config), JText::_('COM_MYJSPACE_EDITLOCKED'), 'error');	
			return;		
		}

		$pparams = JComponentHelper::getParams('com_myjspace');		
		$link_folder = $pparams->get('link_folder', 1);

		if ($user_page->deletePage($link_folder)) // Delete
			$app->redirect($url_redirect, JText::_('COM_MYJSPACE_PAGEDELETED'), 'message');
		else
			$app->redirect($url_redirect, JText::_('COM_MYJSPACE_ERRDELETINGPAGE'), 'error');	
	}
	
// Save (=update) page content
	public static function Adm_save_page_content($id = 0, &$content = null, $name_page_max_size = 0, $url_redirect = 'index.php', $caller = 'site')
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';

		$app = JFactory::getApplication();
			
		// Size test
		if ($name_page_max_size > 0 && strlen($content) > $name_page_max_size) {
			$app->redirect($url_redirect, JText::_('COM_MYJSPACE_ERRCREATEPAGESIZE').' '.$name_page_max_size, 'error');
			return;
		}

		// Param
		$pparams = JComponentHelper::getParams('com_myjspace');
		$user = JFactory::getuser();
		$email_user = $pparams->get('email_user', 0);
		$email_admin_from = $pparams->get('email_admin_from', '');	
		$add_hostname = $pparams->get('add_hostname', 0);	
		
		$user_page = New BSHelperUser();
		$user_page->id = $id; // To set pageid
		$user_page->loadPageInfoOnly(); // Get info (for pagename)
		$user_page->modified_by = $user->id;

		// If page locked (admin & edit)
		if ($user_page->blockEdit == 2 && $caller == 'site') {
			$this->redirect(JRoute::_('index.php?option=com_myjspace&view=config',false), JText::_('COM_MYJSPACE_EDITLOCKED'), 'error');	
			return;		
		}
		
		// Begin workaround
		// Update image link or link (relative & absolute), ok with Tiny mce V 3.4.3.2
		if ($caller == 'admin') {
			$uri_rel = str_replace('/administrator', '', JURI::base(true));
			$content = str_replace('href="../'.$user_page->foldername.'/'.$user_page->pagename.'/', 'href="'.$uri_rel.'/'.$user_page->foldername.'/'.$user_page->pagename.'/', $content);
			$content = str_replace('src="../'.$user_page->foldername.'/'.$user_page->pagename.'/', 'src="'.$uri_rel.'/'.$user_page->foldername.'/'.$user_page->pagename.'/', $content);
		}
		// End workaround

		// Add hostname in / url 
		if ($add_hostname == 1) {
			$uri_rel = str_replace('/administrator', '', JURI::base(true));
			$abs_rel = str_replace('/administrator', '', JURI::base());
		
			$content = str_replace('href="'.$uri_rel.'/', 'href="'.$abs_rel, $content);
			$content = str_replace('src="'.$uri_rel.'/', 'src="'.$abs_rel, $content);

// If for files inside user page only : 			
//			$content = str_replace('href="'.$uri_rel.'/'.$user_page->foldername.'/'.$user_page->pagename, 'href="'.$abs_rel.$user_page->foldername.'/'.$user_page->pagename, $content);
//			$content = str_replace('src="'.$uri_rel.'/'.$user_page->foldername.'/'.$user_page->pagename, 'src="'.$abs_rel.$user_page->foldername.'/'.$user_page->pagename, $content);
		}
	
		$user_page->content = $content; // To set content
		if ($user_page->updateUserContent()) {
			if ($email_user == 1 && $caller == 'admin') { // Send email to user
				$subject = JText::sprintf('COM_MYJSPACE_EMAIL_SUBJECT2', $user_page->pagename);
				$site_msg = str_replace('/administrator', '', JURI::base());
				$body = JText::sprintf('COM_MYJSPACE_EMAIL_CONTENT2', $user_page->pagename, $site_msg);
				$user = JFactory::getuser($user_page->userid);
				send_mail($email_admin_from , $user->email, $subject, $body);			
			}
			$app->redirect($url_redirect, JText::_('COM_MYJSPACE_EUPDATINGPAGE'), 'message');
		} else
			$app->redirect($url_redirect, JText::_('COM_MYJSPACE_ERRUPDATINGPAGE'), 'error');
	}

// Save (=update) page (out of content)
	public static function Adm_save_page_conf($id = 0, $userid = 0, $pagename = null, $blockview = 1, $blockedit = 0, $publish_up = '', $publish_down = '', $metakey = '', $template = '', $mjs_model_page = 0, $catid = null, $access = null, $url_redirect = 'index.php', $caller = 'site')
	{
		// JPATH_ROOT to allow to call out of the component
		require_once JPATH_ROOT.DS.'components'.DS.'com_myjspace'.DS.'helpers'.DS.'user.php';
        require_once JPATH_ROOT.DS.'components'.DS.'com_myjspace'.DS.'helpers'.DS.'util.php';

		$app = JFactory::getApplication();

		$user_page = New BSHelperUser();
		$creation = 0;

		// Param
		$pparams = JComponentHelper::getParams('com_myjspace');		
		$link_folder = $pparams->get('link_folder', 1);
		$msg_error = $pparams->get('name_page_caract_error', JText::_('COM_MYJSPACE_NOTVALIDPAGENAME'));
		$name_page_caract_ok = $pparams->get('name_page_caract_ok', '/^[A-Za-z]+[A-Za-z0-9]+$/');
		$name_page_size_min = $pparams->get('name_page_size_min', 0);
		$name_page_size_max = $pparams->get('name_page_size_max', 20);
		$pagename_username = $pparams->get('pagename_username', 0);
		$uploadadmin = $pparams->get('uploadadmin', 1);
		$uploadimg = $pparams->get('uploadimg', 1);
		$email_admin = $pparams->get('email_admin', 0);	
		$email_user = $pparams->get('email_user', 0);	
		$email_admin_from = $pparams->get('email_admin_from', '');	
		$email_admin_to = $pparams->get('email_admin_to', '');
		$date_fmt = $pparams->get('date_fmt', 'Y-m-d H:i:s');
		$publish_mode = $pparams->get('publish_mode', 2);
		$user_mode_view	= $pparams->get('user_mode_view', 1);
		$default_catid = $pparams->get('default_catid', 0);
		
		$pagename = trim($pagename);
		if (!preg_match($name_page_caract_ok, $pagename) || $pagename == '') { // Test avec les regles de nommage
			$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages', false), $msg_error, 'error');
			return 0;
		}

		if ($pagename_username == 0 && (strlen($pagename) < $name_page_size_min || strlen($pagename) > $name_page_size_max)) { // Test la longueur du nommage
			$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages', false), JText::sprintf('COM_MYJSPACE_ADMIN_NAME_PAGE_SIZE_ERROR', $name_page_size_min, $name_page_size_max), 'error');
			return 0;
		}

		$user_page->id = $id;
		$user_page->loadPageInfoOnly(); // Charge les infos de la page de l'id si existe
		$id_recup = $user_page->id;
		$user_page->userid = $userid; // Cas si la page n'existait pas !
	
		// If page locked (admin & edit)
		if ($user_page->blockEdit == 2 && $caller == 'site') {
			$this->redirect(JRoute::_('index.php?option=com_myjspace&view=config', false), JText::_('COM_MYJSPACE_EDITLOCKED'), 'error');	
			return 0;		
		}
	
		if ($user_page->pagename != $pagename) { // Test si changement de nom (ou nouveau)
			if ($user_page->ifExistPageName($pagename)) {
				$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages', false), JText::_('COM_MYJSPACE_PAGEEXISTS'), 'error');
				return 0;	
			}
			if ($user_page->pagename != '' && $link_folder == 1) { // Si la page existe & pages avec repertoires configuré
// A completer en cas de pb et pas bien affecte ? 
// if (!$user_page->pagename || $user_page->pagename = '' || !(pagename) || pagename = '') 
				@rename(JPATH_SITE.DS.$user_page->foldername.DS.$user_page->pagename, JPATH_SITE.DS.$user_page->foldername.DS.$pagename);
				$user_page->CreateDirFilePage($pagename, $pparams->get('index_pagename_id', 1));
			
				// Dans ce cas si on change le contenu de la page pour les url (cas ou page avec contenu autorisés)
				if ($uploadadmin == 1 || $uploadimg == 1) {
					// Chargement du contenu de la page
					$user_page->loadPageInfo();
					// Modifications des url d'images et de liens absolus et relatifs
					$user_page->content = str_replace('href="'.str_replace('/administrator', '', JURI::base(true)).'/'.$user_page->foldername.'/'.$user_page->pagename, 'href="'.str_replace('/administrator','',JURI::base(true)).'/'.$user_page->foldername.'/'.$pagename, $user_page->content);
					$user_page->content = str_replace('href="'.str_replace('/administrator', '', JURI::base()).$user_page->foldername.'/'.$user_page->pagename, 'href="'.str_replace('/administrator','',JURI::base()).$user_page->foldername.'/'.$pagename, $user_page->content);
					$user_page->content = str_replace('src="'.str_replace('/administrator', '', JURI::base(true)).'/'.$user_page->foldername.'/'.$user_page->pagename, 'src="'.str_replace('/administrator','',JURI::base(true)).'/'.$user_page->foldername.'/'.$pagename, $user_page->content);
					$user_page->content = str_replace('src="'.str_replace('/administrator', '', JURI::base()).$user_page->foldername.'/'.$user_page->pagename, 'src="'.str_replace('/administrator','',JURI::base()).$user_page->foldername.'/'.$pagename, $user_page->content);
					// re-Sauvegarde du contenu modifié !
					$user_page->updateUserContent();
				}
				
			} else {
				$creation = 1;

				// Creation page DB & repertoire et fichier (si page avec répertoire configuré)
				if (!($id_recup) && (!($user_page->id = $user_page->createPage($pagename, $default_catid)) || ($link_folder == 1 && $user_page->CreateDirFilePage($pagename, $pparams->get('index_pagename_id', 1)) == 0))) { // A completer en cas d'erreur de l'un ou de l'autre seulement ?
					$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages', false), JText::_('COM_MYJSPACE_ERRCREATEPAGE'), 'error');
					// Cleanup to be made, in case ?
					return $user_page->id;
				}
				
				// Model Page(s) ?
				$mjs_model_page = BSUserEvent::model_pagename_id($mjs_model_page);
				if ($mjs_model_page) { // If model page to use
					if (intval($mjs_model_page) != 0) // Select for list => find Page id
						$user_page->content = $user_page->GetContentPageId($mjs_model_page);
					else { // File content to upload
						$user_page->content = @file_get_contents($mjs_model_page);
						
						if (strlen($user_page->content) <= 92160 && strstr($user_page->content, '<body>') && preg_match('#<body>(.*)</body>#Us', $user_page->content, $sortie))
							$user_page->content = $sortie[1];
					}
					
					if ($user_page->content)
						$user_page->updateUserContent();
				}
				if (count(BSUserEvent::model_pagename_list()) > 0) {
					// Non SEF
					$url_redirect = str_replace('&id=0', '&id='.$user_page->id, $url_redirect);
					// SEF
					$url_redirect .= '#####';
					$url_redirect = str_replace('/0#####', '/'.$user_page->id, $url_redirect);
				}
				if ($email_admin == 1) { // Send Email to admin
					$subject = JText::sprintf('COM_MYJSPACE_EMAIL_SUBJECT1', $pagename);					
					$body = JText::sprintf('COM_MYJSPACE_EMAIL_CONTENT1', $pagename, JURI::base());
					send_mail($email_admin_from , $email_admin_to, $subject, $body);
				}
			}
		}

		// Maj. des infos transmises et conservation des anciennes si pas données
		$user_page->pagename = $pagename;
		if ($access !== null)
			$user_page->access = $access;
		if ($blockview != null)
			$user_page->blockView = $blockview;
		if ($blockedit != null)
			$user_page->blockEdit = $blockedit;

		// Maj. des Metakey 'transmit'
		$user_page->metakey = trim(substr($metakey, 0, 150)); // Max. 150 characters
		
		// Template choice
		$user_page->template = trim(substr($template, 0, 50)); // Max. 50 characters

		// Catid
		if ($catid != null)
			$user_page->catid = $catid;

		// Maj. des dates de publication transmises au bon format
		$publish_up = trim($publish_up);
		$publish_down = trim($publish_down);
		
		$date_fmt_tab = explode(' ', $date_fmt);
		$user_page->publish_up = valid_date($publish_up, $date_fmt_tab[0]).' 00:00:00';
		$user_page->publish_down = valid_date($publish_down, $date_fmt_tab[0]).' 23:59:59';
		
		// Affectation des droits de mise à jour (tous $droits = 31) pour éviter qu'un user ne change ses parametres non autorisés en changement en url directe
		$droits = 0;
		if ($pagename_username == 0)
			$droits += 1;
		if ($user_mode_view == 1 || $caller == 'admin2')
			$droits += 2;			
		if ($caller == 'admin2')
			$droits += 4;
		if ($publish_mode == 2 || ($publish_mode == 1 && $caller == 'admin2'))
			$droits += 8 + 16;
		$droits += 32; // metakey
		$droits += 64; // template
		if ($catid !== null)
			$droits += 128;	// catid
		if ($access !== null)
			$droits += 512;	// access
		if ($userid != 0 )
			$droits += 256;
			
		if ($user_page->SetConfPage($droits)) { // Mise à jour de la configuration de la page avec les bons parametres
			if ($email_user == 1 && $creation == 0 && $caller == 'admin2') { // Send email to user
				$subject = JText::sprintf('COM_MYJSPACE_EMAIL_SUBJECT2', $pagename);
				$edit_msg = 'COM_MYJSPACE_TITLEMODEEDIT'.$blockedit;
				$site_msg = str_replace('/administrator', '', JURI::base());
				$body = JText::sprintf('COM_MYJSPACE_EMAIL_CONTENT2', $pagename, $site_msg);
				$body .= "\n  ". JText::_('COM_MYJSPACE_TITLEMODEEDIT').' : '.JText::_($edit_msg);
				$body .= "\n  ". JText::_('COM_MYJSPACE_TITLEMODEVIEW').' : '.get_assetgroup_label($blockview);
				$user = JFactory::getuser($user_page->userid);
				send_mail($email_admin_from , $user->email, $subject, $body);			
			}
			if ($caller != 'admin')
				$app->redirect($url_redirect, JText::_('COM_MYJSPACE_EUPDATINGPAGE'), 'message');

		} else if ($caller != 'admin')
			$app->redirect($url_redirect, JText::_('COM_MYJSPACE_ERRUPDATINGPAGE'), 'error');
		else
			$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages', false), JText::_('COM_MYJSPACE_ERRUPDATINGPAGE'), 'error');
			
		return $user_page->id;
	}
	

	// Reset page hit
	public static function Adm_reset_page_access($id = 0, $url_redirect = 'index.php', $caller = 'site')
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';

		$app = JFactory::getApplication();

		$user_page = New BSHelperUser();
		$user_page->id = $id; // To set page id
		$user_page->loadPageInfoOnly();
		
		// If page locked (admin & edit)
		if ($user_page->blockEdit == 2 && $caller == 'site') {
			$this->redirect(JRoute::_('index.php?option=com_myjspace&view=config',false), JText::_('COM_MYJSPACE_EDITLOCKED') , 'error');	
			return;		
		}

		if ($user_page->ResetLastAccess()) // Reset hit
			$app->redirect($url_redirect, JText::_('COM_MYJSPACE_EUPDATINGPAGE'), 'message');
		else
			$app->redirect($url_redirect, JText::_('COM_MYJSPACE_ERRUPDATINGPAGE'), 'error');	
	}	
	
	
// Delete the selected file for a user
	public static function Adm_delete_file($id = 0, $file_name = '', $url_redirect = 'index.php', $caller = 'site') {
	    require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';

		$app = JFactory::getApplication();

		// Extra controls
		$forbiden_files = array('', '.', '..', 'index.html', 'index.htm', 'index.php', 'configuration.php', '.htaccess', basename(__FILE__));
		
		if (in_array(strtolower($file_name), $forbiden_files)) { 
			if ($file_name == '')
				$file_name = JText::_('COM_MYJSPACE_UPLOADCHOOSE');
			$app->redirect($url_redirect, JText::_('COM_MYJSPACE_UPLOADERROR11').$file_name, 'error');
			return;
		}
		
		$user_page = New BSHelperUser();
		$user_page->id = $id; // To set page id
		$user_page->loadPageInfoOnly(); // To get pagename & foldername

		// If page locked (admin & edit)
		if ($user_page->blockEdit == 2 && $caller == 'site') {
			$this->redirect(JRoute::_('index.php?option=com_myjspace&view=config',false), JText::_('COM_MYJSPACE_EDITLOCKED'), 'error');	
			return;		
		}
		
		if (@unlink(JPATH_ROOT.DS.$user_page->foldername.DS.$user_page->pagename.DS.$file_name)) {
			$user_page->SetConfPage(0); // Page update date 
			$app->redirect($url_redirect, JText::_('COM_MYJSPACE_UPLOADERROR10').$file_name, 'message');
		} else
			$app->redirect($url_redirect, JText::_('COM_MYJSPACE_UPLOADERROR11').$file_name, 'error');	
	}
	
		
// Upload the file for a user into his personal folder 
	public static function Adm_upload_file($id = 0, $FileObject = null, $url_redirect = 'index.php', $caller = 'site')
	{
	    require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
	    require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';

		$app = JFactory::getApplication();
			
		// User
		$user_page = New BSHelperUser();
		$user_page->id = $id; // To set page id
		$user_page->loadPageInfoOnly(); // To get pagename & foldername

		// If page locked (admin & edit)
		if ($user_page->blockEdit == 2 && $caller == 'site') {
			$this->redirect(JRoute::_('index.php?option=com_myjspace&view=config',false), JText::_('COM_MYJSPACE_EDITLOCKED') , 'error');	
			return 0;		
		}
		
		// Secure
		if ($user_page->pagename == '') {
			$app->redirect($url_redirect, JText::_(COM_MYJSPACE_UPLOADNOALLOWED), 'error');	
			return 0;
		}

		// 'Params'
		$DestPath = JPATH_ROOT.DS.$user_page->foldername.DS.$user_page->pagename.DS;
		$pparams = JComponentHelper::getParams('com_myjspace');		
		$ResizeSizeX = $pparams->get('resize_x', 800);
		$ResizeSizeY = $pparams->get('resize_y', 600);
		$uploadfile = strtolower(str_replace(' ', '', $pparams->get('uploadfile', '*'))); // Files suffixes
		$uploadimg = $pparams->get('uploadimg', 1);
		$uploadmedia = $pparams->get('uploadmedia', 0);
		
		$forbiden_files = array('', '.', '..', 'index.html', 'index.htm', 'index.php', 'configuration.php', '.htaccess', basename(__FILE__));
			
		$allowed_types = array();
		if ($uploadimg == 1)
			$allowed_types = array_merge($allowed_types, array('jpg', 'png', 'gif'));
		if ($uploadmedia == 1)
			$allowed_types = array_merge($allowed_types, array('flv', 'avi', 'mp4', 'mov', 'wmv'));

		$uploadfile = str_replace(array('|', ' '), array(',', ''), $uploadfile); // Compatibility with MyJspace < 1.7.7 and cleanup
		$uploadfile_tab = explode(',', $uploadfile);
		$allowed_types = array_merge($allowed_types, $uploadfile_tab);
		$File_max_size = $pparams->get('file_max_size', '204800');
		$Dir_max_size = $pparams->get('dir_max_size', '2097152');
		$StatusMessage = '';
		$ActualFileName = '';	
		$ReplaceFile = 'yes';
		$error = 0;
		$retour = false;

		//
		list($rien, $dir_size_var) = dir_size($DestPath);
		
		$FileBasename = basename($FileObject['name']);
		$type_parts = strtolower(pathinfo($FileObject['name'], PATHINFO_EXTENSION));
		if (!isset($FileObject) || $FileObject['size'] <= 0 || !($uploadfile_tab[0] == '*' || in_array($type_parts, $allowed_types)) || in_array(strtolower($FileBasename), $forbiden_files)) {		
			$StatusMessage = JText::_('COM_MYJSPACE_UPLOADERROR2');
			$error = 1;
		} else if ($FileObject["size"] > $File_max_size && $ResizeSizeX == 0 && $ResizeSizeY == 0) {
			$StatusMessage = JText::_('COM_MYJSPACE_UPLOADERROR4').convertSize($FileObject['size']).JText::_('COM_MYJSPACE_UPLOADERROR3').convertSize($File_max_size);
			$error = 1;
		} else if (($dir_size_var + $FileObject["size"]) > $Dir_max_size ) {
			$StatusMessage = JText::_('COM_MYJSPACE_UPLOADERROR5').convertSize($FileObject['size']+$dir_size_var).JText::_('COM_MYJSPACE_UPLOADERROR3').convertSize($Dir_max_size);
			$error = 1;
		} else {	
			$ActualFileName = $DestPath.DS.$FileBasename;													// formulate path to file
			if (@file_exists($ActualFileName)) {															// check to see if the file already exists
				if ($ReplaceFile == 'yes') {
					$StatusMessage .= JText::_('COM_MYJSPACE_UPLOADERROR6');								// if so, we'll let the user know
					$error = 0;
				} else {
					$StatusMessage .= JText::_('COM_MYJSPACE_UPLOADERROR7');
					$error = 1;
				}
			}
			if ($ReplaceFile == 'yes') { // Voir le cas no si plus choix forcé
				if ($ResizeSizeX != 0 || $ResizeSizeY != 0) {												// If we need to resize the file
					$uploadedfile = $FileObject['tmp_name'];												// Get the handle to the file that was just uploaded
					if (resize_image($uploadedfile, $ResizeSizeX, $ResizeSizeY, $ActualFileName) != true) {
						if ($FileObject["size"] <= $File_max_size)											// Just process without resizing
							$retour = move_uploaded_file($FileObject['tmp_name'], $ActualFileName);
						else {
							$retour = false;
							$StatusMessage .= JText::_('COM_MYJSPACE_UPLOADERROR4').convertSize($FileObject['size']).JText::_('COM_MYJSPACE_UPLOADERROR3').convertSize($File_max_size);
						}
					} else {
						$StatusMessage .= JText::_('COM_MYJSPACE_UPLOADERROR1');							// Image resized : ok
						$error = 0;
						$retour = true;
					}
				} else
					$retour = move_uploaded_file($FileObject['tmp_name'], $ActualFileName);

				if ($retour == true) {
					$StatusMessage .= JText::_('COM_MYJSPACE_UPLOADERROR9');								// Image uploaded ok to " . $ActualFileName . "!";						
					$error = 0;
				} else {
					$StatusMessage .= ' '.JText::_('COM_MYJSPACE_UPLOADERROR12');							// Upload error					
					$error = 1;
				}
			}
		}		

		$StatusMessage .= '.';
		$StatusMessage = str_replace("\n", '. ', $StatusMessage);
		$StatusMessage = str_replace(" .", '.', $StatusMessage);
		if (preg_match('#^. #', $StatusMessage) == 1)
			$StatusMessage = substr($StatusMessage, 1);

		if ($error == 0) {
			$link = str_replace('/administrator', '', JURI::base(true)); 
			
			$StatusMessage .= '</li><li>Url: '.$link.'/'.$user_page->foldername.'/'.$user_page->pagename.'/'.$FileObject['name'];
			if ($pparams->get('editor_bbcode','1') == '1') {
				list($Originalwidth, $Originalheight, $image_type) = getimagesize($ActualFileName);
				if ($image_type > 0 && $image_type <= 3)
					$StatusMessage .= '</li><li>BBCode: [img]'.$link.'/'.$user_page->foldername.'/'.$user_page->pagename.'/'.$FileObject['name'].'[/img]';
				else
					$StatusMessage .= '</li><li>BBCode: [url='.$link.'/'.$user_page->foldername.'/'.$user_page->pagename.'/'.$FileObject['name'].']'.$FileObject['name'].'[/url]';
			}
			
			$user_page->SetConfPage(0); // Page update date 

			$app->redirect($url_redirect, $StatusMessage, 'message');
		} else
			$app->redirect($url_redirect, $StatusMessage, 'error');

		return !$error;
	}

	
// Delete all folders and indexes file for personal pages
	public static function adm_delete_folder()
	{
	    require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
		
		$folder = BSHelperUser::getFoldername();
		$userpage_list = BSHelperUser::loadPagename();

		$nb_page = count($userpage_list);
		$compte_dir_ok = 0;
		$compte_dir_ko = 0;
		$compte_ide_ok = 0;
		$compte_ide_ko = 0;
		
		for ($i = 0; $i < $nb_page; $i++) {
			if (@unlink(JPATH_ROOT.DS.$folder.DS.$userpage_list[$i]['pagename'].DS.'index.php'))
				$compte_ide_ok = $compte_ide_ok +1;
			else
				$compte_ide_ko = $compte_ide_ko +1;
			
			if (@rmdir(JPATH_ROOT.DS.$folder.DS.$userpage_list[$i]['pagename']))
				$compte_dir_ok = $compte_dir_ok +1;
			else
				$compte_dir_ko = $compte_dir_ko +1;
		}

		return(array($compte_ide_ko + $compte_dir_ko, JText::_('COM_MYJSPACE_ADMIN_DELETE_FOLDER_1').$compte_dir_ok.' : ok (dir), '.$compte_ide_ok.' : ok (index), ' .$compte_dir_ko.' : ko (dir), '.$compte_ide_ko.' : ko (index)'. ' /'. $nb_page));
	}

	
// Create (or Recreate after delete) all folders and indexes for personal pages
	public static function adm_create_folder()
	{
	    require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';

		$pparams = JComponentHelper::getParams('com_myjspace');
		$user_page = New BSHelperUser();
		$user_page->foldername = BSHelperUser::getFoldername();
		$userpage_list = BSHelperUser::loadPagename();
		
		$nb_page = count($userpage_list);
		if ($nb_page <= 0)
			return('COM_MYJSPACE_ADMIN_CREATE_FOLDER_1');
		
		$retour_ok = 0;
		for ($i = 0; $i < $nb_page; $i++) {
			if ($user_page->CreateDirFilePage($userpage_list[$i]['pagename'], $pparams->get('index_pagename_id', 1), $userpage_list[$i]['id']))
				$retour_ok = $retour_ok+1;
		}
		
		return(array($retour_ok, JText::_('COM_MYJSPACE_ADMIN_CREATE_FOLDER_2').$retour_ok.'/'.$nb_page));
	}

// Delete all empty pages (= content & folders) 
	public static function adm_delete_empty_pages()
	{
	    require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';

		$pparams = JComponentHelper::getParams('com_myjspace');		
		$link_folder = $pparams->get('link_folder', 1);
		
		$folder = BSHelperUser::getFoldername();
		$userpage_list = BSHelperUser::loadPagename(-1, 0, 0, 0, -1); // Page List with content empty

		$nb_page = count($userpage_list);
		$compte_del_ok = 0;
		$compte_del_ko = 0;
		$user_page = New BSHelperUser();

		for ($i = 0; $i < $nb_page; $i++) {
			$user_page->id = $userpage_list[$i]['id'];
			$user_page->pagename = $userpage_list[$i]['pagename'];
			$user_page->foldername = $folder;
			if ($user_page->deletePage($link_folder, 0)) // Delete but do not force to delete files
				$compte_del_ok = $compte_del_ok + 1;
			else
				$compte_del_ko = $compte_del_ko + 1;
				
			echo "<br>DEL:".$user_page->id.' '.$userpage_list[$i]['pagename'].' '.$compte_del_ok.' '.$compte_del_ko;			
		}
	
		return(JText::sprintf('COM_MYJSPACE_ADMIN_DELETE_EMPTY_PAGES_1', $compte_del_ok, $compte_del_ko));
	}
	
// Rename old foldername in all pages
	public static function adm_rename_folder_in_pages($foldername_old = '', $foldername_new = '')
	{
	    require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';

		$userpage_list = BSHelperUser::loadPagename(-1, 0, 0, 0, 1, array('content'), $foldername_old); // Only page with 'potential' url content 	
		$nb_page = count($userpage_list);

		if ($nb_page <= 0)
			return 0;

		$uri_rel = str_replace('/administrator', '', JURI::base(true));
		$uri_abs = str_replace('/administrator', '', JURI::base()); 
		$user_page = New BSHelperUser();			
			
		for ($i = 0; $i < $nb_page; $i++) {
			// User info 
			$user_page->id = $userpage_list[$i]['id'];
			$user_page->loadPageInfo();
			// Update image link or  link (relative & absolute)
			$user_page->content = str_replace('href="'.$uri_rel.'/'.$foldername_old.'/'.$user_page->pagename, 'href="'.$uri_rel.'/'.$foldername_new.'/'.$user_page->pagename, $user_page->content);
			$user_page->content = str_replace('href="'.$uri_abs.$foldername_old.'/'.$user_page->pagename, 'href="'.$uri_abs.$foldername_new.'/'.$user_page->pagename, $user_page->content);
			$user_page->content = str_replace('src="'.$uri_rel.'/'.$foldername_old.'/'.$user_page->pagename, 'src="'.$uri_rel.'/'.$foldername_new.'/'.$user_page->pagename, $user_page->content);
			$user_page->content = str_replace('src="'.$uri_abs.$foldername_old.'/'.$user_page->pagename, 'src="'.$uri_abs.$foldername_new.'/'.$user_page->pagename, $user_page->content);
			// Save modified content content
			$user_page->updateUserContent();
		}
		
		return 1;
	}
	

// move all pernonal pages folders to another root folder
// return the number of subfolders renamed
	public static function adm_rename_folders($old_root_folder = '', $new_root_folder = '')
	{
	    require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
	
		$userpage_list = BSHelperUser::loadPagename();
		
		$nb_page = count($userpage_list);
		if ($nb_page <= 0)
			return 0;
		
		$retour_ok = 0;
		for ($i = 0; $i < $nb_page; $i++) {
			if (@rename($old_root_folder.DS.$userpage_list[$i]['pagename'], $new_root_folder.DS.$userpage_list[$i]['pagename']))
				$retour_ok = $retour_ok+1;
		}
		return $retour_ok;
	}
	

	// List of model pages
	// Return: tab of model
	public static function model_pagename_list()
	{
		$pparams = JComponentHelper::getParams('com_myjspace');
		$model_pagename = str_replace('_', ' ', $pparams->get('model_pagename', ''));
		if ($model_pagename == '')
			return array();
		$model_pagename_tab = array_merge(array(JText::_('COM_MYJSPACE_MODELTOBESELECTED')) ,explode(',',$model_pagename));
		
		$model_pagename_tab_count = count($model_pagename_tab);
		$user_page = New BSHelperUser();
		
		for ($i = 1; $i < $model_pagename_tab_count; $i++) { // Page check and find the name
			if (intval($model_pagename_tab[$i]) == $model_pagename_tab[$i] && intval($model_pagename_tab[$i]) != 0) { // number
				$user_page->id = $model_pagename_tab[$i];
				$user_page->loadPageInfoOnly(0);
				$model_pagename_tab[$i] = str_replace('_', ' ', $user_page->pagename); // Replace the id with the pagename
			} else { // text
				// Check if pagename
				$user_page->id = 0;
				$user_page->pagename = $model_pagename_tab[$i];
				$user_page->loadPageInfoOnly(1);
				if ($user_page->pagename == null) { // Not an existing pagename => file to upload 
					$chaine_tab = explode('.',str_replace('_', ' ', basename($model_pagename_tab[$i])));
					$model_pagename_tab[$i] = $chaine_tab[0];
				} else
					$model_pagename_tab[$i] = str_replace('_', ' ', $user_page->pagename);
			}
		}
		
		return $model_pagename_tab;
	}

	// Retreive the page id or file to use
	public static function model_pagename_id($id = 0)
	{
		if ($id == 0)
			return 0;
			
		$pparams = JComponentHelper::getParams('com_myjspace');
		$model_pagename = $pparams->get('model_pagename', '');
		$model_pagename_tab = array_merge(array(JText::_('COM_MYJSPACE_MODELTOBESELECTED')), explode(',',$model_pagename));

		if ($id >= count($model_pagename_tab))
			return 0;
			
		$user_page = New BSHelperUser();
		
		if (intval($model_pagename_tab[$id]) == 0) { // if not number ...
			$user_page->id = 0;
			$user_page->pagename = $model_pagename_tab[$id];
			$user_page->loadPageInfoOnly(1);
			
			if ($user_page->id != 0)
				$model_pagename_tab[$id] = $user_page->id; // Replace the name with the the page id
			// if $user_page->id = 0 => not a pagename => file name
		}

		return $model_pagename_tab[$id];
	}
	
}

?>
