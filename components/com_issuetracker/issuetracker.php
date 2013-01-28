<?php
/*
 *
 * @Version       $Id: issuetracker.php 67 2012-03-13 18:48:41Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.0.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-03-13 18:48:41 +0000 (Tue, 13 Mar 2012) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// Execute the task.
$controller = JController::getInstance('IssueTracker');
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();

