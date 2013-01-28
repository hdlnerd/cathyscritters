<?php
/*
 *
 * @Version       $Id: view.html.php 311 2012-08-13 10:33:18Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-13 11:33:18 +0100 (Mon, 13 Aug 2012) $
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
class IssueTrackerViewItpeoplelist extends JView
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

      JHtml::stylesheet(JPATH_COMPONENT_ADMINISTRATOR.DS.'css', array(), true, false, false);

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
      $canDo   = IssueTrackerHelper::getActions($state->get('filter.category_id'));

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);

      JToolBarHelper::title(JText::_('COM_ISSUETRACKER_MANAGER_PEOPLE'), 'users');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'itpeople';
        if (file_exists($formPath)) {
            if ($canDo->get('core.create')) {
             JToolBarHelper::addNew('itpeople.add','JTOOLBAR_NEW');
          }
          if ($canDo->get('core.edit')) {
             JToolBarHelper::editList('itpeople.edit','JTOOLBAR_EDIT');
          }
        }

      if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->published)) {
               JToolBarHelper::divider();
               JToolBarHelper::publishList('itpeoplelist.publish', 'JTOOLBAR_PUBLISH', true);
               JToolBarHelper::unpublishList('itpeoplelist.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            }

            JToolBarHelper::divider();
            JToolBarHelper::custom('itpeoplelist.administrator', 'admin.png', 'admin.png','COM_ISSUETRACKER_ADMIN', true);
            JToolBarHelper::custom('itpeoplelist.notadministrator', 'deadmin.png', 'deadmin.png', 'COM_ISSUETRACKER_NOT_ADMIN', true);
            JToolBarHelper::divider();
            JToolBarHelper::custom('itpeoplelist.staff', 'staff.png', 'staff.png','COM_ISSUETRACKER_ISSUES_STAFF', true);
            JToolBarHelper::custom('itpeoplelist.notstaff', 'notstaff.png', 'notstaff.png', 'COM_ISSUETRACKER_ISSUES_NOT_STAFF', true);
            JToolBarHelper::divider();
            JToolBarHelper::custom('itpeoplelist.notify', 'notify.png', 'notify.png','COM_ISSUETRACKER_NOTIFY', true);
            JToolBarHelper::custom('itpeoplelist.nonotify', 'denotify.png', 'denotify.png', 'COM_ISSUETRACKER_DENOTIFY', true);

            JToolBarHelper::divider();
            if (isset($this->items[0]->state)) {
               JToolBarHelper::custom('itpeoplelist.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
               JToolBarHelper::custom('itpeoplelist.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else {
               //If this component does not use state then show a direct delete button as we can not trash
               // JToolBarHelper::deleteList('', 'itpeoplelist.delete','JTOOLBAR_DELETE');
               JToolBarHelper::deleteList(JText::_('COM_ISSUETRACKER_PEOPLE_DELETE_WARNING'),'itpeoplelist.delete');
            }

            if (isset($this->items[0]->state)) {
             JToolBarHelper::divider();
             JToolBarHelper::archiveList('itpeoplelist.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
               JToolBarHelper::custom('itpeoplelist.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
      }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
          if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
             JToolBarHelper::deleteList('', 'itpeoplelist.delete','JTOOLBAR_EMPTY_TRASH');
             JToolBarHelper::divider();
          } else if ($canDo->get('core.edit.state')) {
             JToolBarHelper::trash('itpeoplelist.trash','JTOOLBAR_TRASH');
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