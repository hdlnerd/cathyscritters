<?php
/*
 *
 * @Version       $Id: itissues.php 731 2013-02-26 14:59:01Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-26 14:59:01 +0000 (Tue, 26 Feb 2013) $
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelitem');
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

if (! class_exists('IssueTrackerHelperLog')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'log.php');
}

if (! class_exists('Akismet')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'classes'.DS.'Akismet.php');
}

/**
 * Issue Tracker Model
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssuetrackerModelItissues extends JModelItem
{
   /**
    * Model context string.
    *
    * @var     string
    */
   protected $_context = 'com_issuetracker.itissues';

   /**
    * Itissues data array for tmp store
    *
    * @var array
    */
   private $_data;

   /**
    * Method to auto-populate the model state.
    *
    * Note. Calling getState in this method will result in recursion.
    *
    * @return  void
    * @since   1.6
    */

    /*
     * Constructor
     *
     */
   function __construct()
   {
       parent::__construct();
   }

   protected function populateState()
   {
      $app = JFactory::getApplication('site');

      // Load state from the request.
      $pk = JRequest::getInt('id');
      $this->setState('itissues.id', $pk);

      $offset = JRequest::getUInt('limitstart');
      $this->setState('list.offset', $offset);

      // Get value if pid was specified.
      $pid = JRequest::getCmd('project_value');
      $this->setState('project_value', $pid);

      // Load the parameters.
      $params = $app->getParams();
      $this->setState('params', $params);
   }

   /**
    * Returns the query
    * @return string The query to be used to retrieve the rows from the database
    */
   private function _buildQuery($id)
   {
      // Create a new query object.
      $db      = $this->getDbo();
      $query   = $db->getQuery(true);
      $query->select(
         $this->getState(
         'list.select',
         't1.*'
         )
      );

      $query->from('#__it_issues AS t1');

      // Join over the it_projects table.
      $query->select('t2.title AS project_name, t2.id AS project_id');
      $query->join('LEFT', '#__it_projects AS t2 ON t2.id = t1.related_project_id');

      // Join over the it_people table.
      $query->select('t3.person_name AS assigned_person_name');
      $query->join('LEFT', '#__it_people AS t3 ON t3.id = t1.assigned_to_person_id');

      // Join over the it_people table.
      $query->select('t4.person_name AS identified_person_name');
      $query->join('LEFT', '#__it_people AS t4 ON t4.id = t1.identified_by_person_id');

      // Join over the it_status table.
      $query->select('t5.status_name AS status_name');
      $query->join('LEFT', '#__it_status AS t5 ON t5.id = t1.status');

      // Join over the it_priority table.
      $query->select('t6.priority_name AS priority_name');
      $query->join('LEFT', '#__it_priority AS t6 ON t6.id = t1.priority');

      // Join over the it_priority table.
      $query->select('t7.type_name AS type_name');
      $query->join('LEFT', '#__it_types AS t7 ON t7.id = t1.issue_type');

      $query = $query . $this->_buildQueryWhere($id);

      return $query;
   }


   private function _buildQueryWhere($id)
   {
      $app = JFactory::getApplication();

      // Cannot use use published if we wish guest users to access their own raised issues.
      // $where = " WHERE (( t1.`state`=1) AND ( t1.`id` = {$id} )) ";
      $where = " WHERE ( t1.`id` = {$id} ) ";

      return $where;
   }

   /*
    * Retrieves the data
    * @return array Array of objects containing the data from the database
    */
   public function getItem ($pk = null)
   {
      // Initialise variables.
      $pk = (!empty($pk)) ? $pk : (int) $this->getState('itissues.id');

      if ($this->_item === null) {
        $this->_item = array();
      }

      if (!isset($this->_item[$pk])) {
         try {
            $db      = $this->getDbo();
            $query   = $db->getQuery(true);
            $query   = $this->_buildQuery($pk);
            $db->setQuery($query);

            // $this->_item = $db->loadObject();
            $data = $db->loadObject();

            if ($error = $db->getErrorMsg()) {
               throw new Exception($error);
            }

            if (empty($data)) {
               return JError::raiseError(404,JText::_('COM_ISSUETRACKER_ISSUE_NOT_FOUND'));
            }

            IssueTrackerHelper::updatepname($data);

            // Convert parameter fields to objects.
            $registry = new JRegistry;
 //           $registry->loadString($data->attribs);

            $data->params = clone $this->getState('params');
            $data->params->merge($registry);

            // Compute selected asset permissions.
            $user = JFactory::getUser();

            // Technically guest could edit an issue, but lets not check that to improve performance a little.
            if (!$user->get('guest')) {
               $userId  = $user->get('id');
               $asset   = 'com_issuetracker.itissues.'.$data->id;

               // Check general edit permission first.
               if ($user->authorise('core.edit', $asset)) {
                  $data->params->set('access-edit', true);
               }
               // Now check if edit.own is available.
               elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
                  // Check for a valid user and that they are the owner.
                  // Note that the issue created by is a string not an id.
                  // if ($userId == $data->created_by) {
                  if ($user->username == $data->created_by ) {
                     $data->params->set('access-edit', true);
                  }
                  $person_id = IssueTrackerHelper::get_itpeople_id($user->id);
                  if ($person_id == $data->identified_by_person_id ) {
                     $data->params->set('access-edit', true);
                  }
               // Now add check if issue admin
               elseif ( IssueTrackerHelper::isIssueAdmin($userId) ) {
                  $data->params->set('access-edit', true);
                  }
               }
            }

            // Compute view access permissions.
            if ($access = $this->getState('filter.access')) {
               // If the access filter has been set, we already know this user can view.
               $data->params->set('access-view', true);
            }
            else {
               // If no access filter is set, the layout takes some responsibility for display of limited information.
               $user = JFactory::getUser();
               $groups = $user->getAuthorisedViewLevels();

//              if ($data->catid == 0 || $this->_data->category_access === null) {
//                 $data->params->set('access-view', in_array($data->access, $groups));
//              } else {
//                  $data->params->set('access-view', in_array($data->access, $groups) && in_array($data->category_access, $groups));
//              }
            }
            $this->_item[$pk] = $data;
         }

         catch (JException $e)
         {
            if ($e->getCode() == 404) {
               // Need to go thru the error handler to allow Redirect to work.
               JError::raiseError(404, $e->getMessage());
            }
            else {
               $this->setError($e);
               $this->_item[$pk] = false;
            }
         }
      }

      return $this->_item[$pk];
   }

   // Variation on helper file to expand out project name for an issue.
   function updatepname( $row )
   {
      // This updates a single array entry
      $db = JFactory::getDBO();
      // Now need to merge in to get the full project name.

      $query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid'
         . ' FROM #__it_projects AS a';
      $db->setQuery( $query );
      $rows2 = $db->loadObjectList();

      $catId   = -1;
      $tree    = array();
      $text    = '';
      $tree    = IssueTrackerHelper::ProjectTreeOption($rows2, $tree, 0, $text, $catId);

      foreach ($tree as $key2) {
         if ($row->related_project_id == $key2->value) {
            $row->title = $key2->text;
            break;    // Exit inner foreach since we have found out match.
         }
      }
      return $row;
   }


  /**
     * Method to generate a new alias (Issue number)
     *
     * @param string $len - The length of the desired alias
     * @return the new alias
   */
   private function _generateNewAlias($len = 10, $fchar = 'Z')
   {
      // Possible seeds
      $seeds = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';

      list($usec, $sec) = explode(' ', microtime());
      $seed = (float) $sec + ((float) $usec * 100000);
      mt_srand($seed);

      // Start all front end issues with the letter Z
      $str = $fchar;
      $seeds_count = strlen($seeds);
      $length = $len - 1;
      for ($i = 0; $length > $i; $i++)
      {
         $str .= $seeds{mt_rand(0, $seeds_count - 1)};
      }
      return $str;
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
      $fchar         = $this->_params->get('initial_site', 'Z');
      $open_status   = $this->_params->get('open_status', '4');
      $closed_status = $this->_params->get('closed_status', '1');
      $def_identby   = $this->_params->get('def_identifiedby','0');

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
      if (empty($data['state']))       { $data['state'] = $def_published; }
      if (empty($data['issue_type']))  { $data['issue_type'] = $def_type; }

      // If status is closed and actual resolution date is not set, then set it.
      if ($data['status'] == $closed_status ) {
         // Check time elements on important date fields
         $this->checktime($data['actual_resolution_date']);
         if ( empty($data['actual_resolution_date']) ) $data['actual_resolution_date'] = "$date";
      } else {
         // If status is not closed set actual_resoltion_date to null
         $data['actual_resolution_date'] = "";
      }


      // If identified date is empty set it to today.
      if (empty($data['identified_date'])) { $data['identified_date'] = "$date"; }

      // If identified by field is empty set it to current user.  At this stage we do not know the guest user so set to default.
      if (empty($data['identified_by_person_id']) || $data['identified_by_person_id'] == 1) {
         if ( $user->guest ) {
            $data['identified_by_person_id'] = $def_identby;
         } else {
          if (! IssueTrackerHelper::isIssueAdmin($user->id) )
            $data['identified_by_person_id'] = IssueTrackerHelper::get_itpeople_id($user->id);
         }
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

      // If related project id is null:
      if (empty($data['related_project_id'])) {
         if (empty($data['assigned_to_person_id'])) {
            $proj_id = $this->getDefProject($user->id);
         } else {
            $proj_id = $this->getDefProject($data['assigned_to_person_id']);
         }
         if (empty($proj_id)) {
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
      if (empty($data['status']) )     { $data['status'] = $def_status; }
      // If priority not set set it to Low
      if (empty($data['priority']) )   { $data['priority'] = $def_priority; }

      // Check time elements on important date fields
      if ( $data['status'] == $open_status)
         $this->checktime($data['identified_date']);

      return;
   }

   /*
    * Method to cludge the time element on a date where the time element is missing.
    * typically this is the situation where the 'calendar JForm drop down has been used.
    *
    * Note that often the hour has actually been set with an offset for the time zone applied
    * so it is only the minutes and seconds beinfg zero that we can check.
    * There is a small chance that the time was exactly on the hour but that is hopefully rare.
    *
    */
   private function checktime( & $idate)
   {
      $cdate = JFactory::getDate();

      if (empty($idate) ) return;

      if ( substr($idate, 0, 5) != '00/00' ) {
         if ( substr($idate, 14, 5) == '00:00') {
            $string = $cdate->toFormat('%H:%M:%S');
            $idate = substr($idate,0,11).$string;
         }
      }
      return;
   }

   /**
    * Method to store a record
    *
    * @access  public
    * @return  boolean  True on success
    */
   public function store()
   {
      $app = JFactory::getApplication();
      // Get the input or changed data
      $input   = JRequest::get( 'post' );
      $data    = $input['jform'];        // We mainly want jform fields.

     // Get user details
     $user = JFactory::getUser();

      // Run spam checker on the description.  Mainly a check for guest users.
      $isSpam  = intval($this->_isSpam());
      if ($isSpam) return false;

      // Get parameters for new user creation.
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );
      $def_notify    = $this->_params->get('def_notify', 0);
      $logging       = $this->_params->get('enableloggings', '0');

      // Find out if we are an issue administrator.
      $isadmin = 0;
      if ( ! $user->guest ) {
         $isadmin = IssueTrackerHelper::isIssueAdmin($user->id);
      } else {
         $isadmin = 0;
      }

      // Run check against Akismet if configured unless we are an issue administrator.
      if ( ! $isadmin ) {
         $use_akismet   = $this->_params->get('akismet_api_key','');
         if ( ! empty($use_akismet) ) {
            if ( $this->_check_akismet($input) ) {
               $app->enqueueMessage( JText::_('COM_ISSUETRACKER_AKISMET_DETECTED_SPAM'), 'error' );
               return false;
            }
         }
      }

      // Special case where issue details not display on form.
      // Get the Menu parameters to determine which projects have been selected.
      // Unless we are a Issue Administrator since we may be editing the issue.
      $minput = JFactory::getApplication()->input;
      $menuitemid = $minput->getInt( 'Itemid' ); // this returns the menu id number so we can reference parameters
      $menu = JSite::getMenu();
      if ($menuitemid) {
         $menuparams = $menu->getParams( $menuitemid );
         $projects = $menuparams->get('projects');
      }

      if ( $menuparams->get('show_details_section',0) == 0 ) {
         if ( count($projects) == 1 && $projects[0] != 0 ) {
            $data['related_project_id'] = $projects[0];
         } else {
            $data['related_project_id'] = $this->_params->get('def_project', 0);
         }
      }

      $pid = $this->getState('project_value', '');
      if ( !empty($pid) ) {
         $data['related_project_id'] = $pid;
      }

      // Ensure we capture id field which is outside the jform sub array.
      // Or the values that were not changable in the editor.
      $data['id']       = JRequest::getVar('id', '', 'post', 'double');

      if ( ! $data['id'] == 0 ) {
         $t2 = $data['id'];
         if (empty($db))
            $db = JFactory::getDBO();
         // Get original record.
         $query  = "SELECT issue_summary, issue_description, status, priority, identified_by_person_id from `#__it_issues` WHERE id = '".$t2."'";
         $db->setQuery( $query );
         $origrec = $db->loadRow();

         if ( ! isset($data['status']) )
            $data['status'] = $origrec[2];
         if (! isset($data['issue_summary']) )
            $data['issue_summary'] = $origrec[0];
         if ( ! isset($data['issue_description']) )
            $data['issue_description'] = $origrec[1];
         if ( ! isset($data['priority']) )
            $data['priority'] = $origrec[3];
         if ( ! isset($data['identified_by_person_id']) )
            $data['identified_by_person_id'] = $origrec[4];
      } else {
         if ( ! isset($data['status']) )     $data['status'] = '';
         if ( ! isset($data['priority']) )   $data['priority'] = '';
         if ( ! isset($data['identified_by_person_id']) )   $data['identified_by_person_id'] = '';
      }

      // If a private issue ensure published is not set.
      if ( array_key_exists ('public', $data) && $data['public'] == 0 ) {
         $data['state'] = 0;
      }

      // Ensure defaults are all set.
      $this->_setdefaults($data);

      // Get date.
      $date = JFactory::getDate();

      // Populate the progress field with user details if a guest.   A guest cannot edit existing issues.
      if ($user->guest) {
         // Get details for email.
         $Name = $data['user_details']['name'];
         $Uname = NULL;
         $Email = $data['user_details']['email'];
         // $notify = JRequest::getVar('notify', '', 'post', 'double');
         $gnotify = $data['notify'];

         // Get parameters for new user creation.
         // $this->_params = JComponentHelper::getParams( 'com_issuetracker' );
         $cnewperson    = $this->_params->get('create_new_person','0');
         $autogenuname  = $this->_params->get('auto_generate_username','0');
         $def_identby   = $this->_params->get('def_identifiedby','0');
         $def_role = $this->_params->get('def_role', '2');

         if ( ! array_key_exists ('progress', $data) ) $data['progress'] = null;
         if ( $cnewperson == '0' ) {
            $data['progress'] .= 'Reported By: ' . $data['user_details']['name'] . "<br />";
            $data['progress'] .= 'Email: ' .  $data['user_details']['email'] . "<br />";
            $data['progress'] .= 'Notify: ' . JRequest::getVar('notify', '', 'post', 'double') . "<br />";
            $data['identified_by_person_id'] = $def_identby;
         } else {
            // If generate username use email as a base.
            if(empty($Uname) && $autogenuname) $Uname = ucwords(str_replace(array('.','_','-','@'),'_',substr($Email,0)));
            if ( $gnotify == 2) $gnotify = $def_notify;
            $identby = $this->create_new_person ( $Name, $Uname, $Email, $gnotify, $def_role);
            if ( $identby == '' || $identby == 0 ) {
               if ( $logging )
                  IssueTrackerHelperLog::dblog('Error saving user: '.$Name.' Email: '.$Email.' Username: '.$Uname);
               $identby = $this->_get_anon_user();
               // Add details to progress field since we could not create the user.
               if ( ! array_key_exists ('progress', $data) ) $data['progress'] = null;
               $data['progress'] .= 'Reported By: ' . $data['user_details']['name'] . "<br />";
               $data['progress'] .= 'Email: ' .  $data['user_details']['email'] . "<br />";
               $data['progress'] .= 'Notify: ' . JRequest::getVar('notify', '', 'post', 'double') . "<br />";
            }
            $data['identified_by_person_id'] = $identby;
         }
         $dumm = $input['jform']['user_details']['website'];
         if ( ! empty($dumm) ) {
            $data['progress'] .= 'Web Site: ' .  $dumm . "<br />";
         }
      } else {
         // Just in case!
         if ( empty($data['identified_by_person_id'] ) )
            $data['identified_by_person_id'] =  IssueTrackerHelper::get_itpeople_id($user->id);

         // If a registered user is editing then capture the additional information.
         if ( array_key_exists ('additional_info', $data) ) {
            $additional_data = $input['jform']['additional_info'];
            $additional_data = JFilterOutput::cleanText($additional_data);
            if ( ! empty($additional_data)) {
               // Add some additional details in here such as user updating and date/time of update.
               $data['issue_description'] .= '<br />' . $user->username. ' '.$date.': '.$additional_data;
            }
         }

         // Check if the notification request has changed.  Need to review this logic and make it more robust.
         // key will be blank if it is an issue update where field is not displayed.
         if ( array_key_exists ('notify', $data) && $data['notify'] != '' ) {
            $notify = $data['notify'];
            if ( $notify != 2 )
               $this->_upd_user_notify($user->id, $notify );
         }
      }

      // Determine whether insert or an update
      if ($data['id'] > 0 ) {
         $new = 0;
         $cur_issue_no = $data['alias'];
      } else {
         $new = 1;

         if ( ! array_key_exists ('progress', $data )) {
            $data['progress'] = '';
         }

         // Only check product details for a newly created issue.
         if (($this->_params->get('show_product_req', 0) == 1) && array_key_exists( 'product_details', $input['jform']) ) {
            $dumm = $input['jform']['product_details']['jversion'];
            if (! empty($dumm) )
               $data['progress'] .= 'Joomla Version: ' . $dumm . "<br />";

            $dumm = $input['jform']['product_details']['pversion'];
            if (! empty($dumm) )
               $data['progress'] .= 'Product Version: ' . $dumm . "<br />";
            $dumm = $input['jform']['product_details']['dbtype'];
            if (! empty($dumm) )
               $data['progress'] .= 'DB Type: ' . $dumm . "<br />";
            $dumm = $input['jform']['product_details']['dbversion'];
            if (! empty($dumm) )
               $data['progress'] .= 'DB Version: ' . $dumm . "<br />";
         }
      }
      $cur_issue_no = $data['alias'];

      JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'tables');
      $row           = & $this->getTable('itissues','IssueTrackerTable');

      // Bind the form fields to the table
      if (!$row->bind($data)) {
         $this->setError($this->_db->getErrorMsg());
         // Need to remove any saved attachments
         return false;
      }

      // Make sure the record is valid
      if (!$row->check()) {
         $this->setError($this->_db->getErrorMsg());
         // Need to remove any saved attachments
         return false;
      }

      // Store record in the database
      if (!$row->store()) {
         $this->setError( $row->getError() );
         return false;
      }

      // Ensure it is checked in.
      $pk = $data['id'];
      $this->checkin($pk);

      $rid = $data['id'];
      // Handle the attachments if there are any.
      $file = JRequest::getVar('attachedfile', '', 'files', 'array');
      $emptyFile = true;
      if ( !empty($file) ) {
         if ( !empty($file['name'])) {
            $emptyFile = false;
         }
      }

//      if ( !$emptyFile && $perms['attachment'] ) {
      if ( !$emptyFile ) {
         // Perform file load in model.
         require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'models'.DS.'attachment.php');
         $attmodel = new IssuetrackerModelAttachment();
         $attachment = $attmodel->getItem();

         $ndata = array();

         jimport('joomla.utilities.date');
         $jdate = new JDate();

         $ndata['created_on']  = $jdate->toSql();
//         $ndata['enabled']     = 1;
         $ndata['issue_id']    = $data['alias'];
         $ndata['uid']         = $user->id;
         $ndata['title']       = $data['issue_summary'];
         $ndata['state']       = 1;

         if ( $user->guest) {
            $ndata['created_by']  = $Name;
         } else {
            $ndata['created_by']  = $user->name;
         }

         $result = $attmodel->save($ndata);

         if ( !$result) {
            $app->enqueueMessage( $this->getError(), 'error' );
         } else {
            $app->enqueueMessage( JText::_('COM_ISSUETRACKER_MESSAGES_FILE_ATTACHMENT_SAVED'));
         }
      }

      IssueTrackerHelper::prepare_messages( $data, '1', $new);
      $app->enqueueMessage( JText::_('COM_ISSUETRACKER_MESSAGES_ISSUE_SAVED') . $cur_issue_no );

      return true;
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
    * Method to remove a row
    *
    * @param   pk       The id of the row to remove
    */
   public function delete($pk)
   {
      $row = $this->getTable();
      print("Delete row $pk<p>");
      $row->delete($pk);
   }

   /**
    * Method to checkin/unlock the issue
    *
    * @access   public
    * @return   boolean   True on success
    * @since   1.5
    */
   function checkin($id)
   {
      if ($id) {
         JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'tables');
         $itissues  = & $this->getTable('itissues','IssueTrackerTable');
         if(! $itissues->checkin($id)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
         }
      }
      return false;
   }

   private function _upd_user_notify($user_id, $value )
   {
      $app = JFactory::getApplication();
      if (empty($db)) { $db = JFactory::getDBO(); }
      $query = 'UPDATE `#__it_people` set email_notifications = '.$value.' WHERE id = '. $db->Quote($user_id);
      $db->setQuery($query);
      $ret = $db->query();
      if (!$ret) {
         $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
      }
   }


   private function create_new_person($Name, $Uname, $Email, $notify, $def_role)
   {
      if (empty($db)) { $db = JFactory::getDBO(); }
      // Check if we have this user already registered.
      $query  = "SELECT count(person_name) from `#__it_people` WHERE person_name = '".$Name."' AND person_email = '".$Email."'";
      $db->setQuery( $query );
      $cnt = $db->loadResult();

      if ( $cnt == 0 ) {
         $query  = "INSERT into `#__it_people` (person_name, username, person_email, email_notifications, registered, person_role, assigned_project)";
         $query .= "values('".$Name."','".$Uname."', '".$Email."', '".$notify."', '0', '".$def_role."','1')";
         $db->setQuery($query);
         $ret = $db->query();
         //  if (!$ret) {
         //     $app = JFactory::getApplication('site');
         //     $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
         //  }
      } else {
         $query  = "UPDATE `#__it_people` set email_notifications = ".$notify." WHERE person_name = '".$Name."' AND person_email = '".$Email."'";
         $db->setQuery($query);
         $ret = $db->query();
         //  if (!$ret) {
         //     $app = JFactory::getApplication('site');
         //     $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
         //  }
      }

      $query = "SELECT id from `#__it_people` WHERE person_name = '".$Name."' AND person_email = '".$Email."'";
      $db->setQuery( $query );
      $id = $db->loadResult();
      return $id;
   }

   private function _get_anon_user()
   {
      if (empty($db)) { $db = JFactory::getDBO(); }
      $query = "SELECT id from `#__it_people` WHERE username = 'anon'";
      $db->setQuery( $query );
      $id = $db->loadResult();
      return $id;
   }

   private function _getPersonid($userid)
   {
      if (empty($db)) { $db = JFactory::getDBO(); }
      // Check if we have this user already registered.
      $query = "SELECT id from `#__it_people` WHERE user_id = '".$userid."'";
      $db->setQuery( $query );
      $id = $db->loadResult();
      return $id;
   }

   /**
    * Method to perform internal check for configured spam
    *
    */
   private function _isSpam()
   {
      $this->_params = & JComponentHelper::getParams( 'com_issuetracker' );
      $user = JFactory::getUser();
      //filter out logged in users
      if (! $user->guest) { return 0; }

      //filters first
      $ipList = explode("\r\n",$this->_params->get('ip_list',''));
      $urlList = explode("\r\n",$this->_params->get('url_list',''));
      $emailList = explode("\r\n",$this->_params->get('email_list',''));

      if (in_array($_SERVER['REMOTE_ADDR'], $ipList)) { return 1; }

      if (JRequest::getString('website') && in_array(JRequest::getString('website'), $urlList)) { return 1; }
      if (JRequest::getString('email') && in_array(JRequest::getString('email'), $emailList)) { return 1; }

      //OK, filters have passed. Now check link count & words
      $wordList = explode("\r\n",$this->_params->get('word_list',''));
      if (count($wordList) > 1) {
         foreach ($wordList as $word) {
            if (stristr(JRequest::getString('issue_summary'), $word)) { return 1; }
            if (stristr(JRequest::getString('issue_description'), $word)) { return 1; }
            if (stristr(JRequest::getString('additional_info'), $word)) { return 1; }
         }
      }

      //how many urls - This is a basic form of caching.
      if (substr_count(JRequest::getString('issue_description'), 'http://')   >= $this->_params->get('link_count',3))   { return 1; }
      if (substr_count(JRequest::getString('issue_summary'), 'http://')       >= $this->_params->get('link_count',3))   { return 1; }
      if (substr_count(JRequest::getString('additional_info'), 'http://')     >= $this->_params->get('link_count',3))   { return 1; }

      return 0;
   }

   /**
    * Method to get User's default defined project
    * @return object with data
    */
   public function getDefProject($userid)
   {
      // Load the data
      $query = 'SELECT assigned_project FROM `#__it_people` WHERE `user_id` = '.$userid;
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

   /*
    *
    * Method to check whether the included text is spam using Akismet
    * Details from akismet.com
    *
    * Input is an array with the text in the comment_content element.  Other fields should get populated in the _getAkismet method.
    *
    * $data = array('blog' => 'http://yourblogdomainname.com',
    *   'user_ip' => '127.0.0.1',
    *   'user_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6',
    *   'referrer' => 'http://www.google.com',
    *   'permalink' => 'http://yourblogdomainname.com/blog/post=1',
    *   'comment_type' => 'comment',
    *   'comment_author' => 'admin',
    *   'comment_author_email' => 'test@test.com',
    *   'comment_author_url' => 'http://www.CheckOutMyCoolSite.com',
    *   'comment_content' => 'It means a lot that you would take the time to review our software.  Thanks again.');
    */
   public function _check_akismet($data)
   {
      try {
         if ($this->_getAkismet($data)->isCommentSpam()) {
            // Its defined as spam just return true
            return true;
         }
      } catch (Exception $e) {
         if (JDEBUG) JError::raiseWarning(500, $e->getMessage());
         return;
      }

      return false;
   }

   /**
    * Method to get Project target end date
    * @return object with data
    */
   private function _getAkismet($input)
   {
      $data = $input['jform'];

      $akismet = new Akismet($this->_params->get('site_url'), $this->_params->get('akismet_api_key'));
      if (!$akismet->isKeyValid()){
         throw new Exception(JText::_('COM_ISSUETRACKER_AKISMET_INVALID_API_KEY'));
      }
      $text = null;
      if ( ! empty ($data['issue_summary']) )
         $text .= $data['issue_summary'];
      if ( ! empty ($data['issue_description']) )
         $text .= ' ' . $data['issue_description'];

      $user = JFactory::getUser();    // Assumes registered user
      if ( $user->guest ) {
         $akismet->setCommentAuthor($data['user_details']['name']);
         // Use author set to 'viagra-test-123' to get a positive test back.
         $akismet->setCommentAuthorEmail($data['user_details']['email']);
      } else {
         $akismet->setCommentAuthor($user->user_id ? $user->name : $user->name);
         $akismet->setCommentAuthorEmail($user->user_id ? $user->email : $user->email);
         // Guests cannot add additional information
         if ( array_key_exists ('additional_info', $data) && ! empty ($data['additional_info']) )
            $text .= ' ' . $data['additional_info'];
      }

      $akismet->setCommentContent($text);
      $akismet->setCommentType('comment');
      return $akismet;
   }

/*
   public function getForm($data = array(), $loadData = true)
   {
      // $app = JFactory::getApplication('site');
      // Get the form.
       try {
            //throws new Exception(JText::_('JLIB_FORM_ERROR_NO_DATA'));
            $form = $this->loadForm('com_issuetracker.itissues', 'itissues', array('control' => 'jform', 'load_data' => $loadData));
        } catch (Exception $e) {
            echo "e";
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

      if (empty($form)) {
         return false;
      }
      return $form;
   }
*/
}