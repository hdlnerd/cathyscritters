<?php
/*
 *
 * @Version       $Id: view.html.php 322 2012-08-20 13:14:58Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-20 14:14:58 +0100 (Mon, 20 Aug 2012) $
 *
 */

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Issue Tracker Component
 *
 * @package Joomla.Components
 * @subpackage Issue Tracker
 */
class IssueTrackerViewItissues extends JView
{
   protected $form;
   protected $print;
   protected $state;

   function display($tpl = null)
   {
      $app     = JFactory::getApplication();

      // Get model data.
      $state = $this->get('State');
      // $item = $this->get('Item');

      // $this->get('Data');
      $data    = $this->get('Item');
      $this->assignRef('data', $data);

      $this->form    = $this->get('Form');
      $this->print   = JRequest::getBool('print');

      // Create a shortcut to the parameters.
      $params  = $app->getParams();
      $this->assignRef('params' , $params );
      $this->params = $params;

      //Escape strings for HTML output
      $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

      // Check if an issue was found.
      if (!$data->id) {
         $previousurl = $_SERVER['HTTP_REFERER'];
         $msg = JText::_('COM_ISSUETRACKER_ISSUE_NOT_FOUND');
         $app->redirect($previousurl, $msg);
      }

      $this->_prepareDocument($data);
      parent::display($tpl);
   }

   /**
    * Prepares the document
    */
   protected function _prepareDocument($data)
   {
      $app        = JFactory::getApplication();
      $menus      = $app->getMenu();
//      $pathway    = $app->getPathway();
      $title      = null;

      // Because the application sets a default page title,
      // we need to get it from the menu item itself
      $menu = $menus->getActive();
      if ($menu) {
         $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
      }

      $title = $this->params->def('page_title', JText::_('COM_ISSUETRACKER_FORM_ISSUE'));
      if ($app->getCfg('sitename_pagetitles', 0) == 1) {
         $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);

      } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
         $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
      }
      $this->document->setTitle($title);

      $pathway = $app->getPathWay();
      $pathway->addItem('Issue '.$data->alias, '');

   }
}
?>
