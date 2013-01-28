<?php
/*
 *
 * @Version       $Id: issuetracker.php 260 2012-06-21 17:41:24Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-06-21 18:41:24 +0100 (Thu, 21 Jun 2012) $
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
          $cuser = & JFactory::getUser();

          if ( $isnew) {
            // add a record to #__it_people
            $db = JFactory::getDBO();

            // Get the default role for the new user.
            $params = new JParameter(JComponentHelper::getParams('com_issuetracker'));
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

            $db->setQuery( $sql);
            $db->query();
         } else { // user is updated
            // update the user record in #__it_people
            $db = JFactory::getDBO();
            $sql = "UPDATE " . $db->nameQuote('#__it_people') . " SET " .
                   "registered='1', " .
                   "person_email=\"".$user['email'] . "\", " .
                   "person_name=\"" . $user['name'] ."\", " .
                   "modified_on=\"" . $date . "\" , " .
                   "modified_by=\"" . $cuser->username . "\" , " .
                   "username=\"" . $user['username'] . "\" " .
                   "WHERE " . $db->nameQuote('user_id') . " = " . $user['id'];

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
            $sql = 'DELETE FROM '.$db->nameQuote('#__it_people') . ' WHERE ' .
                              $db->nameQuote('user_id').' = '.$user['id'];

            $db->setQuery( $sql);
            $db->query();

            break;
         }
         case 2: { // soft
            // 1. update issues table, set all issues of this user to specified userid
            // Change assigned to and identified by.
            $sql = 'UPDATE '.$db->nameQuote('#__it_issues') .
                              ' SET ' .
                              $db->nameQuote('assigned_to_person_id') . ' = '. $deleteUser .
                              ' WHERE ' .
                              $db->nameQuote('assigned_to_person_id') . ' = ' . $user['id'];

            $db->setQuery( $sql);
            $db->query();

            $sql = 'UPDATE '.$db->nameQuote('#__it_issues') .
                              ' SET ' .
                              $db->nameQuote('identified_by_person_id') . ' = '. $deleteUser .
                              ' WHERE ' .
                              $db->nameQuote('identified_by_person_id') . ' = ' . $user['id'];

            $db->setQuery( $sql);
            $db->query();

            // 2. now delete user from people table
            $sql = 'DELETE FROM '.$db->nameQuote('#__it_people') . ' WHERE ' .
                              $db->nameQuote('user_id').' = '.$user['id'];

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