<?php
/*
 * Issue Tracker Model for Issue Tracker Component
 *
 * @Version       $Id: itpeoplelist.php 713 2013-02-18 18:15:53Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-18 18:15:53 +0000 (Mon, 18 Feb 2013) $
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
 * @package    Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerModelItpeoplelist extends JModel
{

   /**
    * Itpeoplelist data array for tmp store
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
//       $query = 'SELECT t1.* FROM `#__it_people` t1 ';
      // Create a new query object.
      $db      = $this->getDbo();
      $query   = $db->getQuery(true);
      $query->select(
         $this->getState(
            'list.select',
            't1.*'
         )
      );

      $query->from('#__it_people AS t1');

      // Join over the it_projects table.
      $query->select('t2.title AS project_name, t2.id AS project_id');
      $query->join('LEFT', '#__it_projects AS t2 ON t2.id = t1.assigned_project');

      // Outer join over the it_roles table.
      $query->select('t3.role_name AS role_name, t3.id AS role_id');
      $query->join('LEFT OUTER', '#__it_roles AS t3 ON t3.id = t1.person_role');

      $query = $query . $this->_buildQueryWhere() . $this->_buildQueryOrderBy();
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
      $default_order_field = 'id';
      // Array of allowable order fields
       $allowedOrders = explode(',', 'id,person_name,person_email,person_role,username,assigned_project,title,project_name,created_on,created_by,modified_on,modified_by'); // array('id', 'ordering', 'published');

      // retrive ordering info
      $filter_order = $app->getUserStateFromRequest('com_issuetracker.filter_order', 'filter_order', $default_order_field);
      $filter_order_Dir = strtoupper($app->getUserStateFromRequest('com_issuetracker.filter_order_Dir', 'filter_order_Dir', 'ASC'));

       // validate the order direction, must be ASC or DESC
       if ($filter_order_Dir != 'ASC' && $filter_order_Dir != 'DESC') {
         $filter_order_Dir = 'ASC';
       }

       // if order column is unknown use the default
       if ((isSet($allowedOrders)) && !in_array($filter_order, $allowedOrders)){
         $filter_order = $default_order_field;
       }

       $prefix = 't1';
       switch ( $filter_order ) {
          case 'title':
          case 'project_name':
             $prefix = 't2';
             break;
          case 'role_name';
             $prefix = 't3';
             break;
       }

      // return the ORDER BY clause
       return " ORDER BY {$prefix}.`{$filter_order}` {$filter_order_Dir}";
   }

   private function _buildQueryWhere()
   {
      $app = JFactory::getApplication();

      $where = ' WHERE ( t1.`published`=1) ';

      $search = $app->getUserStateFromRequest('com_issuetrackersearch', 'search', '');

      if (!$search) return $where;

      $allowedSearch = explode(',', 'person_name,person_email,person_role,username,created_by,modified_by'); // array('id', 'ordering', 'published');
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
         $db = JFactory::getDBO();
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
      $recordSet =& $this->getTable('itpeople','IssueTrackerTable');
      $db->setQuery( 'SELECT COUNT(*) FROM `#__it_people` WHERE published = 1 ' );
//      $db->setQuery( 'SELECT COUNT(*) FROM `#__it_people` WHERE ' . (isset($recordSet->published)?'`published`':'1') . ' = 1' );
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
