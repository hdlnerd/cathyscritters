<?php
/**
* JoomPortfolio component for Joomla 1.6
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
 
class JoomBlogViewSettings extends JView
{
	protected $state;
	protected $item;
	protected $form;
	
        function display($tpl = null) 
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
		//JRequest::setVar('hidemainmenu', true);
		JToolBarHelper::title(JText::_('COM_JOOMBLOG_EDIT_SETTINGS'),'settings');
		JToolBarHelper::apply('settings.apply', 'JTOOLBAR_APPLY');
		//JToolBarHelper::save('settings.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::cancel('settings.cancel', 'JTOOLBAR_CANCEL');
	}

	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle( JText::_('COM_JOOMBLOG_EDIT_SETTINGS'));
	}
}