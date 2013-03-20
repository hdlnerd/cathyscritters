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

/**
 * Issuetracker View
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */
class IssuetrackerViewItprioritylist extends JViewLegacy
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

      JToolBarHelper::title(JText::_('COM_ISSUETRACKER_MANAGER_PRIORITIES'), 'priorities');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'itpriority';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
             JToolBarHelper::addNew('itpriority.add','JTOOLBAR_NEW');
          }

          if ($canDo->get('core.edit')) {
             JToolBarHelper::editList('itpriority.edit','JTOOLBAR_EDIT');
          }

        }

      if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
             JToolBarHelper::divider();
             JToolBarHelper::custom('itprioritylist.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
             JToolBarHelper::custom('itprioritylist.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'itprioritylist.delete','JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
             JToolBarHelper::divider();
             JToolBarHelper::archiveList('itprioritylist.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
               JToolBarHelper::custom('itprioritylist.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
      }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
          if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
             JToolBarHelper::deleteList('', 'itprioritylist.delete','JTOOLBAR_EMPTY_TRASH');
             JToolBarHelper::divider();
          } else if ($canDo->get('core.edit.state')) {
             JToolBarHelper::trash('itprioritylist.trash','JTOOLBAR_TRASH');
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