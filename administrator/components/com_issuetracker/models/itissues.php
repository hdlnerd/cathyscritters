<?php
/*
 *
 * @Version       $Id: itissues.php 689 2013-02-06 17:38:45Z geoffc $
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

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

/**
 * Issue Tracker Model
 *
 * @package    Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerModelItissues extends JModelAdmin
{
   /**
    * @var     string   The prefix to use with controller messages.
    * @since   1.6
    */
   protected $text_prefix = 'COM_ISSUETRACKER';

   /**
    * Method override to check if you can edit an existing record.
    *
    * @param   array $data An array of input data.
    * @param   string   $key  The name of the key for the primary key.
    *
    * @return  boolean
    * @since   2.5
    */
   protected function allowEdit($data = array(), $key = 'id')
   {
      // Check specific edit permission then general edit permission.
      return JFactory::getUser()->authorise('core.edit', 'com_issuetracker.itissue.'.
                                            ((int) isset($data[$key]) ? $data[$key] : 0))
             or parent::allowEdit($data, $key);
   }

   /**
    * Returns a reference to the a Table object, always creating it.
    *
    * @param   type  The table type to instantiate
    * @param   string   A prefix for the table class name. Optional.
    * @param   array Configuration array for model. Optional.
    * @return  JTable   A database object
    * @since   1.6
    */
   public function getTable($type = 'Itissues', $prefix = 'IssueTrackerTable', $config = array())
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
      $form = $this->loadForm('com_issuetracker.itissues', 'itissues', array('control' => 'jform', 'load_data' => $loadData));
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
      $data = JFactory::getApplication()->getUserState('com_issuetracker.edit.itissues.data', array());

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
            $db->setQuery('SELECT MAX(ordering) FROM #__it_issues');
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
      $fchar         = $this->_params->get('initial_admin', 'A');
      $def_assignee  = $this->_params->get('def_assignee', 0);

      if ( $data['id'] == 0) {
         $new = 1;
      } else {
         $new = 0;
         $data['alias'] = JRequest::getVar('alias');
      }

      // Check defaults all set correctly.
      $this->_setdefaults($data);

      // Ensure published state is not set if private issue.
      if ( $data['public'] == 1 && $data['state'] == 1 ) {
         $data['state'] = 0;
      }

      // If assigned_to field is empty set it to default assignee if it is valid, NULL otherwise.
      if (empty($data['assigned_to_person_id']) || $data['assigned_to_person_id'] == 0) {
         // Check default assignee
         if ( $def_assignee ) {
            $data['assigned_to_person_id'] = $def_assignee;
         } else {
            $data['assigned_to_person_id'] = NULL;
         }
      }

      // Alter the title for save as copy
      if (JRequest::getVar('task') == 'save2copy') {
         $issue_summary = $this->_generateNewSummary($data['issue_summary']);
         $data['issue_summary'] = $issue_summary;
         $len = 10;
         $data['alias'] = $this->_generateNewAlias($len, $fchar);
      }

      $cur_issue_no = $data['alias'];

      if (parent::save($data)) {
         IssueTrackerHelper::prepare_messages( $data, '0', $new);
         return true;
      }

      return false;
   }

   /*  Method to set up the record defaults.
    *
    * @param string $title
    * @return the modified title
    */

   private function _setdefaults( & $data )
   {
      // Set up access to default parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );

      // Get default settings
      $def_published = $this->_params->get('def_published', 0);
      $def_assignee  = $this->_params->get('def_assignee', 1);
      $def_project   = $this->_params->get('def_project', 1);
      $def_type      = $this->_params->get('def_type', 1);
      $def_priority  = $this->_params->get('def_priority', 2);  // Low
      $def_status    = $this->_params->get('def_status', 4);   // Open
      $notify        = $this->_params->get('email_notify', 0);
      $fchar         = $this->_params->get('initial_admin', 'A');
      $open_status   = $this->_params->get('open_status', '4');
      $closed_status = $this->_params->get('closed_status', '1');

      // Check default assignee.  Set to null if not a staff member.
      if ( ! IssueTrackerHelper::check_assignee($def_assignee) )
         $def_assignee = NULL;

      // Set up our audit fields.   Created audit fields are set up in the it_issues view.
      $user = JFactory::getUser();
      $date = JFactory::getDate();

      // Determine whether insert or an update
      if ($data['id'] ==  0 ) {
         $len = 10;
         $data['alias'] = $this->_generateNewAlias($len, $fchar);
      }

      // Audit fields are set in the table definition store procedure.
      // Ensure FK relationships are set as a minimum: assigned_to, identified_by and related_project_id
      // Ensure default published state is set:
      if (empty($data['state'])) { $data['state'] = $def_published; }
      if (empty($data['issue_type'])) { $data['issue_type'] = $def_type; }

      // If status is closed and actual resolution date is not set, then set it.
      if ($data['status'] == $closed_status ) {
         // Check time elements on date fields
         $this->checktime($data['actual_resolution_date']);
         if ( empty($data['actual_resolution_date']) )  $data['actual_resolution_date'] = "$date";
      } else {
         // If status is not closed set actual_resolution_date to null
         $data['actual_resolution_date'] = "";
      }

      // If identified date is empty set it to today.
      if (empty($data['identified_date'])) { $data['identified_date'] = "$date"; }
      // If identified by field is empty them set it to the current user.  Need to get the id field from the it_people table for the current $user->id.
      if (empty($data['identified_by_person_id']) || $data['identified_by_person_id'] == 1) {
         $data['identified_by_person_id'] = IssueTrackerHelper::get_itpeople_id($user->id);
      }

      // If assigned_to field is empty set it to default assignee if valid, NULL otherwise.
      if ( empty($data['assigned_to_person_id']) || $data['assigned_to_person_id'] == 0) {
         // Check default assignee.  Should be greater than zero.
         if ( $def_assignee ) {
            $data['assigned_to_person_id'] = $def_assignee;
         } else {
            $data['assigned_to_person_id'] = NULL;
         }
      }

      // If related project id is null:
      if (empty($data['related_project_id'])) {
         if (empty($data['assigned_to_person_id'])) {
            $proj_id = $this->getDefProject($user->id);
         } else {
            $proj_id = $this->getDefProject($data['assigned_to_person_id']);
         }
         if ( $proj_id ) {
            $data['related_project_id'] = $def_project;
         } else {
            $data['related_project_id'] = $proj_id;
         }
      }

      // If target_resolution_date is empty or greater than that for the associated project
      // set the target to be the project_target_date.
      // However if current date is greater than project target date then we leave it alone. Assumption is that it is a defect.
      if (!empty($data['related_project_id'])) {
         // First get the project target_end_date.
         $tdate      = $this->getProjectTargetDate($data['related_project_id']);
         if (!empty($tdate)) {
            $tdatetime  = strtotime($tdate);
            $cdate      = JFactory::getDate();
            if ( strtotime($cdate) < $tdatetime) {
               if ( empty($data['target_resolution_date']) || ( strtotime($data['target_resolution_date']) > $tdatetime) ) {
                  $data['target_resolution_date'] = $tdate;
               }
            }
         }
      }

      // If empty status or Undefined set it to the defined default.
      if (empty($data['status']) ) { $data['status'] = $def_status; }
      // If priority not set set it to Low
      if (empty($data['priority']) ) { $data['priority'] = $def_priority; }

      // Check time elements on identified date fields
      if ( $data['status'] == $open_status)
         $this->checktime($data['identified_date']);

      return;
   }

   /*
    * Method to cludge the time element on a date where the time element is missing.
    * typically this is the situation where the 'calendar JForm driopo down has been used.
    *
    * Note that often the hour has actually been set with an offset for the time zone applied
    * so it is only the minutes and seconds beinfg zero that we can check.
    * There is a small chance that the time was exactly on the hour but that is hopefully rare.
    *
    */
   private function checktime( & $idate)
   {
      if ( empty( $idate ) ) return;

      $cdate = JFactory::getDate();

      if ( substr($idate, 0, 5) != '00/00' ) {
         if ( substr($idate, 14, 5) == '00:00') {
            $string = $cdate->toFormat('%H:%M:%S');
            $idate = substr($idate,0,11).$string;
         }
      }
      return;
   }

   /**
    * Method to change the issue summary.
    *
    * @param string $title The title
    * @return the modified title
   */

   private function _generateNewSummary($title)
   {
      // Alter the title
      $title .= ' (2)';
      return $title;
   }

