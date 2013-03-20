<?php
/*
 *
 * @Version       $Id: issuetracker.php 741 2013-02-27 16:33:26Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-27 16:33:26 +0000 (Wed, 27 Feb 2013) $
 *
 */

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
jimport( 'joomla.html.parameter' );

/**
 * Plugin for Issue Tracker
 *
 * @package    Joomla
 * @subpackage Issue Tracker
 */

class plgSystemIssuetracker extends JPlugin
{
   /**
    * Store user method
    *
    * Method is called after user data is stored in the database
    *
    * @param   array    holds the new user data
    * @param   boolean     true if a new user is stored
    * @param   boolean     true if user was succesfully stored in the database
    * @param   string      message
    */
   function onUserAfterSave( $user, $isnew, $success, $msg)
   {
      if ( $success) {
          $date = JFactory::getDate();
          $cuser = JFactory::getUser();

          if ( $isnew) {
            // add a record to #__it_people
            $db = JFactory::getDBO();

            // Get the default role for the new user.  Note JParameter is deprecated.
            // $params = new JParameter(JComponentHelper::getParams('com_issuetracker'));
            $app = JFactory::getApplication();
            $params = JComponentHelper::getParams('com_issuetracker');

            $params = $app->getParams('com_issuetracker');

            $defrole = $params->get('def_role', 6);
            $defproject = $params->get('def_project', 1);

            $sql  = "INSERT INTO `#__it_people`";
            $sql .= " (user_id, person_email, person_name, registered, created_on, created_by, person_role, assigned_project, username  )";
            $sql .= " VALUES ( '" . $user['id'] . "',";
            $sql .= " '" . $user['email'] . "',";
            $sql .= " '" . $user['name'] ."',";
            $sql .= " '1', ";
            $sql .= " '" . $date ."',";
            $sql .= " '" . $cuser->username ."',";
            $sql .= " '" . $defrole ."',";
            $sql .= " '" . $defproject ."',";
            $sql .= " '" . $user['username'] . "')";
            $sql .= " ON DUPLICATE KEY ";
            $sql .= " UPDATE person_email = '".$user['email'] ."', ";
            $sql .= " person_role = '" . $defrole ."',";
            $sql .= " person_name = '" . $user['name'] ."',";
            $sql .= " username = '" . $user['username'] ."',";
            $sql .= " user_id = '". $user['id'] . "',";
            $sql .= " assigned_project = '" . $defproject ."',";
            $sql .= " registered = 1 ";

            $db->setQuery( $sql);
            $db->query();
         } else { // user is updated
            // update the user record in #__it_people
            $db = JFactory::getDBO();
            $sql = "UPDATE " . $db->quoteName('#__it_people') . " SET " .
                   "registered='1', " .
                   "person_email=\"".$user['email'] . "\", " .
                   "person_name=\"" . $user['name'] ."\", " .
                   "modified_on=\"" . $date . "\" , " .
                   "modified_by=\"" . $cuser->username . "\" , " .
                   "username=\"" . $user['username'] . "\" " .
                   "WHERE " . $db->quoteName('user_id') . " = " . $user['id'];

            $db->setQuery( $sql);
            $db->query();
         }
      }
   }


   function onUserAfterDelete( $user, $success, $msg)
   {
      $db = JFactory::getDBO();

      // get Delete Mode setting from com_discussions parameters
      $params = JComponentHelper::getParams('com_issuetracker');

      $deleteMode = $params->get('delete', '0');
      $deleteUser = $params->get('deleteUser', '0');

      switch( $deleteMode) {
         case 1: { // raw
            $sql = 'DELETE FROM '.$db->quoteName('#__it_people') . ' WHERE ' .
                              $db->quoteName('user_id').' = '.$user['id'];

            $db->setQuery( $sql);
            $db->query();

            break;
         }
         case 2: { // soft
            // 1. update issues table, set all issues of this user to specified userid
            // Change assigned to and identified by.
            $sql = 'UPDATE '.$db->quoteName('#__it_issues') .
                              ' SET ' .
                              $db->quoteName('assigned_to_person_id') . ' = '. $deleteUser .
                              ' WHERE ' .
                              $db->quoteName('assigned_to_person_id') . ' = ' . $user['id'];

            $db->setQuery( $sql);
            $db->query();

            $sql = 'UPDATE '.$db->quoteName('#__it_issues') .
                              ' SET ' .
                              $db->quoteName('identified_by_person_id') . ' = '. $deleteUser .
                              ' WHERE ' .
                              $db->quoteName('identified_by_person_id') . ' = ' . $user['id'];

            $db->setQuery( $sql);
            $db->query();

            // 2. now delete user from people table
            $sql = 'DELETE FROM '.$db->quoteName('#__it_people') . ' WHERE ' .
                              $db->quoteName('user_id').' = '.$user['id'];

            $db->setQuery( $sql);
            $db->query();

            break;
         }
         default: { // 0 (=disabled) and other
            // do nothing, just keep user in table
            break;
         }
      }
   }
}