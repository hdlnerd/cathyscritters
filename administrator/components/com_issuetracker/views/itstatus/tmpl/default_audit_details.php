<?php
/*
 *
 * @Version       $Id: default_audit_details.php 191 2012-04-28 19:58:13Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.0.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-04-28 20:58:13 +0100 (Sat, 28 Apr 2012) $
 *
 */
defined('_JEXEC') or die;

?>
   <fieldset class="adminform">
      <legend><?php echo JText::_( 'COM_ISSUETRACKER_AUDIT_INFORMATION' ); ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('created_on'); ?>
         <?php echo $this->form->getInput('created_on'); ?></li>

         <li><?php echo $this->form->getLabel('created_by'); ?>
         <?php echo $this->form->getInput('created_by'); ?></li>

         <li><?php echo $this->form->getLabel('modified_on'); ?>
         <?php echo $this->form->getInput('modified_on'); ?></li>

         <li><?php echo $this->form->getLabel('modified_by'); ?>
         <?php echo $this->form->getInput('modified_by'); ?></li>

         </ul>

   </fieldset>
