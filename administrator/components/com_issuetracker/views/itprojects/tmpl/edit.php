<?php
/*
 *
 * @Version       $Id: edit.php 195 2012-05-02 19:54:34Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.1.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-05-02 20:54:34 +0100 (Wed, 02 May 2012) $
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
      if (task == 'itprojects.cancel' || document.formvalidator.isValid(document.id('type-form'))) {
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

         <li><?php echo $this->form->getLabel('project_name'); ?>
         <?php echo $this->form->getInput('project_name'); ?></li>

         <li><?php echo $this->form->getLabel('project_description'); ?>
         <div class="clr"></div>
         <?php echo $this->form->getInput('project_description'); ?></li>

         <li><?php echo $this->form->getLabel('parent_id'); ?>
         <?php echo $this->form->getInput('parent_id'); ?></li>

         <li><?php echo $this->form->getLabel('start_date'); ?>
         <?php echo $this->form->getInput('start_date'); ?></li>

         <li><?php echo $this->form->getLabel('target_end_date'); ?>
         <?php echo $this->form->getInput('target_end_date'); ?></li>

         <li><?php echo $this->form->getLabel('actual_end_date'); ?>
         <?php echo $this->form->getInput('actual_end_date'); ?></li>

         <li><?php echo $this->form->getLabel('state'); ?>
         <?php echo $this->form->getInput('state'); ?></li>

         <li><?php echo $this->form->getLabel('checked_out'); ?>
         <?php echo $this->form->getInput('checked_out'); ?></li>

         <li><?php echo $this->form->getLabel('checked_out_time'); ?>
         <?php echo $this->form->getInput('checked_out_time'); ?></li>

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