/*
   private function _generateNewSummary($title)
   {
      // Alter the title
      $table = $this->getTable();
      while ($table->load(array('issue_summary' => $title))) {
         $m = null;
         if (preg_match('#\((\d+)\)$#', $title, $m)) {
            $title = preg_replace('#\(\d+\)$#', '('.($m[1] + 1).')', $title);
         } else {
            $title .= ' (2)';
         }
      }
      return $title;
   }
*/

   /**
    * Method to test whether a record can be deleted.
    *
    * @param   object   $record  A record object.
    *
    * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
    * @since   1.6
    */
   protected function canDelete($record)
   {
      if (!empty($record->id)) {
         if ($record->state != -2) {
            return ;
         }
         $user = JFactory::getUser();
         return $user->authorise('core.delete', 'com_issuetracker.issues.'.(int) $record->id);
      }
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
      // $cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
      $pks = (array) $pks;

      $row = $this->getTable();

      // Set reference to parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );
      $app = JFactory::getApplication();
      $delmode = $this->_params->get('delete', 0);

      if ($delmode == 0 ) {
         // Delete mode disabled.  Should give a message as well.
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DELETE_MODE_DISABLED_MSG'));
         return false;
      } else if ( $delmode == 1 || $delmode == 2 ) {
         // Iterate the items to delete each one.
         foreach ($pks as $i => $pk)
         {
            // Remove attachments if any.
            $this->delete_attachments($pk);

            if (!$row->delete( $pk )) {
               $this->setError( $row->getError() );
               return false;
            }
         }
         return true;
      } else if ( $delmode > 2 ) {
         // Unknown mode.  Mode 2 is only applicable for user deletion..
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DELETE_MODE_UNKNOWN_MSG'),'error');
         return false;
      }
   }

   /**
     * Method to generate a new alias (Issue number)
     *
     * @param string $len - The length of the desired alias
     * @return the new alias
   */
   private function _generateNewAlias($len = 10, $fchar = 'A')
   {
      // Possible seeds
      $seeds = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';

      list($usec, $sec) = explode(' ', microtime());
      $seed = (float) $sec + ((float) $usec * 100000);
      mt_srand($seed);

      // Start all issues with the requested letter
      $str = $fchar;
      $seeds_count = strlen($seeds);
      $length = $len - 1;
      for ($i = 0; $length > $i; $i++)
      {
         $str .= $seeds{mt_rand(0, $seeds_count - 1)};
      }
      return $str;
   }

   /**
    * Method to get User's default defined project
    * @return object with data
    */
   public function getDefProject($userid)
   {
      // Load the data
      $query = 'SELECT assigned_project FROM `#__it_people` WHERE `id` = '.$userid;
      $this->_db->setQuery( $query );
      $projid = $this->_db->loadResult();

      return $projid;
   }

   /**
    * Method to get Project target end date
    * @return object with data
    */
   public function getProjectTargetDate($projectid)
   {
      // Load the data
      $query = 'SELECT target_end_date FROM `#__it_projects` WHERE `id` = '.$projectid;
      $this->_db->setQuery( $query );
      $penddate = $this->_db->loadResult();

      return $penddate;
   }

   /**
    * Method to remove any attachments associated with the issue.
    */
   private function delete_attachments($issue)
   {
      $query  = "SELECT count(*) FROM `#__it_attachment` WHERE issue_id = ";
      $query .= "(SELECT alias FROM `#__it_issues` WHERE id = '".$issue."')";
      $this->_db->setQuery( $query );
      $delcnt = $this->_db->loadResult();

      if ( $delcnt > 0 ) {
         $query  = "DELETE FROM `#__it_attachment` WHERE issue_id = ";
         $query .= "(SELECT alias FROM `#__it_issues` WHERE id = '".$issue."')";
         $this->_db->setQuery( $query );
         $delcnt = $this->_db->loadResult();
      }
   }

}