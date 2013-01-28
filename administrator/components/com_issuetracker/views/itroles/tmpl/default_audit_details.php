<?php
/*
 *
 * @Version       $Id: default_audit_details.php 197 2012-05-04 16:10:32Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.1.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-05-04 17:10:32 +0100 (Fri, 04 May 2012) $
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