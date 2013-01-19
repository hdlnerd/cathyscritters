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

class JoomBlogViewTags extends JView
{
	function display($tpl = null) 
	{
		$this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html');

		$this->tags = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');

		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->addToolBar();
		$this->setDocument();

		parent::display($tpl);
	}

	protected function addToolBar() 
	{
		$this->canDo = $canDo = JoomBlogHelper::getActions();
		JToolBarHelper::title(JText::_('COM_JOOMBLOG_MANAGER_TAGS'), 'tags');
		if ($canDo->get('core.manage')) 
		{
			JToolBarHelper::addNew('tag.add', 'JTOOLBAR_NEW');
			JToolBarHelper::editList('tag.edit', 'JTOOLBAR_EDIT');
			JToolBarHelper::deleteList('', 'tags.delete', 'JTOOLBAR_DELETE');
		}
	}

	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_JOOMBLOG_MANAGER_TAGS'));
	}
}
