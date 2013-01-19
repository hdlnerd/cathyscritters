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

class JoomBlogViewBlog extends JView
{
	public function display($tpl = null) 
	{
		$this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html');

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');

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
		JRequest::setVar('hidemainmenu', true);
		$user = JFactory::getUser();
		$isNew = $this->item->id == 0;
		$canDo = JoomBlogHelper::getActions();
		JToolBarHelper::title($isNew ? JText::_('COM_JOOMBLOG_BLOG_CREATING') : JText::_('COM_JOOMBLOG_BLOG_EDITING'), 'blogs');
		if ($isNew) {
			if ($canDo->get('core.create')) {
				JToolBarHelper::apply('blog.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('blog.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('blog.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
		} else {
			if ($canDo->get('core.edit') or ($canDo->get('core.edit.own') and ($this->item->user_id == $user->get('id')))) {
				JToolBarHelper::apply('blog.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('blog.save', 'JTOOLBAR_SAVE');
				if ($canDo->get('core.create')) {
					JToolBarHelper::custom('blog.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
					JToolBarHelper::custom('blog.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
				}
			}
		}
		JToolBarHelper::cancel('blog.cancel', 'JTOOLBAR_CANCEL');
	}

	protected function setDocument() 
	{
		$isNew = $this->item->id == 0;
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_JOOMBLOG_BLOG_CREATING') : JText::_('COM_JOOMBLOG_BLOG_EDITING'));
	}
}
