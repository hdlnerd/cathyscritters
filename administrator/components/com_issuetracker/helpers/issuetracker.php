<?php
/*
 *
 * @Version       $Id: issuetracker.php 446 2012-09-10 15:33:37Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.1
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-09-10 16:33:37 +0100 (Mon, 10 Sep 2012) $
 *
 */
defined('_JEXEC') or die;

# Import JMailHelper
jimport('joomla.mail.helper');

/*
 *
 * Issue Tracker helper.
 *
 */
class IssueTrackerHelper
{
   /*
    *
    * addSubmenu
    *
    * Configure the Linkbar.
    */
   public static function addSubmenu($vName = '')
   {
        JSubMenuHelper::addEntry(
         JText::_('COM_ISSUETRACKER_MENU_CPANEL'),
         'index.php?option=com_issuetracker',
         $vName == 'cpanel'
      );
      JSubMenuHelper::addEntry(
         JText::_('COM_ISSUETRACKER_MENU_ISSUES'),
         'index.php?option=com_issuetracker&view=itissueslist',
         $vName == 'issues'
      );
      JSubMenuHelper::addEntry(
         JText::_('COM_ISSUETRACKER_MENU_PEOPLE'),
         'index.php?option=com_issuetracker&view=itpeoplelist',
         $vName == 'people'
      );
      JSubMenuHelper::addEntry(
         JText::_('COM_ISSUETRACKER_MENU_PROJECTS'),
         'index.php?option=com_issuetracker&view=itprojectslist',
         $vName == 'projects'
      );
      JSubMenuHelper::addEntry(
         JText::_('COM_ISSUETRACKER_MENU_PRIORITIES'),
         'index.php?option=com_issuetracker&view=itprioritylist',
         $vName == 'priorities'
      );
      JSubMenuHelper::addEntry(
         JText::_('COM_ISSUETRACKER_MENU_ROLES'),
         'index.php?option=com_issuetracker&view=itroleslist',
         $vName == 'roles'
      );
      JSubMenuHelper::addEntry(
         JText::_('COM_ISSUETRACKER_MENU_STATUSES'),
         'index.php?option=com_issuetracker&view=itstatuslist',
         $vName == 'statuses'
      );
      JSubMenuHelper::addEntry(
         JText::_('COM_ISSUETRACKER_MENU_TYPES'),
         'index.php?option=com_issuetracker&view=ittypeslist',
         $vName == 'types'
      );
      JSubMenuHelper::addEntry(
         JText::_('COM_ISSUETRACKER_MENU_EMAILS'),
         'index.php?option=com_issuetracker&view=emails',
         $vName == 'emails'
      );
/*
      JSubMenuHelper::addEntry(
         JText::_('COM_ISSUETRACKER_MENU_ATTACHMENTS'),
         'index.php?option=com_issuetracker&view=attachments',
         $vName == 'attachments'
      );
*/
      JSubMenuHelper::addEntry(
         JText::_('COM_ISSUETRACKER_MENU_SUPPORT'),
         'index.php?option=com_issuetracker&view=support',
         $vName == 'support'
      );
      JSubMenuHelper::addEntry(
         JText::_('COM_ISSUETRACKER_MENU_DOCUMENTATION'),
         'index.php?option=com_issuetracker&view=documentation',
         $vName == 'documentation'
      );
   }

   // Change to stop Strict Standards message.
   public static function ProjectTreeOption($data, $tree, $id=0, $text='', $currentId)
   {
      foreach ($data as $key) {
         $show_text =  $text . $key->text;

         if ($key->parentid == $id && $currentId != $id && $currentId != $key->value) {
            $tree[$key->value]         = new JObject();
            $tree[$key->value]->text   = $show_text;
            $tree[$key->value]->value  = $key->value;
            $tree = self::ProjectTreeOption($data, $tree, $key->value, $show_text . " - ", $currentId );
         }
      }
      return($tree);
   }

   /*
    *
    * Update Project Name
    * Updates the input array so that the full Project name is display
    * which includes the parent project and sub project names.
    *
    */
   public static function updateprojectname( $rows )
   {
      // This updates an array of arrays
      $db = JFactory::getDBO();
      // Now need to merge in to get the full project name.

      $query = 'SELECT a.project_name AS text, a.id AS value, a.parent_id as parentid'
         . ' FROM #__it_projects AS a';
//      $query .= ' WHERE a.state = 1'
//              . ' ORDER BY a.ordering';
      $db->setQuery( $query );
      $rows2 = $db->loadObjectList();

      $catId   = -1;
      $tree    = array();
      $text    = '';
      $tree    = self::ProjectTreeOption($rows2, $tree, 0, $text, $catId);

      foreach ($rows as $key ) {
         foreach ($tree as $key2) {
            if ($key->project_id == $key2->value) {
               $key->project_name = $key2->text;
               break;    // Exit inner foreach since we have found our match.
            }
         }
      }
      return $rows;
   }

   function updatepname( $row )
   {
      // This updates a single array entry
      $db = JFactory::getDBO();
      // Now need to merge in to get the full project name.

      $query = 'SELECT a.project_name AS text, a.id AS value, a.parent_id as parentid'
         . ' FROM #__it_projects AS a';
//         . ' WHERE a.state = 1'
//         . ' ORDER BY a.ordering';
      $db->setQuery( $query );
      $rows2 = $db->loadObjectList();

      $catId   = -1;
      $tree    = array();
      $text    = '';
      $tree    = self::ProjectTreeOption($rows2, $tree, 0, $text, $catId);

      foreach ($tree as $key2) {
         if ($row->id == $key2->value) {
            $row->project_name = $key2->text;
            break;    // Exit inner foreach since we have found our match.
         }
      }
      return $row;
   }

