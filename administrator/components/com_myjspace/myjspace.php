<?php
/**
* @version $Id: myjspace.php $
* @version		2.0.3 20/10/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// Access check
if (version_compare(JVERSION, '1.6.0', 'ge') && !JFactory::getUser()->authorise('core.manage', 'com_myjspace')) {
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Create the controller
if (version_compare(JVERSION, '2.5.6', 'ge')) {
	$controller	= JControllerLegacy::getInstance('myjspace');
} else { // Allow Legacy for J!1.5, J!1.6, J!1.7, J!2.5 < 2.5.6
	require_once (JPATH_COMPONENT.DS.'controller.php');
	if ($controller = JRequest::getVar('controller')) {
		require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
	}

	$classname	= 'MyjspaceController'.$controller;
	$controller = new $classname( );
}
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
// Redirect if set by the controller
$controller->redirect();

// Pour afficher Paramètres de configurations
// OK pour afficher bouton à droite pour configuration
if (version_compare(JVERSION, '1.6.0', 'ge'))
	JToolBarHelper::preferences('com_myjspace', 400, 875, JText::_('COM_MYJSPACE_ADMIN_OPTIONS') );
else
	JToolBarHelper::preferences('com_myjspace', 400, 875, JText::_('COM_MYJSPACE_ADMIN_OPTIONS'), 'administrator/components/com_myjspace/my_config.xml' );
JToolBarHelper::divider();		

require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'version.php';
$bar = JToolBar::getInstance('toolbar');
$bar->appendButton( 'Popup', 'help', JText::_('COM_MYJSPACE_HELP'), BS_Helper_version::get_xml_item('com_myjspace', 'authorUrl'), 1024, 768);

?>
