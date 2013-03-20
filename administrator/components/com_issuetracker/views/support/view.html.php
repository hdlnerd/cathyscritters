<?php
/*
 *
 * @Version       $Id: view.html.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.3
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-01-04 14:39:25 +0000 (Fri, 04 Jan 2013) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class IssueTrackerViewSupport extends JViewLegacy
{
   function display($tpl = null)
   {
      $user  = JFactory::getUser();

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);

      JToolBarHelper::title("Issue Tracker - " . JText::_('COM_ISSUETRACKER_TITLE_SUPPORT'), "systeminfo");

      if($user->authorise('core.admin', 'com_issuetracker')){
         JToolBarHelper::preferences('com_issuetracker', '600', '800');
      }

      JToolBarHelper::divider();
      JToolBarHelper::help( 'screen.issuetracker', true );

      parent::display($tpl);
   }

}
?>
