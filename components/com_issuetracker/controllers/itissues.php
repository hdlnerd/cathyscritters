<?php
/*
 *
 * @Version       $Id: itissues.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.1
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-01-04 14:39:25 +0000 (Fri, 04 Jan 2013) $
 *
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * @package    Joomla.Site
 * @subpackage com_issuetracker
 */
class IssuetrackerControllerItissues extends JControllerForm
{
   /**
    * @since   1.6
    */
   protected $view_item = 'form';
   protected $view_list = 'itissueslist';

   /**
    * Method to add a new record.
    *
    * @return  boolean  True if the issue can be added, false if not.
    * @since   1.6
    */
   public function add()
   {
      if (!parent::add()) {
         // Redirect to the return page.
         $this->setRedirect($this->getReturnPage());
      }
   }

   /**
    * Method override to check if you can add a new record.
    *
    * @param   array An array of input data.
    *
    * @return  boolean
    * @since   1.6
    */
   protected function allowAdd($data = array())
   {
      // Initialise variables.
      $user    = JFactory::getUser();
      //  $categoryId = JArrayHelper::getValue($data, 'catid', JRequest::getInt('catid'), 'int');
      $allow      = null;
/*
      if ($categoryId) {
         // If the category has been passed in the data or URL check it.
         $allow   = $user->authorise('core.create', 'com_issuetracker.category.'.$categoryId);
      }
*/
      if ($allow === null) {
         // In the absense of better information, revert to the component permissions.
         return parent::allowAdd();
      }
      else {
         return $allow;
      }
   }

   /**
    * Method override to check if you can edit an existing record.
    *
    * @param   array $data An array of input data.
    * @param   string   $key  The name of the key for the primary key.
    *
    * @return  boolean
    * @since   1.6
    *
    * Issues administrator can edit any issue.
    */
   protected function allowEdit($data = array(), $key = 'id')
   {
      // Initialise variables.
      $recordId   = (int) isset($data[$key]) ? $data[$key] : 0;
      $user       = JFactory::getUser();
      $userId     = $user->get('id');
      $asset      = 'com_issuetracker.itissues.'.$recordId;

      // Check general edit permission first.
      if ($user->authorise('core.edit', $asset)) {
         return true;
      }

      // Check if issues admin
      if ( $this->issue_admin( $user->id) ) return true;

      // Fallback on edit.own.
      // First test if the permission is available.
      if ($user->authorise('core.edit.own', $asset)) {
         // Now test the owner is the user.   Note that our created_by is a name not an id.
           $ownerId = '';
//         $ownerId = $data['identified_by_person_id'];
//         $ownerId = (int) isset($data['identified_by_person_id']) ? $data['identified_by_person_id'] : 0;
//       if (empty($ownerId) && $recordId) {
         if ( $recordId) {
            // Need to do a lookup from the model.
            $record     = $this->getModel()->getItem($recordId);

            if (empty($record)) {
               return false;
            }

            $ownerId = $this->getuserid($record->created_by);
            $identby = $record->identified_by_person_id;
            $person_id = $this->getpersonid($userId);
         }

         // If the creator or identified by user matches current user then do the test.
         if ( $ownerId == $userId || $identby == $person_id ) {
            return true;
         }
      }

      // Check if Joomla admin
      $app = JFactory::getApplication();
      if ( $app->isAdmin() || JDEBUG ) { return true; }

      // Since there is no asset tracking, revert to the component permissions.
      return parent::allowEdit($data, $key);
   }

   /**
    * Method to check if an issue administrator
    *
    */
   public function issue_admin ($id = null)
   {
      // Check it_people table to see if this user is an issue administrator
      $isadmin = 0;

      $db = JFactory::getDBO();
      $query = 'SELECT issues_admin FROM #__it_people WHERE user_id = '.$id;
      $db->setQuery($query);
      $isadmin = $db->loadResult();

      return $isadmin;
   }

   /**
    * Method to return the id of a specified user id
    *
    */
   public function getuserid ($name = null)
   {
      $uid = 0;

      $db = JFactory::getDBO();
      $query = "SELECT user_id FROM #__it_people WHERE username = '".$name."'";
      $db->setQuery($query);
      $uid = $db->loadResult();

      if ( empty($uid) ) $uid = 0;

      return $uid;
   }

   /**
    * Method to return the id of a specified user id
    *
    */
   public function getpersonid ($uid = null)
   {
      $pid = 0;

      $db = JFactory::getDBO();
      $query = "SELECT id FROM #__it_people WHERE user_id = '".$uid."'";
      $db->setQuery($query);
      $uid = $db->loadResult();

      if ( empty($pid) ) $pid = 0;

      return $pid;
   }


