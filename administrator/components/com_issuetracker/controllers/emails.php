<?php
/*
 *
 * @Version       $Id: emails.php 290 2012-07-16 12:10:59Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-07-16 13:10:59 +0100 (Mon, 16 Jul 2012) $
 *
 */


defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * Issuetracker Controller
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */

class IssueTrackerControllerEmails extends JControllerAdmin
{
   /**
    * Proxy for getModel.
    * @since   1.6
    */

   public function &getModel($name = 'email', $prefix = 'IssuetrackerModel')
   {
      $model = parent::getModel($name, $prefix, array('ignore_request' => true));
      return $model;
   }
}