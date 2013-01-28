<?php
/*
 *
 * @Version       $Id: itpeople.php 197 2012-05-04 16:10:32Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.1.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-05-04 17:10:32 +0100 (Fri, 04 May 2012) $
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

class IssueTrackerControllerItpeople extends JControllerForm
{

    function __construct() {
        $this->view_list = 'itpeoplelist';
        parent::__construct();
    }

}