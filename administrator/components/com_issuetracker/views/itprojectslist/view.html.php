<?php
/*
 *
 * @Version       $Id: view.html.php 197 2012-05-04 16:10:32Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.1.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-05-04 17:10:32 +0100 (Fri, 04 May 2012) $
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
class IssueTrackerViewItprojectslist extends JView
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
         $istrt = 0;
         if (count($this->items) == 1 ) {
            // Cludge for situation where we have performed a search and have only one element.
            $istrt = $this->items[0]->parent_id;
         }
         $text = ''; // text is tree name e.g. Category >> Subcategory
         $tree = array();
         $this->items = $this->processTree($this->items, $tree, $istrt, $text, -1);
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
         JToolBarHelper::preferences('com_issuetracker', '600','800');
      }

      JToolBarHelper::divider();
      JToolBarHelper::help( 'screen.issuetracker', true );

   }


   protected function processTree( $data, $tree, $id = 0, $text='', $currentId)
   {
      $countItemsInCat  = 0;// Ordering

      foreach ($data as $key) {
         $show_text =  $text . $key->project_name;
         static $iCT = 0;// All displayed items
         if ($key->parent_id == $id && $currentId != $id && $currentId != $key->id ) {

            $tree[$iCT]                = new JObject();

            // Ordering MUST be solved here
            if ($countItemsInCat > 0) {
               $tree[$iCT]->orderup          = 1;
            } else {
               $tree[$iCT]->orderup          = 0;
            }

            if ($countItemsInCat < ($key->countid - 1)) {
               $tree[$iCT]->orderdown        = 1;
            } else {
               $tree[$iCT]->orderdown        = 0;
            }

            $tree[$iCT]->id                  = $key->id;
            $tree[$iCT]->project_name        = $show_text;
            $tree[$iCT]->project_description = $key->project_description;
            $tree[$iCT]->parent_id           = $key->parent_id;
            $tree[$iCT]->state               = $key->state;
            $tree[$iCT]->ordering            = $key->ordering;
            $tree[$iCT]->start_date          = $key->start_date;
            $tree[$iCT]->target_end_date     = $key->target_end_date;
            $tree[$iCT]->actual_end_date     = $key->actual_end_date;
            $tree[$iCT]->checked_out         = $key->checked_out;
            $tree[$iCT]->checked_out_time    = $key->checked_out_time;
            $tree[$iCT]->parent_project_name = $key->parent_project_name;
            $tree[$iCT]->created_on          = $key->created_on;
            $tree[$iCT]->created_by          = $key->created_by;
            $tree[$iCT]->modified_on         = $key->modified_on;
            $tree[$iCT]->modified_by         = $key->modified_by;

            $iCT++;

            $tree = $this->processTree($data, $tree, $key->id, $show_text . " - ", $currentId );
            $countItemsInCat++;
         }
      }
      return($tree);
   }
}