   /*
    * Given a real user->id  get the associated it_people id.
    *
    */
   public static function get_itpeople_id ( $user_id )
   {

      $db = JFactory::getDBO();
      $query = 'SELECT a.id FROM #__it_people AS a WHERE user_id = '.$user_id;
      $db->setQuery( $query );
      $id = $db->loadResult();

      return $id;
   }

   /*
    * Method to return the project name
    * as a value given a project id.
    * A variation on the above methods.
    *
    */
   function getprojname( $pid )
   {

      $db = JFactory::getDBO();
      // Now need to merge in to get the full project name.

      $query = 'SELECT a.project_name AS text, a.id AS value, a.parent_id as parentid'
         . ' FROM #__it_projects AS a';
      $db->setQuery( $query );
      $rows2 = $db->loadObjectList();

      $catId   = -1;
      $tree    = array();
      $text    = '';
      $tree    = self::ProjectTreeOption($rows2, $tree, 0, $text, $catId);

      foreach ($tree as $key2) {
         if ($pid == $key2->value) {
            return $key2->text;
            break;    // Exit inner foreach since we have found our match.
         }
      }
      return $pid;
   }

   public static function get_filtered_Project_name($catId)
   {
         $db = JFactory::getDBO();

          //build the list of categories
         $query = 'SELECT a.project_name AS text, a.id AS value, a.parent_id as parentid';
         $query .= ' FROM #__it_projects AS a';
         $query .= ' ORDER BY a.ordering';
         $db->setQuery( $query );
         $data = $db->loadObjectList();

         $tree = array();
         $text = '';
         $tree = self::ProjectTreeOption($data, $tree, 0, $text, $catId);

         array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PROJECT').' -', 'value', 'text'));

         return $tree;
   }

   public static function getProject_name()
   {
         $db = JFactory::getDBO();

          //build the list of categories
         $query = 'SELECT a.project_name AS text, a.id AS value, a.parent_id as parentid';
         $query .= ' FROM #__it_projects AS a';
         $query .= ' ORDER BY a.ordering';
         $db->setQuery( $query );
         $data = $db->loadObjectList();

         $catId   = -1;

         $tree = array();
         $text = '';
         $tree = self::ProjectTreeOption($data, $tree, 0, $text, $catId);

         array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PROJECT').' -', 'value', 'text'));

         return $tree;
   }

   public static function getAssigned_name()
   {
      $db = JFactory::getDBO();
      $db->setQuery( 'SELECT `user_id` AS value, `person_name` AS text FROM `#__it_people` ORDER BY user_id');
      $options = array();
      // Add a null value line for those users without assigned projects
      $options[] = JHTML::_('select.option', '', JText::_('COM_ISSUETRACKER_NONE_ASSIGNED') );

      foreach( $db->loadObjectList() as $r){
         $options[] = JHTML::_('select.option',  $r->value, $r->text );
      }
      return $options;
   }

   /*
    * method to check that the default assignee is a staff member.
    *
    * Input is the user id of the assignee to check.
    */
   public static function check_assignee($aid)
   {
      $db = JFactory::getDBO();
      $db->setQuery( 'SELECT count(user_id) FROM `#__it_people` WHERE staff = 1 and user_id = '.$aid);
      $_count = $db->loadResult();

      if ( $_count == 0) {
         return false;
      } else {
         return true;
      }
   }

   public static function getPerson_name()
   {
      $db = JFactory::getDBO();
      $db->setQuery( 'SELECT `id` AS value, `person_name` AS text FROM `#__it_people` ORDER BY id');
      $options = array();
      // Add a null value line for those users without assigned projects
      // $options[] = JHTML::_('select.option', '', JText::_('COM_ISSUETRACKER_NONE_ASSIGNED') );

      foreach( $db->loadObjectList() as $r){
         $options[] = JHTML::_('select.option',  $r->value, $r->text );
      }
      return $options;
   }

   public static function getAssignedPeople()
   {
      $db = JFactory::getDBO();
      $query   = $db->getQuery(true);

      $query  = 'select distinct `assigned_to_person_id` AS value, `person_name` AS text ';
      $query .= 'from `#__it_issues` t1 ';
      $query .= 'left join `#__it_people` t2 on t2.user_id = t1.assigned_to_person_id ';
      $query .= ' where assigned_to_person_id IS NOT NULL ';

      $db->setQuery( $query );
      $options = array();
      $options[] = JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_ASSIGNED').' -' );

      foreach( $db->loadObjectList() as $r){
         $options[] = JHTML::_('select.option',  $r->value, $r->text );
      }
      return $options;
   }

   public static function getIdentifyingPeople()
   {
      $db = JFactory::getDBO();
      $query   = $db->getQuery(true);

      $query  = 'select distinct `identified_by_person_id` AS value, `person_name` AS text ';
      $query .= 'from `#__it_issues` t1 ';
      $query .= 'left join `#__it_people` t2 on t2.id = t1.identified_by_person_id ';
      $query .= ' where identified_by_person_id IS NOT NULL ';

      $db->setQuery( $query );
      $options = array();
      $options[] = JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_IDENTIFIER').' -' );

      foreach( $db->loadObjectList() as $r){
         $options[] = JHTML::_('select.option',  $r->value, $r->text );
      }
      return $options;
   }

   public static function getStatuses()
   {
      $db = JFactory::getDBO();
      $db->setQuery( 'SELECT `id` AS value, `status_name` AS text FROM `#__it_status` WHERE state = 1 ORDER BY id');
      $options = array();
      // Add a null value line for those users without assigned projects
      $options[] = JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_STATUS').' -' );

      foreach( $db->loadObjectList() as $r){
         $options[] = JHTML::_('select.option',  $r->value, $r->text );
      }
      return $options;
   }

