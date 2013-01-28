<?php
/*
 *
 * @Version       $Id: edit.php 392 2012-08-29 15:18:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.1
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-29 16:18:25 +0100 (Wed, 29 Aug 2012) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access' );

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// echo "<pre>";var_dump($this->item);echo "</pre>";
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task)
   {
      if (task == 'itissues.cancel' || document.formvalidator.isValid(document.id('type-form'))) {
         Joomla.submitform(task, document.getElementById('type-form'));
      }
      else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="type-form" class="form-validate">
   <div class="width-100 fltlft">
      <fieldset class="adminform">
         <legend><?php echo JText::_('JDETAILS').' - Issue: '.$this->item->alias; ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('issue_summary'); ?>
         <div class="clr"></div>
         <?php echo $this->form->getInput('issue_summary'); ?></li>

         <li><?php echo $this->form->getLabel('issue_description'); ?>
         <div class="clr"></div>
         <?php echo $this->form->getInput('issue_description'); ?></li>

         <li><?php echo $this->form->getLabel('identified_by_person_id'); ?>
         <?php echo $this->form->getInput('identified_by_person_id'); ?></li>

         <li><?php echo $this->form->getLabel('identified_date'); ?>
         <?php echo $this->form->getInput('identified_date'); ?></li>

         <li><?php echo $this->form->getLabel('related_project_id'); ?>
         <?php echo $this->form->getInput('related_project_id'); ?></li>

         </ul>
      </fieldset>

      <fieldset class="adminform">
         <legend><?php echo JText::_( 'COM_ISSUETRACKER_PROGRESS_INFORMATION' ); ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('assigned_to_person_id'); ?>
         <?php echo $this->form->getInput('assigned_to_person_id'); ?></li>

         <li><?php echo $this->form->getLabel('issue_type'); ?>
         <?php echo $this->form->getInput('issue_type'); ?></li>

         <li><?php echo $this->form->getLabel('status'); ?>
         <?php echo $this->form->getInput('status'); ?></li>

         <li><?php echo $this->form->getLabel('state'); ?>
         <?php echo $this->form->getInput('state'); ?></li>

         <li><?php echo $this->form->getLabel('checked_out'); ?>
         <?php echo $this->form->getInput('checked_out'); ?></li>

         <li><?php echo $this->form->getLabel('checked_out_time'); ?>
         <?php echo $this->form->getInput('checked_out_time'); ?></li>

         <li><?php echo $this->form->getLabel('priority'); ?>
         <?php echo $this->form->getInput('priority'); ?></li>

         <li><?php echo $this->form->getLabel('target_resolution_date'); ?>
         <?php echo $this->form->getInput('target_resolution_date'); ?></li>

         <li><?php echo $this->form->getLabel('progress'); ?>
         <div class="clr"></div>
         <?php echo $this->form->getInput('progress'); ?></li>

         </ul>
      </fieldset>

      <fieldset class="adminform">
         <legend><?php echo JText::_( 'COM_ISSUETRACKER_RESOLUTION_INFORMATION' ); ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('actual_resolution_date'); ?>
         <?php echo $this->form->getInput('actual_resolution_date'); ?></li>

         <li><?php echo $this->form->getLabel('resolution_summary'); ?>
         <div class="clr"></div>
         <?php echo $this->form->getInput('resolution_summary'); ?></li>

          </ul>
      </fieldset>

   <?php echo $this->loadTemplate('audit_details');?>

<!-- begin ACL definition-->

   <div class="clr"></div>

   <?php if ($this->canDo->get('core.admin')): ?>
      <div class="width-100 fltlft">
         <?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

            <?php echo JHtml::_('sliders.panel', JText::_('COM_ISSUETRACKER_FIELDSET_RULES'), 'access-rules'); ?>
            <fieldset class="panelform">
               <?php echo $this->form->getLabel('rules'); ?>
               <?php echo $this->form->getInput('rules'); ?>
            </fieldset>

         <?php echo JHtml::_('sliders.end'); ?>
      </div>
   <?php endif; ?>

   <!-- end ACL definition-->

   </div>

   <input type="hidden" name="task" value="" />
   <input type="hidden" name="alias" value="<?php echo $this->item->alias; ?>" />
   <?php echo JHtml::_('form.token'); ?>
   <div class="clr"></div>
</form>
