<?php
/*
 *
 * @Version       $Id: itprojectslist.php 394 2012-08-29 15:20:14Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.1
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-29 16:20:14 +0100 (Wed, 29 Aug 2012) $
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
class IssueTrackerModelItprojectslist extends JModel{

   /**
    * Itprojectslist data array for tmp store
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
   }

   protected function populateState($ordering = null, $direction = null)
   {
      // Initialise variables.
      $app = JFactory::getApplication();
      $session = JFactory::getSession();

      // Get pagination request variables
      $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
      $limitstart = JRequest::getVar('limitstart', 0, '', 'int');

      // In case limit has been changed, adjust it
      $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

      $this->setState('limit', $limit);
      $this->setState('limitstart', $limitstart);

      $filter_order = JRequest::getCmd('filter_order');
      $filter_order_Dir = JRequest::getCmd('filter_order_Dir');

      $this->setState('filter_order', $filter_order);
      $this->setState('filter_order_Dir', $filter_order_Dir);
   }


   /**
    * Returns the query
    * @return string The query to be used to retrieve the rows from the database
    */
   private function _buildQuery()
   {
      // use alias t1 for easier JOINs writing
      $query = 'SELECT t1.id AS project_id, t1.parent_id, t1.project_name, t1.alias, t1.project_description, ' .
                 't1.state, t1.ordering, t1.checked_out, t1.checked_out_time, t1.start_date, t1.target_end_date, t1.actual_end_date, ' .
                 't1.created_on, t1.created_by, t1.modified_on, t1.modified_by';
      $query .= ' FROM `#__it_projects` t1 ';
      $query .= $this->_buildQueryWhere() . $this->_buildQueryOrderBy();
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
      $default_order_field = 't1.`parent_id`, t1.`ordering`';
      // Array of allowable order fields
       $allowedOrders = explode(',', 'project_name,project_description,state,start_date,target_end_date,actual_end_date,created_on,created_by,modified_on,modified_by');

      // retrive ordering info
      $filter_order = $app->getUserStateFromRequest('com_issuetrackerfilter_order', 'filter_order', $default_order_field);
      $filter_order_Dir = strtoupper($app->getUserStateFromRequest('com_issuetrackerfilter_order_Dir', 'filter_order_Dir', 'ASC'));

       // validate the order direction, must be ASC or DESC
       if ($filter_order_Dir != 'ASC' && $filter_order_Dir != 'DESC') {
         $filter_order_Dir = 'ASC';
       }

       // if order column is unknown use the default
       if ((isSet($allowedOrders)) && !in_array($filter_order, $allowedOrders)){
         $filter_order = $default_order_field;
       }

      // $prefix = 't1';
       // return the ORDER BY clause
       return " ORDER BY {$filter_order} {$filter_order_Dir}";
   }

   private function _buildQueryWhere()
   {
      $app = JFactory::getApplication();

      $where = ' WHERE ( t1.`state`=1) ';

      $search = $app->getUserStateFromRequest('com_issuetrackersearch', 'search', '');

      if (!$search) return $where;

      $allowedSearch = explode(',', 'project_name,created_by,modified_by');
      $wheres = '';
      foreach($allowedSearch as $field){
         if (!$field) return '';
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
      // Lets load the data if it doesn't already exist
      if (empty( $this->_data ))    {
         $query = $this->_buildQuery();
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
      $db = JFactory::getDBO();
//      $recordSet =& $this->getTable('itprojects');
//      $db->setQuery( 'SELECT COUNT(*) FROM `#__it_projects` WHERE ' . (isset($recordSet->state)?'`state`':'1') . ' = 1' );
      $db->setQuery( 'SELECT COUNT(*) FROM `#__it_projects` WHERE state = 1' );

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