   public static function getPriorities()
   {
      $db = JFactory::getDBO();
      $db->setQuery( 'SELECT `id` AS value, `priority_name` AS text FROM `#__it_priority` WHERE state = 1 ORDER BY id');
      $options = array();
      // Add a null value line for those users without assigned projects
      $options[] = JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PRIORITY').' -' );

      foreach( $db->loadObjectList() as $r){
         $options[] = JHTML::_('select.option',  $r->value, $r->text );
      }
      return $options;
   }

   public static function getTypes()
   {
      $db = JFactory::getDBO();
      $db->setQuery( 'SELECT `id` AS value, `type_name` AS text FROM `#__it_types` WHERE state = 1 ORDER BY id');
      $options = array();
      // Add a null value line for those users without assigned projects
      $options[] = JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_TYPE').' -' );

      foreach( $db->loadObjectList() as $r){
         $options[] = JHTML::_('select.option',  $r->value, $r->text );
      }
      return $options;
   }

   public static function getRoles()
   {
      $db = JFactory::getDBO();
      $db->setQuery( 'SELECT `id` AS value, `role_name` AS text FROM `#__it_roles` ORDER BY id');
      $options = array();
      // Add a null value line for those users without assigned projects
      $options[] = JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_ROLE').' -' );

      foreach( $db->loadObjectList() as $r){
         $options[] = JHTML::_('select.option',  $r->value, $r->text );
      }
      return $options;
   }

   /*
    *
    * getActions
    *
    * Gets a list of the actions that can be performed.
    *
    * @return  JObject
    * @since   1.0
    */
   public static function getActions($messageId = 0)
   {
      jimport('joomla.access.access');
      $user = JFactory::getUser();
      $result  = new JObject;

      if (empty($messageId)) {
         $assetName = 'com_issuetracker';
      }
      else {
         $assetName = 'com_issuetracker.itissues.'.(int) $messageId;
      }

      $actions = JAccess::getActions('com_issuetracker', 'component');

      foreach ($actions as $action) {
         $result->set($action->name, $user->authorise($action->name, $assetName));
      }

      return $result;
   }
/*
   public static function getActions()
   {
      $user = JFactory::getUser();
      $result  = new JObject;

      $assetName = 'com_issuetracker';

      $actions = array(
         'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
      );

      foreach ($actions as $action) {
         $result->set($action,   $user->authorise($action, $assetName));
      }
      return $result;
   }
*/
   /*
    *
    * Get a list of filter options for the blocked state of a user.
    *
    * @return  array An array of JHtmlOption elements.
    * @since   1.6
    *
    */
   static function getStateOptions()
   {
      // Build the filter options.
      $options = array();
      $options[]  = JHtml::_('select.option', '0', JText::_('JENABLED'));
      $options[]  = JHtml::_('select.option', '1', JText::_('JDISABLED'));

      return $options;
   }

   public static function getComponentUrl( )
   {
       return 'administrator/components/com_issuetracker';
    }

   /*
    *
    * A blank option for use in select.genericlist
    * @access private
    * @param array An array of objects
    * @return array An array of objects prependes
    *  with a blank row with 'Select..' in text and 0 in value
    */
   public function addBlankRow($arr, $text, $defaultValue = null)
   {
      if(!$defaultValue) $defaultValue="0";
      $blank = new stdClass();
      $blank->value = $defaultValue;
      $blank->text = $text;
      $pre = array($blank);
      return array_merge($pre, $arr);
   }

   /*
    *
    * Obtains component version.
    *
    */
   public function getVersion()
   {
      $db   = JFactory::getDBO();
      $sql = "SELECT version FROM ".$db->quoteName('#__it_meta')." WHERE id='1'";

      $db->setQuery( $sql);
      $version = $db->loadResult();

      if ( !$version) {
         return "0";
      } else {
         return $version;
      }
   }

   /*
    *
    * Determines if user is on line.
    *
    */
   function isUserOnlineById( $id)
   {
      $db   = JFactory::getDBO();

      $sql = 'SELECT count(*) FROM #__session WHERE userid=' . $db->Quote($id) ;

      $db->setQuery( $sql);
      $_count = $db->loadResult();

      if ( $_count == 0) {
         return false;
      } else {
         return true;
      }
   }

