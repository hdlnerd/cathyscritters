<?php
/*
 *
 * @Version       $Id: dbtasks.php 387 2012-08-28 15:17:58Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-28 16:17:58 +0100 (Tue, 28 Aug 2012) $
 *
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class IssueTrackerModelDbtasks extends JModel
{

   public function addsampledata()
   {
      $app = JFactory::getApplication();

      // First check whether we have the people ids in use.
      $db = $this->getDBO();
      $query  = 'select count(*) from `#__it_people` where id between 2 and 18';
      $db->setQuery($query);
      $result = $db->loadResult();

      if ($result > 0 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_WARNING_ITPEOPLE_IDS_INUSE'));
         // We have one (or possibly more?) of the ids we are using for the sample data in use.
         // We need to modify the sample people and then after loading update the it_issues with the
         // revised person id.
         if ( $result == 1 ) {
            // We have one of the ids in use, probably the Super User but might be the Anonymous user we created.
            $query = "select id from `#__it_people` where person_name = 'Super User'";
            $db->setQuery($query);
            $super_id = $db->loadResult();

            $query = "select id from `#__it_people` where person_name = 'Anonymous'";
            $db->setQuery($query);
            $anon_id = $db->loadResult();

            // if ( ! $super_id ) return false;  // We have another id in the range so just return.

            // Call add projects separately
            $db->setQuery('CALL #__create_sample_projects');
            $res = $db->query();
            // Now add people having modified the used super id.
            // We generally use 2->18 but with the Super User using one of these we use 19 instead.
            // The anonymous user will normally be id=1 but it the super user is using it it will be id=2.
            // Need to insert the records individually since the classes do not like multiple inserts on a single statement.  Very Strange!!
            // Build up our insert string.
            $query = "INSERT INTO `#__it_people` (`id`, `person_name`, `person_email`, `registered`, `person_role`, `username`, `assigned_project`) VALUES";
            if ( $super_id == 2 || $anon_id == 2 ) {
               $query1 = $query . " ('19', 'Thomas Cobley', 'tom.cobley@bademail.com', '0', '1', 'tcobley', null) ";
            } else {
               $query1 = $query . " ('2', 'Thomas Cobley', 'tom.cobley@bademail.com', '0', '1', 'tcobley', null) ";
            }
            $db->setQuery($query1);
            $db->query();

            if ( $super_id == 3 ) {
               $query1 = $query . " (19, 'Harry Hawke', 'harry.hawke@bademail.com', '0', '4', 'hhawke', null) ";
            } else {
               $query1 = $query . " (3, 'Harry Hawke', 'harry.hawke@bademail.com', '0', '4', 'hhawke', null) ";
            }
            $db->setQuery($query1);
            $db->query();

            if ( $super_id == 4 ) {
               $query1 = $query . " (19, 'Tom Pearce', 'tom.pearce@bademail.com', '0', '4', 'tpearce', null) ";
            } else {
               $query1 = $query . " (4, 'Tom Pearce', 'tom.pearce@bademail.com', '0', '4', 'tpearce', null) ";
            }
            $db->setQuery($query1);
            $db->query();
            if ( $super_id == 5 ) {
               $query1 = $query . " (19, 'Bill Brewer', 'bill.brewer@bademail.com', '0', '3', 'bbrewer', 2) ";
            } else {
               $query1 = $query . " (5, 'Bill Brewer', 'bill.brewer@bademail.com', '0', '3', 'bbrewer', 2) ";
            }
            $db->setQuery($query1);
            $db->query();
            if ( $super_id == 6 ) {
               $query1 = $query . " (19, 'Jan Stewer', 'jan.stewer@bademail.com', '0', '3', 'jstewer', 3) ";
            } else {
               $query1 = $query . " (6, 'Jan Stewer', 'jan.stewer@bademail.com', '0', '3', 'jstewer', 3) ";
            }
            $db->setQuery($query1);
            $db->query();
            if ( $super_id == 7 ) {
               $query1 = $query . " (19, 'Peter Gurney', 'peter.gurney@bademail.com', '0', '3', 'pgurney', 4) ";
            } else {
               $query1 = $query . " (7, 'Peter Gurney', 'peter.gurney@bademail.com', '0', '3', 'pgurney', 4) ";
            }
            $db->setQuery($query1);
            $db->query();
            if ( $super_id == 8 ) {
               $query1 = $query . " (19, 'Peter Davy', 'peter.davy@bademail.com', '0', '3', 'pdavy', 5) ";
            } else {
               $query1 = $query . " (8, 'Peter Davy', 'peter.davy@bademail.com', '0', '3', 'pdavy', 5) ";
            }
            $db->setQuery($query1);
            $db->query();
            if ( $super_id == 9 ) {
               $query1 = $query . " (19, 'Daniel Whiddon', 'daniel.whiddon@bademail.com', '0', '3', 'dwhiddon', 6) ";
            } else {
               $query1 = $query . " (9, 'Daniel Whiddon', 'daniel.whiddon@bademail.com', '0', '3', 'dwhiddon', 6) ";
            }
            $db->setQuery($query1);
            $db->query();
            if ( $super_id == 10 ) {
               $query1 = $query . " (19, 'Jack London', 'jack.london@bademail.com', '0', '5', 'jlondon', 2) ";
            } else {
               $query1 = $query . " (10, 'Jack London', 'jack.london@bademail.com', '0', '5', 'jlondon', 2) ";
            }
            $db->setQuery($query1);
            $db->query();
            if ( $super_id == 11 ) {
               $query1 = $query . " (19, 'Mark Tyne', 'mark.tyne@bademail.com', '0', '5', 'mtyne', 2) ";
            } else {
               $query1 = $query . " (11, 'Mark Tyne', 'mark.tyne@bademail.com', '0', '5', 'mtyne', 2) ";
            }
            $db->setQuery($query1);
            $db->query();
            if ( $super_id == 12 ) {
               $query1 = $query . " (19, 'Jane Kerry', 'jane.kerry@bademail.com', '0', '5', 'jkerry', 6) ";
            } else {
               $query1 = $query . " (12, 'Jane Kerry', 'jane.kerry@bademail.com', '0', '5', 'jkerry', 6) ";
            }
            $db->setQuery($query1);
            $db->query();
            if ( $super_id == 13 ) {
               $query1 = $query . " (19, 'Olive Pope', 'olive.pope@bademail.com', '0', '5','opope', 3) ";
            } else {
               $query1 = $query . " (13, 'Olive Pope', 'olive.pope@bademail.com', '0', '5','opope', 3) ";
            }
            $db->setQuery($query1);
            $db->query();
            if ( $super_id == 14 ) {
               $query1 = $query . " (19, 'Russ Sanders', 'russ.sanders@bademail.com', '0', '5', 'rsanders', 4) ";
            } else {
               $query1 = $query . " (14, 'Russ Sanders', 'russ.sanders@bademail.com', '0', '5', 'rsanders', 4) ";
            }
            $db->setQuery($query1);
            $db->query();
            if ( $super_id == 15 ) {
               $query1 = $query . " (19, 'Tucker Uberton', 'tucker.uberton@bademail.com', '0', '5', 'ruberton', 4) ";
            } else {
               $query1 = $query . " (15, 'Tucker Uberton', 'tucker.uberton@bademail.com', '0', '5', 'ruberton', 4) ";
            }
            $db->setQuery($query1);
            $db->query();
            if ( $super_id == 16 ) {
               $query1 = $query . " (19, 'Vicky Mitchell', 'vicky.mitchell@bademail.com', '0', '5', 'vmitchell', 5) ";
            } else {
               $query1 = $query . " (16, 'Vicky Mitchell', 'vicky.mitchell@bademail.com', '0', '5', 'vmitchell', 5) ";
            }
            $db->setQuery($query1);
            $db->query();
            if ( $super_id == 17 ) {
               $query1 = $query . " (19, 'Scott Tiger', 'scott.tiger@bademail.com', '0', '5', 'stiger', 5)";
            } else {
               $query1 = $query . " (17, 'Scott Tiger', 'scott.tiger@bademail.com', '0', '5', 'stiger', 5)";
            }
            $db->setQuery($query1);
            $db->query();
            if ( $super_id == 18 ) {
               $query1 = $query . " (19, 'John Gilpin', 'john.gilpin@bademail.com', '0', '5', 'jgilpin', 5)";
            } else {
               $query1 = $query . " (18, 'John Gilpin', 'john.gilpin@bademail.com', '0', '5', 'jgilpin', 5)";
            }
            $db->setQuery($query1);
            $db->query();

            // Now add the issues
            $db->setQuery('CALL #__create_sample_issues');
            $res = $db->query();
            // Finally update the issues with our revised person_id.
            $db->setQuery("UPDATE #__it_issues set identified_by_person_id = 19 where identified_by_person_id = ".$super_id);
            $db->query();
            $db->setQuery("UPDATE #__it_issues set assigned_to_person_id = 19 where assigned_to_person_id = ".$super_id);
            $db->query();

            // Now update the staff field in it_people
            $db->setQuery("UPDATE #__it_people SET staff = 1 WHERE user_id IN (SELECT distinct assigned_to_person_id FROM #__it_issues)");
            $db->query();
            return true;
         } else {
            $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_CANNOT_LOAD_SAMPLE_DATA'));
            return false;
         }
      } else {
         $db->setQuery('CALL #__add_it_sample_data');
         $result = $db->query();

         if ( ! $result ) {
            $err = $db->getErrorNum();
            // Check for duplicate key, data already loaded.  Note that if system debug is on then the error is still displayed.
            if ($err == 1062) {
               $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_SAMPLE_DATA_ALREADY_LOADED'));
            } else {
               $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
            }
            return false;
         }

         // Now update the staff field in it_people
         $db->setQuery("UPDATE #__it_people set staff = 1 WHERE user_id IN (SELECT distinct assigned_to_person_id FROM #__it_issues)");
         $db->query();
         return true;
      }

      $db->setQuery('OPTIMIZE TABLE '.$db->nameQuote('#__it_issues'));
      $db->query();
      $db->setQuery('OPTIMIZE TABLE '.$db->nameQuote('#__it_projects'));
      $db->query();
      $db->setQuery('OPTIMIZE TABLE '.$db->nameQuote('#__it_people'));
      $db->query();

      return true;
   }

   public function remsampledata()
   {
      $app = JFactory::getApplication();
      // Get parameters
      $this->_params = & JComponentHelper::getParams( 'com_issuetracker' );
      $defproject = $this->_params->get('def_project', 1);

      $db = $this->getDBO();
      // Check that it_people contains the sample people.  This may include the Super User of course.
      $query = "SELECT COUNT(*) from `#__it_people` where id between 2 AND 18";
      $db->setQuery($query);
      $result = $db->loadResult();

      if ( $result != 17 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_SAMPLEDATA_NOT_INSTALLED'),'error');
         return false;
      }

      // Double Check that it is indeed our sample users in the range.  Should be sufficient to check for three users.
      // If the super user was created with a low id then subsequent users will follow on, and hence if they later load our component and try adding
      // the sample data it will fail, but they may try and remove it, perhaps by accident!
      $query = "SELECT COUNT(*) from `#__it_people` where person_name in ('Thomas Cobley','Peter Davy','John Gilpin') AND id between 2 AND 19";
      $db->setQuery($query);
      $result = $db->loadResult();
      if ( $result != 3 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_SAMPLEDATA_NOT_INSTALLED'),'error');
         return false;
      }

      // Check that the default project is not one of the sample projects.
      if ( $defproject >2 && $defproject < 11 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_DEFPROJECT_ASSIGNMENT'),'error');
         return false;
      }

      // Check if they have created any issues of their own and/or assigned people or issues to any of the samples
      $query = "SELECT COUNT(*) from `#__it_issues` where related_project_id between 2 AND 10 AND id > 28";
      $db->setQuery($query);
      $result = $db->loadResult();

      if ( $result > 0 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_ISSUES_DEFPROJECT_ASSIGNMENT'),'error');
         return false;
      }

      // Check if the Super User id is in our range 2->18.
      $query = "select id from `#__it_people` where person_name = 'Super User'";
      $db->setQuery($query);
      $super_id = $db->loadResult();

      // Check anonymous id as well.
      $query = "select id from `#__it_people` where person_name = 'Anonymous'";
      $db->setQuery($query);
      $anon_id = $db->loadResult();

      $query = "SELECT COUNT(*) from `#__it_people` where assigned_project between 2 AND 6 AND id > ";
      if ( $super_id <= 18 ) {
         $query .= '19 AND id != '.$super_id;
      } else {
         $query .= '18';
      }
      $db->setQuery($query);
      $result = $db->loadResult();
      if ( $result > 0 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_PEOPLE_DEFPROJECT_ASSIGNMENT'),'error');
         return false;
      }

      $query = "SELECT COUNT(*) from `#__it_issues` where identified_by_person_id between 2 AND ";
      if ($super_id <= 18 ) {
         $query .= '19 and id > 28  AND identified_by_person_id != '.$super_id;
      } else {
         $query .= '18 AND id > 28 ';
      }
      $db->setQuery($query);
      $result = $db->loadResult();
      if ( $result > 0 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_ISSUES_IDENTPEOPLE_ASSIGNMENT'),'error');
         return false;
      }

      $query = "SELECT COUNT(*) from `#__it_issues` where assigned_to_person_id between 2 AND ";
      if ($super_id <= 18 ) {
         $query .= '19 and id > 28 AND identified_by_person_id != '.$super_id;
      } else {
         $query .= '18 AND id > 28 ';
      }
      $db->setQuery($query);
      $result = $db->loadResult();
      if ( $result > 0 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_ISSUES_ASSIGNPEOPLE_ASSIGNMENT'),'error');
         return false;
      }

      // Also need to check that the Super user is not within our range.  Joomla 2.5.4 and above change.
      // If it is we have to remove the people ids around it.
      if ($super_id <= 18 ) {
         // Remove the sample data
         $db->setQuery("delete from `#__it_issues` where id < 29");
         $db->query();
         $db->setQuery("delete from `#__it_people` where id >1 AND id < 20 AND id NOT IN (".$super_id.",".$anon_id.")");
         $db->query();
         $db->setQuery("delete from `#__it_projects` where id > 1 AND id < 7");
         $db->query();
      } else {
         $db->setQuery("CALL #__remove_it_sample_data");
         $db->query();
      }

      $db->setQuery('OPTIMIZE TABLE '.$db->nameQuote('#__it_issues'));
      $db->query();
      $db->setQuery('OPTIMIZE TABLE '.$db->nameQuote('#__it_projects'));
      $db->query();
      $db->setQuery('OPTIMIZE TABLE '.$db->nameQuote('#__it_people'));
      $db->query();

      return true;
   }

   public function syncusers()
   {
      $app = JFactory::getApplication();

      // Get parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );
      $defrole = $this->_params->get('def_role', 6);
      $defproject = $this->_params->get('def_project', 1);

      $db = $this->getDBO();
      // $db->setQuery('CALL #__update_it_users');
      $query = "INSERT IGNORE INTO `#__it_people` (user_id, person_name, username, person_email, registered, person_role, assigned_project, created_by, created_on)";
      $query.= "\n   SELECT id, name, username, email, '1', ";
      $query .= "'" .$defrole."','" .$defproject. "','" .$user->username. "', registerDate FROM `#__users`";
      $db->setQuery($query);
      $result = $db->query();

      if ( ! $result ) {
         $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
      }

      $db->setQuery('OPTIMIZE TABLE '.$db->nameQuote('#__it_issues'));
      $db->query();
      $db->setQuery('OPTIMIZE TABLE '.$db->nameQuote('#__it_projects'));
      $db->query();
      $db->setQuery('OPTIMIZE TABLE '.$db->nameQuote('#__it_people'));
      $db->query();
   }
}