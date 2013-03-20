<?php
/*
 *
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

jimport('joomla.database.tablenested');

/**
 * Issue Tracker Table
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerTableItprojects extends JTableNested
{
   var $id                    = null;       // Primary Key
   var $parent_id             = null;
   var $title                 = null;
   var $alias                 = null;
   var $description           = null;
   var $state                 = null;
//   var $ordering              = null;
   var $checked_out           = null;
   var $checked_out_time      = null;
   var $start_date            = null;
   var $target_end_date       = null;
   var $actual_end_date       = null;
   var $created_on            = null;
   var $created_by            = null;
   var $modified_on           = null;
   var $modified_by           = null;

   /**
    * Constructor
    *
    * @param object Database connector object
    */
   function __construct(&$db)
   {
      parent::__construct('#__it_projects', 'id', $db);
   }

   function check()
   {
      //If there is an ordering column and this is a new row then get the next ordering value
      if (property_exists($this, 'ordering') && $this->id == 0) {
         $this->ordering = self::getNextOrder();
      }

      // Data validation code
      if (trim($this->title) == '') {
         $this->setError(JText::_('COM_ISSUETRACKER_WARNING_PROVIDE_VALID_PROJECT_NAME'));
         return false;
      }

/*
      if (!empty($this->description)) {
         // Only process if not empty
         $this->description = JFilterOutput::cleanText($this->description);
      }
*/

      $this->alias = trim($this->alias);
      if (empty($this->alias)) {
         $this->alias = $this->title;
      }

      $this->alias = JApplication::stringURLSafe($this->alias);
      if (trim(str_replace('-', '', $this->alias)) == '') {
         $this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
      }


      return parent::check();
   }

   /**
    * Add the root node to an empty table.
    *
    * @return    integer  The id of the new root node.
    */
   public function addRoot()
   {
       $db = JFactory::getDbo();
       $sql = 'INSERT INTO `#__it_projects` '
           . ' SET parent_id = 0'
           . ', lft = 0'
           . ', rgt = 1'
           . ', level = 0'
           . ', title = '.$db->quote( 'Root' )
           . ', description = '.$db->quote( 'Root' )
           . ', alias = '.$db->quote( 'Root' )
           . ', access = 1'
           . ', path = '.$db->quote( '' )
           ;
       $db->setQuery( $sql );
       $db->query();

       return $db->insertid();
   }


   /**
     * Overrides JTable::store to set modified data and user id.
     *
     * @param   boolean  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     *
     * @since   11.1
     */
    public function store($updateNulls = false)
    {
        $date  = JFactory::getDate();
        $user  = JFactory::getUser();

        // Set up audit fields in here, and app defaults in the model.
        if ($this->id) {
            // Existing item
            $this->modified_on   = $date->toSql();
            $this->modified_by   = $user->get('username');
        } else {
            // New issue. An issue created_on and created_by field can not be set by the user,
            $this->created_on = $date->toSql();
            $this->created_by = $user->get('username');
        }

        // Verify that the alias is unique
        $table = JTable::getInstance('Itprojects','IssueTrackerTable');
        if ($table->load(array('alias'=>$this->alias, 'parent_id'=>$this->parent_id)) && ($table->id != $this->id || $this->id==0)) {
           $this->setError(JText::_('COM_ISSUETRACKER_ERROR_UNIQUE_ALIAS'));
           return false;
        }

        return parent::store($updateNulls);
    }

    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param    mixed    An optional array of primary key values to update.  If not
     *                    set the instance property value is used.
     * @param    integer The publishing state. eg. [0 = unpublished, 1 = published]
     * @param    integer The user id of the user performing the operation.
     * @return    boolean    True on success.
     * @since    1.0.4
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        // Initialise variables.
        $k = $this->_tbl_key;

        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state  = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks))
        {
            if ($this->$k) {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else {
                $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                return false;
            }
        }

        // Build the WHERE clause for the primary keys.
        $where = $k.'='.implode(' OR '.$k.'=', $pks);

        // Determine if there is checkin support for the table.
        if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
            $checkin = '';
        }
        else {
            $checkin = ' AND (checked_out = 0 OR checked_out = '.(int) $userId.')';
        }

        // Update the publishing state for rows with the given primary keys.
        $this->_db->setQuery(
            'UPDATE `'.$this->_tbl.'`' .
            ' SET `state` = '.(int) $state .
            ' WHERE ('.$where.')' .
            $checkin
        );
        $this->_db->query();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
        {
            // Checkin the rows.
            foreach($pks as $pk)
            {
                $this->checkin($pk);
            }
        }

        // If the JTable instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks)) {
            $this->state = $state;
        }

        $this->setError('');
        return true;
    }

   /**
    * Method to delete a node and, optionally, its child nodes from the table.
    *
    * @param   integer  $pk        The primary key of the node to delete.
    * @param   boolean  $children  True to delete child nodes, false to move them up a level.
    *
    * @return  boolean  True on success.
    *
    * @see     http://docs.joomla.org/JTableNested/delete
    * @since   2.5
    */
   public function delete($pk = null, $children = false)
   {
      return parent::delete($pk, $children);
   }
}
