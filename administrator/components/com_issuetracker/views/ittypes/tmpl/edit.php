<?php
/*
 *
 * @Version       $Id: edit.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.1.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-01-04 14:39:25 +0000 (Fri, 04 Jan 2013) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access' );

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task)
   {
      if (task == 'ittypes.cancel' || document.formvalidator.isValid(document.id('type-form'))) {
         Joomla.submitform(task, document.getElementById('type-form'));
      }
      else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="type-form" class="form-validate">
   <div class="width-60 fltlft">
      <fieldset class="adminform">
         <legend><?php echo JText::_('JDETAILS'); ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('type_name'); ?>
         <?php echo $this->form->getInput('type_name'); ?></li>

         <li><?php echo $this->form->getLabel('description'); ?>
         <div class="clr"></div>
         <?php echo $this->form->getInput('description'); ?></li>

         <li>
            <?php echo $this->form->getLabel('state'); ?>
            <?php echo $this->form->getInput('state'); ?></li><li><?php echo $this->form->getLabel('checked_out'); ?>
            <?php echo $this->form->getInput('checked_out'); ?></li><li><?php echo $this->form->getLabel('checked_out_time'); ?>
            <?php echo $this->form->getInput('checked_out_time'); ?>
         </li>

         </ul>
      </fieldset>
   </div>
   <div class="width-40 fltlft">
      <?php echo $this->loadTemplate('audit_details');?>
   </div>

   <input type="hidden" name="task" value="" />
   <?php echo JHtml::_('form.token'); ?>
   <div class="clr"></div>
</form>
