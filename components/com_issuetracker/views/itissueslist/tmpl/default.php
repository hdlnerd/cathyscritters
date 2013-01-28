<?php
/*
 *
 * @Version       $Id: default.php 393 2012-08-29 15:19:43Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.1
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-29 16:19:43 +0100 (Wed, 29 Aug 2012) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.pagination');
$numCols = 0;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

/** Add in system adminlist css **/
$document = JFactory::getDocument();
$document->addStyleSheet('media/system/css/adminlist.css');

$link = JRoute::_( "index.php?option=com_issuetracker&view=itissueslist" );
$canEdit = $this->params->get('access-edit');
?>

<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
<h1>
   <?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>

<?php if ($canEdit ||  $this->params->get('showl_print_icon') || $this->params->get('showl_email_icon')) : ?>
   <ul class="actions">
   <?php if (!$this->print) : ?>
      <?php if ($this->params->get('showl_print_icon')) : ?>
         <li class="print-icon">
         <?php echo JHtml::_('icon.print_popup',  $this->data, $this->params); ?>
         </li>
      <?php endif; ?>

      <?php if ($this->params->get('showl_email_icon')) : ?>
         <li class="email-icon">
         <?php echo JHtml::_('icon.email',  $this->data, $this->params); ?>
         </li>
      <?php endif; ?>

      <?php if ($canEdit) : ?>
         <li class="edit-icon">
         <?php echo JHtml::_('icon.edit', $this->data, $this->params); ?>
         </li>
      <?php endif; ?>

   <?php else : ?>
      <li>
      <?php echo JHtml::_('icon.print_screen',  $this->data, $this->params); ?>
      </li>
   <?php endif; ?>

   </ul>
<?php endif; ?>
</div>

<script language="javascript" type="text/javascript">
function tableOrdering( order, dir, task )
{
        var form = document.adminForm;

        form.filter_order.value = order;
        form.filter_order_Dir.value = dir;
        document.adminForm.submit( task );
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itissueslist');?>" method="post" name="adminForm" id="adminForm">

<table class="adminlist">
   <thead>
      <tr>
         <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
            <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ISSUE_SUMMARY'), 'issue_summary', $this->sortDirection, $this->sortColumn); ?>
         </th>
         <?php if ($this->params->get('showl_issue_description', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ISSUE_DESCRIPTION'), 'issue_description', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_no', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ISSUE_NUMBER'), 'alias', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_identified_by', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_IDENTIFIED_BY_PERSON_ID'), 'identified_person_name', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_identified_date', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_IDENTIFIED_DATE'), 'identified_date', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_project_name', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PROJECT_NAME'), 't2.project_name', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_staff_details', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ASSIGNED_TO_PERSON_ID'), 'assigned_person_name', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_status', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('JSTATUS'), 'state_name', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>

            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_TYPE'), 'type_name', $this->sortDirection, $this->sortColumn); ?>
            </th>

         <?php if ($this->params->get('showl_issue_priority', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PRIORITY'), 'priority_name', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_target_date_field', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_TARGET_RESOLUTION_DATE'), 'target_resolution_date', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_progress_field', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PROGRESS'), 'progress', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_actual_res_date', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ACTUAL_RESOLUTION_DATE'), 'actual_resolution_date', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_resolution_field', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_RESOLUTION_SUMMARY'), 'resolution_summary', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_audit_fields', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_CREATED_ON'), 'created_on', $this->sortDirection, $this->sortColumn); ?>
            </th>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_CREATED_BY'), 'created_by', $this->sortDirection, $this->sortColumn); ?>
            </th>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_MODIFIED_ON'), 'modified_on', $this->sortDirection, $this->sortColumn); ?>
            </th>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_MODIFIED_BY'), 'modified_by', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_id', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ISSUE_ID'), 'id', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
      </tr>
   </thead>

   <tfoot>
      <tr>
         <td colspan="<?php echo $numCols; ?>">
            <?php echo $this->pagination->getListFooter(); ?>
            <!-- div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?> - <?php echo $this->pagination->getPagesCounter(); ?></div -->
         </td>
      </tr>
   </tfoot>

   <tbody>
      <?php foreach($this->data as $dataItem): ?>
      <?php $link = JRoute::_( "index.php?option=com_issuetracker&view=itissues&id={$dataItem->id}" ); ?>
      <!-- ?php $link = JRoute::_( "index.php?option=com_issuetracker&view=form&id={$dataItem->id}" ); ? -->
      <tr>
         <td class="fieldDiv fieldValue">
            <?php if ($this->params->get('show_linked_child_detail', 0)) : ?>
               <span title="<?php echo JText::_( 'COM_ISSUETRACKER_VIEW_ISSUE' );?>::<?php echo $this->escape($dataItem->issue_summary); ?>">
                  <!-- a href="<?php echo $link.'?id='.$dataItem->id; ?>"><?php echo $dataItem->issue_summary; ?></a -->
                  <a href="<?php echo $link; ?>"><?php echo $dataItem->issue_summary; ?></a>
               </span>
            <?php else: echo $dataItem->issue_summary; endif; ?>
         </td>
         <?php if ($this->params->get('showl_issue_description', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->issue_description; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_no', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->alias; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_identified_by', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->identified_person_name; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_identified_date', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php if ( !empty($dataItem->identified_date) && $dataItem->identified_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->identified_date, JText::_('DATE_FORMAT_LC4')); ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_project_name', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->project_name; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_staff_details', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->assigned_person_name; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_status', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->status_name; ?>
            </td>
         <?php endif; ?>

            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->type_name; ?>
            </td>

         <?php if ($this->params->get('showl_issue_priority', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->priority_name; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_target_date_field', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php if ( !empty($dataItem->target_resolution_date) && $dataItem->target_resolution_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->target_resolution_date, JText::_('DATE_FORMAT_LC4')); ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_progress_field', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->progress; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_actual_res_date', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php if ( !empty($dataItem->actual_resolution_date) && $dataItem->actual_resolution_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->actual_resolution_date, JText::_('DATE_FORMAT_LC4')); ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_resolution_field', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->resolution_summary; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_audit_fields', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php if ( !empty($dataItem->created_on) && $dataItem->created_on != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->created_on, JText::_('DATE_FORMAT_LC4')); ?>
            </td>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->created_by; ?>
            </td>
            <td class="fieldDiv fieldValue">
              <?php if ( !empty($dataItem->modified_on) && $dataItem->modified_on != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->modified_on, JText::_('DATE_FORMAT_LC4')); ?>
            </td>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->modified_by; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_id', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->id; ?>
            </td>
         <?php endif; ?>
      </tr>
      <?php endforeach; ?>
   <tbody>
</table>
   <input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
   <input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
</form>