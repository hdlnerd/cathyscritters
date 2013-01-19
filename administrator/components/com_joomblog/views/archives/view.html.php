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

class JoomBlogViewArchives extends JView
{
	function display($tpl = null) 
	{
		$this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html');

		$this->archives = $this->get('Items');
		$this->state = $this->get('State');
		$this->blogs = $this->get('Blogs');
		$this->categories = $this->get('Categories');
		$this->users = $this->get('Users');
		$this->pagination = $this->get('Pagination');

		$this->user = &JFactory::getUser();
		
		$model = $this->getModel();
		 if (sizeof($this->archives))
		 {
		 	foreach ( $this->archives as $archive )
		 	{
		 		$archive->cats = $model->getMulticats($archive->id);
		 		$archive->tags = $model->getTags($archive->id);
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
		JToolBarHelper::title(JText::_('COM_JOOMBLOG_MANAGER_ARCHIVES'), 'archives');
		if ($canDo->get('core.edit') or $canDo->get('core.edit.own')) {
			JToolBarHelper::editList('post.edit', 'JTOOLBAR_EDIT');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::custom('archives.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('archives.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'archives.delete', 'JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.admin')) {
		}
	}

	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_JOOMBLOG_MANAGER_ARCHIVES'));
	}
}