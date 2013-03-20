<?php
/*
 *
 * @Version       $Id: itprojectslist.php 689 2013-02-06 17:38:45Z geoffc $
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
                'lft', 'a.lft',
                'rgt', 'a.rgt',
                'level', 'a.level',
                'access', 'a.access',
                'alias', 'a.alias',
                'state', 'a.state',
                'path', 'a.path',
                'title', 'a.title',
                'description', 'a.description',
                'parent_id', 'a.parent_id',
                'parent_title', 'a.parent_title',
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

      $level = $this->getUserStateFromRequest($this->context.'.filter.level', 'filter_level', 0, 'int');
      $this->setState('filter.level', $level);

      // Load the parameters.
      $params = JComponentHelper::getParams('com_issuetracker');
      $this->setState('params', $params);

      // List state information.
      parent::populateState('a.lft', 'asc');
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
      $query->select('b.title AS parent_title');
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

      // Ignore the root entry.
      $query->where('a.level > 0');

      // Filter on the level.
      if ($level = $this->getState('filter.level')) {
         $query->where('a.level <= '.(int) $level);
      }

      // Filter by search in title
      $search = $this->getState('filter.search');
      if (!empty($search)) {
         if (stripos($search, 'id:') === 0) {
            $query->where('a.id = '.(int) substr($search, 3));
         } else {
            $search = $db->Quote('%'.$db->escape($search, true).'%');
            $query->where('( a.title LIKE '.$search.'  OR  a.description LIKE '.$search.' )');
         }
      }

      // Add the list ordering clause.
      $orderCol   = $this->state->get('list.ordering');
      $orderDirn  = $this->state->get('list.direction');

      $query->order($db->escape($orderCol.' '.$orderDirn));

      return $query;
   }

   public function projectsTree( $row = NULL)
   {
      $db = JFactory::getDBO();

      if ( isset($row->id)) {
         $idCheck = ' WHERE id != '.( int )$row->id;
      } else {
         $idCheck = null;
      }

      if ( !isset($row->parent_id)) {
         $row->parent_id = 0;
      }

      $query = "SELECT * FROM #__it_projects {$idCheck}";
      // $query.=" ORDER BY parent_id, ordering";
      $query.=" ORDER BY lft";
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
         $options[] = JHTML::_( 'select.option', $entry->id, $entry->title);
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
               $txt = $this->_data->title;
            } else {
               $txt = '&nbsp;-&nbsp;' . $this->_data->title;
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