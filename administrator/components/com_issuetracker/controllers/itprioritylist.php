<?php
/*
 *
 * @Version       $Id: itprioritylist.php 194 2012-05-02 19:52:10Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.0.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-05-02 20:52:10 +0100 (Wed, 02 May 2012) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Issuetracker Controller
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */

jimport('joomla.application.component.controlleradmin');

class IssuetrackerControllerItprioritylist extends JControllerAdmin
{
   /**
    * Proxy for getModel.
    * @since   1.6
    */
   public function &getModel($name = 'itpriority', $prefix = 'IssuetrackerModel')
   {
      $model = parent::getModel($name, $prefix, array('ignore_request' => true));
      return $model;
   }
}