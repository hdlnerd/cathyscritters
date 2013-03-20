<?php
/*
 *
 * @Version       $Id: issuetrackerprojectfe.php 719 2013-02-20 17:22:47Z geoffc $
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

class JFormFieldIssueTrackerProjectfe extends JFormField
{
   protected $type   = 'IssueTrackerProjectfe';

   protected function getInput()
   {

      $user = JFactory::getUser();
      if ( IssueTrackerHelper::isIssueAdmin($user->id) ) {
         $isadmin = 1;
      } else {
         $isadmin = 0;
      }

      if ( $isadmin == 0 ) {
         // Get the Menu parameters to determine which projects have been selected.
         // Unless we are a Issue Administrator since we may be editing the issue.
         $minput = JFactory::getApplication()->input;
         $menuitemid = $minput->getInt( 'Itemid' );  // this returns the menu id number so we can reference parameters
         // $menu = JSite::getMenu();
         $menu = JFactory::getApplication()->getMenu();
         if ($menuitemid) {
            $menuparams = $menu->getParams( $menuitemid );
            $projects = $menuparams->get('projects');
         }
      }

      $db = JFactory::getDBO();

       // Build the list of projects.  Cannot filter in the query since we need to expand out the full project name.
       // Do not get root node.
      $query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid';
      $query .= ' FROM #__it_projects AS a';
      $query .= ' WHERE a.state = 1 ';
      $query .= ' ORDER BY a.lft';
      $db->setQuery( $query );
      $data = $db->loadObjectList();

      $catId   = -1;
      $required   = ((string) $this->element['required'] == 'true') ? TRUE : FALSE;

      $tree = array();
      $text = '';
      $tree = IssueTrackerHelper::ProjectTreeOption($data, $tree, 0, $text, $catId);

      // Now filter out the rows we do not want.
      if ( $isadmin == 0 && ! empty($projects) && $projects[0] != 0 )
         $tree = $this->array_keep($tree, $projects);

      if (count($tree) > 1)
         array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PROJECT').' -', 'value', 'text'));
      return JHTML::_('select.genericlist',  $tree,  $this->name, 'class="inputbox"', 'value', 'text', $this->value);
   }

   /*
    * Function to filter the project tree retaining only the projects we desire.
    */
   function array_keep($array, $projects)
   {
      if ( empty($projects) || $projects[0] == 0 ) return $array;

      $thisarray = array ();
      foreach($array as $key) {
      $k = $key->value;
      foreach ( $projects as $item)
         if ( $k == $item)
            $thisarray[] = $key;
      }
      return $thisarray;
   }
}
?>
