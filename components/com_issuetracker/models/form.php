<?php
/*
 *
 * @Version       $Id: form.php 385 2012-08-28 15:16:26Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.1.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-28 16:16:26 +0100 (Tue, 28 Aug 2012) $
 *
 */
defined('_JEXEC') or die;

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_issuetracker/models/itissues.php';

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

/**
 * Issue Tracker Component Issue Model
 *
 * @package    Issue Tracker
 * @subpackage com_issuetracker
 * @since 1.5
 */
class IssuetrackerModelForm extends IssuetrackerModelItissues
{
   /**
    * Method to auto-populate the model state.
    *
    * Note. Calling getState in this method will result in recursion.
    *
    * @since   1.6
    */
   protected function populateState()
   {
      $app = JFactory::getApplication();

      // Load state from the request.
      $pk = JRequest::getInt('a_id');
      $this->setState('itissues.id', $pk);

//      $this->setState('itissues.catid', JRequest::getInt('catid'));

      $return = JRequest::getVar('return', null, 'default', 'base64');
      $this->setState('return_page', base64_decode($return));

      // Load the parameters.
      $params  = $app->getParams();
      $this->setState('params', $params);

      $this->setState('layout', JRequest::getCmd('layout'));
   }

   /**
    * Method to get issue data.
    *
    * @param   integer  The id of the issue.
    *
    * @return  mixed Issues item data object on success, false on failure.
    */
   public function getItem($itemId = null)
   {

      // Set up access to default parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );

      // Get default settings
      $wysiwyg = $this->_params->get('wysiwyg_editor', 0);

      // Initialise variables.
      $itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('itissues.id');

      // Get a row instance.
      $table = $this->getTable();

      // Attempt to load the row.
      $return = $table->load($itemId);

      // Check for a table object error.
      if ($return === false && $table->getError()) {
         $this->setError($table->getError());
         return false;
      }

      $properties = $table->getProperties(1);
      $value = JArrayHelper::toObject($properties, 'JObject');

      // Convert attrib field to Registry.
      $value->params = new JRegistry;
      $value->params->loadString($value->params);

      // Compute selected asset permissions.
      $user = JFactory::getUser();
      $userId  = $user->get('id');

      // Technically guest could edit an issue, but lets not check that to improve performance a little.
      if (!$user->get('guest')) {
         $userId  = $user->get('id');
         $asset   = 'com_issuetracker.itissues.'.$value->id;

         $value->params->set('wysiwyg_editor', $wysiwyg);

         // Check general edit permission first.
         if ($user->authorise('core.edit', $asset)) {
            $value->params->set('access-edit', true);
         }
         // Now check if edit.own is available.
         elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
            // Check for a valid user and that they are the owner.
            // Note that the issue created by is a string not an id.
            // if ($userId == $value->created_by) {
            if ($user->username == $value->created_by ) {
               $value->params->set('access-edit', true);
            }
            if ($userId == $value->identified_by_person_id ) {
               $value->params->set('access-edit', true);
            }
         // Now add check if issue admin
         elseif ( IssueTrackerHelper::isIssueAdmin($userId) ) {
            $value->params->set('access-edit', true);
            }
         }
      }

      // Compute view access permissions.
      if ($access = $this->getState('filter.access')) {
         // If the access filter has been set, we already know this user can view.
         $value->params->set('access-view', true);
      } else {
         // If no access filter is set, the layout takes some responsibility for display of limited information.
         $user = JFactory::getUser();
         $groups = $user->getAuthorisedViewLevels();

//         if ($value->catid == 0 || $this->_data->category_access === null) {
//            $value->params->set('access-view', in_array($value->access, $groups));
//         }  else {
//                  $value->params->set('access-view', in_array($value->access, $groups) && in_array($value->category_access, $groups));
//              }
      }


      // Check edit state permission.
      if ($itemId) {
         // Existing item
         $value->params->set('access-change', $user->authorise('core.edit.state', $asset));
      } else {
         // New item.
//         $catId = (int) $this->getState('issue.catid');
//         if ($catId) {
//            $value->params->set('access-change', $user->authorise('core.edit.state', 'com_issuetracker.category.'.$catId));
//         }
//         else {
            $value->params->set('access-change', $user->authorise('core.edit.state', 'com_issuetracker'));
//         }
      }
      return $value;
   }

   /**
    * Get the return URL.
    *
    * @return  string   The return URL.
    * @since   1.6
    */
   public function getReturnPage()
   {
      return base64_encode($this->getState('return_page'));
   }

   /**
    * Get select list for edit form
    *
    */
   public function &getProject_name()
   {
      $db = JFactory::getDBO();

      //build the list of categories
      $query = 'SELECT a.project_name AS text, a.id AS value, a.parent_id as parentid'
      . ' FROM #__it_projects AS a'
      . ' WHERE a.state = 1'
      . ' ORDER BY a.ordering';
      $db->setQuery( $query );
      $data = $db->loadObjectList();

      $catId   = -1;
      $tree = array();
      $text = '';
      $tree = IssueTrackerHelper::ProjectTreeOption($data, $tree, 0, $text, $catId);

      // array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PROJECT').' -', 'value', 'text'));

      return $tree;
   }
}