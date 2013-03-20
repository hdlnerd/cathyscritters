<?php
/*
 *
 * @Version       $Id: view.html.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.3
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-01-04 14:39:25 +0000 (Fri, 04 Jan 2013) $
 *
 */

defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Issuetracker view
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */
class IssueTrackerViewEmail extends JViewLegacy
{
   protected $state;
   protected $item;
   protected $form;

   /**
    * Display the view
    */
   public function display($tpl = null)
   {

      // get the Data
      $this->state   = $this->get('State');
      $this->form    = $this->get('Form');
      $this->item    = $this->get('Item');
      // $this->script = $this->get('Script');

      // Check for errors
      if (count($errors = $this->get('Errors'))) {
         JError::raiseError(500, implode('<br />', $errors));
         return false;
      }

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);

      $this->addToolbar();
      parent::display($tpl);
   }

   protected function addToolBar()
   {
      require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'issuetracker.php';

      JRequest::setVar('hidemainmenu', true);

      $user    = JFactory::getUser();
      $isNew   = ($this->item->id == 0);
      if (isset($this->item->checked_out)) {
         $checkedOut   = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
      } else {
         $checkedOut = false;
      }
      $canDo   = IssueTrackerHelper::getActions();

      $text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
      JToolBarHelper::title(   JText::_( 'COM_ISSUETRACKER' ).': <small>[ ' . $text.' ]</small>', 'mail' );

      // If not checked out, can save the item.
      if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create')))) {
         JToolBarHelper::apply('email.apply', 'JTOOLBAR_APPLY');
         JToolBarHelper::save('email.save', 'JTOOLBAR_SAVE');
      }
      if (!$checkedOut && ($canDo->get('core.create'))) {
         JToolBarHelper::custom('email.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
      }
      // If an existing item, can save to a copy.
      if (!$isNew && $canDo->get('core.create')) {
         JToolBarHelper::custom('email.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
      }
      if (empty($this->item->id)) {
         JToolBarHelper::cancel('email.cancel', 'JTOOLBAR_CANCEL');
      } else {
         JToolBarHelper::cancel('email.cancel', 'JTOOLBAR_CLOSE');
      }
   }
}