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
class IssueTrackerViewItissueslist extends JView
{
   protected $print;

   function display($tpl = null){
      $app = JFactory::getApplication();

      // Filter for userid
      $user = JFactory::getUser();
      if (!$user->guest) {
         JRequest::setVar('cuserid', $user->id);
      }

      $this->state      = $this->get('State');

      $params  = $app->getParams();
      $this->assignRef('params'  , $params  );

      $data    = $this->get('Data');
      $this->assignRef('data', $data);

      //Escape strings for HTML output
      $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

      $this->print   = JRequest::getBool('print');

      $pagination    = $this->get('Pagination');
      $this->assignRef('pagination', $pagination);

      $this->sortDirection    = $this->state->get('filter_order_Dir');
      $this->sortColumn       = $this->state->get('filter_order');
      $this->pid              = $this->state->get('project_value');

      $this->_prepareDocument();

      parent::display($tpl);
   }

   /**
   * Prepares the document
   */
   protected function _prepareDocument()
   {
      $app        = JFactory::getApplication();
      $menus      = $app->getMenu();
      $pathway    = $app->getPathway();
      $title      = null;

      // Because the application sets a default page title,
      // we need to get it from the menu item itself
      $menu       = $menus->getActive();

      if ($menu) {
         $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
      } else {
         $this->params->def('page_heading', JText::_('COM_ISSUETRACKER_ISSUES'));
      }

      $id      = (int) @$menu->query['id'];

      $title   = $this->params->get('page_title', '');

      if (empty($title)) {
         $title = $app->getCfg('sitename');
      }
      elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
         $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
      }
      elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
         $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
      }

      $this->document->setTitle($title);

      // Special case for when we are called via the Project menu item link.
      $ppid = JRequest::getVar('pid');
      if ( $ppid) {
         $ntitle = JText::_('COM_ISSUETRACKER_PROJECT_ISSUESLIST_TITLE');
         $pathway->addItem($ntitle, '');
         // Set page title and heading.
         $this->document->setTitle($ntitle);
         $this->params->set('page_heading', $ntitle);
      }
   }
}
?>
