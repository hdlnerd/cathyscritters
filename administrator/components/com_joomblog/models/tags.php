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

class JoomBlogModelTags extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'i.id','i.name', 'i.default'
			);
		}
		parent::__construct($config);
	}
	
	protected function populateState()
	{
		$this->setState('filter.search', $this->getUserStateFromRequest('com_joomportfolio.filter.search', 'filter_search'));
		
		parent::populateState();
	}

	protected function getListQuery() 
	{
		$db = $this->_db;
		$query = $db->getQuery(true);

		$query->select('i.*');
		$query->from('#__joomblog_tags AS i');

		$query->order($db->getEscaped($this->getState('list.ordering', 'i.id')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));

		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
			$query->where('i.name LIKE '.$search);
		}
		
		$query->group('i.id');

		return $query;
	}
}
