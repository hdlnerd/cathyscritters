<?php

/**
* JoomBlog component for Joomla 1.6 & 1.7
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class JoomBlogModelPlugins extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'a.name','b.published','a.folder'
			);
		}
		parent::__construct($config);
	}
	
	
	protected function populateState()
	{
		$this->setState('filter.search_plugin', $this->getUserStateFromRequest('com_joomblog.filter.search_plugin', 'filter_search_plugin'));
		$this->setState('filter.search_plugin_type', $this->getUserStateFromRequest('com_joomblog.filter.search_plugin_type', 'filter_plugin_type'));

		parent::populateState();
	}	
	
	protected function getListQuery() 
	{
		$this->updatePluginsTable();
		$db = $this->_db;
		$query = $db->getQuery(true);
		$query->select('a.name, a.folder, a.element, b.id, b.published');
		$query->from('`#__extensions` AS a, `#__joomblog_plugins` AS b');
		$query->where('b.id=a.extension_id');
		$query->where('a.enabled=1');
		$type = $this->getState('filter.search_plugin_type');
		if (!empty($type)) { $query->where('a.folder="'.$type.'"'); }
		$query->where('a.type="plugin"');
		$query->where('a.element !="jom_comment_bot"');
		$search = $this->getState('filter.search_plugin');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
			$query->where('a.name LIKE '.$search);
		}
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.ordering ')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));
		return $query;
	}
	
	function updatePluginsTable()
	{
		$strSQL	= "SELECT a.name, a.folder, a.extension_id AS id FROM `#__extensions` AS a "
				. "LEFT OUTER JOIN `#__joomblog_plugins` AS b "
				. "ON (a.extension_id=b.id) "
				. "WHERE b.id IS NULL "
				. "AND (a.folder='content' OR a.folder='editors-xtd')"
				. "AND a.enabled=1 "
				. "AND a.type='plugin' "
				. "AND a.element!='jom_comment_bot'";

		$this->_db->setQuery($strSQL);
		$plugins	= $this->_db->loadObjectList();
		
		if($plugins)
		{
			foreach($plugins as $plugin)
			{
				$strSQL	= "INSERT INTO `#__joomblog_plugins` SET id='{$plugin->id}'";
				$this->_db->setQuery($strSQL);
				$this->_db->query();
			}
		}
	}
		
	
}
