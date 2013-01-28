<?php
/*
 *
 * @Version       $Id: issuetrackerprojectfe.php 309 2012-08-13 10:31:49Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-13 11:31:49 +0100 (Mon, 13 Aug 2012) $
 *
 */
defined('_JEXEC') or die('Restricted access');

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

class JFormFieldIssueTrackerProjectfe extends JFormField
{
   protected $type      = 'IssueTrackerProject';

   protected function getInput() {

      $db = JFactory::getDBO();

       //build the list of projects
      $query = 'SELECT a.project_name AS text, a.id AS value, a.parent_id as parentid'
      . ' FROM #__it_projects AS a'
      . ' WHERE a.state = 1 '
      . ' ORDER BY a.ordering';
      $db->setQuery( $query );
      $data = $db->loadObjectList();

      $catId   = -1;
      $required   = ((string) $this->element['required'] == 'true') ? TRUE : FALSE;

      $tree = array();
      $text = '';
      $tree = IssueTrackerHelper::ProjectTreeOption($data, $tree, 0, $text, $catId);

      // array_unshift($tree, JHTML::_('select.option', '0', JText::_('JALL'), 'value', 'text'));
      array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PROJECT').' -', 'value', 'text'));
      return JHTML::_('select.genericlist',  $tree,  $this->name, 'class="inputbox"', 'value', 'text', $this->value);
      // explode(',', $this->project_ids)); // $this->value, $this->id );
   }
}
?>
