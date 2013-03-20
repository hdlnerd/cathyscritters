<?php
/*
 *
 * @Version       $Id: view.html.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.3
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-01-04 14:39:25 +0000 (Fri, 04 Jan 2013) $
 *
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

class IssueTrackerViewDbtasks extends JViewLegacy
{
   function  display($tpl = null)
   {
      // Get the task set in the model
      $model = $this->getModel();
      $task = $model->getState('task','browse');

      switch ($task) {
         case 'addsampledata':
            $model->addsampledata();
            $msg = JText::_( 'COM_ISSUETRACKER_SDATA_ADDED' );
            break;
         case 'remsampledata':
            $model->remsampledata();
            $msg = JText::_( 'COM_ISSUETRACKER_SDATA_REMOVED' );
            break;
         case 'syncusers':
            $model->syncusers();;
            $msg = JText::_( 'COM_ISSUETRACKER_USERS_SYNCHRONISED' );
            break;
      }

      // Shouldn't really do this here, but for the moment it will suffice.
      $app = JFactory::getApplication();
      $app->redirect('index.php?option=com_issuetracker', $msg);
      return;
   }
}