<?php
/*
 *
 * @Version       $Id: helper.php 406 2012-09-04 11:42:35Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.1
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-09-04 12:42:35 +0100 (Tue, 04 Sep 2012) $
 *
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}
class IssueTrackerHelperSite
{
   public static function getConfig()
   {
      static $config = null;

      if( is_null( $config ) )
      {
         //load default ini data first
         $ini     = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_isssuetracker' . DS . 'configuration.ini';
         $raw     = JFile::read($ini);
         $config  = new JParameter($raw);

         //get config stored in db
         JTable::addIncludePath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_issuetracker' . DS . 'tables' );
         $dbConfig   = IssueTrackerHelperSite::getTable( 'configs' , 'IssueTrackerTable' );
         $dbConfig->load( 'config' );

         $config->bind( $dbConfig->params , 'INI' );
      }
      return $config;
   }


   /**
    * Retrieve JTable objects.
    *
    * @param   string   $tableName  The table name.
    * @param   string   $prefix     JTable prefix.
    * @return  object   JTable object.
    **/
   public static function getTable( $tableName , $prefix = 'IssueTrackerTable' )
   {
      JTable::addIncludePath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_issuetracker' . DS . 'tables' );

      $tbl    = JTable::getInstance( $tableName , $prefix );
      return $tbl;
   }

   public static function getProject_name()
   {
         $db = JFactory::getDBO();

          //build the list of categories
         $query = 'SELECT a.project_name AS text, a.id AS value, a.parent_id as parentid';
         $query .= ' FROM #__it_projects AS a';
         $query .= ' WHERE a.state = 1';
         $query .= ' ORDER BY a.ordering';
         $db->setQuery( $query );
         $data = $db->loadObjectList();

         $catId   = -1;

         $tree = array();
         $text = '';
         $tree = IssueTrackerHelper::ProjectTreeOption($data, $tree, 0, $text, $catId);

         array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PROJECT').' -', 'value', 'text'));

         return $tree;
   }

   public static function getTypes()
   {
      $db = JFactory::getDBO();
      $db->setQuery( 'SELECT `id` AS value, `type_name` AS text FROM `#__it_types` ORDER BY id');
      $options = array();
      // Add a null value line for those users without assigned projects
      $options[] = JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_TYPE').' -' );

      foreach( $db->loadObjectList() as $r){
         $options[] = JHTML::_('select.option',  $r->value, $r->text );
      }
      return $options;
   }

   public static function getUserdefproj($id)
   {
      $db   = JFactory::getDBO();
      $sql = "SELECT assigned_project FROM ".$db->nameQuote('#__it_people')." WHERE user_id=" . $db->Quote($id);

      $db->setQuery( $sql);
      $projid = $db->loadResult();

      return $projid;
   }

   public static function isIssueAdmin($id)
   {
      $db   = JFactory::getDBO();
      $sql = "SELECT issues_admin FROM ".$db->nameQuote('#__it_people')." WHERE user_id=" . $db->Quote($id);

      $db->setQuery( $sql);
      $isadmin = $db->loadResult();

      return $isadmin;
   }

}
