<?php
/*
 *
 * @Version       $Id: view.html.php 158 2012-04-20 13:25:48Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.1.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-04-20 14:25:48 +0100 (Fri, 20 Apr 2012) $
 *
 */

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Issue Tracker Component
 *
 * @package    Joomla.Components
 * @subpackage Issue Tracker
 */
class IssueTrackerViewItpeople extends JView
{
   protected $print;

   function display($tpl = null)
   {
      $app =& JFactory::getApplication();

      $params  = $app->getParams();
      $this->assignRef('params'  , $params  );

      //Escape strings for HTML output
      $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

      $this->print   = JRequest::getBool('print');


      $data = $this->get('Data');
      $this->assignRef('data', $data);

      parent::display($tpl);
   }
}
?>
