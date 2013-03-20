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

      $data    = $this->get('Item');
      $this->assignRef('data', $data);

      $this->form    = $this->get('Form');
      $this->print   = JRequest::getBool('print');

     if ( $data->id != 0 ) {
         $this->attachment    = $this->check_attachments($data);
      }

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
    * Check if any attachments and get details.
    * This code should be in the model. Move when convenient.
    */

   function check_attachments($data)
   {
      $issue_id = $data->alias;

      $db = JFactory::getDbo();
      $query = "SELECT count(*) FROM `#__it_attachment` WHERE issue_id = '".$issue_id."'";
      $db->setQuery($query);
      $cnt = $db->loadResult();

      if ( $cnt == 0 ) {
         return false;
      } else {
         $query = "SELECT * FROM `#__it_attachment` WHERE issue_id = '".$issue_id."'";
         $db->setQuery($query);
         $attachment = $db->loadObjectList();
         return $attachment;
      }
   }

   /**
    * Prepares the document
    */
   protected function _prepareDocument($data)
   {
      $app        = JFactory::getApplication();
      $menus      = $app->getMenu();
      $pathway    = $app->getPathway();
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

      // Special case to trap situation where we are called from the projects list links.
      if ( strpos($menu->link, 'itprojectslist') ) {
         $ntitle = JText::_('COM_ISSUETRACKER_PROJECT_ISSUEDETAIL_TITLE');
         $this->document->setTitle($ntitle);
         $this->params->set('page_heading', $ntitle);
      }

      $pathway->addItem('Issue '.$data->alias, '');

   }
}
?>
