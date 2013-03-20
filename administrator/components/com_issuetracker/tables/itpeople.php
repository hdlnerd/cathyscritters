<?php
/*
 *
 * @Version       $Id: itpeople.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-01-04 14:39:25 +0000 (Fri, 04 Jan 2013) $
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

class IssueTrackerTableItpeople extends JTable
{
   var $id                 = null;     // Primary Key
   var $user_id            = null;
   var $person_name        = null;
   var $alias              = null;
   var $person_email       = null;
   var $person_role        = null;
   var $username           = null;
   var $assigned_project   = null;
   var $issues_admin       = null;
   var $staff              = null;
   var $email_notifications = null;
   var $registered         = null;
   var $published          = null;
   var $checked_out        = null;
   var $checked_out_time   = null;
   var $created_on         = null;
   var $created_by         = null;
   var $modified_on        = null;
   var $modified_by        = null;

   /**
    * Constructor
    *
    * @param object Database connector object
    */
   function __construct(&$db)
   {
      parent::__construct('#__it_people', 'id', $db);
   }

   function check()
   {
      //If there is an ordering column and this is a new row then get the next ordering value
      if (property_exists($this, 'ordering') && $this->id == 0) {
         $this->ordering = self::getNextOrder();
      }

      // data validation code
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
      if ($this->id) {
         // Existing item
         $this->modified_on   = $date->toSql();
         $this->modified_by   = $user->get('username');
      } else {
         // New issue. An issue created_on and created_by field can not be set by the user.
         $this->created_on = $date->toSql();
         $this->created_by = $user->get('username');
      }
      // Verify that the alias is unique
      // $table = JTable::getInstance('Itpeople','IssueTrackerTable');

      return parent::store($updateNulls);
   }

   /*
    * Functions to toggle  table fields on or off
    *
    */

   public function toggle($pks = null, $state = 1, $userId = 0, $ttype = 'staff' )
   {
      // Initialise variables.
      $k = $this->_tbl_key;

      if (empty($ttype)) {
         $this->setError(JText::_('COM_ISSUETRACKER_NO_TOGGLE_SPECIFIED'));
         return false;
      }

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
      switch ($ttype) {
         case 'staff':
            $this->_db->setQuery(
               'UPDATE `'.$this->_tbl.'`' .
               ' SET `staff` = '.(int) $state .
               ' WHERE ('.$where.')' .
               $checkin
            );
            break;
         case 'admin':
            $this->_db->setQuery(
               'UPDATE `'.$this->_tbl.'`' .
               ' SET `issues_admin` = '.(int) $state .
               ' WHERE ('.$where.')' .
               $checkin
            );
            break;
         case 'notify':
            $this->_db->setQuery(
               'UPDATE `'.$this->_tbl.'`' .
               ' SET `email_notifications` = '.(int) $state .
               ' WHERE ('.$where.')' .
               $checkin
            );
            break;
         default:
            $this->setError(JText::_('COM_ISSUETRACKER_INVALID_TOGGLE_FIELD'));
            return false;
      }

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
}
