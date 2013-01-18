<?php
/**
* @version $Id: install.php $
* @version		2.0.3 21/10/2012
* @package		plg_myjsp
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/*
 * Enable, the plugin after install >= J1.6
 */

class plgeditorsmyjspInstallerScript {
	function postflight($type, $parent) {

		// Get this plugin group, element
		$group = 'editors';
		$element = 'myjsp';

		// Rename manifest ...
		$retour = JFile::move('_'.$element.'.xml',$element.'.xml',JPATH_ROOT.DS.'plugins'.DS.$group.DS.$element);
		if ($retour != 1)
			echo "Retour:".$retour;
   }
}
?>
