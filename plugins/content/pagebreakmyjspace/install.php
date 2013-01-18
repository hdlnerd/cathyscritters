<?php
/**
* @version $Id: install.php $
* @version		2.0.3 21/10/2012
* @package		plg_pagebreakmyjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/*
 * Enable, the plugin after install >= J!1.6
 */
 
class plgContentpagebreakmyjspaceInstallerScript {
	function postflight($type, $parent) {

		// Get this plugin group, element
		$group = 'content';
		$element = 'pagebreakmyjspace';

		// Rename manifest ...
		$retour = JFile::move('_'.$element.'.xml',$element.'.xml',JPATH_ROOT.DS.'plugins'.DS.$group.DS.$element);
		if ($retour != 1)
			echo "Retour:".$retour;
			
		if ($type == 'install') { // Enable plugin
			$db = JFactory::getDBO();
			$query = 'update `#__extensions`' .
				' set `enabled`=1' .
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
