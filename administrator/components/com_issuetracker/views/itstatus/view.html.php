<?php
/*
 *
 * @Version       $Id: view.html.php 286 2012-07-06 13:52:57Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-07-06 14:52:57 +0100 (Fri, 06 Jul 2012) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * Issuetracker view
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */
class IssuetrackerViewItstatus extends JView
{
   protected $state;
   protected $item;
   protected $form;

   /**
    * Display the view
    */
   public function display($tpl = null)
   {
      $this->state   = $this->get('State');
      $this->item    = $this->get('Item');
      $this->form    = $this->get('Form');

      // Check for errors.
      if (count($errors = $this->get('Errors'))) {
         JError::raiseError(500, implode("\n", $errors));
         return false;
      }

      $this->addToolbar();
      parent::display($tpl);
   }

   /**
    * Add the page title and toolbar.
    */
   protected function addToolbar()
   {
      require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'issuetracker.php';

      JRequest::setVar('hidemainmenu', true);

      $user    = JFactory::getUser();
      $isNew      = ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
          $checkedOut   = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
      $canDo      = IssueTrackerHelper::getActions();
      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);

      $text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
      JToolBarHelper::title(   JText::_( 'COM_ISSUETRACKER' ).': <small>[ ' . $text.' ]</small>', 'status' );

      // If not checked out, can save the item.
      if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
      {
         JToolBarHelper::apply('itstatus.apply', 'JTOOLBAR_APPLY');
         JToolBarHelper::save('itstatus.save', 'JTOOLBAR_SAVE');
      }
      if (!$checkedOut && ($canDo->get('core.create'))){
         JToolBarHelper::custom('itstatus.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
      }
      // If an existing item, can save to a copy.
      if (!$isNew && $canDo->get('core.create')) {
         JToolBarHelper::custom('itstatus.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
      }
      if (empty($this->item->id)) {
         JToolBarHelper::cancel('itstatus.cancel', 'JTOOLBAR_CANCEL');
      }
      else {
         JToolBarHelper::cancel('itstatus.cancel', 'JTOOLBAR_CLOSE');
      }

   }
}