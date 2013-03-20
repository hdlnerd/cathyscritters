<?php
/*
 *
 * @Version       $Id: view.html.php 729 2013-02-22 18:03:16Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-22 18:03:16 +0000 (Fri, 22 Feb 2013) $
 *
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

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
      $app = JFactory::getApplication();
      $pathway = $app->getPathway();
      $params  = $app->getParams();
      $this->assignRef('params'  , $params  );

      //Escape strings for HTML output
      $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

      $this->print   = JRequest::getBool('print');


      $data = $this->get('Data');
      $this->assignRef('data', $data);

      // Special case capture for title and page heading where called from a link
      // rather than a menu item directly.
      $menus   = $app->getMenu();
      $menu    = $menus->getActive();

      $ntitle = JText::_('COM_ISSUETRACKER_PEOPLE_DETAIL_TITLE');
      if ( strpos($menu->link, 'itpeoplelist') ) {
         $this->document->setTitle($ntitle);
         $params->set('page_heading', $ntitle);
      }

      $pathway->addItem($ntitle, '');

      parent::display($tpl);
   }
}
?>
