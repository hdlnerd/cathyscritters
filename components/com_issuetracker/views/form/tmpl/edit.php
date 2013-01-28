<?php
/*
 *
 * @Version       $Id: edit.php 445 2012-09-10 14:12:23Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.1
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-09-10 15:12:23 +0100 (Mon, 10 Sep 2012) $
 *
 */
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

// Load administrator language to avoid duplicate translations
JFactory::getLanguage()->load('com_issuetracker', JPATH_ADMINISTRATOR.'/components/com_issuetracker');

$user = JFactory::getUser();


// Create shortcut to parameters.
$parameters = $this->state->get('params');

// Uncomment out to view what form fields are available
//echo '<pre>';var_dump($this->form);'</pre>';
?>

<script type="text/javascript">
   Joomla.submitbutton = function(task) {
      if (task == 'itissues.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
         Joomla.submitform(task);
      } else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
<?php if ($parameters->get('show_page_heading', 1)) : ?>
<h1>
   <?php echo $this->escape($parameters->get('page_heading')); ?>
</h1>
<?php endif; ?>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itissues&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">

   <div class="formelm-buttons">
      <button type="button" onclick="Joomla.submitbutton('itissues.save')">
         <?php echo JText::_('JSAVE') ?>
      </button>
      <button type="button" onclick="Joomla.submitbutton('itissues.cancel')">
         <?php echo JText::_('JCANCEL') ?>
      </button>
   </div>

   <?php $intro = $parameters->get('create_intro',''); if ( !empty($intro) && empty($this->item->id) ) { echo '<br />'.$intro.'<br /><br />'; } ?>

   <fieldset>
      <legend><?php if (empty($this->item->id)) { echo JText::_('COM_ISSUETRACKER_FORM_CREATE_ISSUE'); } else { echo JText::_('COM_ISSUETRACKER_FORM_EDIT_ISSUE').' '.$this->item->alias; } ?></legend>

         <div class="formelm">
             <?php echo $this->form->getLabel('alias'); ?>
             <?php echo $this->form->getInput('alias'); ?>
         </div>

         <div class="formelm">
            <?php if ($parameters->get('admin_edit','0') || $parameters->get('new_record','0') ) : ?>
               <?php echo $this->form->getLabel('issue_summary'); ?>
               <?php echo $this->form->getInput('issue_summary'); ?>
            <?php else : ?>
               <dt>
                  <?php echo $this->form->getLabel('issue_summary'); ?>
               </dt>
               <dd>
                  <?php echo $this->item->issue_summary; ?>
               </dd>
            <?php endif; ?>
         </div>
         <div class="clr"> </div>

         <br /><br /><br />

         <div class="formelm">
            <?php if ($parameters->get('admin_edit','0') || $parameters->get('new_record','0') ) : ?>
               <?php echo $this->form->getLabel('issue_description'); ?>
               <?php echo $this->form->getInput('issue_description'); ?>
            <?php else : ?>
               <dt>
                  <?php echo $this->form->getLabel('issue_description'); ?>
               </dt>
               <dd>
                  <?php echo $this->item->issue_description; ?>
               </dd>
            <?php endif; ?>
         </div>
         <div class="clr"> </div>
   </fieldset>

   <?php if ( !(empty($this->item->id)) && ($parameters->get('issues_admin', 0) == 0 ) ) : ?>
      <fieldset>
         <legend><?php echo JText::_('COM_ISSUETRACKER_ADDITIONAL_INFORMATION_LEGEND'); ?></legend>
         <div class="formelm">
             <!-- ?php echo $this->form->getLabel('additional_info'); ? -->
             <?php echo $this->form->getInput('additional_info'); ?>
         </div>
      </fieldset>
   <?php endif; ?>

   <fieldset>
      <legend><?php echo JText::_('COM_ISSUETRACKER_ISSUE_DETAILS_LEGEND'); ?></legend>

         <?php if ($parameters->get('show_identified_by', 0)) : ?>
         <div class="formelm">
            <?php echo $this->form->getLabel('identified_by_person_id'); ?>
            <?php echo $this->form->getInput('identified_by_person_id'); ?>
         </div>
         <?php endif; ?>

         <div class="formelm">
            <?php echo $this->form->getLabel('identified_date'); ?>
            <?php echo $this->form->getInput('identified_date'); ?>
         </div>

         <div class="formelm">
            <?php echo $this->form->getLabel('related_project_id'); ?>
            <?php echo $this->form->getInput('related_project_id'); ?>
         </div>

         <div class="formelm">
             <?php echo $this->form->getLabel('issue_type'); ?>
             <?php echo $this->form->getInput('issue_type'); ?>
         </div>

         <div class="formelm">
             <?php echo $this->form->getLabel('priority'); ?>
             <?php echo $this->form->getInput('priority'); ?>
         </div>

         <div class="formelm">
             <?php echo $this->form->getLabel('notify'); ?>
             <!-- ?php echo JHTML::_('select.booleanlist', 'notify', 'class="inputbox"', $this->form->getInput('notify')); ? -->
             <?php echo $this->form->getInput('notify'); ?>
         </div>
   </fieldset>

   <?php if ( !(empty($this->item->id)) || ($parameters->get('issues_admin', 0) == 1 ) ) : ?>
      <fieldset>
         <legend><?php echo JText::_('COM_ISSUETRACKER_ISSUE_PROGRESS_LEGEND'); ?></legend>
         <?php if ($parameters->get('show_staff_details', 0)) : ?>
            <div class="formelm">
               <?php echo $this->form->getLabel( 'assigned_to_person_id' ); ?>
               <?php echo $this->form->getInput( 'assigned_to_person_id' ); ?>
            </div>
         <?php endif; ?>

         <?php if ($parameters->get('show_issue_status', 0)) : ?>
            <div class="formelm">
               <?php echo $this->form->getLabel( 'status' ); ?>
               <?php echo $this->form->getInput( 'status' ); ?>
            </div>
         <?php endif; ?>

         <?php if ($parameters->get('show_issue_state', 0)) : ?>
            <div class="formelm">
              <?php echo $this->form->getLabel('state'); ?>
              <?php echo $this->form->getInput('state'); ?>
            </div>
         <?php endif; ?>

         <?php if ($parameters->get('show_target_date_field', 0)) : ?>
            <div class="formelm">
               <?php echo $this->form->getLabel( 'target_resolution_date' ); ?>
               <?php echo $this->form->getInput( 'target_resolution_date' ); ?>
            </div>
         <?php endif; ?>

         <?php if ($parameters->get('show_progress_field', 0)) : ?>
            <div class="formelm">
              <?php if ($parameters->get('admin_edit','0')) : ?>
                  <?php echo $this->form->getLabel('progress'); ?>
                  <?php echo $this->form->getInput('progress'); ?>
               <?php else : ?>
                  <dt>
                     <?php echo $this->form->getLabel('progress'); ?>
                  </dt>
                  <dd>
                     <?php echo $this->item->progress; ?>
                  </dd>
               <?php endif; ?>
            </div>
         <?php endif; ?>
         <div class="clr"> </div>

      </fieldset>

      <fieldset>
         <legend><?php echo JText::_('COM_ISSUETRACKER_ISSUE_RESOLUTION_LEGEND'); ?></legend>
            <div class="formelm">
               <?php if ($parameters->get('show_actual_res_date', 0)) : ?>
                  <?php echo $this->form->getLabel( 'actual_resolution_date' ); ?>
                  <?php echo $this->form->getInput( 'actual_resolution_date' ); ?>
                  <!-- ?php if ( !empty($data->actual_resolution_date) && $data->actual_resolution_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->actual_resolution_date, JText::_('DATE_FORMAT_LC1')); ? -->
             <?php endif; ?>
          <div>

         <div class="formelm">
            <?php if ($parameters->get('show_resolution_field', 0)) : ?>
              <?php if ($parameters->get('admin_edit','0')) : ?>
                  <?php echo $this->form->getLabel('resolution_summary'); ?>
                  <?php echo $this->form->getInput('resolution_summary'); ?>
               <?php else : ?>
                  <dt>
                     <?php echo $this->form->getLabel('resolution_summary'); ?>
                  </dt>
                  <dd>
                     <?php echo $this->item->resolution_summary; ?>
                  </dd>
               <?php endif; ?>
            <?php endif; ?>
         <div>
         <div class="clr"> </div>
      </fieldset>
   <?php endif; ?>

   <?php if ($parameters->get('show_product_req', 0)) echo $this->loadTemplate('product_details'); ?>

   <?php if ($user->guest) echo $this->loadTemplate('user_details'); ?>

   <div class="formelm-buttons">
      <button type="button" onclick="Joomla.submitbutton('itissues.save')">
         <?php echo JText::_('JSAVE') ?>
      </button>
      <button type="button" onclick="Joomla.submitbutton('itissues.cancel')">
         <?php echo JText::_('JCANCEL') ?>
      </button>
   </div>

   <input type="hidden" name="task" value="" />
   <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
   <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
   <input type="hidden" name="issue_id" value="<?php echo $this->item->id; ?>" />

   <?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>