   /**
    * Method to cancel an edit.
    *
    * @param   string   $key  The name of the primary key of the URL variable.
    *
    * @return  Boolean  True if access level checks pass, false otherwise.
    * @since   1.6
    */
   public function cancel($key = 'a_id')
   {
      parent::cancel($key);

      // Redirect to the return page.
      $this->setRedirect($this->getReturnPage());
   }

   /**
    * Method to edit an existing record.
    *
    * @param   string   $key  The name of the primary key of the URL variable.
    * @param   string   $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
    *
    * @return  Boolean  True if access level check and checkout passes, false otherwise.
    * @since   1.6
    */
   public function edit($key = null, $urlVar = 'a_id')
   {
      $result = parent::edit($key, $urlVar);

      return $result;
   }

   /**
    * Method to get a model object, loading it if required.
    *
    * @param   string   $name The model name. Optional.
    * @param   string   $prefix  The class prefix. Optional.
    * @param   array $config  Configuration array for model. Optional.
    *
    * @return  object   The model.
    * @since   1.5
    */
   public function &getModel($name = 'form', $prefix = '', $config = array('ignore_request' => true))
   {
      $model = parent::getModel($name, $prefix, $config);

      return $model;
   }

   /**
    * Gets the URL arguments to append to an item redirect.
    *
    * @param   int      $recordId   The primary key id for the item.
    * @param   string   $urlVar     The name of the URL variable for the id.
    *
    * @return  string   The arguments to append to the redirect URL.
    * @since   1.6
    */
   protected function getRedirectToItemAppend($recordId = null, $urlVar = 'a_id')
   {
      // Need to override the parent method completely.
      $tmpl    = JRequest::getCmd('tmpl');
      $layout     = JRequest::getCmd('layout', 'edit');
      $append     = '';

      // Setup redirect info.
      if ($tmpl) {
         $append .= '&tmpl='.$tmpl;
      }

      // TODO This is a bandaid, not a long term solution.
//    if ($layout) {
//       $append .= '&layout='.$layout;
//    }
      $append .= '&layout=edit';

      if ($recordId) {
         $append .= '&'.$urlVar.'='.$recordId;
      }

      $itemId  = JRequest::getInt('Itemid');
      $return  = $this->getReturnPage();

      if ($itemId) {
         $append .= '&Itemid='.$itemId;
      }

      if ($return) {
         $append .= '&return='.base64_encode($return);
      }

      return $append;
   }

   /**
    * Get the return URL.
    *
    * If a "return" variable has been passed in the request
    *
    * @return  string   The return URL.
    * @since   1.6
    */
   protected function getReturnPage()
   {
      $return = JRequest::getVar('return', null, 'default', 'base64');

      if (empty($return) || !JUri::isInternal(base64_decode($return))) {
         return JURI::base();
      }
      else {
         return base64_decode($return);
      }
   }

   /**
    * Function that allows child controller access to model data after the data has been saved.
    *
    * @param   JModel   $model      The data model object.
    * @param   array $validData  The validated data.
    *
    * @return  void
    * @since   1.6
    */
   protected function postSaveHook(& $model, $validData)
   {
      $task = $this->getTask();

      if ($task == 'save') {
//         $this->setRedirect(JRoute::_('index.php?option=com_issuetracker&view=category&id='.$validData['catid'], false));
         $this->setRedirect(JRoute::_('index.php?option=com_issuetracker&view=itissues&id='.$validData['id'], false));
      }
   }

   /**
    * Method to save a record.
    *
    * @param   string   $key  The name of the primary key of the URL variable.
    * @param   string   $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
    *
    * @return  Boolean  True if successful, false otherwise.
    * @since   1.6
    */
   public function save($key = null, $urlVar = 'a_id')
   {
      // Load the backend helper for filtering.
      require_once JPATH_ADMINISTRATOR.'/components/com_issuetracker/helpers/issuetracker.php';

      $model = $this->getModel('itissues');

      $result = $model->store();
      if ($result) {
          $msg = JText::_( 'COM_ISSUETRACKER_ISSUE_SAVED_MSG' );
      } else {
          $msg = JText::_( 'COM_ISSUETRACKER_ISSUE_SAVING_ERROR_MSG' );
      }

      // If ok, redirect to the return page.
      // if ($result) {
         $this->setRedirect($this->getReturnPage(), $msg);
      // }

      return $result;
   }
}