   /*
    *
    * Method to determine which messages are to be sent when an issue is created, or changed.
    *
    *  Input:  $data   - array of input record.
    *          $src    - indicator for whether we are being called from the front or back end.
    *          $newrec - Flag to indicate whether we are a new record.  Required since we saved record in db already.
    *
    */
   public function prepare_messages( $data, $src, $newrec )
   {
      // var_dump($data);
       print ("<p>New record: $newrec   Source: $src ");
      $app     = JFactory::getApplication();
      $user    = JFactory::getUser();

      // get settings from com_issuetracker parameters
      $params  = JComponentHelper::getParams('com_issuetracker');

      $notify  = $params->get('email_notify', 0);
      if ($notify == 0) return 0;            // If we are not using notifications just return.

      // Find out who we are sending to.
      $ass_new_notify      = $params->get('send_ass_msg_new', 0);
      $ass_upd_notify      = $params->get('send_ass_msg_update', 0);
      $ass_close_notify    = $params->get('send_ass_msg_close', 0);
      $usr_new_notify      = $params->get('send_user_msg_new', 0);
      $usr_upd_notify      = $params->get('send_user_msg_update', 0);
      $usr_close_notify    = $params->get('send_user_msg_close', 0);
      $adm_new_notify      = $params->get('send_adm_msg_new', 0);
      $adm_upd_notify      = $params->get('send_adm_msg_update', 0);
      $adm_close_notify    = $params->get('send_adm_msg_close', 0);
      $open_status         = $params->get('open_status', '4');
      $closed_status       = $params->get('closed_status', '1');

      $db      = JFactory::getDBO();
      $status  = $data['status'];   // get state of issue  1= closed, 4 = open  anything else is an update.

      // if its a new case, get the real issue id
      if ( $newrec ) {
         $query = "SELECT id FROM #__it_issues WHERE alias = '".$data['alias']."'";
         $db->setQuery($query);
         $nid = $db->loadResult();
         $data['id'] = $nid;            // Update array for send_mail method.
      } else {
        $nid = $data['id'];
      }
      // print ("<p>Real issue id = $nid ");
      // $def_assignee  = $this->_params->get('def_assignee', '');

      // Logic for issue messages follows

      // Notify user and assignee if the issue is new except where it is closed immediately
      // Notify admin unless admin actually opened the issue.
      if ( $newrec ) {
         if ( $status != $closed_status ) {
            if ( $usr_new_notify ) {
               if ( $user->guest ) {
                  // Not a registered user - Parse out the username, email and notify fields we require.
                  $res = preg_match("/Reported By:\s(.*)\sEmail:\s([a-zA-Z\@\.]*)\sNotify:\s([01])([A-Za-z:\s].*)/", $data['progress'], $matches);

                  if ( $res == 0 || empty($matches) ) {
                     // No details in the progress field so must be in our it_people table.
                     // Could get from the database or use the values in the $data array.

                     //$query = "SELECT email_notifications FROM #__it_people WHERE person_name = '".$data['user_details']['name'];
                     //$query .= "' AND person_email = '".$data['user_details']['email']."'";
                     //$notify = $db->setResult($query);

                     $notify = $data['notify'];

                     if ( $notify == 1 ) {
                        self::send_email('user_new', $data['user_details']['email'], $data);   // Notify user
                     }
                  } else {
                     if ( $res == 1 && $matches[3] == 1 ) {
                        self::send_email('user_new', $matches[2], $data);   // Notify user
                     }
                  }
               } else {
                  $query = "SELECT person_email, email_notifications, user_id FROM #__it_people WHERE id = ".$data['identified_by_person_id'];
                  $db->setQuery($query);
                  $usr_email = $db->loadRow();

                  if ( $usr_email[1] == 1 || (array_key_exists('notify',$data) && $data['notify'])  ) {   // User requests notifications.
                     self::send_email('user_new', $usr_email[0], $data);   // Notify user
                  }
               }
            }

            if ( $ass_new_notify && (! empty($data['assigned_to_person_id']) ) ) {
               // Notify Assignee if we didn't open it and assign it to ourselves
               $ident_id = self::get_itpeople_id($data['assigned_to_person_id']);
               if ( $data['identified_by_person_id'] != $ident_id ) {
                  //get assignee details
                  $query = "SELECT person_email FROM #__it_people WHERE user_id = ".$data['assigned_to_person_id'];
                  $db->setQuery($query);
                  $ass_email = $db->loadResult();

                  self::send_email('ass_new',  $ass_email,  $data);      // Notify assignee
               }
            }

            if ( $adm_new_notify) {
               // Do not notify issue administrators IF an issue administrator opened it.
               // $query = "SELECT 1 FROM #__it_people WHERE user_id = ".$user->id." AND issues_admin = 1 ";
               // $db->setQuery($query);
               // $is_admin = $db->loadResult();
               $is_admin = self::isIssueAdmin($user->id);

               if ( $is_admin != 1 ) {
                  // $app->enqueueMessage('Sending new issue message to admin ');
                  self::send_adm_email('admin_new', $data);
               }
            }
         } else if ( $status == $closed_status ) {
            // Issue being closed or is closed.  Treat this as a special case.
            // If admin is closing it do not notify them
            if ( $adm_close_notify ) {
               // $query = "SELECT 1 FROM #__it_people WHERE user_id = ".$user->id." AND issues_admin = 1 ";
               // $db->setQuery($query);
               // $is_admin = $db->loadResult();
               $is_admin = self::isIssueAdmin($user->id);

               if ( $is_admin != 1 ) {
                  self::send_adm_email('admin_close', $data);
               }
            }

            // If user has requested it notify them of closure
            if ( $usr_close_notify ) {
              if ( $user->guest ) {
                   // Not a registered user - Parse out the username, email and notify fields we require.
                  $res = preg_match("/Reported By:\s(.*)\sEmail:\s([a-zA-Z\@\.]*)\sNotify:\s([01])([A-Za-z:\s].*)/", $data['progress'], $matches);

                  if ( $res == 0 || empty($matches) ) {
                     // No details in the progress field so must be in our it_people table.
                     // Could get from the database or use the values in the $data array.

                     //$query = "SELECT email_notifications FROM #__it_people WHERE person_name = '".$data['user_details']['name'];
                     //$query .= "' AND person_email = '".$data['user_details']['email']."'";
                     //$notify = $db->setResult($query);

                     $notify = $data['notify'];

                     if ( $notify == 1 ) {
                        self::send_email('user_new', $data['user_details']['email'], $data);   // Notify user
                     }
                  } else {
                     if ( $res == 1 && $matches[3] == 1 ) {
                        self::send_email('user_new', $matches[2], $data);   // Notify user
                     }
                  }
               } else {
                  $query = "SELECT person_email, email_notifications, user_id FROM #__it_people WHERE id = ".$data['identified_by_person_id'];
                  $db->setQuery($query);
                  $usr_email = $db->loadRow();

                  if ( $usr_email[1] == 1 || (array_key_exists('notify',$data) && $data['notify'])  ) {   // User requests notifications.
                     self::send_email('user_close', $usr_email[0], $data);   // Notify user
                  }
               }
            }

            // If assignee is closing it then do not notify them
            if ( $ass_new_notify && ( ! empty($data['assigned_to_person_id']) ) ) {
               if ( $user->id != $data['assigned_to_person_id'] ) {
                  //get assignee details
                  $query = "SELECT person_email FROM #__it_people WHERE user_id = ".$data['assigned_to_person_id'];
                  $db->setQuery($query);
                  $ass_email = $db->loadResult();
                  self::send_email('ass_close', $ass_email, $data);
               }
            }
         }
      } elseif ( $status == $closed_status ) {
         // Issue being closed or is closed
         // If admin is closing it do not notify them
         if ( $adm_close_notify ) {
            $is_admin = self::isIssueAdmin($user->id);

            if ( $is_admin != 1 ) {
               self::send_adm_email('admin_close', $data);
            }
         }

         // If user has requested it notify them of closure
         if ( $usr_close_notify ) {
            if ( $user->guest ) {
               // Not a registered user - Parse out the username, email and notify fields we require.
               $res = preg_match("/Reported By:\s(.*)\sEmail:\s([a-zA-Z\@\.]*)\sNotify:\s([01])([A-Za-z:\s].*)/", $data['progress'], $matches);

               if ( $res == 0 || empty($matches) ) {
                  // No details in the progress field so must be in our it_people table.
                  // Could get from the database or use the values in the $data array.

                  //$query = "SELECT email_notifications FROM #__it_people WHERE person_name = '".$data['user_details']['name'];
                  //$query .= "' AND person_email = '".$data['user_details']['email']."'";
                  //$notify = $db->setResult($query);

                  $notify = $data['notify'];

                  if ( $notify == 1 ) {
                     self::send_email('user_new', $data['user_details']['email'], $data);   // Notify user
                  }
               } else {
                  if ( $res == 1 && $matches[3] == 1 ) {
                     self::send_email('user_new', $matches[2], $data);   // Notify user
                  }
               }
            } else {
               $query = "SELECT person_email, email_notifications, user_id FROM #__it_people WHERE id = ".$data['identified_by_person_id'];
               $db->setQuery($query);
               $usr_email = $db->loadRow();

               // Allow for future posibility to allow user to close from front end.
               if ( $usr_email[1] == 1 || (array_key_exists('notify',$data) && $data['notify'])  ) {   // User requests notifications.
                  self::send_email('user_close', $usr_email[0], $data);   // Notify user
               }
            }
         }

         // If assignee is closing it then do not notify them
         if ( $ass_new_notify && ( ! empty($data['assigned_to_person_id']) ) ) {
            if ( $user->id != $data['assigned_to_person_id'] ) {
               //get assignee details
               $query = "SELECT person_email FROM #__it_people WHERE user_id = ".$data['assigned_to_person_id'];
               $db->setQuery($query);
               $ass_email = $db->loadResult();
               self::send_email('ass_close', $ass_email, $data);
            }
         }
      } else {
         // On an update, notify all admin users, except if an admin user updated it.
         if ( $adm_upd_notify ) {
            $is_admin = self::isIssueAdmin($user->id);

            if ( $is_admin != 1 ) {
               self::send_adm_email('admin_update', $data);
            }
         }
         // On an update notify the user if requested
         if ( $usr_upd_notify ) {
            if ( $user->guest ) {
               // Not a registered user - Parse out the username, email and notify fields we require.
               $res = preg_match("/Reported By:\s(.*)\sEmail:\s([a-zA-Z\@\.]*)\sNotify:\s([01])([A-Za-z:\s].*)/", $data['progress'], $matches);

               if ( $res == 0 || empty($matches) ) {
                  // No details in the progress field so must be in our it_people table.
                  // Could get from the database or use the values in the $data array.

                  //$query = "SELECT email_notifications FROM #__it_people WHERE person_name = '".$data['user_details']['name'];
                  //$query .= "' AND person_email = '".$data['user_details']['email']."'";
                  //$notify = $db->setResult($query);

                  $notify = $data['notify'];

                  if ( $notify == 1 ) {
                     self::send_email('user_new', $data['user_details']['email'], $data);   // Notify user
                  }
               } else {
                  if ( $res == 1 && $matches[3] == 1 ) {
                     self::send_email('user_new', $matches[2], $data);   // Notify user
                  }
               }
            } else {
               $query = "SELECT person_email, email_notifications, user_id FROM #__it_people WHERE id = ".$data['identified_by_person_id'];
               $db->setQuery($query);
               $usr_email = $db->loadRow();

               if ( $usr_email[1] == 1 || (array_key_exists('notify',$data) && $data['notify'])  ) {   // User requests notifications.
                  self::send_email('user_update', $usr_email[0], $data);   // Notify user
               }
            }
         }

         // Notify assignee of updates and closure except if assignee made the change
         if ( $ass_upd_notify && ( ! empty($data['assigned_to_person_id']) ) ) {
            if ( $user->id != $data['assigned_to_person_id'] ) {
               //get assignee details
               $query = "SELECT person_email FROM #__it_people WHERE user_id = ".$data['assigned_to_person_id'];
               $db->setQuery($query);
               $ass_email = $db->loadResult();
               self::send_email('ass_update', $ass_email, $data);
            }
         }
      }
      return true;
   }


