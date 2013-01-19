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

class JoomBlogViewComment extends JView
{
	public function display($tpl = null) 
	{
		$this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html');

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->custom = $this->get('Custom');

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
		$isNew = $this->item->id == 0;
		$canDo = JoomBlogHelper::getActions();
		JToolBarHelper::title($isNew ? JText::_('COM_JOOMBLOG_COMMENT_CREATING') : JText::_('COM_JOOMBLOG_COMMENT_EDITING'), 'comments');
		if ($canDo->get('core.manage')) {
			JToolBarHelper::apply('comment.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('comment.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('comment.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			if (!$isNew) {
				JToolBarHelper::custom('comment.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('comment.cancel', 'JTOOLBAR_CLOSE');
		}
	}

	protected function setDocument() 
	{
		$isNew = $this->item->id == 0;
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_JOOMBLOG_COMMENT_CREATING') : JText::_('COM_JOOMBLOG_COMMENT_EDITING'));
	}
}
