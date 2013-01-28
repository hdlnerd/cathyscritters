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
class IssueTrackerViewItissueslist extends JView
{
   protected $print;

   function display($tpl = null){
      $app =& JFactory::getApplication();

      // Filter for userid
      $user =& JFactory::getUser();
      if (!$user->guest) {
         JRequest::setVar('cuserid', $user->id);
      }

      $params  = $app->getParams();
      $this->assignRef('params'  , $params  );

      $data    = $this->get('Data');
      $this->assignRef('data', $data);

      //Escape strings for HTML output
      $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

      $this->print   = JRequest::getBool('print');

      $pagination    = $this->get('Pagination');
      $this->assignRef('pagination', $pagination);

      $state = $this->get('State');

      $this->sortDirection   = $state->get('filter_order_Dir');
      $this->sortColumn      = $state->get('filter_order');

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
     }
}
?>
