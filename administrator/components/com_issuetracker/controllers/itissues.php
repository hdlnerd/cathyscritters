<?php
/*
 *
 * @Version       $Id: itissues.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.1.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-01-04 14:39:25 +0000 (Fri, 04 Jan 2013) $
 *
 */
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Issue Tracker Controller
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
jimport('joomla.application.component.controllerform');

/**
 * Issue controller class.
 */
class IssueTrackerControllerItissues extends JControllerForm
{

    function __construct() {
        $this->view_list = 'itissueslist';
        parent::__construct();
    }
}