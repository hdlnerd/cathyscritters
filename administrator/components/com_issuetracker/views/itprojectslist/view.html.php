<?php
/**
 * @Version       $Id: view.html.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
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
 * Issue Tracker View
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerViewItprojectslist extends JViewLegacy
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

       // Preprocess the list of items to find ordering divisions.
      foreach ($this->items as &$item) {
         $this->ordering[$item->parent_id][] = $item->id;
      }

      // Levels filter.
      $options = array();
      $options[]  = JHtml::_('select.option', '1', JText::_('J1'));
      $options[]  = JHtml::_('select.option', '2', JText::_('J2'));
      $options[]  = JHtml::_('select.option', '3', JText::_('J3'));
      $options[]  = JHtml::_('select.option', '4', JText::_('J4'));
      $options[]  = JHtml::_('select.option', '5', JText::_('J5'));
      $options[]  = JHtml::_('select.option', '6', JText::_('J6'));
      $options[]  = JHtml::_('select.option', '7', JText::_('J7'));
      $options[]  = JHtml::_('select.option', '8', JText::_('J8'));
      $options[]  = JHtml::_('select.option', '9', JText::_('J9'));
      $options[]  = JHtml::_('select.option', '10', JText::_('J10'));

      $this->f_levels = $options;

      /*
       * We need to load all items because of creating tree
       * After creating tree we get info from pagination
       * and will set displaying of categories for current pagination
       * E.g. pagination is limitstart 5, limit 5 - so only categories from 5 to 10 will be displayed
       */
/*
      if (!empty($this->items)) {
         $istrt = 0;
         if (count($this->items) == 1 ) {
            // Cludge for situation where we have performed a search and have only one element.
            $istrt = $this->items[0]->parent_id;
         }
         $text = ''; // text is tree name e.g. Category >> Subcategory
         $tree = array();
         $this->items = $this->processTree($this->items, $tree, $istrt, $text, -1);
      }
*/
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

      JToolBarHelper::title(JText::_('COM_ISSUETRACKER_MANAGER_PROJECTS'), 'projects');

      //Check if the form exists before showing the add/edit buttons
      $formPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'itprojects';
      if (file_exists($formPath)) {
         if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('itprojects.add','JTOOLBAR_NEW');
         }
         if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('itprojects.edit','JTOOLBAR_EDIT');
         }
      }

      if ($canDo->get('core.edit.state')) {
         if (isset($this->items[0]->state)) {
            JToolBarHelper::divider();
            JToolBarHelper::custom('itprojectslist.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
            JToolBarHelper::custom('itprojectslist.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
         } else {
            //If this component does not use state then show a direct delete button as we can not trash
            JToolBarHelper::deleteList('', 'itprojectslist.delete','JTOOLBAR_DELETE');
         }

         if (isset($this->items[0]->state)) {
            JToolBarHelper::divider();
            JToolBarHelper::archiveList('itprojectslist.archive','JTOOLBAR_ARCHIVE');
         }
         if (isset($this->items[0]->checked_out)) {
            JToolBarHelper::custom('itprojectslist.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
         }
      }

      //Show trash and delete for components that uses the state field
      if (isset($this->items[0]->state)) {
         if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'itprojectslist.delete','JTOOLBAR_EMPTY_TRASH');
            JToolBarHelper::divider();
         } else if ($canDo->get('core.edit.state')) {
            JToolBarHelper::trash('itprojectslist.trash','JTOOLBAR_TRASH');
            JToolBarHelper::divider();
         }
      }

      if ($canDo->get('core.admin')) {
         JToolBarHelper::custom('itprojectslist.rebuild', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
         JToolBarHelper::divider();
      }

      if ($canDo->get('core.admin')) {
         JToolBarHelper::preferences('com_issuetracker', '600','800');
      }

      JToolBarHelper::divider();
      JToolBarHelper::help( 'screen.issuetracker', true );
   }
}