   /*
    *
    * Method to update the strings with substituted values.
    *
    * Note that subject and body are passed by reference so that
    * changes we make in this method apply to the called arrays.
    *
    */
   function update_strings( &$subject, &$body, $data )
   {
      $db         = JFactory::getDBO();

     //set up the front end URL
      $url = JURI::root(); //get site root from Joomla
      if(substr($url, -1) != '/') $url = $url.'/'; //first make sure base URL has '/' at the end
      $urlb      = 'index.php?option=com_issuetracker&view=itissueslist';  //basic URL parms to get itemid
      $url       .= 'index.php?option=com_issuetracker&view=itissues';
      $url       .= '&id='.$data['id']; //problem number
      $menus      = JApplication::getMenu('site',array());
      $menuItem   = $menus->getItems( 'link', $urlb, true );
      $url       .= '&Itemid='.$menuItem->id;              // Add Menu item id
      $url       = '<a href="'.$url.'">'.$url.'</a>';      // Add link tags for email

      $subject    = str_replace('[url]', $url, $subject);
      $body       = str_replace('[url]', $url, $body);

      $subject    = str_replace('[issue_id]', $data['alias'], $subject);
      $body       = str_replace('[issue_id]', $data['alias'], $body);

      $subject    = str_replace('[title]', $data['issue_summary'], $subject);
      $body       = str_replace('[title]', $data['issue_summary'], $body);

      $subject    = str_replace('[description]', $data['issue_description'], $subject);
      $body       = str_replace('[description]', $data['issue_description'], $body);

      if ( array_key_exists ('progress', $data )) {
         $progress = $data['progress'];
      } else {
         $progress = '';
      }

      // Check if we have any user details in the progress field
      // This will contain the user details IF the issue was raised on the front end AND if
      // we are configured not to create users in the it_people table.
      //
      // Also applies if we created issues in the front end
      // in version 1.1.0 as well and it is an existing issue.

      // Parse out the username, email and notify fields we require.
      $res = preg_match("/Reported By:\s(.*)\sEmail:\s([a-zA-Z\@\.]*)\sNotify:\s([01])([A-Za-z:\s].*)/", $progress, $matches);

      if ( $res == 1 ) {
         $username   = $matches[1];
         $email      = $matches[2];

         $subject    = str_replace('[user_name]', $username, $subject);
         $body       = str_replace('[user_name]', $username, $body);

         $subject    = str_replace('[user_email]', $email, $subject);
         $body       = str_replace('[user_email]', $email, $body);

         $subject    = str_replace('[user_fullname]', $username, $subject);
         $body       = str_replace('[user_fullname]', $username, $body);

      } else {
         // Otherwise we assume that the user is recorded and we can get their details from the it_people table.
         $query      = "SELECT person_name, person_email, username FROM #__it_people WHERE id = '".$data['identified_by_person_id']."'";
         $db->setQuery($query);
         $prow       = $db->loadRow();

         $subject    = str_replace('[user_name]', $prow[2], $subject);
         $body       = str_replace('[user_name]', $prow[2], $body);

         $subject    = str_replace('[user_email]', $prow[1], $subject);
         $body       = str_replace('[user_email]', $prow[1], $body);

         $subject    = str_replace('[user_fullname]', $prow[0], $subject);
         $body       = str_replace('[user_fullname]', $prow[0], $body);
      }

      // Unlikely to put progress in email subject field.
//      $subject    = str_replace('[progress]', $progress[0], $subject);
      $body       = str_replace('[progress]', $progress, $body);

      // for the project, get the project id and expand out the full name
      $query      = "SELECT project_name, id FROM #__it_projects WHERE id = ".$data['related_project_id'];
      $db->setQuery($query);
      $project    = $db->loadRow();
      $pname      = self::getprojname( $project[1] );
      if ( $pname != $project[1] )  $project[0] = $pname;

      $subject    = str_replace('[project]', $project[0], $subject);
      $body       = str_replace('[project]', $project[0], $body);

      //for the priority, get the priority name
      $query      = "SELECT priority_name FROM #__it_priority WHERE id = ".$data['priority'];
      $db->setQuery($query);
      $priority   = $db->loadResult();

      $subject    = str_replace('[priority]', $priority, $subject);
      $body       = str_replace('[priority]', $priority, $body);

      //for the status, get the priority name
      $query      = "SELECT status_name FROM #__it_status WHERE id = ".$data['status'];
      $db->setQuery($query);
      $status     = $db->loadResult();

      $subject    = str_replace('[status]', $status, $subject);
      $body       = str_replace('[status]', $status, $body);

      $subject    = str_replace('[startdate]', $data['identified_date'], $subject);
      $body       = str_replace('[startdate]', $data['identified_date'], $body);

      $subject    = str_replace('[closedate]', $data['actual_resolution_date'], $subject);
      $body       = str_replace('[closedate]', $data['actual_resolution_date'], $body);

      // get the assignee details
      $query      = "SELECT person_name, person_email, username FROM #__it_people WHERE user_id = '".$data['assigned_to_person_id']."'";
      $db->setQuery($query);
      $arow       = $db->loadRow();

      if ( empty($arow) ) {
         $subject    = str_replace('[assignee_fullname]', '', $subject);
         $body       = str_replace('[assignee_fullname]', '', $body);

         $subject    = str_replace('[assignee_email]', '', $subject);
         $body       = str_replace('[assignee_email]', '', $body);

         $subject    = str_replace('[assignee_uname]', '', $subject);
         $body       = str_replace('[assignee_uname]', '', $body);
      } else {
         $subject    = str_replace('[assignee_fullname]', $arow[0], $subject);
         $body       = str_replace('[assignee_fullname]', $arow[0], $body);

         $subject    = str_replace('[assignee_email]', $arow[1], $subject);
         $body       = str_replace('[assignee_email]', $arow[1], $body);

         $subject    = str_replace('[assignee_uname]', $arow[2], $subject);
         $body       = str_replace('[assignee_uname]', $arow[2], $body);
      }

      if ( array_key_exists ('resolution_summary', $data )) {
         $subject    = str_replace('[resolution]', $data['resolution_summary'], $subject);
         $body       = str_replace('[resolution]', $data['resolution_summary'], $body);
      } else {
         $subject    = str_replace('[resolution]', '', $subject);
         $body       = str_replace('[resolution]', '', $body);
      }
   }

