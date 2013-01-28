<?php
/*
 *
 * @Version       $Id: view.html.php 322 2012-08-20 13:14:58Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-20 14:14:58 +0100 (Mon, 20 Aug 2012) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * Issue Tracker View
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerViewItissueslist extends JView
{
   protected $items;
   protected $pagination;
   protected $state;

   /**
    * Display the view
    */
   public function display($tpl = null)
   {
      $this->state      = $this->get('State');
      $this->items      = $this->get('Items');
      $this->pagination = $this->get('Pagination');

      // Check for errors.
      if (count($errors = $this->get('Errors'))) {
         JError::raiseError(500, implode("\n", $errors));
         return false;
      }

      /*
       * We need to load all items because of creating tree
       * After creating tree we get info from pagination
       * and will set displaying of categories for current pagination
       * E.g. pagination is limitstart 5, limit 5 - so only categories from 5 to 10 will be displayed
       */

      if (!empty($this->items)) {
//         $istrt = 0;
//         if (count($this->items) == 1 ) {
//            // Cludge for situation where we have performed a search and have only one element.
//            $istrt = $this->items[0]->parent_id;
//         }
         $this->items = IssueTrackerHelper::updateprojectname($this->items);
      }

      $this->addToolbar();
      parent::display($tpl);
   }

   /**
    * Add the page title and toolbar.
    *
    * @since   1.6
    */
   protected function addToolbar()
   {
      require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'issuetracker.php';

      $state   = $this->get('State');
      // $canDo   = IssueTrackerHelper::getActions($state->get('filter.category_id'));
      $canDo   = IssueTrackerHelper::getActions();

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);

      JToolBarHelper::title(JText::_('COM_ISSUETRACKER_MANAGER_ISSUES'), 'issues');

      //Check if the form exists before showing the add/edit buttons
      $formPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'itissues';
      if (file_exists($formPath)) {
         if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('itissues.add','JTOOLBAR_NEW');
         }
         if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('itissues.edit','JTOOLBAR_EDIT');
         }
      }

      if ($canDo->get('core.edit.state')) {
         if (isset($this->items[0]->state)) {
            JToolBarHelper::divider();
            JToolBarHelper::custom('itissueslist.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
            JToolBarHelper::custom('itissueslist.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
         } else {
            //If this component does not use state then show a direct delete button as we can not trash
            JToolBarHelper::deleteList('', 'itissueslist.delete','JTOOLBAR_DELETE');
         }

         if (isset($this->items[0]->state)) {
            JToolBarHelper::divider();
            JToolBarHelper::archiveList('itissueslist.archive','JTOOLBAR_ARCHIVE');
         }
         if (isset($this->items[0]->checked_out)) {
            JToolBarHelper::custom('itissueslist.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
         }
      }

      //Show trash and delete for components that uses the state field
      if (isset($this->items[0]->state)) {
         if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'itissueslist.delete','JTOOLBAR_EMPTY_TRASH');
            JToolBarHelper::divider();
         } else if ($canDo->get('core.edit.state')) {
            JToolBarHelper::trash('itissueslist.trash','JTOOLBAR_TRASH');
            JToolBarHelper::divider();
         }
      }

      if ($canDo->get('core.admin')) {
         JToolBarHelper::preferences('com_issuetracker', '600','800');
      }

      JToolBarHelper::divider();
      JToolBarHelper::help( 'screen.issuetracker', true );

   }
}