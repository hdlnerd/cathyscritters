<?php
/*
 *
 * @Version       $Id: view.html.php 688 2013-02-05 17:41:09Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-05 17:41:09 +0000 (Tue, 05 Feb 2013) $
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
class IssueTrackerViewItissues extends JViewLegacy
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

      if ( $this->item->id != 0 ) {
         $this->attachment    = $this->check_attachments();
      }

      $this->canDo   = IssueTrackerHelper::getActions($this->item->id);

      // Check for errors.
      if (count($errors = $this->get('Errors'))) {
         JError::raiseError(500, implode("\n", $errors));
         return false;
      }

      $this->addToolbar();
      parent::display($tpl);
   }

   /**
    * Check if any attachments and get details.
    * This code should be in the model. Move when convenient.
    */

   function check_attachments()
   {
      $issue_id = $this->item->alias;
      print("Issue No = $issue_id<p>");

      $db = JFactory::getDbo();
      $query = "SELECT count(*) FROM `#__it_attachment` WHERE issue_id = '".$issue_id."'";
      $db->setQuery($query);
      $cnt = $db->loadResult();

      if ( $cnt == 0 ) {
         return false;
      } else {
         $query = "SELECT * FROM `#__it_attachment` WHERE issue_id = '".$issue_id."'";
         $db->setQuery($query);
         $attachment = $db->loadObjectList();
         return $attachment;
      }
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

      // JToolBarHelper::title(JText::_('COM_ISSUETRACKER'), 'type.png');
      $text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
      JToolBarHelper::title(   JText::_( 'COM_ISSUETRACKER' ).': <small>[ ' . $text.' ]</small>', 'issues-add' );

      // If not checked out, can save the item.
      if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
      {
         JToolBarHelper::apply('itissues.apply', 'JTOOLBAR_APPLY');
         JToolBarHelper::save('itissues.save', 'JTOOLBAR_SAVE');
      }
      if (!$checkedOut && ($canDo->get('core.create'))){
         JToolBarHelper::custom('itissues.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
      }
      // If an existing item, can save to a copy.
      if (!$isNew && $canDo->get('core.create')) {
         JToolBarHelper::custom('itissues.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
      }
      if (empty($this->item->id)) {
         JToolBarHelper::cancel('itissues.cancel', 'JTOOLBAR_CANCEL');
      }
      else {
         JToolBarHelper::cancel('itissues.cancel', 'JTOOLBAR_CLOSE');
      }

   }
}
