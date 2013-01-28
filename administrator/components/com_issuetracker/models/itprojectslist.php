<?php
/*
 *
 * @Version       $Id: itprojectslist.php 284 2012-07-06 13:51:19Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-07-06 14:51:19 +0100 (Fri, 06 Jul 2012) $
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
class IssueTrackerModelItprojectslist extends JModelList
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
                'project_name', 'a.project_name',
                'project_description', 'a.project_description',
                'parent_id', 'a.parent_id',
                'parent_project_name', 'a.parent_project_name',
                'start_date', 'a.start_date',
                'target_end_date', 'a.target_end_date',
                'actual_end_date', 'a.actual_end_date',
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

      $published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
      $this->setState('filter.state', $published);

      // Load the parameters.
      $params = JComponentHelper::getParams('com_issuetracker');
      $this->setState('params', $params);

      // List state information.
      parent::populateState('a.project_name', 'asc');
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
      $query->from('`#__it_projects` AS a');

      // Join over the it_projects table (itself) to resolve parent project name.
      $query->select('b.project_name AS parent_project_name');
      $query->join('LEFT', '#__it_projects AS b ON b.id = a.parent_id');

      $query->select('c.countid AS countid');
      $query->join('LEFT', '(SELECT c.parent_id, count(*) AS countid'
      . ' FROM #__it_projects AS c'
      . ' GROUP BY c.parent_id ) AS c'
      . ' ON a.parent_id = c.parent_id');

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


      // Filter by search in title
      $search = $this->getState('filter.search');
      if (!empty($search)) {
         if (stripos($search, 'id:') === 0) {
            $query->where('a.id = '.(int) substr($search, 3));
         } else {
            $search = $db->Quote('%'.$db->getEscaped($search, true).'%');
                $query->where('( a.project_name LIKE '.$search.'  OR  a.project_description LIKE '.$search.' )');
         }
      }

      // Add the list ordering clause.
      $orderCol   = $this->state->get('list.ordering');
      $orderDirn  = $this->state->get('list.direction');
      if ($orderCol == 'a.ordering' || $orderCol == 'parent_project_name') {
         $orderCol = 'parent_project_name '.$orderDirn.', a.ordering';
      }
      $query->order($db->getEscaped($orderCol.' '.$orderDirn));

//        if ($orderCol && $orderDirn) {
//          $query->order($db->getEscaped($orderCol.' '.$orderDirn));
//        }

      return $query;
   }

   public function orderup()
   {
      $app = JFactory::getApplication('administrator');

      $cid = JRequest::getVar('cid');

      $row =& JTable::getInstance('itprojects', 'IssueTrackerTable');
      $row->load( $cid[0]);
      $row->move( -1, 'parent_id = ' . $this->_data->parent_id);
      $row->reorder( 'parent_id = ' . $this->_data->parent_id);

      $msg = JText::_('COM_ISSUETRACKER_NEW_ORDERING_SAVED');

      $app->redirect('index.php?option=com_issuetracker&view=itprojectslist', $msg);
   }

   public function orderdown()
   {
      $app = JFactory::getApplication('administrator');

      $cid = JRequest::getVar('cid');

      $row =& JTable::getInstance('itprojects', 'IssueTrackerTable');
      $row->load( $cid[0]);
      $row->move( 1, 'parent_id = ' . $this->_data->parent_id);
      $row->reorder( 'parent_id = ' . $this->_data->parent_id);

      $msg = JText::_('COM_ISSUETRACKER_NEW_ORDERING_SAVED');

      $app->redirect('index.php?option=com_issuetracker&view=itprojectslist', $msg);
   }


   public function projectsTree( $row = NULL)
   {
      $db = & JFactory::getDBO();

      if ( isset($row->id)) {
         $idCheck = ' WHERE id != '.( int )$row->id;
      } else {
         $idCheck = null;
      }

      if ( !isset($row->parent_id)) {
         $row->parent_id = 0;
      }

      $query = "SELECT * FROM #__it_projects {$idCheck}";
      $query.=" ORDER BY parent_id, ordering";
      $db->setQuery($query);

      $rows = $db->loadObjectList();

      if( count( $rows)){
         foreach ( $rows as $row) {
            $pt = $row->parent_id;
            $list = @$children[$pt] ? $children[$pt] : array ();
            array_push( $list, $row);
            $children[$pt] = $list;
         }
      }

      $list = projectTreeRecurse( 0, '', array (), $children, 10, 0, 1);

      $options = array ();
      foreach ($list as $entry) {
         $options[] = JHTML::_( 'select.option', $entry->id, $entry->project_name);
      }
      return $options;
   }

   /**
    * Get recursive category array
    *
    * @return array
    */
   public function projectTreeRecurse( $id, $indent, $list, &$children, $maxlevel=9999, $level=0, $type=1 )
   {
      if (isset($children[$id]) && $level <= $maxlevel) {
         foreach ($children[$id] as $this->_data) {
            $id = $this->_data->id;
            if ( $this->_data->parent_id == 0 ) {
               $txt = $this->_data->project_name;
            } else {
               $txt = '&nbsp;-&nbsp;' . $this->_data->project_name;
            }

            $pt = $this->_data->parent_id;
            $list[$id] = $this->_data;
            $list[$id]->treename = $indent . $txt;
            $list[$id]->children = !empty($children[$id]) ? count( $children[$id] ) : 0;
            $list[$id]->section = ($this->_data->parent_id==0);

            // recursive call
            $list = projectTreeRecurse( $id, $indent . '&nbsp;&nbsp;&nbsp;', $list, $children, $maxlevel, $level+1, $type );
         }
      }
      return $list;
   }
}