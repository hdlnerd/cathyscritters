<?php
/*
 *
 * @Version       $Id: dbtasks.php 174 2012-04-24 14:53:57Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.1.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-04-24 15:53:57 +0100 (Tue, 24 Apr 2012) $
 *
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class IssueTrackerControllerDbtasks extends JController
{
   public function __construct($config = array())
   {
      parent::__construct($config);

      $this->modelName = 'dbtasks';

      // Register Extra tasks
      $this->registerTask('addsampledata', 'addsampledata');
      $this->registerTask('remsampledata', 'remsampledata');
      $this->registerTask('syncusers', 'syncusers');
   }

   /**
    * Executes a given controller task. The onBefore<task> and onAfter<task>
    * methods are called automatically if they exist.
    *
    * @param string $task
    * @return null|bool False on execution failure
    */
   public function _execute($task)
   {
/*
      $method_name = 'onBefore'.ucfirst($task);
      if(method_exists($this, $method_name)) {
         $result = $this->$method_name();
         if(!$result) return false;
      }
*/
      // Do not allow the display task to be directly called
      $task = strtolower($task);
      if (isset($this->taskMap[$task])) {
         $doTask = $this->taskMap[$task];
      }
      elseif (isset($this->taskMap['__default'])) {
         $doTask = $this->taskMap['__default'];
      }
      else {
         $doTask = null;
      }
      if($doTask == 'display') {
         JError::raiseError(400, 'Bad Request');
      }

      parent::execute($task);
/*
      $method_name = 'onAfter'.ucfirst($task);
      if(method_exists($this, $method_name)) {
         $result = $this->$method_name();
         if(!$result) return false;
      }
 */
 }

   public function execute($task)
   {
      if(!in_array($task, array('addsampledata','remsampledata','syncusers'))) $task = 'browse';
      $this->_execute($task);
   }

   public function browse()
   {
/*
      $model = $this->getThisModel();
      $from = JRequest::getString('from',null);

      $tables = (array)$model->findTables();
      $lastTable = $model->repairAndOptimise($from);
      if(empty($lastTable))
      {
         $percent = 100;
      }
      else
      {
         $lastTableID = array_search($lastTable, $tables);
         $percent = round(100 * ($lastTableID+1) / count($tables));
         if($percent < 1) $percent = 1;
         if($percent > 100) $percent = 100;
      }

      $this->getThisView()->assign('table',     $lastTable);
      $this->getThisView()->assign('percent',      $percent);

      $model->setState('lasttable', $lastTable);
      $model->setState('percent', $percent);
*/
      // print("Dummy routine");
      $this->display(false);
   }

   public function addsampledata()
   {
      $model = $this->getModel('dbtasks');

      if ($model->addsampledata()) {
         $msg = JText::_( 'COM_ISSUETRACKER_SDATA_ADDED' );
      } else {
         $msg = JText::_( 'COM_ISSUETRACKER_ERROR_ADDING_SAMPLEDATA' );
      }

      // print("Add Sample Data procedure");

      $this->setRedirect('index.php?option=com_issuetracker');
   }

   public function remsampledata()
   {
      $model = $this->getModel('dbtasks');
      // print("Remove sample data procedure  $model");
      if ($model->remsampledata()) {
         $msg = JText::_( 'COM_ISSUETRACKER_SDATA_REMOVED' );
      } else {
         $msg = JText::_( 'COM_ISSUETRACKER_ERROR_REMOVING_SDATA' );
      }
      $this->setRedirect('index.php?option=com_issuetracker');
   }

   public function syncusers()
   {
      $model = $this->getModel('dbtasks');
      // print("Synchronise with Joomla Users procedure  $model");
      $model->syncusers();
      $this->setRedirect('index.php?option=com_issuetracker',JText::_('COM_ISSUETRACKER_SYNCHRONISED'));
   }
}
