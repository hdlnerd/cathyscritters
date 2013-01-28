<?php
/*
 *
 * @Version       $Id: itissueslist.php 454 2012-09-11 17:06:01Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.2
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-09-11 18:06:01 +0100 (Tue, 11 Sep 2012) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.modellist' );

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}
/**
 * Issue Tracker Model
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerModelItissueslist extends JModelList
{
     /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'ordering', 'a.ordering',
                'state', 'a.state',
                'issue_summary', 'a.issue_summary',
                'issue_description', 'a.issue_description',
                'alias', 'a.alias',
                'project_name', 't2.project_name',
                'person_name', 't3.person_name',
                'identifying_name', 't7.person_name',
                'status', 'a.status',
                'issue_type', 'a.issue_type',
                'priority', 'a.priority',
                'created_by','a.created_by',
                'created_on','a.created_on',
                'modified_by','a.modified_by',
                'modified_on','a.modified_on'
            );
        }

        parent::__construct($config);
    }


   /**
    * Method to auto-populate the model state.
    *
    * Note. Calling getState in this method will result in recursion.
    */
   protected function populateState($ordering = null, $direction = null)
   {
      // Initialise variables.
      $app = JFactory::getApplication('administrator');

      // Load the filter state.
      $search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
      $this->setState('filter.search', $search);

      $assigned = $this->getUserStateFromRequest($this->context.'.filter.assigned', 'filter_assigned', '');
      $this->setState('filter.assigned', $assigned);

      $identifier = $this->getUserStateFromRequest($this->context.'.filter.identifier', 'filter_identifier', '');
      $this->setState('filter.identifier', $identifier);

      $published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
      $this->setState('filter.state', $published);

      $projectId = $this->getUserStateFromRequest($this->context.'.filter.project_id', 'filter_project_id');
      $this->setState('filter.project_id', $projectId);

      $statusId = $this->getUserStateFromRequest($this->context.'.filter.status_id', 'filter_status_id');
      $this->setState('filter.status_id', $statusId);

      $typeId = $this->getUserStateFromRequest($this->context.'.filter.type_id', 'filter_type_id');
      $this->setState('filter.type_id', $typeId);

      $priorityId = $this->getUserStateFromRequest($this->context.'.filter.priority_id', 'filter_priority_id');
      $this->setState('filter.priority_id', $priorityId);

      $createdbyId = $this->getUserStateFromRequest($this->context.'.filter.created_by_id', 'filter_created_by');
      $this->setState('filter.created_by', $createdbyId);
      $createdonId = $this->getUserStateFromRequest($this->context.'.filter.created_on_id', 'filter_created_on');
      $this->setState('filter.created_on', $createdonId);
      $modifiedbyId = $this->getUserStateFromRequest($this->context.'.filter.modified_id', 'filter_modified_by');
      $this->setState('filter.modified_by', $modifiedbyId);
      $modifiedonId = $this->getUserStateFromRequest($this->context.'.filter.modified_on', 'filter_modified_on');
      $this->setState('filter.modified_on', $modifiedonId);

      // Load the parameters.
      $params = JComponentHelper::getParams('com_issuetracker');
      $this->setState('params', $params);

      // List state information.
      parent::populateState('a.ordering', 'desc');

   }

   /**
    * Method to get a store id based on model configuration state.
    *
    * This is necessary because the model is used by the component and
    * different modules that might need different sets of data or different
    * ordering requirements.
    *
    * @param   string      $id   A prefix for the store id.
    * @return  string      A store id.
    * @since   1.6
    */
   protected function getStoreId($id = '')
   {
      // Compile the store id.
      $id.= ':' . $this->getState('filter.search');
      $id.= ':' . $this->getState('filter.state');
      $id.= ':' . $this->getState('filter.assigned');
      $id.= ':' . $this->getState('filter.identifier');
      $id.= ':' . $this->getState('filter.project_id');
      $id.= ':' . $this->getState('filter.type_id');
      $id.= ':' . $this->getState('filter.status_id');
      $id.= ':' . $this->getState('filter.priority_id');
      $id.= ':' . $this->getState('filter.created_by');
      $id.= ':' . $this->getState('filter.created_on');
      $id.= ':' . $this->getState('filter.modified_by');
      $id.= ':' . $this->getState('filter.modified_on');

      return parent::getStoreId($id);
   }

   /**
    * Build an SQL query to load the list data.
    *
    * @return  JDatabaseQuery
    * @since   1.6
    */
   protected function getListQuery()
   {
      // Create a new query object.
      $db      = $this->getDbo();
      $query   = $db->getQuery(true);

      // Select the required fields from the table.
      $query->select(
         $this->getState(
            'list.select',
            'a.*'
         )
      );

      $query->from('`#__it_issues` AS a');

      // Join over the it_projects table.
      $query->select('t2.project_name AS project_name, t2.id AS project_id');
      $query->join('LEFT', '#__it_projects AS t2 ON t2.id = a.related_project_id');

      // Join over the it_people table.
      $query->select('t3.person_name AS person_name');
      $query->join('LEFT', '#__it_people AS t3 ON t3.user_id = a.assigned_to_person_id');

      // Join over the it_people table.
      $query->select('t7.person_name AS identifying_name');
      $query->join('LEFT', '#__it_people AS t7 ON t7.id = a.identified_by_person_id');

      // Join over the it_status table.
      $query->select('t4.status_name AS status_name');
      $query->join('LEFT', '#__it_status AS t4 ON t4.id = a.status');

      // Join over the it_priority table.
      $query->select('t5.priority_name AS priority_name');
      $query->join('LEFT', '#__it_priority AS t5 ON t5.id = a.priority');

      // Join over the it_types table.
      $query->select('t6.type_name AS type_name');
      $query->join('LEFT', '#__it_types AS t6 ON t6.id = a.issue_type');

      // Join over the users for the checked out user.
      $query->select('uc.name AS editor');
      $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

      // Filter by published state
      $published = $this->getState('filter.state');
      if (is_numeric($published)) {
         $query->where('a.state = '.(int) $published);
      } else if ($published === '') {
         $query->where('(a.state IN (0, 1))');
      }

      // Filter by project_id
      $pid = $this->getState('filter.project_id');
      if (is_numeric($pid)) {
         $query->where('t2.id = ' . (int) $pid );
      }

      // Filter by assigned person
      $pid = $this->getState('filter.assigned');
      if (is_numeric($pid)) {
         $query->where('a.assigned_to_person_id = ' . (int) $pid);
      }

      // Filter by identifying person
      $pid = $this->getState('filter.identifier');
      if (is_numeric($pid)) {
         $query->where('a.identified_by_person_id = ' . (int) $pid);
      }

      // Filter by status_id
      $sid = $this->getState('filter.status_id');
      if (is_numeric($sid)) {
         $query->where('t4.id = ' . (int) $sid);
      }

      // Filter by priority_id
      $pid = $this->getState('filter.priority_id');
      if (is_numeric($pid)) {
         $query->where('t5.id = ' . (int) $pid);
      }

      // Filter by type_id
      $tid = $this->getState('filter.type_id');
      if (is_numeric($tid)) {
         $query->where('t6.id = ' . (int) $tid);
      }

      // Filter by created_by
      $tid = $this->getState('filter.created_by');
      if (is_numeric($tid)) {
         $query->where('a.created_by = ' . (int) $tid);
      }

      // Filter by created_on
      $tid = $this->getState('filter.created_on');
      if (is_numeric($tid)) {
         $query->where('a.created_on = ' . (int) $tid);
      }

      // Filter by created_by
      $tid = $this->getState('filter.modified_by');
      if (is_numeric($tid)) {
         $query->where('a.modified_by = ' . (int) $tid);
      }

      // Filter by created_by
      $tid = $this->getState('filter.modified_on');
      if (is_numeric($tid)) {
         $query->where('a.modified_on = ' . (int) $tid);
      }

      // Filter by search in title
      $search = $this->getState('filter.search');
      if (!empty($search)) {
         if (stripos($search, 'id:') === 0) {
            $query->where('a.id = '.(int) substr($search, 3));
         } else {
            $search = $db->Quote('%'.$db->getEscaped($search, true).'%');
                $query->where('( a.issue_summary LIKE '.$search.'  OR  a.issue_description LIKE '.$search.' OR a.progress LIKE '.$search.' OR a.resolution_summary LIKE '.$search.' OR a.alias LIKE '.$search.')');
         }
      }

      // Add the list ordering clause.
      $orderCol   = $this->state->get('list.ordering');
      $orderDirn  = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
          $query->order($db->getEscaped($orderCol.' '.$orderDirn));
        }

      return $query;
   }

   /**
    * Methods to get the latest opened issues
    * @return object with data
    * 20/8/12 Removetime element from output. Was %k:%i
    */
   function latestIssues( $count = 10)
   {
      $db = JFactory::getDBO();

      $query  = "SELECT t1.id, t1.issue_summary, t2.project_name, t1.state, DATE_FORMAT( t1.identified_date, \"%d.%m.%Y\") AS issuedate ";
      $query .= " ,t2.id AS project_id ";
      $query .= "FROM #__it_issues t1 ";
      $query .= "LEFT JOIN #__it_projects AS t2 ON t2.id = t1.related_project_id ";
      $query .= "ORDER BY t1.created_on DESC LIMIT " . $count;

      $db->setQuery($query);
      $rows = $db->loadObjectList();
      $rows = IssueTrackerHelper::updateprojectname($rows);

      return $rows;
   }

   /**
    * Methods to get the Overdue issues
    * @return object with data
    */
   function overdueIssues( $count = 10)
   {
      $db = JFactory::getDBO();

      $query  = "SELECT i.id, pr.priority_name AS priority, i.issue_summary, ";
      $query .= "       p.person_name assignee, DATE_FORMAT(i.target_resolution_date, \"%d.%m.%Y\") AS target_resolution_date, r.project_name ";
      $query .= " ,r.id as project_id ";
      $query .= "FROM `#__it_issues` i ";
      $query .= "RIGHT OUTER JOIN `#__it_people` p ";
      $query .= "  ON i.assigned_to_person_id = p.id ";
      $query .= "LEFT JOIN `#__it_projects` r ";
      $query .= " ON i.related_project_id = r.id ";
      $query .= "LEFT JOIN `#__it_priority` pr ";
      $query .= " ON i.priority = pr.id ";
      $query .= "WHERE i.target_resolution_date < sysdate() ";
      $query .= "      AND i.target_resolution_date IS NOT NULL ";
      $query .= "      AND i.target_resolution_date != '0000-00-00 00:00:00' ";
      $query .= "  AND i.status != '1' ";
      $query .= "ORDER BY i.priority, i.target_resolution_date ASC LIMIT " . $count;

      $db->setQuery($query);
      $rows = $db->loadObjectList();
      $rows = IssueTrackerHelper::updateprojectname($rows);

      return $rows;
   }

   /**
    * Methods to get the Issue Summary
    * @return object with data
    */
   function issueSummary ()
   {
      $db = JFactory::getDBO();

      $query  = "SELECT project_name, t2.id as project_id, ";
      $query .= "   DATE_FORMAT( MIN(identified_date), \"%d.%m.%Y\") AS first_identified, ";
      $query .= "   DATE_FORMAT( MAX(actual_resolution_date), \"%d.%m.%Y\") AS last_closed, ";
      $query .= "   COUNT(t1.id) AS total_issues, ";
      $query .= "   SUM(IF(status='4',1,0)) AS open_issues, ";              // Open = 4
      $query .= "   SUM(IF(status='3',1,0)) AS onhold_issues, ";            // On-Hold = 3
      $query .= "   SUM(IF(status='2',1,0)) AS inprogress_issues, ";        // In-Progress = 2
      $query .= "   SUM(IF(status='1',1,0)) AS closed_issues, ";       // Closed = 1
      $query .= "   SUM(IF(status='4',IF(priority IS NULL,1,0),0)) AS open_no_prior, ";
      $query .= "   SUM(IF(status='4',IF(priority='1',1,0),0))  AS open_high_prior, ";   // High = 1
      $query .= "   SUM(IF(status='4',IF(priority='3',1,0),0)) AS open_medium_prior, ";  // Medium = 2
      $query .= "   SUM(IF(status='4',IF(priority='2',1,0),0)) AS open_low_prior ";      // Low = 3
      $query .= "FROM #__it_issues t1 ";
      $query .= "RIGHT OUTER JOIN #__it_projects t2 ";
      $query .= " ON t1.related_project_id = t2.id ";
      $query .= "GROUP BY related_project_id ";
      $query .= "HAVING COUNT(related_project_id) > 0 ";
      $query .= "ORDER BY t2.ordering ";

      $db->setQuery($query);
      $rows = $db->loadObjectList();
      $rows = IssueTrackerHelper::updateprojectname($rows);

      return $rows;
   }

   /**
    * Methods to get the Unassigned Issue Report
    * @return object with data
    */
   function unassignedissues ()
   {
      // Get default assignee from parameters.
      $db = JFactory::getDBO();

      $query  = "SELECT i.id, ";
      $query .= "    pr.priority_name AS priority, ";
      $query .= "    i.issue_summary, ";
      $query .= "    DATE_FORMAT(i.target_resolution_date, \"%d.%m.%Y\") AS target_resolution_date, ";
      $query .= "    r.project_name, ";
      $query .= "    r.id AS project_id, ";
      $query .= "    p.person_name AS identifiee ";
      $query .= "FROM #__it_issues i, ";
      $query .= "     #__it_people p, ";
      $query .= "     #__it_projects r, ";
      $query .= "     #__it_priority pr ";
      $query .= "WHERE (i.assigned_to_person_id IS NULL ";
      $query .= "      OR i.assigned_to_person_id = 1 ) ";
      $query .= "  AND i.status != '1' ";
      $query .= "  AND i.related_project_id = r.id ";
      $query .= "  AND i.identified_by_person_id = p.id ";
      $query .= "  AND i.priority = pr.id ";

      $db->setQuery($query);
      $rows = $db->loadObjectList();

      $rows = IssueTrackerHelper::updateprojectname($rows);
      return $rows;
   }
}
