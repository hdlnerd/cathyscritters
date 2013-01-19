<?php
/**
* @version $Id: view.html.php $
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

jimport('joomla.application.component.view');
jimport('joomla.html.parameter');

class MyjspaceViewPage extends JViewLegacy
{
	function display($tpl = null)
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util_acl.php';
		
		JToolBarHelper::title(JText::_('COM_MYJSPACE_HOME') .': <small>'.JText::_('COM_MYJSPACE_PAGE').'</small>', 'article.png' );
		JToolBarHelper::apply('adm_save_page', JText::_('COM_MYJSPACE_SAVE_DETAILS'));
		JToolBarHelper::save('adm_save_page_exit', JText::_('COM_MYJSPACE_SAVE_DETAILS_EXIT'));
		JToolBarHelper::divider();
		JToolBarHelper::apply('adm_save_page_content', JText::_('COM_MYJSPACE_SAVE_PAGE'));
		JToolBarHelper::save('adm_save_page_content_exit', JText::_('COM_MYJSPACE_SAVE_PAGE_EXIT'));
		JToolBarHelper::divider();

		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$app = JFactory::getApplication();
		
		$link_folder = $pparams->get('link_folder', 1);
		$link_folder_print = $pparams->get('link_folder_print', 1);
		
		// User info
		$id = JRequest::getVar('id', -1);
		if ($id < 0) {	
			$pageid_tab = JRequest::getVar('cid', array(0));
			$id = $pageid_tab[0];
		}
		
		// Personnal page info
		$user_page = New BSHelperUser();
		$user_page->id = $id;		
		$user_page->loadPageInfo();

		if ($user_page->id <= 0) {
			$app->redirect(Jroute::_('index.php?option=com_myjspace&view=pages', false));	
			return;
		}
		
		// User (for page) info
		$table = JUser::getTable();
		if ($table->load($user_page->userid)) { // Test if user exist before to retreive info
			$user = JFactory::getUser($user_page->userid);
		} else { // User no no exist any more !
			$user = new stdClass();
			$user->id = -1;
			$user->username = ' '; // '' to do NOT display a page with no user
			$user->name = '';
		}

		// Last update name: not necessary for J!1.5, not used
		if ($table->load($user_page->modified_by)) { // Test if user exist before to retreive info
			$modified_by = JFactory::getUser($user_page->modified_by);
		} else { // User no no exist any more !
			$modified_by = new stdClass();
			$modified_by->id = -1;
			$modified_by->username = ' ';
			$modified_by->name = '';
		}

		// Page link
		if ($user_page->pagename != '') {
			if ($link_folder_print == 1)
				$link = JURI::base().$user_page->foldername.'/'.$user_page->pagename;
			else
				$link = str_replace(JURI::base(true).'/', '', JURI::base()).Jroute::_('index.php?option=com_myjspace&view=see&pagename='.$user_page->pagename);
		} else 
			$link = null;
		$link = str_replace('/administrator', '', $link); 

		// Editor selection
		$editor_selection = $pparams->get('editor_selection', 'myjsp');
		if (check_editor_selection($editor_selection) == false || $editor_selection == '-') // Use the Joomla default editor
			$editor_selection = null;

		// Editor 'windows' size
		$edit_x = $pparams->get('admin_edit_x', '100%');
		$edit_y = $pparams->get('admin_edit_y', '400px');

		// Editor button
		if ($pparams->get('allow_editor_button', 1) == 1)
			$editor_button = array('readmore', 'article');
		else
			$editor_button = false;

		$date_fmt = $pparams->get('date_fmt', 'Y-m-d H:i:s');
		$uploadimg = $pparams->get('uploadimg', 1);
		$uploadmedia = $pparams->get('uploadmedia', 1);
		$publish_mode = $pparams->get('publish_mode', 2);
		$downloadimg = $pparams->get('downloadimg', 1);
		
		// Files uploaded
		if ($link_folder == 1 && ($uploadimg > 0 || $uploadmedia > 0)) {
			list($page_number, $page_size) = dir_size(JPATH_ROOT.DS.$user_page->foldername.DS.$user_page->pagename);
			$page_size = round($page_size/1024);
		} else {
			$page_size = 0;
			$page_number = 0;		
		}
		$page_size = convertSize($page_size);
		$dir_max_size = convertSize($pparams->get('dir_max_size', 2097152)); // Max upload
		$file_img_size = convertSize($pparams->get('file_max_size', 204800));
		$resize_x = $pparams->get('resize_x', 800);
		$resize_y = $pparams->get('resize_y', 600);
		if ($resize_x != 0 || $resize_y != 0) {
			if ($resize_x == 0)
				$resize_x = '&#8734';
			if ($resize_y == 0)
				$resize_y = '&#8734';
			if (function_exists("gd_info"))
				$file_img_size .= JText::sprintf('COM_MYJSPACE_LABELUSAGE3',$resize_x,$resize_y);
			else
				$file_img_size .= JText::_('COM_MYJSPACE_LABELUSAGE4');
		}

		// Files list
		$uploadadmin = $pparams->get('uploadadmin', 1);
		$forbiden_files = array('.', '..', 'index.html', 'index.htm', 'index.php');
		$tab_list_file = null;
		if ($uploadadmin == 1 && ($uploadimg > 0 || $uploadmedia > 0))
			$tab_list_file = list_file_dir(JPATH_ROOT.DS.$user_page->foldername.DS.$user_page->pagename, array('*'), $forbiden_files, 1);
		
		// Dates check if no set with interesting date
		$vide = '';
	
		if ($user_page->create_date == '0000-00-00 00:00:00' || $user_page->create_date == null)
			$this->assignRef('create_date', $vide);
		else {
			$create_date = date($date_fmt, strtotime($user_page->create_date));
			$this->assignRef('create_date', $create_date);
		}

		if ($user_page->last_update_date == '0000-00-00 00:00:00' || $user_page->last_update_date == null)
			$this->assignRef('last_update_date', $vide);
		else {
			$last_update_date = date($date_fmt, strtotime($user_page->last_update_date));
			$this->assignRef('last_update_date', $last_update_date);
		}
		
		if ($user_page->last_access_date == '0000-00-00 00:00:00' || $user_page->last_access_date ==  null)
			$this->assignRef('last_access_date', $vide);
		else {
			$last_access_date = date($date_fmt, strtotime($user_page->last_access_date));
			$this->assignRef('last_access_date', $last_access_date);
		}
		
		// Lock or not
		$lock_img = JURI::base().'components/com_myjspace/assets/checked_out.png';
		$lock_img = str_replace('/administrator', '', $lock_img);
		$aujourdhui = time();
		if (strtotime($user_page->publish_up) >= $aujourdhui)
			$img_publish_up = '<img src="'.$lock_img.'" alt="lock" />';
		else
			$img_publish_up = '';
		if ($user_page->publish_down != '0000-00-00 00:00:00'  && $user_page->publish_down != null && strtotime($user_page->publish_down) < $aujourdhui)
			$img_publish_down = '<img src="'.$lock_img.'" alt="lock" />';
		else
			$img_publish_down = '';

		// Publish date : for several date format
		$date_fmt_tab = explode(' ',$date_fmt);
		$replace = array('%Y', '%d', '%m');
		$search = array('Y', 'd', 'm');		
		$date_fmt_pub = str_replace($search, $replace, $date_fmt_tab[0]);
		
		if ($user_page->publish_up == '0000-00-00 00:00:00' || $user_page->publish_up == null)
			$this->assignRef('publish_up', $vide);
		else {
			$publish_up = date($date_fmt_tab[0], strtotime($user_page->publish_up));
			$this->assignRef('publish_up', $publish_up);
		}
		if ($user_page->publish_down == '0000-00-00 00:00:00' || $user_page->publish_down == null)
			$this->assignRef('publish_down', $vide);
		else {
			$publish_down = date($date_fmt_tab[0], strtotime($user_page->publish_down));
			$this->assignRef('publish_down', $publish_down);
		}

		// Templates list proposed for user selection
		$template_list = $pparams->get('template_list', '');
		if ($template_list != '')
			$tab_template = explode(',', $template_list);
		else
			$tab_template = null;
		
		// Automatic configuration :-)
		if ($user_page->pagename == '' || $link_folder == 0) {
			$uploadadmin = 0;
			$uploadimg = 0;
			$uploadmedia = 0;
		}
		
		// Categories
		$categories = BSHelperUser::GetCategories(1);

		// Build the script (change user)
		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			$script = array();
			$script[] = 'function jSelectUser_jform_created_by(id, title) {';
			$script[] = '	var old_id = document.getElementById("mjs_userid").value;';
			$script[] = '	if (old_id != id) {';
			$script[] = '		document.getElementById("mjs_username2").value = title;';
			$script[] = '		document.getElementById("mjs_userid").value = id;';
			$script[] = '	}';
			$script[] = '	SqueezeBox.close();';
			$script[] = '}';

			// Add the script to the document head
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		}
		
		// Share edit
		if ($pparams->get('share_page', 0) != 0)
			$group_list = JHtml::_('access.assetgroups');
		else
			$group_list = null;

		// Blockview list
		$blockview_list = get_assetgroup_list();

		$this->assignRef('id', $id);
		$this->assignRef('editor_selection', $editor_selection);
		$this->assignRef('username', $user->username);
		$this->assignRef('pagename', $user_page->pagename);
		$this->assignRef('link', $link);
		$this->assignRef('blockview', $user_page->blockView);
		$this->assignRef('blockview_list', $blockview_list);
		$this->assignRef('blockedit', $user_page->blockEdit);
		$this->assignRef('last_access_ip', $user_page->last_access_ip);
		$this->assignRef('hits', $user_page->hits);
		$this->assignRef('content', $user_page->content);
		$this->assignRef('edit_x', $edit_x);
		$this->assignRef('edit_y', $edit_y);
		$this->assignRef('resize_x', $resize_x);
		$this->assignRef('resize_y', $resize_y);		
		$this->assignRef('uploadimg', $uploadimg);
		$this->assignRef('uploadmedia', $uploadmedia);
		$this->assignRef('file_max_size', $file_max_size);
		$this->assignRef('tab_list_file', $tab_list_file);
		$this->assignRef('downloadimg', $downloadimg);
		$this->assignRef('uploadadmin', $uploadadmin);
		$this->assignRef('link_folder', $link_folder);
		$this->assignRef('page_size', $page_size);
		$this->assignRef('page_number', $page_number);
		$this->assignRef('foldername', $user_page->foldername);
		$this->assignRef('publish_mode', $publish_mode);
		$this->assignRef('date_fmt_pub', $date_fmt_pub);
		$this->assignRef('img_publish_up', $img_publish_up);
		$this->assignRef('img_publish_down', $img_publish_down);
		$this->assignRef('metakey', $user_page->metakey);
		$this->assignRef('dir_max_size', $dir_max_size);
		$this->assignRef('file_img_size', $file_img_size);
		$this->assignRef('tab_template', $tab_template);
		$this->assignRef('template', $user_page->template);
		$this->assignRef('editor_button', $editor_button);
		$this->assignRef('categories', $categories);
		$this->assignRef('catid', $user_page->catid);
		$this->assignRef('access', $user_page->access);
		$this->assignRef('group_list', $group_list);
		$this->assignRef('modified_by', $modified_by->username);
			
		parent::display($tpl);
	}
	
}

?>
