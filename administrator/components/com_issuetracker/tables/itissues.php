<?php
/*
 *
 * @Version       $Id: itissues.php 362 2012-08-27 13:33:33Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-27 14:33:33 +0100 (Mon, 27 Aug 2012) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Issue Tracker Table
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerTableItissues extends JTable
{
   var $id                       = null;         // Primary Key
   var $asset_id                 = null;
   var $alias                    = null;
   var $issue_summary            = null;
   var $issue_description        = null;
   var $identified_by_person_id  = null;
   var $identified_date          = null;
   var $related_project_id       = null;
   var $assigned_to_person_id    = null;
   var $issue_type               = null;
   var $status                   = null;
   var $state                    = null;
   var $checked_out              = null;
   var $checked_out_time         = null;
   var $ordering                 = null;
   var $priority                 = null;
   var $target_resolution_date   = null;
   var $progress                 = null;
   var $actual_resolution_date   = null;
   var $resolution_summary       = null;
   var $created_on               = null;
   var $created_by               = null;
   var $modified_on              = null;
   var $modified_by              = null;

   /**
    * Constructor
    *
    * @param   database  &$db  A database connector object
    *
    * @return  JTableContent
    *
    * @since   11.1
    */
   function __construct(&$db)
   {
      parent::__construct('#__it_issues', 'id', $db);
   }


   /**
    * Overloaded bind function
    *
    * @param   array  $array   Named array
    * @param   mixed  $ignore  An optional array or space separated list of properties
    *                          to ignore while binding.
    *
    * @return  mixed  Null if operation was satisfactory, otherwise returns an error string
    *
    * @see     JTable:bind
    * @since   11.1
    */
   public function bind($array, $ignore = '')
   {
      if (isset($array['userdetails']) && is_array($array['userdetails'])) {
         $registry = new JRegistry;
         $registry->loadArray($array['userdetails']);
         $array['userdetails'] = (string)$registry;
      }

      // Bind the rules.
      if (isset($data['rules']) && is_array($data['rules'])) {
          $rules = new JRules($data['rules']);
          $this->setRules($rules);
      }

      return parent::bind($array, $ignore);
   }

   /**
    * Overloaded check function
    *
    * @return  boolean  True on success, false on failure
    *
    * @see     JTable::check
    * @since   11.1
    */
   public function check()
   {
      if (trim($this->issue_summary) == '') {
         $this->setError(JText::_('COM_ISSUETRACKER_WARNING_PROVIDE_VALID_SUMMARY'));
         return false;
      }

      // Clean up keywords -- eliminate extra spaces between phrases
      // and cr (\r) and lf (\n) characters from string
      if (!empty($this->issue_summary)) {
         // Only process if not empty
         $this->issue_summary = JFilterOutput::cleanText($this->issue_summary);
      }
/*
      // Clean up keywords -- eliminate extra spaces between phrases
      // and cr (\r) and lf (\n) characters from string
      if (!empty($this->issue_description)) {
         // Only process if not empty
         $this->issue_description = JFilterOutput::cleanText($this->issue_description);
      }

      // Clean up keywords -- eliminate extra spaces between phrases
      // and cr (\r) and lf (\n) characters from string
      if (!empty($this->resolution_summary)) {
         // Only process if not empty
         $this->resolution_summary = JFilterOutput::cleanText($this->resolution_summary);
      }
*/

      // Clean up keywords -- eliminate extra spaces between phrases
      // and cr (\r) and lf (\n) characters from string
      if (!empty($this->progress)) {
         // Only process if not empty
         // $this->progress = JFilterOutput::cleanText($this->progress);
         $this->progress = strip_tags($this->progress, '<p><br>');
      }

      // return true;
      //If there is an ordering column and this is a new row then get the next ordering value
      if (property_exists($this, 'ordering') && $this->id == 0) {
         $this->ordering = self::getNextOrder();
      }

      return parent::check();
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
      if ($this->id) {  // Existing item
         $this->modified_on   = $date->toMySQL();
         $this->modified_by   = $user->get('username');
      } else {
         // New issue. An issue created_on and created_by field can not be set by the user,
         $this->created_on = $date->toMySQL();
         $this->created_by = $user->get('username');
      }

        // Verify that the alias is unique
//        $table = JTable::getInstance('Itissues','IssueTrackerTable');
//        if ($table->load(array('alias'=>$this->alias, 'related_project_id'=>$this->releated_project_id)) && ($table->id != $this->id || $this->id==0)) {
//           $this->setError(JText::_('COM_ISSUETRACKER_ERROR_UNIQUE_ALIAS'));
//           return false;
//        }

      return parent::store($updateNulls);
   }

   /**
    * Method to set the publishing state for a row or list of rows in the database
    * table. The method respects checked out rows by other users and will attempt
    * to checkin rows that it can after adjustments are made.
    *
    * @param   mixed    $pks      An optional array of primary key values to update.  If not
    *                            set the instance property value is used.
    * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
    * @param   integer  $userId  The user id of the user performing the operation.
    *
    * @return  boolean  True on success.
    *
    * @since   11.1
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
      if (empty($pks)) {
         if ($this->$k) {
            $pks = array($this->$k);
         } else {
            // Nothing to set publishing state on, return false.
            $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
            return false;
         }
      }

      // Build the WHERE clause for the primary keys.
      $where = $k.'='.implode(' OR '.$k.'=', $pks);

      // Set the JDatabaseQuery object now to work with the below if clause
      $query = $this->_db->getQuery(true);

      // Determine if there is checkin support for the table.
      if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
         // Do nothing
      } else {
         $query->where('('.$this->_db->quoteName('checked_out').' = 0 OR '.$this->_db->quoteName('checked_out').' = '.(int) $userId.')');
      }

      // Update the publishing state for rows with the given primary keys.
      $query->update($this->_db->quoteName($this->_tbl));
      $query->set($this->_db->quoteName('state').' = '.(int) $state);
      $query->where($where);
      $this->_db->setQuery($query);
      $this->_db->query();

      // Check for a database error.
      if ($this->_db->getErrorNum()) {
         $this->setError($this->_db->getErrorMsg());
         return false;
      }

      // If checkin is supported and all rows were adjusted, check them in.
      if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
         // Checkin the rows.
         foreach($pks as $pk) {
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
    * Method to compute the default name of the asset.
    * The default name is in the form `table_name.id`
    * where id is the value of the primary key of the table.
    *
    * @return  string
    * @since   2.5
    */
   protected function _getAssetName()
   {
       $k = $this->_tbl_key;
       return 'com_issuetracker.itissues.' . (int) $this->$k;
   }

   /**
    * Method to return the title to use for the asset table.
    *
    * @return      string
    * @since       2.5
    */
   protected function _getAssetTitle()
   {
      return 'Issue_'.$this->alias;
   }

   /**
    * Get the parent asset id for the record
    *
    * @return  int
    * @since   2.5
    */
   protected function _getAssetParentId($table = null, $id = null)
   {
      $asset = JTable::getInstance('Asset');
      $asset->loadByName('com_issuetracker');
      return $asset->id;
   }
}
