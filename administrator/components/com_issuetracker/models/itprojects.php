<?php
/**
 * @Version       $Id: itprojects.php 689 2013-02-06 17:38:45Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-06 17:38:45 +0000 (Wed, 06 Feb 2013) $
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
      // Initialise variables;
      $dispatcher = JDispatcher::getInstance();
      $table = $this->getTable();
      $pk = (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');
      $isNew = true;

      // Load the row if saving an existing project.
      if ($pk > 0) {
         $table->load($pk);
         $isNew = false;
      }

      // Set the new parent id if parent id not matched OR while New/Save as Copy .
      if ($table->parent_id != $data['parent_id'] || $data['id'] == 0) {
         $table->setLocation($data['parent_id'], 'last-child');
      }

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
         list($pname, $alias) = $this->_generateNewProjectName($data['parent_id'], $data['alias'], $data['title']);
         $data['title'] = $pname;
         $data['alias'] = $alias;
      }

      // Set parent id to the Root if it is not explicitly set.
      $rootId = $table->getRootId();
      if ($rootId === false) {
         $rootId = $table->addRoot();
      }
      if ( $data['parent_id'] == 0 ) $data['parent_id'] = $rootId;

      // Bind the data.
      if (!$table->bind($data)) {
         $this->setError($table->getError());
         return false;
      }

      // Bind the rules.
      if (isset($data['rules'])) {
         $rules = new JAccessRules($data['rules']);
         $table->setRules($rules);
      }

      // Check the data.
      if (!$table->check()) {
         $this->setError($table->getError());
         return false;
      }

      // Trigger the onContentBeforeSave event.
      $result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, &$table, $isNew));
      if (in_array(false, $result, true)) {
         $this->setError($table->getError());
         return false;
      }

      if (!$table->store()) {
         return false;
      }

      // Trigger the onContentAfterSave event.
      $dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));

      // Rebuild the path for the project:
      if (!$table->rebuildPath($table->id)) {
         $this->setError($table->getError());
         return false;
      }

      // Rebuild the paths of the project's children:
      if (!$table->rebuild($table->id, $table->lft, $table->level, $table->path)) {
         $this->setError($table->getError());
         return false;
      }

      $this->setState($this->getName() . '.id', $table->id);

      // Clear the cache
      // $this->cleanCache();

      return true;
   }


   /**
    * Method to change the project name.
    *
    * @param string $title The title
    * @return the modified title
    */

   private function _generateNewProjectName($parent_id, $alias, $title)
   {
      // Alter the title
      // $title .= ' (2)';
      // return $title;

      // Alter the title & alias
      $table = $this->getTable();
      while ($table->load(array('alias' => $alias, 'parent_id' => $parent_id))) {
         $title = JString::increment($title);
         $alias = JString::increment($alias, 'dash');
      }

      return array($title, $alias);
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
            $query  = 'SELECT c.id, c.title, COUNT( s.parent_id ) AS numcat';
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
                  $err_cat[] = $row->title;
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

   /**
   * Method rebuild the entire nested set tree.
   *
   * @return  boolean  False on failure or error, true otherwise.
   *
   * @since   1.6
   */
   public function rebuild()
   {
      // Get an instance of the table object.
      $table = $this->getTable();

      if (!$table->rebuild()) {
         $this->setError($table->getError());
         return false;
      }

      // Clear the cache
     // $this->cleanCache();

      return true;
   }

   /**
    * Method to save the reordered nested set tree.
    * First we save the new order values in the lft values of the changed ids.
    * Then we invoke the table rebuild to implement the new ordering.
    *
    * @param   array    $idArray    An array of primary key ids.
    * @param   integer  $lft_array  The lft value
    *
    * @return  boolean  False on failure or error, True otherwise
    *
    * @since   1.6
    */
   public function saveorder($idArray = null, $lft_array = null)
   {
      // Get an instance of the table object.
      $table = $this->getTable();

      if (!$table->saveorder($idArray, $lft_array)) {
         $this->setError($table->getError());
         return false;
      }

      // Clear the cache
      //    $this->cleanCache();

      return true;
   }
}
