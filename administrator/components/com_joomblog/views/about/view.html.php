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

class JoomBlogViewAbout extends JView
{
	function display($tpl = null) 
	{
		$this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html');

		$this->addToolBar();
		$this->setDocument();

		parent::display($tpl);
	}

	protected function addToolBar() 
	{
		JToolBarHelper::title(JText::_('COM_JOOMBLOG_MANAGER_ABOUT'), 'about');
	}

	protected function setDocument() 
	{
		$document = &JFactory::getDocument();
		$document->setTitle(JText::_('COM_JOOMBLOG_MANAGER_ABOUT'));
		
	}
}