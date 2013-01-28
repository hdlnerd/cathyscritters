<?php
/*
 *
 * @Version       $Id: itprojects.php 389 2012-08-28 16:20:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.1.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-28 17:20:39 +0100 (Tue, 28 Aug 2012) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modeladmin');

/**
 * Issue Tracker Model
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerModelItprojects extends JModelAdmin
{
   /**
    * @var     string   The prefix to use with controller messages.
    * @since   1.6
    */
   protected $text_prefix = 'COM_ISSUETRACKER';


   /**
    * Returns a reference to the a Table object, always creating it.
    *
    * @param   type  The table type to instantiate
    * @param   string   A prefix for the table class name. Optional.
    * @param   array Configuration array for model. Optional.
    * @return  JTable   A database object
    * @since   1.6
    */
   public function getTable($type = 'Itprojects', $prefix = 'IssueTrackerTable', $config = array())
   {
      return JTable::getInstance($type, $prefix, $config);
   }

   /**
    * Method to get the record form.
    *
    * @param   array $data    An optional array of data for the form to interogate.
    * @param   boolean  $loadData   True if the form is to load its own data (default case), false if not.
    * @return  JForm A JForm object on success, false on failure
    * @since   1.6
    */
   public function getForm($data = array(), $loadData = true)
   {
      // Initialise variables.
      $app  = JFactory::getApplication();

      // Get the form.
      $form = $this->loadForm('com_issuetracker.itprojects', 'itprojects', array('control' => 'jform', 'load_data' => $loadData));
      if (empty($form)) {
         return false;
      }

      return $form;
   }

   /**
    * Method to get the data that should be injected in the form.
    *
    * @return  mixed The data for the form.
    * @since   1.6
    */
   protected function loadFormData()
   {
      // Check the session for previously entered form data.
      $data = JFactory::getApplication()->getUserState('com_issuetracker.edit.itprojects.data', array());

      if (empty($data)) {
         $data = $this->getItem();
      }

      return $data;
   }

   /**
    * Method to get a single record.
    *
    * @param   integer  The id of the primary key.
    *
    * @return  mixed Object on success, false on failure.
    * @since   1.6
    */
   public function getItem($pk = null)
   {
      if ($item = parent::getItem($pk)) {

         //Do any procesing on fields here if needed

      }

      return $item;
   }

   /**
    * Prepare and sanitise the table prior to saving.
    *
    * @since   1.6
    */
   protected function prepareTable(&$table)
   {
      jimport('joomla.filter.output');

      if (empty($table->id)) {

         // Set ordering to the last item if not set
         if (@$table->ordering === '') {
            $db = JFactory::getDbo();
            $db->setQuery('SELECT MAX(ordering) FROM #__it_projects');
            $max = $db->loadResult();
            $table->ordering = $max+1;
         }

      }
   }

  /**
    * Method to save the form data.
    *
    * @param   array The form data.
    *
    * @return  boolean  True on success.
    * @since   1.6
    */
   public function save($data)
   {
      // Set up access to default parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );

      // Get default settings
      $def_published = $this->_params->get('def_published', 0);

      // Ensure default published state is set:
      if (empty($data['state'])) {
         $data['state'] = $def_published;
      }

      $date = JFactory::getDate();
      // Set start date to today if not set.
      if (empty($data['start_date'])) {
         $data['start_date'] = "$date";
      }

      // Alter the title for save as copy
      if (JRequest::getVar('task') == 'save2copy') {
         $pname = $this->_generateNewProjectName($data['project_name']);
         $data['project_name'] = $pname;
      }

      if (parent::save($data)) {
         return true;
      }

      return false;
   }


    /**
      * Method to change the project name.
      *
      * @param string $title The title
      * @return the modified title
   */

   private function _generateNewProjectName($title)
   {
      // Alter the title
      $title .= ' (2)';
      return $title;
   }

    /**
    * Method to store a record
    *
    * @access  public
    * @return  boolean  True on success
    */
   public function store($data)
   {
      // Set up access to default parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );

      // Get default settings
      $def_published = $this->_params->get('def_published', 0);

      $row =& $this->getTable('itprojects','IssueTrackerTable');

      // Ensure default published state is set:
      if (empty($data['state'])) {
         $data['state'] = $def_published;
      }

      $data['id'] = JRequest::getVar('id', '', 'post', 'double');
      $date = JFactory::getDate();

      // Set start date to today if not set.
      if (empty($data['start_date'])) {
         $data['start_date'] = "$date";
      }

      // Bind the form fields to the table
      if (!$row->bind($data)) {
         $this->setError($this->_db->getErrorMsg());
         return false;
      }

      // Make sure the record is valid
      if (!$row->check()) {
         $this->setError($this->_db->getErrorMsg());
         return false;
      }

      // Store the web link table to the database
      if (!$row->store()) {
         $this->setError( $row->getErrorMsg() );
         return false;
      }
      return true;
   }

   /**
    * Method to delete one or more records.
    *
    * @param   array  &$pks  An array of record primary keys.
    *
    * @return  boolean  True if successful, false if an error occurs.
    *
    * @since   11.1
    */
   public function delete(&$pks)
   {
      $pks = (array) $pks;
      $row = $this->getTable('Itprojects','IssueTrackerTable');

      // Get parameters setting.
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );
      $app           = JFactory::getApplication();
      $delmode       = $this->_params->get('delete', 0);

      if ($delmode == 0 ) {
         // Delete mode disabled.
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DELETE_MODE_DISABLED_MSG'));
         return false;
      } else if ( $delmode == 1 || $delmode == 2 ) {
         // Treat modes 1 and 2 the same for projects and delete them.  No concept of re-assignment.
         if (count( $pks )) {
            $defproject = $this->_params->get('def_project', 0);

            // First check if we are trying to delete the default project.
            if ( in_array($defproject, $pks) ) {
                $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DEF_PROJECT_DELETE_ATTEMPT_MSG'));
                return false;
            }

            // Check if there are any subprojects
            $query  = 'SELECT c.id, c.project_name, COUNT( s.parent_id ) AS numcat';
            $query .= ' FROM #__it_projects AS c' ;
            $query .= ' LEFT JOIN #__it_projects AS s ON s.parent_id = c.id' ;
            $query .= ' WHERE c.id IN ( '.implode(',',$pks).' )' ;
            $query .= ' GROUP BY c.id';

            $this->_db->setQuery( $query );

            if (!($rows2 = $this->_db->loadObjectList())) {
               JError::raiseError( 500, $db->stderr('Load Data Problem') );
               return false;
            }

            // Build a new array without projects containing sub-projects (we do not delete projects with sub-projects)
            // Also check we are not deleting the default defined project.
            $err_cat = array();
            $cida    = array();
            foreach ($rows2 as $row) {
               if ($row->numcat == 0 || $row->id == $defproject ) {
                  $cida[] = (int) $row->id;
               } else {
                  $err_cat[] = $row->project_name;
               }
            }

            if (count( $cida )) {
               // Remove associated project issues
               $query = 'DELETE FROM `#__it_issues` WHERE related_project_id in (';
               foreach($cida as $cid) {
                  $query .= $cid . ',';
               }
               $query = substr($query, 0, -1) . ')';

               $this->_db->setQuery( $query );
               $this->_db->query();

               // Must also update the default project for the people.
               $query = 'UPDATE `#__it_people` SET assigned_project = '.$defproject.' WHERE assigned_project in (';
               foreach($cida as $cid) {
                  $query .= $cid . ',';
               }
               $query = substr($query, 0, -1) . ')';

               $this->_db->setQuery( $query );
               $this->_db->query();

               $row = $this->getTable('Itprojects','IssueTrackerTable');  // Reset
               // Now remove the projects themselves
               foreach($cida as $cid) {
                  if (!$row->delete( $cid )) {
                     $this->setError( $row->getError() );
                     return false;
                  }
               }
            }
         }

         // Were there any projects with sub-projects - which we didn't delete
         $msg = '';
         if (count( $err_cat )) {
            $cids_cat = implode( ", ", $err_cat );
            $msg .= JText::plural( 'COM_ISSUETRACKER_ERROR_DELETE_CONTAIN_SUB', $cids_cat );
            $app->enqueueMessage($msg);
         }

         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_PROJECT_ISSUES_DELETED_MSG'));
         return true;
      } else if ( $delmode > 2 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DELETE_MODE_UNKNOWN_MSG'),'error');
         return false;
      }
   }
}
