<?php
/*
 *
 * @Version       $Id: issuetrackerprojectparent.php 719 2013-02-20 17:22:47Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-20 17:22:47 +0000 (Wed, 20 Feb 2013) $
 *
 */
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}

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
