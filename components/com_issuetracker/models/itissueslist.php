<?php
/*
 * Issue Tracker Model for Issue Tracker Component
 *
 * @Version       $Id: itissueslist.php 447 2012-09-10 19:50:26Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.1
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-09-10 20:50:26 +0100 (Mon, 10 Sep 2012) $
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}
/**
 * Issue Tracker Model
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerModelItissueslist extends JModel{

   /**
    * Itissueslist data array for tmp store
    *
    * @var array
    */
   private $_data;

   /**
   * Pagination object
   * @var object
   */
   private $_pagination = null;

   /*
    * Constructor
    *
    */
   function __construct()
   {
      parent::__construct();

      $app = JFactory::getApplication();

        // Get pagination request variables
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);
   }

   /*
    *
    * Added to populate the sort order
    *
    */
   public function populateState()
   {
      $filter_order = JRequest::getCmd('filter_order');
      $filter_order_Dir = JRequest::getCmd('filter_order_Dir');

      $this->setState('filter_order', $filter_order);
      $this->setState('filter_order_Dir', $filter_order_Dir);
   }

   /**
    * Returns the query
    * @return string The query to be used to retrieve the rows from the database
    */
   private function _buildQuery($cid = '', $admin = 0 )
   {
      // use alias t1 for easier JOINs writing
      //  $query = 'SELECT t1.* FROM `#__it_issues` t1 ' . $this->_buildQueryWhere() . $this->_buildQueryOrderBy();

      // Create a new query object.
      $db      = $this->getDbo();
      $query   = $db->getQuery(true);
      $query->select(
         $this->getState(
         'list.select',
         't1.id, t1.alias, t1.issue_summary, t1.issue_description, t1.identified_by_person_id, ' .
         't1.identified_date, t1.related_project_id, t1.assigned_to_person_id, t1.status, t1.state, t1.priority, ' .
         't1.issue_type, ' .
         't1.target_resolution_date, t1.progress, t1.actual_resolution_date, t1.resolution_summary, ' .
         't1.created_on, t1.created_by, t1.modified_on, t1.modified_by'
         )
      );

      $query->from('#__it_issues AS t1');

      // Join over the it_projects table.
      $query->select('t2.project_name AS project_name, t2.id AS project_id');
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

      // Join over the it_types table.
      $query->select('t7.type_name AS type_name');
      $query->join('LEFT', '#__it_types AS t7 ON t7.id = t1.issue_type');

      $query = $query . $this->_buildQueryWhere($cid, $admin) . $this->_buildQueryOrderBy();

      return $query;
   }

   /**
    * Returns the 'order by' part of the query
    * @return string the order by''  part of the query
    */
   private function _buildQueryOrderBy()
   {
       $app = JFactory::getApplication();

      // default field for records list
      $default_order_field = 'ordering';
      // Array of allowable order fields
      $allowedOrders = explode(',', 'id,issue_summary,issue_description,identified_by_person_id,identified_date,related_project_id,project_name,assigned_to_person_id,assigned_person_name,status,state,priority,target_resolution_date,progress,actual_resolution_date,resolution_summary,created_on,created_by,modified_on,modified_by,ordering');

      // retrive ordering info
      $filter_order = $app->getUserStateFromRequest('com_issuetrackerfilter_order', 'filter_order', $default_order_field);
      $filter_order_Dir = strtoupper($app->getUserStateFromRequest('com_issuetrackerfilter_order_Dir', 'filter_order_Dir', 'DESC'));

      // validate the order direction, must be ASC or DESC
      if ($filter_order_Dir != 'ASC' && $filter_order_Dir != 'DESC') {
        $filter_order_Dir = 'DESC';
      }

      // if order column is unknown use the default
      if ((isSet($allowedOrders)) && !in_array($filter_order, $allowedOrders)){
        $filter_order = $default_order_field;
      }

      $prefix = 't1';
      // return the ORDER BY clause
      return " ORDER BY {$prefix}.`{$filter_order}` {$filter_order_Dir}";
   }


   private function _buildQueryWhere($cid = '', $admin = 0 )
   {
      $app = JFactory::getApplication();

      if (empty($cid) && $admin == 0 ) {
         $where = ' WHERE ( t1.`state`=1) ';
      } else {
         // Refine this to check the it_person id not the user_id.
         $person_id = IssueTrackerHelper::get_itpeople_id($cid);
         $where = ' WHERE ( t1.`identified_by_person_id` = ' .$person_id.') ';
      }

      if ( $admin == 1 ) {
         $where = ' WHERE 1=1 ';
         return $where;
      }

      // Get params
      $params = $app->getParams();
      $projids = $params->get('project_ids', array());  // It is an array even if there is only one element!

      // Check if we have 0 in our array, if so ignore the where clause inclusion.
      $pids = implode(',', $projids);                   // Put in a form suitable for our query.
      if (strpos($pids, '0') === FALSE) {
         $where .= ' AND t1.`related_project_id` IN ( '.$pids.')';
      }

      $search = $app->getUserStateFromRequest('com_issuetrackersearch', 'search', '');

      // if (!$search) return '';
      if (!$search) return $where;

      $allowedSearch = explode(',', 'issue_summary,issue_description,status,state,priority,progress,resolution_summary,created_by,modified_by');
      // $where = ' WHERE (0=1) ';
      $wheres = '';
      foreach($allowedSearch as $field){
         //if (!$field) return '';
         if (!$field) return $where;
         $wheres .= " OR (t1.`$field` LIKE '%" . addSlashes($search) . "%') ";
      }
      $where .= " AND ( " . substr($wheres, 4) . ") ";

      return $where;
   }


   /**
    * Retrieves the data
    * @return array Array of objects containing the data from the database
    */
   public function getData()
   {
      $app = JFactory::getApplication();

      // Check if we have a user
      $cid = JRequest::getvar('cuserid','');
      $params = $app->getParams();
      if ($params->get('show_own_issues',0) == 0 )  $cid = '';
      $admin = 0;
      if ($params->get('show_all_issues',0) == 1 )  {
         // Check that we are indeed an issue administrator.
         $user       = JFactory::getUser();
         $is_admin   = IssueTrackerHelper::isIssueAdmin($user->id);
         if ( $is_admin ) {
            $admin = 1;
            $cid = '';            // Ensure we not using show_own as well.
         } else {
            $admin = 0;
         }
      }


      // Lets load the data if it doesn't already exist
      if (empty( $this->_data ))    {
         $query = $this->_buildQuery($cid, $admin);
         $this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit'));
      }
      $this->_data = IssueTrackerHelper::updateprojectname($this->_data);
      return $this->_data;
   }


   /**
    * Gets the number of published records
    * @return int
    */
   public function getTotal()
   {
      $app = JFactory::getApplication();

      // Check if we have a user
      $cid = JRequest::getvar('cuserid','');
      $params = $app->getParams();
      if ($params->get('show_own_issues',0) == 0 )  $cid = '';

      $db = JFactory::getDBO();
      $query   = $db->getQuery(true);

      $query->select(' COUNT(*) ');
      $query->from('#__it_issues AS t1');

      $where = $this->_buildQueryWhere($cid);
      if ( empty($where) ) {
         // No where clause required.
      } else {
         $query .= $where;
      }

      $db->setQuery($query);
      $db->query();
      return $db->loadResult();
   }

   /**
    * Gets the Pagination Object
    * @return object JPagination
    */
   public function getPagination()
   {
      // Load the content if it doesn't already exist
      if (empty($this->_pagination)) {
         jimport('joomla.html.pagination');
         $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
      }
      return $this->_pagination;
   }
}
