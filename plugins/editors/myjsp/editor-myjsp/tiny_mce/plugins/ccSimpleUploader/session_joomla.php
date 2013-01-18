<?php
/**
* @version $Id: session_joomla.php $
* @version		21/07/2012 For MyJspace >= 2.0.0
* @package		pluging : BS ccSimpleUploader for Tiny Mce
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

define( '_JEXEC', 1 );
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
$tab_base = explode(DS.'plugins'.DS.'editors',__FILE__);
define( 'JPATH_BASE', $tab_base[0] );

require_once (JPATH_BASE.DS.'includes'.DS.'defines.php' );
require_once (JPATH_BASE.DS.'includes'.DS.'framework.php' );

jimport ('joomla.plugin.helper'); // J!1.5
jimport( 'joomla.html.parameter' );

function iam_connected() {
	// Environement
	$view = isset($_REQUEST['view']) ? $_REQUEST['view'] : '';
	if ($view == 'page') // Specific for MyJspace backend
		$site = 'administrator';
	else
		$site = 'site';

	$mainframe = JFactory::getApplication($site);
	$session   = JFactory::getSession();
	
	// Allow upload from editor plugin myjsp ?
	$plugin = JPluginHelper::getPlugin('editors', 'myjsp'); // No plugin or not enable
	if (!$plugin)
		return 0;
	
	// Not allowed to upload from the plugin
	if (version_compare(JVERSION, '1.6.0', 'ge')) {
		$data = json_decode($plugin->params, true);
		 if ($data['allow_upload'] == 0)
			return 0;
	} else {
		$params_object = new JParameter($plugin->params );
		if ($params_object->get('allow_upload', 1) == 0)
			return 0;
	}

	// Allow upload from com_myjspace ?
	$pparams = JComponentHelper::getParams('com_myjspace');
	if ($pparams->get('uploadmedia', 1) == 0 && $pparams->get('downloadimg', 1) == 0 )
		return 0;
		
	// And connected
	$user = JFactory::getuser();
	return $user->id;
}

function my_rep($id = 0, $is_admin = false) {
    require_once JPATH_BASE.DS.'components'.DS.'com_myjspace'.DS.'helpers'.DS.'user.php';

	// Safety controls
	$pparams = JComponentHelper::getParams('com_myjspace');
	if ($pparams->get('link_folder', 1) == 0)
		return null;
	
	if ($id) {
		$user_page = New BSHelperUser();

		$user_page->id = $id;
		$user_page->loadPageInfoOnly();

		if ($is_admin == false) { /// If no admin check to be sure : only my pages
			$user = JFactory::getuser(); // Check if user exists & connected
			if ($user->id != $user_page->userid) // Check for only my page
				return null;
		}
		
		if ($user_page->pagename == '')
			return null;
		else
			return $user_page->foldername.'/'.$user_page->pagename.'/';
	}
	return null;
}

// Function to check if the admin can upload for user = from back-end + admin rights
function upload_isAdmin($user = null) {

	if (JFactory::getApplication()->isAdmin() && version_compare(JVERSION,'1.6.0','ge') && $user->authorise('core.manage', 'com_myjspace') )
		return true;
	
	if (JFactory::getApplication()->isAdmin() && version_compare(JVERSION,'1.6.0','lt') && ($user->usertype == "Super Administrator" || $user->usertype == "Administrator" ))
		return true;

	return false;
}
?>
