<?php
/*
 *
 * @Version       $Id: issuetrackerprojectparent.php 194 2012-05-02 19:52:10Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.1.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-05-02 20:52:10 +0100 (Wed, 02 May 2012) $
 *
 */
defined('_JEXEC') or die('Restricted access');

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

class JFormFieldIssueTrackerProjectParent extends JFormField
{
   protected $type      = 'IssueTrackerProjectParent';

   protected function getInput()
   {
      $tree = array();
      $pid = JRequest::getVar('id',0);
      if ($pid == 0 ) {
         $catID = -1;
      } else {
         $catID = $pid;
      }

      $tree = IssueTrackerHelper::get_filtered_Project_name($catID);

      return JHTML::_('select.genericlist',  $tree,  $this->name, 'class="inputbox"', 'value', 'text', $this->value);
   }
}
?>
