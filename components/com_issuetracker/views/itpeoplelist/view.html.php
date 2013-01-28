<?php
/*
 *
 * @Version       $Id: view.html.php 393 2012-08-29 15:19:43Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.1
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-29 16:19:43 +0100 (Wed, 29 Aug 2012) $
 *
 */

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Issue Tracker Component
 *
 * @package    Issue Tracker
 * @subpackage Components
 */
class IssueTrackerViewItpeoplelist extends JView
{
   protected $print;

   function display($tpl = null){
      $app =& JFactory::getApplication();
      /*
      $params =& JComponentHelper::getParams( 'com_issuetracker' );
      $params =& $app->getParams( 'com_issuetracker' );
      $dummy = $params->get( 'dummy_param', 1 );
      */
      $params  = $app->getParams();
      $this->assignRef('params'  , $params  );

      //Escape strings for HTML output
      $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

      $this->print   = JRequest::getBool('print');

      $data =& $this->get('Data');
      $this->assignRef('data', $data);

      $pagination =& $this->get('Pagination');
      $this->assignRef('pagination', $pagination);

      $state = $this->get('State');

      $this->sortDirection   = $state->get('filter_order_Dir');
      $this->sortColumn      = $state->get('filter_order');

      parent::display($tpl);
   }
}
?>
