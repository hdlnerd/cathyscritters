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
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

$numCols = 0;

/** custom css **/
$document = JFactory::getDocument();
$document->addStyleSheet('media/system/css/adminlist.css');
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
         <?php echo JHtml::_('icon.print_popup',  $this->data, $this->params); ?>
         </li>
      <?php endif; ?>

      <?php if ($this->params->get('show_email_icon')) : ?>
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

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itprojectslist');?>" method="post" name="adminForm" id="adminForm">
   <table class="adminlist">
      <thead>
         <tr>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PROJECT_NAME'), 'project_name', $this->sortDirection, $this->sortColumn); ?>
            </th>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PROJECT_DESCRIPTION'), 'project_description', $this->sortDirection, $this->sortColumn); ?>
            </th>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_START_DATE'), 'start_date', $this->sortDirection, $this->sortColumn); ?>
            </th>
            <?php if ($this->params->get('show_target_date_field', 0)) : ?>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_TARGET_END_DATE'), 'target_end_date', $this->sortDirection, $this->sortColumn); ?>
               </th>
            <?php endif; ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ACTUAL_END_DATE'), 'actual_end_date', $this->sortDirection, $this->sortColumn); ?>
            </th>
            <?php if ($this->params->get('show_audit_fields', 0)) : ?>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_CREATED_ON'), 'created_on', $this->sortDirection, $this->sortColumn); ?>
               </th>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_CRAETED_BY'), 'created_by', $this->sortDirection, $this->sortColumn); ?>
               </th>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_MODIFIED_ON'), 'modified_on', $this->sortDirection, $this->sortColumn); ?>
               </th>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_MODIFIED_BY'), 'modified_by', $this->sortDirection, $this->sortColumn); ?>
               </th>
            <?php endif; ?>
            <?php if ($this->params->get('show_project_id', 0)) : ?>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PROJECT_ID'), 'project_id', $this->sortDirection, $this->sortColumn); ?>
               </th>
            <?php endif; ?>
         </tr>
      </thead>

      <tfoot>
         <tr>
            <td colspan="<?php echo $numCols; ?>">
               <?php echo $this->pagination->getListFooter(); ?>
            </td>
         </tr>
      </tfoot>

      <tbody>
         <?php foreach($this->data as $dataItem): ?>
         <?php $link = JRoute::_( "index.php?option=com_issuetracker&view=itprojects&id={$dataItem->project_id}" ); ?>
         <tr>
            <td class="fieldDiv fieldValue">
               <?php if ($this->params->get('show_linked_child_detail', 0)) : ?>
                  <span title="<?php echo JText::_( 'COM_ISSUETRACKER_VIEW_PROJECT' );?>::<?php echo $this->escape($dataItem->project_name); ?>">
                     <a href="<?php echo $link; ?>"><?php echo $dataItem->project_name; ?></a>
                  </span>
               <?php else: echo $dataItem->project_name; endif; ?>
            </td>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->project_description; ?>
            </td>
            <td class="fieldDiv fieldValue">
               <?php if ( !empty($dataItem->start_date) && $dataItem->start_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->start_date, JText::_('DATE_FORMAT_LC4')); ?>
            </td>
            <?php if ($this->params->get('show_target_date_field', 0)) : ?>
               <td class="fieldDiv fieldValue">
                 <?php if ( !empty($dataItem->target_end_date) && $dataItem->target_end_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->target_end_date, JText::_('DATE_FORMAT_LC4')); ?>
               </td>
            <?php endif; ?>
            <td class="fieldDiv fieldValue">
               <?php if ( !empty($dataItem->actual_end_date) && $dataItem->actual_end_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->actual_end_date, JText::_('DATE_FORMAT_LC4')); ?>
            </td>
            <?php if ($this->params->get('show_audit_fields', 0)) : ?>
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
            <?php if ($this->params->get('show_project_id', 0)) : ?>
               <td align="center" class="fieldDiv fieldValue">
                  <?php echo $dataItem->project_id; ?>
               </td>
            <?php endif; ?>
         </tr>
         <?php endforeach; ?>
      <tbody>
   </table>

   <input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
   <input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
</form>