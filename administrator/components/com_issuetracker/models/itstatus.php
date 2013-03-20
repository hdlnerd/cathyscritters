<?php
/*
 *
 * @Version       $Id: itstatus.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.1.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-01-04 14:39:25 +0000 (Fri, 04 Jan 2013) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modeladmin');

/**
 * Issuetracker Model
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */
class IssuetrackerModelItstatus extends JModelAdmin
{
   /**
    * @var     string   The prefix to use with controller messages.
    * @since   1.6
    */
   protected $text_prefix = 'COM_ISSUETRACKER';


   /**
    * Returns a reference to the a Table object, always creating it.
    *
    * @param   type  The table type to instantiate
    * @param   string   A prefix for the table class name. Optional.
    * @param   array Configuration array for model. Optional.
    * @return  JTable   A database object
    * @since   1.6
    */
   public function getTable($type = 'Itstatus', $prefix = 'IssueTrackerTable', $config = array())
   {
      return JTable::getInstance($type, $prefix, $config);
   }

   /**
    * Method to get the record form.
    *
    * @param   array $data    An optional array of data for the form to interogate.
    * @param   boolean  $loadData   True if the form is to load its own data (default case), false if not.
    * @return  JForm A JForm object on success, false on failure
    * @since   1.6
    */
   public function getForm($data = array(), $loadData = true)
   {
      // Initialise variables.
      $app  = JFactory::getApplication();

      // Get the form.
      $form = $this->loadForm('com_issuetracker.itstatus', 'itstatus', array('control' => 'jform', 'load_data' => $loadData));
      if (empty($form)) {
         return false;
      }

      return $form;
   }

   /**
    * Method to get the data that should be injected in the form.
    *
    * @return  mixed The data for the form.
    * @since   1.6
    */
   protected function loadFormData()
   {
      // Check the session for previously entered form data.
      $data = JFactory::getApplication()->getUserState('com_issuetracker.edit.itstatus.data', array());

      if (empty($data)) {
         $data = $this->getItem();
      }

      return $data;
   }

   /**
    * Method to get a single record.
    *
    * @param   integer  The id of the primary key.
    *
    * @return  mixed Object on success, false on failure.
    * @since   1.6
    */
   public function getItem($pk = null)
   {
      if ($item = parent::getItem($pk)) {

         //Do any procesing on fields here if needed

      }

      return $item;
   }

   /**
    * Prepare and sanitise the table prior to saving.
    *
    * @since   1.6
    */
   protected function prepareTable(&$table)
   {
      jimport('joomla.filter.output');

      if (empty($table->id)) {

         // Set ordering to the last item if not set
         if (@$table->ordering === '') {
            $db = JFactory::getDbo();
            $db->setQuery('SELECT MAX(ordering) FROM #__it_status');
            $max = $db->loadResult();
            $table->ordering = $max+1;
         }

      }
   }

   /**
    * Method to delete one or more records.
    *
    * @param   array  &$pks  An array of record primary keys.
    *
    * @return  boolean  True if successful, false if an error occurs.
    *
    * @since   11.1
    */
   public function delete(&$pks)
   {
      $pks = (array) $pks;
      $app  = JFactory::getApplication();

      // Check if there are any of the items to be deleted are in use.
      $query = 'SELECT COUNT( i.id ) AS numcat';
      $query .= ' FROM #__it_issues AS i' ;
      $query .= ' WHERE i.status IN ( '.implode(',',$pks).' )' ;

      $this->_db->setQuery( $query );
      $cnt = $this->_db->loadResult();
      if ($cnt > 0) {
         $app->enqueueMessage(JText::plural('COM_ISSUETRACKER_N_DELETE_ITEMS_IN_USE_MSG', $cnt), 'error');
         return false;
      }

      $row = $this->getTable();

      // Iterate the items to delete each one.
      foreach ($pks as $i => $pk)
      {
        if (!$row->delete( $pk )) {
           $this->setError( $row->getErrorMsg() );
           return false;
        }
      }
      return true;
   }

}
