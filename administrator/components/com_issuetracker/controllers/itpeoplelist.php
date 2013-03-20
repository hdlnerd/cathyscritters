<?php
/*
 *
 * @Version       $Id: itpeoplelist.php 669 2013-01-04 14:39:25Z geoffc $
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
 * Issue Tracker Controller
 *
 * @package       Joomla.Components
 * @subpackage    com_issuetracker
 */
jimport('joomla.application.component.controlleradmin');

class IssueTrackerControllerItpeoplelist extends JControllerAdmin
{
   protected   $option     = 'com_issuetracker';

   public function __construct($config = array())
   {
      parent::__construct($config);

      $this->registerTask('notadministrator',   'administrator');
      $this->registerTask('nonotify',   'notify');
      $this->registerTask('notstaff',   'staff');
   }

   /**
    * Proxy for getModel.
    * @since   1.6
    */
   public function &getModel($name = 'itpeople', $prefix = 'IssuetrackerModel')
   {
      $model = parent::getModel($name, $prefix, array('ignore_request' => true));
      return $model;
   }

   function administrator()
   {
      // Check for request forgeries
      JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

      // Get items to publish from the request.
      $cid  = JRequest::getVar('cid', array(), '', 'array');
      $data = array('administrator' => 1, 'notadministrator' => 0);
      $task    = $this->getTask();
      $value   = JArrayHelper::getValue($data, $task, 0, 'int');

      if (empty($cid)) {
         JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
      } else {
         // Get the model.
         $model = $this->getModel();

         // Make sure the item ids are integers
         JArrayHelper::toInteger($cid);

         // Publish the items.

         if (!$model->administration($cid, $value)) {
            JError::raiseWarning(500, $model->getError());
         } else {
            if ($value == 1) {
               $ntext = $this->text_prefix.'_N_ITEMS_ADMINISTRATOR';
            } else if ($value == 0) {
               $ntext = $this->text_prefix.'_N_ITEMS_NOTADMINISTRATOR';
            }
            $this->setMessage(JText::plural($ntext, count($cid)));
         }
      }

      $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
   }

   function notify()
   {
      // Check for request forgeries
      JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

      // Get items to publish from the request.
      $cid  = JRequest::getVar('cid', array(), '', 'array');
      $data = array('notify' => 1, 'nonotify' => 0);
      $task    = $this->getTask();
      $value   = JArrayHelper::getValue($data, $task, 0, 'int');

      if (empty($cid)) {
         JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
      } else {
         // Get the model.
         $model = $this->getModel();

         // Make sure the item ids are integers
         JArrayHelper::toInteger($cid);

         // Publish the items.

         if (!$model->notify($cid, $value)) {
            JError::raiseWarning(500, $model->getError());
         } else {
            if ($value == 1) {
               $ntext = $this->text_prefix.'_N_ITEMS_NOTIFIED';
            } else if ($value == 0) {
               $ntext = $this->text_prefix.'_N_ITEMS_NOTNOTIFIED';
            }
            $this->setMessage(JText::plural($ntext, count($cid)));
         }
      }

      $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
   }

   function staff()
   {
      // Check for request forgeries
      JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

      // Get items to publish from the request.
      $cid  = JRequest::getVar('cid', array(), '', 'array');
      $data = array('staff' => 1, 'notstaff' => 0);
      $task    = $this->getTask();
      $value   = JArrayHelper::getValue($data, $task, 0, 'int');

      if (empty($cid)) {
         JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
      } else {
         // Get the model.
         $model = $this->getModel();

         // Make sure the item ids are integers
         JArrayHelper::toInteger($cid);

         // Publish the items.

         if (!$model->staff($cid, $value)) {
            JError::raiseWarning(500, $model->getError());
         } else {
            if ($value == 1) {
               $ntext = $this->text_prefix.'_N_ITEMS_STAFF';
            } else if ($value == 0) {
               $ntext = $this->text_prefix.'_N_ITEMS_NOTSTAFF';
            }
            $this->setMessage(JText::plural($ntext, count($cid)));
         }
      }

      $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
   }
}