   /*
    *
    * Generic email sending routine for updating users and assignees
    * about issue status changes.
    *
    */
   function send_email($what, $to, $data)
   {
      $app  = JFactory::getApplication();

      // print ("<p>In send_email $what $to ");

      if ( empty($to) ) {
         // print ("Input to send_email: $what $to <p>");
         // echo "<pre>"; var_dump($data); echo "</pre>";
         $app->enqueueMessage('No email recipients specified ');
         return false;
      }

      // check email address
      if ( !JMailHelper::isEmailAddress( $to)) return false;

      //get the message subject and body
      $query      = "SELECT subject, body FROM #__it_emails WHERE type = '".$what."' AND state = 1";
      $db         = JFactory::getDBO();
      $db->setQuery($query);
      $mdetails   = $db->loadRow();

      // get settings from com_issuetracker parameters
      $params = JComponentHelper::getParams('com_issuetracker');

      $SiteName   = $params->get('emailSiteName', '');
      $from       = $params->get('emailFrom', '');
      $sender     = $params->get('emailSender', '');
      $link       = $params->get('emailLink', '');
      $replyto    = $params->get('emailReplyto', '');
      $replyname  = $params->get('emailReplyname','');

      $subprefix  = $params->get('emailMSGSubject', '');
      $msgprefix  = $params->get('emailMSGMessagePrefix', '');
      $msgpostfix = $params->get('emailMSGMessagePostfix', '');

      // set up base for message
      $subject    = $mdetails[0];
      $body       = $mdetails[1];

      // Update the strings
      self::update_strings ($subject, $body, $data);

      if ($subprefix != "" )
         $subject = $subprefix . ' ' . $subject;

      // $nbody     = sprintf( $msgprefix, $body, $msgpostfix, $SiteName, $sender, $from, $link);
      $nbody     = $msgprefix . $body . $msgpostfix . '<br /><br />' . $sender . '<br />' . $from . '<br />'.  $link;

      // Clean the email data
      $subject = JMailHelper::cleanSubject( $subject);
      $body    = JMailHelper::cleanBody( $nbody);
      $sender  = JMailHelper::cleanAddress( $sender);

      //var_dump($subject);
      //var_dump($body);

      //setup the mailer & create message
      $mail = JFactory::getMailer();
      $mail->isHTML(true);
      $mail->Encoding = 'base64';
      $mail->addRecipient($to);
      //$mail->setSender($sender);
      $mail->setFrom($from,$sender,false);

      if ( !empty($replyto) ) $mail->addReplyTo(array($replyto,$replyname));
      $mail->setSubject($subject);
      $mail->setBody($body);

      if (!$mail->Send()) {
         // echo "<pre>"; var_dump ($mail); echo "</pre>";
         return false;   // if there was trouble, return false for error checking in the caller
      }
   }

