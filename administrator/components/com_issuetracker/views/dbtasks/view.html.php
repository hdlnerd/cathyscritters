<?php
/*
 *
 * @Version       $Id: view.html.php 74 2012-03-27 16:33:46Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.0.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-03-27 17:33:46 +0100 (Tue, 27 Mar 2012) $
 *
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

class IssueTrackerViewDbtasks extends JView
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
      $app =& JFactory::getApplication();
      $app->redirect('index.php?option=com_issuetracker', $msg);
      return;
   }
}