<?php
/*
 *
 * @Version       $Id: view.html.php 234 2012-06-07 17:27:15Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-06-07 18:27:15 +0100 (Thu, 07 Jun 2012) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
//jimport('joomla.html.pane' );

// import Joomla controlleradmin library
//jimport('joomla.application.component.controlleradmin');

class IssueTrackerViewCPanel extends JView
{
   //public $tmpl;
   function display($tpl = null)
   {
      $user  = JFactory::getUser();

      $params = JComponentHelper::getParams( 'com_issuetracker' );
      $this->assignRef('params'  , $params  );

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);
      JToolBarHelper::title("Issue Tracker - " . JText::_('COM_ISSUETRACKER_CPANEL_TITLE'), 'cpanel');

      if($user->authorise('core.admin', 'com_issuetracker')){
         JToolBarHelper::divider();
         JToolBarHelper::preferences('com_issuetracker', '600','800');
      }

      JToolBarHelper::divider();
      JToolBarHelper::help( 'screen.issuetracker', true );

      require_once ( JPATH_COMPONENT.DS.'models'.DS.'itissueslist.php');
      $issuesModel = new IssueTrackerModelItissueslist;

      $latestIssues = $issuesModel->latestIssues( 10);       // get 10 latest issues
      $this->assignRef( 'latestIssues', $latestIssues);

      $overdueIssues = $issuesModel->overdueIssues( 10);     // get 10 worse overdue issues
      $this->assignRef( 'overdueIssues', $overdueIssues);

      if ($params->get('show_summary_rep', 0)) {
         $summaryIssues = $issuesModel->issueSummary();      // get project summary counts
         $this->assignRef( 'summaryIssues', $summaryIssues);
      }

      $unassignedIssues = $issuesModel->unassignedissues();  // get unassigned issues
      $this->assignRef( 'unassignedIssues', $unassignedIssues);

      parent::display($tpl);
   }
}
