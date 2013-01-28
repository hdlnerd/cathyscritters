<?php
/*
 * Issue Tracker Model for Issue Tracker Component
 *
 * @Version       $Id: itprojects.php 260 2012-06-21 17:41:24Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-06-21 18:41:24 +0100 (Thu, 21 Jun 2012) $
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
