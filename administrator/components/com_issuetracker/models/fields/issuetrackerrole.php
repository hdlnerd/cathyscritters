<?php
/*
 *
 * @Version       $Id: issuetrackerrole.php 197 2012-05-04 16:10:32Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.1.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-05-04 17:10:32 +0100 (Fri, 04 May 2012) $
 *
 */
defined('_JEXEC') or die('Restricted access');

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

class JFormFieldIssueTrackerRole extends JFormField
{
   protected $type      = 'IssueTrackerRole';

   protected function getInput()
   {
      $tree = array();
      $tree = IssueTrackerHelper::getRoles();

//      array_unshift($tree, JHTML::_('select.option', '0', JText::_('JALL'), 'value', 'text'));
//      array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_TYPE').' -', 'value', 'text'));
      return JHTML::_('select.genericlist',  $tree,  $this->name, 'class="inputbox"', 'value', 'text', $this->value);
   }
}
?>
