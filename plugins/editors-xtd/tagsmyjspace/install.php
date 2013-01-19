<?php
/**
* @version $Id: install.php $
* @version		2.0.3 21/10/2012
* @package		plg_jsmyjspace
* @author       Bernard Saulm�
* @copyright	Copyright (C) 2012 Bernard Saulm�
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/*
 * Enable, the plugin after install >= J1.6
 */
 
class plgeditorsxtdtagsmyjspaceInstallerScript {
	function postflight($type, $parent) {

		// Get this plugin group, element
		$group = 'editors-xtd';
		$element = 'tagsmyjspace';

		// Rename manifest ...
		$retour = JFile::move('_'.$element.'.xml',$element.'.xml',JPATH_ROOT.DS.'plugins'.DS.$group.DS.$element);
		if ($retour != 1)
			echo "Retour:".$retour;

		if ($type == 'install') { // Enable plugin
			$db = JFactory::getDBO();
			$query = 'UPDATE `#__extensions`' .
				' SET `enabled` = 1' .
				' WHERE folder = '.$db->Quote($group) .
				' AND element = '.$db->Quote($element);
				
			$db->setQuery($query);
			try {
				$db->Query();
			}
			catch(JException $e)
			{
				// Return warning message that cannot update order			
				echo JText::_('Cannot enable the plugin');
			}
		}
   }
}

?>