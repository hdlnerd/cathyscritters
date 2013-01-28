<?php
/*
 *
 * @Version       $Id: view.html.php 74 2012-03-27 16:33:46Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.0.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-03-27 17:33:46 +0100 (Tue, 27 Mar 2012) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class IssueTrackerViewDocumentation extends JView{

   function display($tpl = null)
   {
      $user  = JFactory::getUser();

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);
      JToolBarHelper::title("Issue Tracker - " . JText::_('COM_ISSUETRACKER_TITLE_DOCUMENTATION'), 'documentation');

      if($user->authorise('core.admin', 'com_issuetracker')){
         JToolBarHelper::preferences('com_issuetracker', '600', '800');
      }

      JToolBarHelper::divider();
      JToolBarHelper::help( 'screen.issuetracker', true );

      parent::display($tpl);
   }
}
?>
