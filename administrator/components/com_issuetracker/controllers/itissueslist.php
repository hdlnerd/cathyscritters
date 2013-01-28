<?php
/*
 *
 * @Version       $Id: itissueslist.php 260 2012-06-21 17:41:24Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-06-21 18:41:24 +0100 (Thu, 21 Jun 2012) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Issue Tracker Controller
 *
 * @package    Joomla.Components
 * @subpackage    com_issuetracker
 */
jimport('joomla.application.component.controlleradmin');

class IssueTrackerControllerItissueslist extends JControllerAdmin
{
   /**
    * Proxy for getModel.
    * @since   1.6
    */
   public function &getModel($name = 'itissues', $prefix = 'IssuetrackerModel')
   {
      $model = parent::getModel($name, $prefix, array('ignore_request' => true));
      return $model;
   }

}