<?php

/**
* JoomBlog component for Joomla 1.6
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class JoomBlogModelComments extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'i.id', 'i.comment', 'i.published', 'c.title', 'u.name'
			);
		}
		parent::__construct($config);
	}
	
	protected function populateState()
	{
		$this->setState('filter.search', $this->getUserStateFromRequest('com_joomblog.filter.search', 'filter_search'));

		parent::populateState();
	}

	protected function getListQuery() 
	{
		$db = $this->_db;
		$query = $db->getQuery(true);

		$query->select('i.*');
		$query->from('#__joomblog_comment AS i');

		$query->select('c.title AS post_title');
		$query->join('LEFT', '#__joomblog_posts AS c ON c.id=i.contentid');

		$query->select('u.name AS author');
		$query->join('LEFT', '#__users AS u ON u.id=i.user_id');

		$query->order($db->getEscaped($this->getState('list.ordering', 'i.id')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));

		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
			$query->where('i.comment LIKE '.$search);
		}
		
		$query->group('i.id');

		return $query;
	}
}
