<?php

/**
* JoomBlog component for Joomla 1.6
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JoomBlogViewPosts extends JView
{
	function display($tpl = null) 
	{
		$this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html');

		$this->posts = $this->get('Items');
		$this->state = $this->get('State');
		$this->blogs = $this->get('Blogs');
		$this->categories = $this->get('Categories');
		$this->users = $this->get('Users');
		$this->pagination = $this->get('Pagination');

		$this->user = &JFactory::getUser();
		
		$model = $this->getModel();
		 if (sizeof($this->posts))
		 {
		 	foreach ( $this->posts as $post ) 
		 	{
		 		$post->cats = $model->getMulticats($post->id);
		 		$post->tags = $model->getTags($post->id);
		 	}
		 }
			
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->addToolBar();
		$this->setDocument();

		parent::display($tpl);
	}

	protected function addToolBar() 
	{
		$this->canDo = $canDo = JoomBlogHelper::getActions($this->state->get('filter.category_id'));
		JToolBarHelper::title(JText::_('COM_JOOMBLOG_MANAGER_POSTS'), 'posts');
		if ($canDo->get('core.create') and $this->categories) {
			JToolBarHelper::addNew('post.add', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit') or $canDo->get('core.edit.own')) {
			JToolBarHelper::editList('post.edit', 'JTOOLBAR_EDIT');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::custom('posts.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('posts.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'posts.delete', 'JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.admin')) {
			//JToolBarHelper::divider();
			//JToolBarHelper::preferences('com_joomblog');
		}
	}

	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_JOOMBLOG_MANAGER_POSTS'));
	}
}