   /*
    *
    * Method to send email to issue administrators
    * Similar to send_email method only we may have multiple issue administrators to inform.
    *
    */
   function send_adm_email($what, $data)
   {
      $app  = JFactory::getApplication();

      // print ("<p>In send_adm_email $what ");

      //get the message subject and body
      $query      = "SELECT subject, body FROM #__it_emails WHERE type = '".$what."' AND state = 1 ";
      $db         = JFactory::getDBO();
      $db->setQuery($query);
      $mdetails   = $db->loadRow();

      // get settings from com_issuetracker parameters
      $params = JComponentHelper::getParams('com_issuetracker');

      $SiteName   = $params->get('emailSiteName', '');
      $from       = $params->get('emailFrom', '');
      $sender     = $params->get('emailSender', '');
      $link       = $params->get('emailLink', '');
      $replyto    = $params->get('emailReplyto', '');
      $replyname  = $params->get('emailReplyname','');
      $subprefix  = $params->get('emailADMSubject', '');

      // set up base for message
      $subject    = $mdetails[0];
      $body       = $mdetails[1];

      // Update the strings
      self::update_strings ($subject, $body, $data);

      if ($subprefix != "" )
         $subject = $subprefix . ' ' . $subject;

      // Clean the email data
      $subject = JMailHelper::cleanSubject( $subject);
      $body    = JMailHelper::cleanBody( $body);
      $sender  = JMailHelper::cleanAddress( $sender);

      // var_dump($subject);
      // var_dump($body);

      // get all administrators with email notifications set
      $query = "SELECT p.username, p.person_email FROM ".$db->quoteName( '#__it_people') . " p " .
               " WHERE p.issues_admin = 1 AND p.email_notifications = 1";

      $db->setQuery( $query);
      $_administrator_list = $db->loadAssocList();

      if ( empty($_administrator_list) ) {
         if ( $app->isAdmin() )
            $app->enqueueMessage(JText::_("COM_ISSUETRACKER_WARNING_NO_ISSUE_ADMINISTRATORS"));
         return;
      }

      // For efficiency build up the administrator recipient list so we only send one email.
      $recipient = array();
      reset( $_administrator_list);
      while (list($key, $val) = each( $_administrator_list)) {
         $username = $_administrator_list[$key]['username'];
         $email    = $_administrator_list[$key]['person_email'];
         if ( JMailHelper::isEmailAddress( $email ) ) {
            $recipient[] = $email;
         }
      }

      $mail = JFactory::getMailer();
      $mail->isHTML(true);
      $mail->Encoding = 'base64';
      $mail->addRecipient($recipient);
      if ( !empty($replyto) ) $mail->addReplyTo(array($replyto,$replyname));
      // $mail->setSender($sender);
      $mail->setFrom($from,$sender,false);
      $mail->setSubject($subject);
      $mail->setBody($body);

      if (!$mail->Send()) {
         // echo "<pre>"; var_dump ($mail); echo "</pre>";
         // die ("In send email ");
         return false;   // if there was trouble, return false for error checking in the caller
      }
   }


   function getUsernameById($id)
   {
      $db   = JFactory::getDBO();
      $sql = "SELECT username FROM ".$db->quoteName('#__users')." WHERE id=" . $db->Quote($id);

      $db->setQuery( $sql);
      $username = $db->loadResult();

      if ( !$username) {
         return "-";
      }
      else {
         return $username;
      }
   }

