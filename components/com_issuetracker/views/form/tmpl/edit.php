<?php
/*
 *
 * @Version       $Id: edit.php 721 2013-02-20 19:41:01Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-20 19:41:01 +0000 (Wed, 20 Feb 2013) $
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

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

IssueTrackerHelper::addCSS('media://com_issuetracker/css/issuetracker.css');

// Create shortcut to parameters.
$parameters = $this->state->get('params');

$allow_attachment = $parameters->get('enable_attachments', 0);
$allow_private    = $parameters->get('allow_private_issues');
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

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itissues&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-validate">

   <div class="formelm-buttons">
      <button type="button" onclick="Joomla.submitbutton('itissues.save')">
         <?php echo JText::_('JSAVE') ?>
      </button>
      <button type="button" onclick="Joomla.submitbutton('itissues.cancel')">
         <?php echo JText::_('JCANCEL') ?>
      </button>
   </div>

   <?php $intro = $parameters->get('create_intro',''); if ( !empty($intro) && empty($this->item->id) ) { echo '<br />'.$intro.'<br /><br />'; } ?>

   <?php if ( $allow_private && !empty($this->item->id) ) : ?>
      <fieldset>
         <?php if ($this->item->public) echo '<br/>'.JText::_('COM_ISSUETRACKER_PUBNOTE_PUBLIC_MSG').'<br/>'; else echo '<br/>'.JText::_('COM_ISSUETRACKER_PUBNOTE_PRIVATE_MSG').'<br/>'; ?>
      </fieldset>
   <?php endif; ?>

   <fieldset>
      <legend><?php if (empty($this->item->id)) echo JText::_('COM_ISSUETRACKER_FORM_CREATE_ISSUE');  else  echo JText::_('COM_ISSUETRACKER_FORM_EDIT_ISSUE').' '.$this->item->alias;  ?></legend>

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

   <?php if ($parameters->get('show_details_section',0)) : ?>
      <fieldset>
         <legend><?php echo JText::_('COM_ISSUETRACKER_ISSUE_DETAILS_LEGEND'); ?></legend>

         <?php if ($parameters->get('show_visibility', 0)) : ?>
         <div class="formelm">
            <?php echo $this->form->getLabel('public'); ?>
            <?php echo $this->form->getInput('public'); ?>
         </div>
         <?php endif; ?>

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

         <?php if ($parameters->get('show_project_name', 0)) : ?>
         <div class="formelm">
            <?php echo $this->form->getLabel('related_project_id'); ?>
            <?php echo $this->form->getInput('related_project_id'); ?>
         </div>
         <?php endif; ?>

         <div class="formelm">
             <?php echo $this->form->getLabel('issue_type'); ?>
             <?php echo $this->form->getInput('issue_type'); ?>
         </div>

         <div class="formelm">
             <?php echo $this->form->getLabel('priority'); ?>
             <?php echo $this->form->getInput('priority'); ?>
         </div>

         <div class="btn-group">
             <?php echo $this->form->getLabel('notify'); ?>
             <?php echo $this->form->getInput('notify'); ?>
         </div>
      </fieldset>
   <?php endif; ?>


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

   <?php if ($allow_attachment) echo $this->loadTemplate('attachment'); ?>

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
   <input type="hidden" name="project_value" value="<?php echo $this->pid; ?>" />

   <?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>