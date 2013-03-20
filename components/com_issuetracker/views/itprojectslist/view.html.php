<?php
/*
 *
 * @Version       $Id: view.html.php 724 2013-02-22 15:53:06Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-22 15:53:06 +0000 (Fri, 22 Feb 2013) $
 *
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Issue Tracker Component
 *
 * @package    Issue Tracker
 * @subpackage Components
 */
class IssueTrackerViewItprojectslist extends JView
{
   protected $print;

   function display($tpl = null){
      $app = JFactory::getApplication();
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