   /*
    *
    * Method to determine whether the user is an issue administrator
    *
    */
   public static function isIssueAdmin($id)
   {
      $db   = JFactory::getDBO();
      $sql  = "SELECT issues_admin FROM ".$db->quoteName('#__it_people')." WHERE user_id=" . $db->Quote($id);

      $db->setQuery( $sql);
      $isadmin = $db->loadResult();

      return $isadmin;
   }

   /*
    *
    * Method to determine whether email notifications are required
    *
    */
   function EmailNotify($id)
   {
      $db   = JFactory::getDBO();
      $sql  = "SELECT email_notifications FROM ".$db->quoteName('#__it_people')." WHERE user_id=" . $db->Quote($id);

      $db->setQuery( $sql);
      $notify = $db->loadResult();

      return $notify;
   }


   /*
    *
    * Applies the content tag filters to arbitrary text as per settings for current user group
    * @param text The string to filter
    * @return string The filtered string
    *
    */
   public static function filterText($text)
   {
      // Filter settings
      jimport('joomla.application.component.helper');
      $config     = JComponentHelper::getParams('com_issuetracker');
      $user       = JFactory::getUser();
      $userGroups = JAccess::getGroupsByUser($user->get('id'));

      $filters = $config->get('filters');

      $blackListTags       = array();
      $blackListAttributes = array();

      $whiteListTags       = array();
      $whiteListAttributes = array();

      $noHtml     = false;
      $whiteList  = false;
      $blackList  = false;
      $unfiltered = false;

      // Cycle through each of the user groups the user is in.
      // Remember they are include in the Public group as well.
      foreach ($userGroups AS $groupId)
      {
         // May have added a group by not saved the filters.
         if (!isset($filters->$groupId)) {
            continue;
         }

         // Each group the user is in could have different filtering properties.
         $filterData = $filters->$groupId;
         $filterType = strtoupper($filterData->filter_type);

         if ($filterType == 'NH') {
            // Maximum HTML filtering.
            $noHtml = true;
         }
         else if ($filterType == 'NONE') {
            // No HTML filtering.
            $unfiltered = true;
         }
         else {
            // Black or white list.
            // Preprocess the tags and attributes.
            $tags             = explode(',', $filterData->filter_tags);
            $attributes       = explode(',', $filterData->filter_attributes);
            $tempTags         = array();
            $tempAttributes   = array();

            foreach ($tags AS $tag)
            {
               $tag = trim($tag);

               if ($tag) {
                  $tempTags[] = $tag;
               }
            }

            foreach ($attributes AS $attribute)
            {
               $attribute = trim($attribute);

               if ($attribute) {
                  $tempAttributes[] = $attribute;
               }
            }

            // Collect the black or white list tags and attributes.
            // Each list is cummulative.
            if ($filterType == 'BL') {
               $blackList           = true;
               $blackListTags       = array_merge($blackListTags, $tempTags);
               $blackListAttributes = array_merge($blackListAttributes, $tempAttributes);
            }
            else if ($filterType == 'WL') {
               $whiteList           = true;
               $whiteListTags       = array_merge($whiteListTags, $tempTags);
               $whiteListAttributes = array_merge($whiteListAttributes, $tempAttributes);
            }
         }
      }

      // Remove duplicates before processing (because the black list uses both sets of arrays).
      $blackListTags       = array_unique($blackListTags);
      $blackListAttributes = array_unique($blackListAttributes);
      $whiteListTags       = array_unique($whiteListTags);
      $whiteListAttributes = array_unique($whiteListAttributes);

      // Unfiltered assumes first priority.
      if ($unfiltered) {
         // Dont apply filtering.
      }
      else {
         // Black lists take second precedence.
         if ($blackList) {
            // Remove the white-listed attributes from the black-list.
            $filter = JFilterInput::getInstance(
               array_diff($blackListTags, $whiteListTags),        // blacklisted tags
               array_diff($blackListAttributes, $whiteListAttributes), // blacklisted attributes
               1,                                        // blacklist tags
               1                                         // blacklist attributes
            );
         }
         // White lists take third precedence.
         else if ($whiteList) {
            $filter  = JFilterInput::getInstance($whiteListTags, $whiteListAttributes, 0, 0, 0);  // turn off xss auto clean
         }
         // No HTML takes last place.
         else {
            $filter = JFilterInput::getInstance();
         }
         $text = $filter->clean($text, 'html');
      }
      return $text;
    }

   /*
    *
    * Method used by back end attachments
    *
    */
   function getManagerGroup($manager)
   {
      $group = array();
      switch ($manager) {
         case 'icon':
         case 'iconspec1':
         case 'iconspec2':
            $group['f'] = 2;//File
            $group['i'] = 1;//Image
            $group['t'] = 'icon';//Text
            $group['c'] = '&amp;tmpl=component';
         break;

         case 'image':
            $group['f'] = 2;//File
            $group['i'] = 1;//Image
            $group['t'] = 'image';//Text
            $group['c'] = '&amp;tmpl=component';
         break;

         case 'filepreview':
            $group['f'] = 3;
            $group['i'] = 1;
            $group['t'] = 'filename';
            $group['c'] = '&amp;tmpl=component';
         break;

         case 'fileplay':
            $group['f'] = 3;
            $group['i'] = 0;
            $group['t'] = 'filename';
            $group['c'] = '&amp;tmpl=component';
         break;

         case 'filemultiple':
            $group['f'] = 1;
            $group['i'] = 0;
            $group['t'] = 'filename';
            $group['c'] = '';
         break;

         case 'file':
         default:
            $group['f'] = 1;
            $group['i'] = 0;
            $group['t'] = 'filename';
            $group['c'] = '&amp;tmpl=component';
         break;
      }
      return $group;
   }
}
