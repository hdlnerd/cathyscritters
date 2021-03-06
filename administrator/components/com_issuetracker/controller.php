<?php
/*
 *
 * @Version       $Id: controller.php 669 2013-01-04 14:39:25Z geoffc $
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

jimport('joomla.application.component.controller');

/**
 * Issue Tracker Model
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
$jversion = new JVersion();
if( version_compare( $jversion->getShortVersion(), '2.5.6', 'lt' ) ) {
   class IssueTrackerController extends JController
{
   /**
    * Method to display the view
    *
    * @access  public
    */
   public function display ( $cachable=false, $urlparams=false)
   {
      // require_once JPATH_COMPONENT.'/helpers/issuetracker.php';
      require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/issuetracker.php';
      //make sure mootools is loaded
      JHTML::_('behavior.mootools');

      // Load the submenu.
      IssueTrackerHelper::addSubmenu(JRequest::getCmd('view', 'cpanel'));

      $view    = JRequest::getCmd('view', 'cpanel');
      JRequest::setVar('view', $view);

      parent::display();
      return $this;
   }
}
} else {
   class IssueTrackerController extends JControllerLegacy
{
   /**
    * Method to display the view
    *
    * @access  public
    */
   public function display ( $cachable=false, $urlparams=false)
   {
      // require_once JPATH_COMPONENT.'/helpers/issuetracker.php';
      require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/issuetracker.php';
      //make sure mootools is loaded
      JHTML::_('behavior.framework');

      // Load the submenu.
      IssueTrackerHelper::addSubmenu(JRequest::getCmd('view', 'cpanel'));

      $view    = JRequest::getCmd('view', 'cpanel');
      JRequest::setVar('view', $view);

      parent::display();
      return $this;
   }
}
}

// class IssueTrackerController extends JController
// {
   /**
    * Method to display the view
    *
    * @access  public
    */
/*
   public function display ( $cachable=false, $urlparams=false)
   {
      // require_once JPATH_COMPONENT.'/helpers/issuetracker.php';
      require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/issuetracker.php';
      //make sure mootools is loaded
      JHTML::_('behavior.mootools');

      // Load the submenu.
      IssueTrackerHelper::addSubmenu(JRequest::getCmd('view', 'cpanel'));

      $view    = JRequest::getCmd('view', 'cpanel');
      JRequest::setVar('view', $view);

      parent::display();
      return $this;
   }
}
*/