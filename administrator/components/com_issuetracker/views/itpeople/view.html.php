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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

/**
 * Issue Tracker view
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerViewItpeople extends JViewLegacy
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

      $text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );

      if ( $isNew ) {
         // Change field display settings
         $this->form->setFieldAttribute('user_id', 'type', 'hidden');
         $this->form->setFieldAttribute('person_name', 'required', 'true');
         $this->form->setFieldAttribute('person_name', 'readonly', 'false');
         $this->form->setFieldAttribute('person_name', 'disabled', 'false');
         $this->form->setFieldAttribute('username', 'required', 'true');
         $this->form->setFieldAttribute('username', 'readonly', 'false');
         $this->form->setFieldAttribute('username', 'disabled', 'false');
         $this->form->setFieldAttribute('person_email', 'required', 'true');
         $this->form->setFieldAttribute('person_email', 'readonly', 'false');
         $this->form->setFieldAttribute('person_email', 'disabled', 'false');
         $this->form->setFieldAttribute('issues_admin', 'default', '0');
         $this->form->setFieldAttribute('staff', 'default', '0');
         $this->form->setFieldAttribute('email_notifications', 'default', '0');
         $this->form->setFieldAttribute('published', 'default', '0');
         $this->form->setFieldAttribute('assigned_project', 'default', '1');
         $this->form->setFieldAttribute('person_role', 'default', '2');
      } else {
         // Edit person fields only if unregistered.
         if ($this->item->registered == 0 ) {
         $this->form->setFieldAttribute('user_id', 'type', 'hidden');
         $this->form->setFieldAttribute('person_name', 'required', 'true');
         $this->form->setFieldAttribute('person_name', 'readonly', 'false');
         $this->form->setFieldAttribute('person_name', 'disabled', 'false');
         $this->form->setFieldAttribute('username', 'required', 'true');
         $this->form->setFieldAttribute('username', 'readonly', 'false');
         $this->form->setFieldAttribute('username', 'disabled', 'false');
         $this->form->setFieldAttribute('person_email', 'required', 'true');
         $this->form->setFieldAttribute('person_email', 'readonly', 'false');
         $this->form->setFieldAttribute('person_email', 'disabled', 'false');
         }
      }

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);

      JToolBarHelper::title(   JText::_( 'COM_ISSUETRACKER' ).': <small>[ ' . $text.' ]</small>', 'users' );

      // If not checked out, can save the item.
      if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
      {
         JToolBarHelper::apply('itpeople.apply', 'JTOOLBAR_APPLY');
         JToolBarHelper::save('itpeople.save', 'JTOOLBAR_SAVE');
      }
/*
      if (!$checkedOut && ($canDo->get('core.create'))){
         JToolBarHelper::custom('itpeople.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
      }
      // If an existing item, can save to a copy.
      if (!$isNew && $canDo->get('core.create')) {
         JToolBarHelper::custom('itpeople.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
      }
*/
      if (empty($this->item->id)) {
         JToolBarHelper::cancel('itpeople.cancel', 'JTOOLBAR_CANCEL');
      }
      else {
         JToolBarHelper::cancel('itpeople.cancel', 'JTOOLBAR_CLOSE');
      }

   }
}