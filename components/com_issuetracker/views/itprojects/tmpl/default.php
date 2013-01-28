<?php
/*
 *
 * @Version       $Id: default.php 194 2012-05-02 19:52:10Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.0.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-05-02 20:52:10 +0100 (Wed, 02 May 2012) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

$data = $this->data;
$link = JRoute::_( "index.php?option=com_issuetracker&view=itprojects&id={$data->id}" );
$canEdit = $this->params->get('access-edit');
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
         <legend><?php echo JText::_('COM_ISSUETRACKER_PROJECT_DEFAULT_LEGEND'); ?></legend>
         <dl>
            <?php if ($this->params->get('show_project_id', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_PROJECT_ID_LABEL' ); ?>  </dt>
               <dd> <?php echo $data->id; ?> </dd>
            <?php endif; ?>

            <dt><?php echo JText::_( 'COM_ISSUETRACKER_FIELD_PROJECT_NAME_LABEL' ); ?></dt>
            <dd><?php echo $data->project_name; ?></dd>

            <dt><?php echo JText::_( 'COM_ISSUETRACKER_FIELD_PROJECT_DESCRIPTION_LABEL' ); ?></dt>
            <dd><?php echo $data->project_description; ?></dd>

            <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_START_DATE_LABEL' ); ?> </dt>
            <dd>
               <?php if ( !empty($data->start_date) && $data->start_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->start_date, JText::_('DATE_FORMAT_LC4')); ?>
            </dd>

            <?php if ($this->params->get('show_target_date_field', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_TARGET_END_DATE_LABEL' ); ?> </dt>
               <dd>
                  <?php if ( !empty($data->target_end_date) && $data->target_end_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->target_end_date, JText::_('DATE_FORMAT_LC4')); ?>
               </dd>
            <?php endif; ?>

            <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ACTUAL_END_DATE_LABEL' ); ?> </dt>
            <dd>
               <?php if ( !empty($data->actual_end_date) && $data->actual_end_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->actual_end_date, JText::_('DATE_FORMAT_LC4')); ?>
            </dd>
         <dl>
      </fieldset>

      <?php if ($this->params->get('show_audit_fields', 0)) : ?>
         <fieldset>
            <legend><?php echo JText::_('COM_ISSUETRACKER_AUDIT_LEGEND'); ?></legend>
            <dl>

               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_CREATED_ON_LABEL' ); ?> </dt>
               <dd>
                    <?php if ( !empty($data->created_on) && $data->created_on != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->created_on, JText::_('DATE_FORMAT_LC4')); ?>
               </dd>

               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_CREATED_BY_LABEL' ); ?> </dt>
               <dd> <?php echo $data->created_by; ?> </dd>

               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_MODIFIED_ON_LABEL' ); ?> </dt>
               <dd>
                  <?php if ( !empty($data->modified_on) && $data->modified_on != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->modified_on, JText::_('DATE_FORMAT_LC4')); ?>
               </dd>

               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_MODIFIED_BY_LABEL' ); ?> </dt>
               <dd> <?php echo $data->modified_by; ?> </dd>
            <dl>
         </fieldset>
      <?php endif; ?>
   </div>
</form>
</div>
