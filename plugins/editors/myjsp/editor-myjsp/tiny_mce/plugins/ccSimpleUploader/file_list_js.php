<?php
/**
* @version $Id: file_list_js.php $
* @version		09/07/2012 For MyJspace >= 2.0.0
* @package		pluging : BS ccSimpleUploader for Tiny Mce & BS myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Get the list of images for Tiny mce an create a javascript variable for outpout.
// There images will be displayed as a dropdown in all image dialogs if the 
// external_link_image_url, media_external_list_url or external_link_list_url options are defined in TinyMCE init.

// Test if user connected & constant
require_once "session_joomla.php";
iam_connected() or die('Restricted access');

	$fmt_param = isset($_GET["fmt"]) ? $_GET["fmt"] : 0;
	$type = isset($_GET["type"]) ? $_GET["type"] : '';
	$id	= isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
	
	if ($type != 'file' && $type != 'media' && $type != 'image') // Safety
		die('Restricted access - 1');
		
	$url_rep = my_rep($id, upload_isAdmin(JFactory::getuser())); // Page folder
	if ($url_rep == null) // Safety
		die('Restricted access - 2');
		
	// Headers
	header('Content-type: text/javascript'); // Make output a real JavaScript file! Browser will now recognize the file as a valid JS file
	header('pragma: no-cache'); // prevent browser from caching
	header('expires: 0'); // i.e. contents have already expired

//  Get info for folder to list : starting in Jommla Root
//	$rep_base = isset($_GET['d']) ? $_GET['d'] : "./"; // => JPATH_BASE
//	$url_rep = isset($_GET['subs']) ? $_GET['subs'] : './'; 
//  unused anymore => security :-)
	$rep = JPATH_BASE.DS.$url_rep; // no rewrite for the image forder by the server (if rewrite => change the line)
	$tab_url = explode('/plugins/editors/',$_SERVER['SCRIPT_NAME']);
	$url_base = $tab_url[0].'/'.$url_rep;
	
	$pparams = JComponentHelper::getParams('com_myjspace');	
	// For more control
	if ($type == 'media' && $pparams->get('uploadmedia', 1) == 1) { // Use the files configured: 'flv','avi','mp4','mov','wmv' ...
		$uploadfile = strtolower(str_replace(' ', '', $pparams->get('uploadfile', '*'))); // Files suffixes	
		$uploadfile = str_replace(array('|',' '), array(',',''), $uploadfile); // Compatibility with MyJspace < 1.7.7 and cleanup
		$allowed_types = explode(',', $uploadfile);
	} else if ($type == 'image' &&  $pparams->get('downloadimg', 1) == 1)
		$allowed_types = array('jpg','png','gif');
	else if ($type == 'file' && $pparams->get('uploadmedia', 1) == 1)
		$allowed_types = array('*');
	else
		$allowed_types = array();
		
	$forbiden_files = array('.','..','index.html','index.htm','index.php','configuration.php','.htaccess',basename(__FILE__));

	if ($fmt_param == 0) {
		if ($type == 'media')
			echo "var tinyMCEMediaList = [\n";
		else if ($type == 'image')
			echo "var tinyMCEImageList = new Array(\n";
		else if ($type == 'file')
			echo "var tinyMCELinkList = new Array(\n";
	} else // Liste pour la fenetre de uploader
		echo "var maliste = new Array(\n";
		
	$oldfolder = getcwd();
	@chdir($rep);
	if ($dir = @opendir('.')) {
		$compte = 0;
		while (false !== ($File = @readdir($dir))) {
			$path_parts = strtolower(pathinfo($File, PATHINFO_EXTENSION));
			
			if (!is_dir($File) && ( $allowed_types[0] == '*' || in_array($path_parts, $allowed_types)) && (!in_array(strtolower($File), $forbiden_files))) {
				if ($compte != 0)
					echo ",\n";
				if ($fmt_param == 0)
					echo '["'.utf8_encode($File).'","'.utf8_encode($url_base.$File).'"]';
				else
					echo '["'.utf8_encode($File).'"]';
				
				$compte++;
			}
		}
		@closedir($dir);
	}
	@chdir($oldfolder);
	
	if ($fmt_param == 0 && $type == 'media')
		echo "\n];";
	else
		echo "\n);";
?>
