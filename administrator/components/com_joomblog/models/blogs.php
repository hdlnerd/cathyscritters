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

class JoomBlogModelBlogs extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'i.id','i.published','i.ordering','i.title','i.hits','i.rate'
			);
		}
		parent::__construct($config);
	}
	
	protected function populateState()
	{
		$this->setState('filter.search', $this->getUserStateFromRequest('com_joomblog.filter.search', 'filter_search'));
		$this->setState('filter.author_id', $this->getUserStateFromRequest('com_joomblog.filter.author_id', 'filter_author_id'));

		parent::populateState();
	}

	protected function getListQuery() 
	{
		$db = $this->_db;
		$query = $db->getQuery(true);

		$query->select('i.*');
		$query->from('#__joomblog_list_blogs AS i');

		$query->select('u.name AS author');
		$query->join('LEFT', '#__users AS u ON u.id=i.user_id');

		$query->order($db->getEscaped($this->getState('list.ordering', 'i.title')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));

		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
			$query->where('i.title LIKE '.$search.' OR i.description LIKE '.$search);
		}

		$search = $this->getState('filter.author_id');
		if (!empty($search)) {
			$query->where('i.user_id='.(int)$search);
		}
		
		$query->group('i.id');

		return $query;
	}
	
	public function getUsers()
	{
		$model = JModel::getInstance('Users', 'JoomBlogModel', array('ignore_request'=>true));
		$items = $model->getItems();
		
		return $items;
	}
}
