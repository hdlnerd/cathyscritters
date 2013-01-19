<?php

/**
* JoomBlog component for Joomla 1.6 & 1.7
* @package JoomBlog
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

if (!JFactory::getUser()->authorise('core.manage', 'com_joomblog')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JLoader::register('JoomBlogHelper', dirname(__FILE__).DS.'helpers'.DS.'joomblog.php');
JLoader::register('JBImageHelper', dirname(__FILE__).DS.'helpers'.DS.'image.php');

jimport('joomla.version');
$version = new JVersion();
$joomla_version = $version->getShortVersion();
if ($joomla_version <= 1.7) 
{
	JLoader::register('JRule', JPATH_PLATFORM . '/joomla/access/rule.php');
	JLoader::register('JRules', JPATH_PLATFORM . '/joomla/access/rules.php');
	class JAccessRules extends JRules { }
}

jimport('joomla.application.component.controller');

$controller = JController::getInstance('JoomBlog');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
