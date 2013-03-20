<?php
/*
 * Issue Tracker Model for Issue Tracker Component
 *
 * @Version       $Id: itprojects.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-01-04 14:39:25 +0000 (Fri, 04 Jan 2013) $
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}
/**
 * Issue Tracker Model
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerModelItprojects extends JModel{

   /**
    * Itprojects data array for tmp store
    *
    * @var array
    */
   private $_data;

   /**
    * Gets the data
    * @return mixed The data to be displayed to the user
    */
   public function getData(){
      if (empty( $this->_data )){
         $id = JRequest::getInt('id',  0);
         $db = JFactory::getDBO();
         $query = "SELECT * FROM `#__it_projects` where `id` = {$id}";
         $db->setQuery( $query );
         $this->_data = $db->loadObject();
      }
      $this->_data = IssueTrackerHelper::updatepname($this->_data);
      return $this->_data;
   }
}
