<?php
/*
 *
 * @Version       $Id: issuetracker.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.1
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-01-04 14:39:25 +0000 (Fri, 04 Jan 2013) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Required for Joomla 3.0
if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}
// From Akeeba Live Update
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'liveupdate'.DS.'liveupdate.php';
if(JRequest::getCmd('view','') == 'liveupdate') {
   LiveUpdate::handleRequest();
return;
}

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_issuetracker')){
   return JError::raiseWarning(404, JText::_( 'JERROR_ALERTNOAUTHOR' ));
}

// Include dependancies
jimport('joomla.application.component.controller');
// Require the base controller
// require_once( JPATH_COMPONENT.DS.'controller.php' );

$jversion = new JVersion();
if( version_compare( $jversion->getShortVersion(), '2.5.6', 'lt' ) ) {
   $controller = JController::getInstance('IssueTracker');
} else {
   $controller = JControllerLegacy::getInstance('IssueTracker');
}
$controller->execute( JRequest::getCmd( 'task' ) );
$controller->redirect();
