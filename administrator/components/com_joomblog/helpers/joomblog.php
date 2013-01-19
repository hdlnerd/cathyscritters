<?php

/**
* JoomBlog component for Joomla 1.6 & 1.7
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

class JoomBlogHelper
{
	public static function addSubmenu($submenu) 
	{
		JSubMenuHelper::addEntry(JText::_('COM_JOOMBLOG_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_joomblog', $submenu == 'categories');
		JSubMenuHelper::addEntry(JText::_('COM_JOOMBLOG_SUBMENU_BLOGS'), 'index.php?option=com_joomblog&view=blogs', $submenu == 'blogs');
        JSubMenuHelper::addEntry(JText::_('COM_JOOMBLOG_SUBMENU_DRAFTS'), 'index.php?option=com_joomblog&view=drafts', $submenu == 'drafts');
		JSubMenuHelper::addEntry(JText::_('COM_JOOMBLOG_SUBMENU_POSTS'), 'index.php?option=com_joomblog&view=posts', $submenu == 'posts');
        JSubMenuHelper::addEntry(JText::_('COM_JOOMBLOG_SUBMENU_ARCHIVES'), 'index.php?option=com_joomblog&view=archives', $submenu == 'archive');
		JSubMenuHelper::addEntry(JText::_('COM_JOOMBLOG_SUBMENU_COMMENTS'), 'index.php?option=com_joomblog&view=comments', $submenu == 'comments');
		JSubMenuHelper::addEntry(JText::_('COM_JOOMBLOG_SUBMENU_TAGS'), 'index.php?option=com_joomblog&view=tags', $submenu == 'tags');
		JSubMenuHelper::addEntry(JText::_('COM_JOOMBLOG_SUBMENU_USERS'), 'index.php?option=com_joomblog&view=users', $submenu == 'users');
		JSubMenuHelper::addEntry(JText::_('COM_JOOMBLOG_SUBMENU_SETTINGS'), 'index.php?option=com_joomblog&view=settings', $submenu == 'settings');
		JSubMenuHelper::addEntry(JText::_('COM_JOOMBLOG_SUBMENU_HELP'), 'index.php?option=com_joomblog&view=help', $submenu == 'help');
		JSubMenuHelper::addEntry(JText::_('COM_JOOMBLOG_SUBMENU_ABOUT'), 'index.php?option=com_joomblog&view=about', $submenu == 'about');
		
		if ($submenu == 'categories') {
			$document = &JFactory::getDocument();
			$document->setTitle(JText::_('COM_CATEGORIES_CATEGORIES_TITLE'));
			$document->addStyleSheet(JURI::root().'administrator/components/com_joomblog/assets/css/joomblog.css');

			$controller = JController::getInstance('Categories');
			$view = $controller->getView('categories', 'html');
			$view->addTemplatePath(JPATH_BASE . DS . 'components' . DS . 'com_joomblog' . DS . 'helpers' . DS . 'html');
			$view->setLayout('categories');
		}
	}

	public static function getActions($categoryId = 0, $postId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($postId) && empty($categoryId)) {
			$assetName = 'com_joomblog';
		}
		else if (empty($postId)) {
			$assetName = 'com_joomblog.category.'.(int)$categoryId;
		}
		else {
			$assetName = 'com_joomblog.article.'.(int)$postId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}

	public static function getVersion() 
	{
		$params = self::getManifest();
		return $params->version;
	}

	public static function getManifest()
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT `manifest_cache` FROM #__extensions WHERE element="com_joomblog"');
		$params = json_decode($db->loadResult());
		return $params;
	}
	
	public static function getParams()
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT `params` FROM #__extensions WHERE element="com_joomblog"');
		$params = json_decode($db->loadResult());
		return $params;
	}
	
	public static function loadData($file = 'custom.xml')
	{
		$xmlFile = self::getDataFile($file);
		$xml = simplexml_load_file($xmlFile);
		return $xml;
	}

	public static function saveData($xml, $file = 'custom.xml')
	{
		$xmlString = $xml->asXML();
		$xmlFile = self::getDataFile($file);
		if ($xmlFile = fopen($xmlFile, 'w')) {
			fwrite($xmlFile, $xmlString);
			fclose($xmlFile);
			return true;
		}

		return false;
	}
	
	public static function getDataFile($file) {
		$mediaFile = JPATH_SITE.DS.'media'.DS.'com_joomblog'.DS.$file;
		if (file_exists($mediaFile)) {
			$xmlFile = $mediaFile;
		} else {
			$xmlFile = JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'forms'.DS.$file;
		}
		return $xmlFile;
	}
}
