<?php
/*
 *
 * @Version       $Id: default.php 204 2012-05-07 16:40:58Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.1.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-05-07 17:40:58 +0100 (Mon, 07 May 2012) $
 *
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<form class="adminForm">
<p style="margin-left: 4px;">
   <?php echo JText::_('COM_ISSUETRACKER_SUPPORT_INFO'); ?>:
</p>
<table class="adminlist">
   <tr>
      <td style="width: 10px;">
         1.
      </td>
      <td>
         <a href="http://www.macrotoneconsulting.co.uk/index.php/Macrotone/issue-tracker-faq.html" target="_blank"><?php echo JText::_('COM_ISSUETRACKER_FAQS'); ?></a>
      </td>
      <td>
         <?php echo JText::_('COM_ISSUETRACKER_FAQS_INFO'); ?>
      </td>
   </tr>
   <tr>
      <td>
         2.
      </td>
      <td>
         <a href="http://www.macrotoneconsulting.co.uk/index.php/12.html" target="_blank"><?php echo JText::_('COM_ISSUETRACKER_SEARCH_FORUM'); ?></a>
      </td>
      <td>
         <?php echo JText::_('COM_ISSUETRACKER_SEARCH_FORUM_INFO'); ?> 'Issue Tracker Joomla!'
      </td>
   </tr>
   <tr>
      <td>
         3.
      </td>
      <td>
         <a href="http://www.macrotoneconsulting.co.uk/index.php/12.html" target="_blank"><?php echo JText::_('COM_ISSUETRACKER_POST_FORUM'); ?></a>
      </td>
      <td>
         <?php echo JText::_('COM_ISSUETRACKER_POST_FORUM_INFO'); ?> 'Issue Tracker Joomla!'
      </td>
   </tr>
   <tr>
      <td>
         4.
      </td>
      <td>
         <a href="http://www.macrotoneconsulting.co.uk/index.php/Raise-an-Issue.html" target="_blank"><?php echo JText::_('COM_ISSUETRACKER_RAISE_ISSUE'); ?></a>
      </td>
      <td>
         <?php echo JText::_('COM_ISSUETRACKER_RAISE_AN_ISSUE'); ?>
      </td>
   </tr>
   <tr>
      <td>
         5.
      </td>
      <td>
         <a href="http://www.macrotoneconsulting.co.uk/index.php/Support.html" target="_blank"><?php echo JText::_('COM_ISSUETRACKER_CONTACT'); ?></a>
      </td>
      <td>
         <?php echo JText::_('COM_ISSUETRACKER_CONTACT_INFO'); ?>
      </td>
   </tr>
</table>
<br /><br />
<p style="margin-left: 4px;">
<?php echo JText::_('COM_ISSUETRACKER_UPDATE_NOTIFICATIONS'); ?>:
</p>
<table class="adminlist">
   <tr>
      <td style="width: 10px;">
         <img src="components/com_issuetracker/images/16/mail.png" alt="mail" />
      </td>
      <td>
         <a href="http://macrotoneconsulting.co.uk/index.php/Create-an-account.html" target="_blank"><?php echo JText::_('COM_ISSUETRACKER_REGISTER'); ?></a>
      </td>
   </tr>
   <tr>
      <td>
         <img src="components/com_issuetracker/images/16/rss.png" alt="rss" />
      </td>
      <td>
         <a href="http://www.macrotoneconsulting.co.uk/index.php/12/feed.html" target="_blank"><?php echo JText::_('COM_ISSUETRACKER_RSS'); ?></a>
      </td>
   </tr>
</table>
</form>