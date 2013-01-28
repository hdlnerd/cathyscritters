<?php
/*
 *
 * @Version       $Id: default.php 322 2012-08-20 13:14:58Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-20 14:14:58 +0100 (Mon, 20 Aug 2012) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
$data = $this->data;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Or in view.
// JHTML::stylesheet('components/com_issuetracker/assets/issuetracker.css' );

$link = JRoute::_( "index.php?option=com_issuetracker&view=itissues&id={$data->id}" );
// $canEdit = $this->params->get('access-edit');
$canEdit = $data->params->get('access-edit');
?>

<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
<h1>
   <?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>

<?php if ($canEdit ||  $this->params->get('show_print_icon') || $this->params->get('show_email_icon')) : ?>
   <ul class="actions">
   <?php if (!$this->print) : ?>
      <?php if ($this->params->get('show_print_icon')) : ?>
         <li class="print-icon">
            <?php echo JHtml::_('icon.print_popup',  $data, $this->params); ?>
         </li>
      <?php endif; ?>

      <?php if ($this->params->get('show_email_icon')) : ?>
         <li class="email-icon">
            <?php echo JHtml::_('icon.email',  $data, $this->params); ?>
         </li>
      <?php endif; ?>

      <?php if ($canEdit) : ?>
         <li class="edit-icon">
            <?php echo JHtml::_('icon.edit', $data, $this->params); ?>
         </li>
      <?php endif; ?>

   <?php else : ?>
      <li>
         <?php echo JHtml::_('icon.print_screen',  $data, $this->params); ?>
      </li>
   <?php endif; ?>

   </ul>
<?php endif; ?>

<form id="adminForm" action="<?php echo JRoute::_('index.php')?>" method="post">
   <div class="fieldDiv">
      <fieldset>
         <legend><?php echo JText::_('COM_ISSUETRACKER_ISSUE_DEFAULT_LEGEND'); ?></legend>
         <dl>
            <?php if ($this->params->get('show_issue_id', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ISSUE_ID_LABEL' ); ?>  </dt>
               <dd> <?php echo $data->id; ?> </dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_issue_no', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ISSUE_NUMBER_LABEL' ); ?>  </dt>
               <dd> <?php echo $data->alias; ?> </dd>
            <?php endif; ?>

            <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ISSUE_SUMMARY_LABEL' ); ?> </dt>
            <dd> <?php echo $data->issue_summary; ?> </dd>

            <?php if ($this->params->get('show_issue_description', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ISSUE_DESCRIPTION_LABEL' ); ?> </dt>
               <dd> <?php echo $data->issue_description; ?> </dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_identified_by', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_IDENTIFIED_PERSON_NAME_LABEL' ); ?> </dt>
               <dd> <?php echo $data->identified_person_name; ?> </dd>
            <?php endif; ?>

               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ISSUE_TYPE_LABEL' ); ?> </dt>
               <dd> <?php echo $data->type_name; ?> </dd>

            <?php if ($this->params->get('show_identified_date', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_IDENTIFIED_DATE_LABEL' ); ?> </dt>
               <dd>
                  <?php if ( !empty($data->identified_date) && $data->identified_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->identified_date, JText::_('DATE_FORMAT_LC1')); ?>
               </dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_project_name', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_PROJECT_NAME_LABEL' ); ?> </dt>
               <dd> <?php echo $data->project_name; ?> </dd>
            <?php endif; ?>
          </dl>
      </fieldset>

      <fieldset>
         <legend><?php echo JText::_('COM_ISSUETRACKER_ISSUE_PROGRESS_LEGEND'); ?></legend>
         <dl>
           <?php if ($this->params->get('show_staff_details', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ASSIGNED_PERSON_NAME_LABEL' ); ?> </dt>
               <dd> <?php echo $data->assigned_person_name; ?> </dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_issue_status', 0)) : ?>
               <dt> <?php echo JText::_( 'JSTATUS' ); ?> </dt>
               <dd> <?php echo $data->status_name; ?> </dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_issue_priority', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_PRIORITY_LABEL' ); ?> </dt>
               <dd> <?php echo $data->priority_name; ?> </dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_target_date_field', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_TARGET_RESOLUTION_DATE_LABEL' ); ?> </dt>
               <dd>
                  <?php if ( !empty($data->target_resolution_date) && $data->target_resolution_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->target_resolution_date, JText::_('DATE_FORMAT_LC1')); ?>
               </dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_progress_field', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_PROGRESS_LABEL' ); ?> </dt>
               <dd> <?php echo $data->progress; ?> </dd>
            <?php endif; ?>
         </dl>
      </fieldset>

      <fieldset>
         <legend><?php echo JText::_('COM_ISSUETRACKER_ISSUE_RESOLUTION_LEGEND'); ?></legend>
         <dl>
            <?php if ($this->params->get('show_actual_res_date', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ACTUAL_RESOLUTION_DATE_LABEL' ); ?> </dt>
               <dd>
                  <?php if ( !empty($data->actual_resolution_date) && $data->actual_resolution_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->actual_resolution_date, JText::_('DATE_FORMAT_LC1')); ?>
               </dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_resolution_field', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_RESOLUTION_SUMMARY_LABEL' ); ?> </dt>
               <dd> <?php echo $data->resolution_summary; ?> </dd>
            <?php endif; ?>
         <dl>
      </fieldset>

      <?php if ($this->params->get('show_audit_fields', 0)) : ?>
         <fieldset>
            <legend><?php echo JText::_('COM_ISSUETRACKER_AUDIT_LEGEND'); ?></legend>
            <dl>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_CREATED_ON_LABEL' ); ?> </dt>
               <dd>
                    <?php if ( !empty($data->created_on) && $data->created_on != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->created_on, JText::_('DATE_FORMAT_LC1')); ?>
               </dd>

               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_CREATED_BY_LABEL' ); ?> </dt>
               <dd> <?php echo $data->created_by; ?> </dd>

               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_MODIFIED_ON_LABEL' ); ?> </dt>
               <dd>
                  <?php if ( !empty($data->modified_on) && $data->modified_on != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->modified_on, JText::_('DATE_FORMAT_LC1')); ?>
               </dd>

               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_MODIFIED_BY_LABEL' ); ?> </dt>
               <dd> <?php echo $data->modified_by; ?> </dd>
            <dl>
         </fieldset>
      <?php endif; ?>
   </div>
</form>
</div>