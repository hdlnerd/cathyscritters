<?php
/*
 *
 * @Version       $Id: default.php 412 2012-09-04 12:11:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.1
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-09-04 13:11:39 +0100 (Tue, 04 Sep 2012) $
 *
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
$user = JFactory::getUser();
$userId  = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canOrder   = $user->authorise('core.edit.state', 'com_issuetracker');
$saveOrder  = $listOrder == 'a.ordering';

// echo "<pre>"; var_dump($this->items); echo "</pre>";
// echo "<pre>"; var_dump($this->state->params); echo "</pre>";
// Get the optional headings settings
$show_assigned = $this->state->params->get('show_assigned_to_headings');
$show_identifier = $this->state->params->get('show_identified_by_headings');
$show_created_by = $this->state->params->get('show_created_by_headings');
$show_created_on = $this->state->params->get('show_created_on_headings');
$show_modified_by = $this->state->params->get('show_modified_by_headings');
$show_modified_on = $this->state->params->get('show_modified_on_headings');
?>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itissueslist'); ?>" method="post" name="adminForm" id="adminForm">
   <fieldset id="filter-bar">
      <div class="filter-search fltlft">
         <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
         <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
         <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
         <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
      </div>

      <div class="filter-select fltrt">
         <?php if ($show_assigned == 1 ) : ?>
            <select name="filter_assigned" class="inputbox" onchange="this.form.submit()">
               <?php echo JHtml::_('select.options', IssueTrackerHelper::getAssignedPeople(), 'value', 'text', $this->state->get('filter.assigned'));?>
            </select>
         <?php endif; ?>

        <?php if ($show_identifier == 1 ) : ?>
           <select name="filter_identifier" class="inputbox" onchange="this.form.submit()">
              <?php echo JHtml::_('select.options', IssueTrackerHelper::getIdentifyingPeople(), 'value', 'text', $this->state->get('filter.identifier'));?>
            </select>
         <?php endif; ?>

         <select name="filter_project_id" class="inputbox" onchange="this.form.submit()">
            <?php echo JHtml::_('select.options', IssueTrackerHelper::getProject_name(), 'value', 'text', $this->state->get('filter.project_id'));?>
         </select>

         <select name="filter_status_id" class="inputbox" onchange="this.form.submit()">
            <?php echo JHtml::_('select.options', IssueTrackerHelper::getStatuses(), 'value', 'text', $this->state->get('filter.status_id'));?>
         </select>

         <select name="filter_priority_id" class="inputbox" onchange="this.form.submit()">
            <?php echo JHtml::_('select.options', IssueTrackerHelper::getPriorities(), 'value', 'text', $this->state->get('filter.priority_id'));?>
         </select>

         <select name="filter_type_id" class="inputbox" onchange="this.form.submit()">
            <?php echo JHtml::_('select.options', IssueTrackerHelper::getTypes(), 'value', 'text', $this->state->get('filter.type_id'));?>
         </select>

         <select name="filter_published" class="inputbox" onchange="this.form.submit()">
            <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
            <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true);?>
         </select>
      </div>
   </fieldset>
   <div class="clr"> </div>

   <table class="adminlist">
      <thead>
         <tr>
            <th width="1%">
               <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
            </th>

            <th class='left'>
               <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_ISSUE_SUMMARY' ), 'a.issue_summary', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
               <?php echo JHtml::_('grid.sort', JText::_('COM_ISSUETRACKER_ISSUE_NUMBER'), 'a.alias', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
               <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_PROJECT_NAME' ), 't2.project_name', $listDirn, $listOrder); ?>
            </th>
            <?php if ($show_identifier == 1 ) : ?>
               <th class='left'>
                  <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_IDENTIFYING_PERSON' ), 't7.person_name', $listDirn, $listOrder); ?>
               </th>
            <?php endif; ?>
            <?php if ($show_assigned == 1 ) : ?>
               <th class='left'>
                  <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_ASSIGNED_PERSON' ), 'a.person_name', $listDirn, $listOrder); ?>
               </th>
            <?php endif; ?>
            <th class='left'>
                  <?php echo JHTML::_('grid.sort', JText::_( 'JSTATUS' ), 'a.status', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
               <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_TYPE' ), 'a.issue_type', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
               <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_PRIORITY' ), 'a.priority', $listDirn, $listOrder); ?>
            </th>

            <?php if (isset($this->items[0]->state)) { ?>
               <th width="5%">
                  <?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
               </th>
            <?php } ?>

            <?php if ($show_created_by == 1 ) : ?>
               <th class='left'>
                  <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_FIELD_CREATED_BY_LABEL' ), 'a.created_by', $listDirn, $listOrder); ?>
               </th>
            <?php endif; ?>

            <?php if ($show_created_on == 1 ) : ?>
               <th class='left'>
                  <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_FIELD_CREATED_ON_LABEL' ), 'a.created_on', $listDirn, $listOrder); ?>
               </th>
            <?php endif; ?>

            <?php if ($show_modified_by == 1 ) : ?>
               <th class='left'>
                  <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_FIELD_MODIFIED_BY_LABEL' ), 'a.modified_by', $listDirn, $listOrder); ?>
               </th>
            <?php endif; ?>

            <?php if ($show_modified_on == 1 ) : ?>
               <th class='left'>
                  <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_FIELD_MODIFIED_ON_LABEL' ), 'a.modified_on', $listDirn, $listOrder); ?>
               </th>
            <?php endif; ?>

            <?php if (isset($this->items[0]->ordering)) { ?>
               <th width="10%">
                  <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
                  <?php if ($canOrder && $saveOrder) :?>
                     <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'itissueslist.saveorder'); ?>
                  <?php endif; ?>
               </th>
            <?php } ?>

            <?php if (isset($this->items[0]->id)) { ?>
                <th width="1%" class="nowrap">
                    <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
            <?php } ?>
         </tr>
      </thead>
      <tfoot>
         <tr>
            <td colspan="10">
               <?php echo $this->pagination->getListFooter(); ?>
            </td>
         </tr>
      </tfoot>
      <tbody>
      <?php foreach ($this->items as $i => $item) :
         $ordering   = ($listOrder == 'a.ordering');
         $canCreate  = $user->authorise('core.create',      'com_issuetracker');
         $canEdit    = $user->authorise('core.edit',        'com_issuetracker');
         $canCheckin = $user->authorise('core.manage',      'com_issuetracker');
         $canChange  = $user->authorise('core.edit.state',  'com_issuetracker');
         ?>
         <tr class="row<?php echo $i % 2; ?>">
            <td class="center">
               <?php echo JHtml::_('grid.id', $i, $item->id); ?>
            </td>

            <td>
            <?php if (isset($item->checked_out) && $item->checked_out) : ?>
               <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'itissueslist.', $canCheckin); ?>
            <?php endif; ?>
            <?php if ($canEdit) : ?>
               <a href="<?php echo JRoute::_('index.php?option=com_issuetracker&task=itissues.edit&id='.(int) $item->id); ?>">
               <?php echo $this->escape($item->issue_summary); ?></a>
            <?php else : ?>
               <?php echo $this->escape($item->issue_summary); ?>
            <?php endif; ?>
            </td>

            <td>
               <?php echo $item->alias; ?>
            </td>
            <td>
               <?php echo $item->project_name; ?>
            </td>
           <?php if ($show_identifier == 1 ) : ?>
               <td>
                  <?php echo $item->identifying_name; ?>
               </td>
            <?php endif; ?>
            <?php if ($show_assigned == 1 ) : ?>
               <td>
                  <?php echo $item->person_name; ?>
               </td>
            <?php endif; ?>
            <td>
               <?php echo $item->status_name; ?>
            </td>
            <td>
               <?php echo $item->type_name; ?>
            </td>
            <td>
               <?php echo $item->priority_name; ?>
            </td>

            <?php if (isset($this->items[0]->state)) { ?>
               <td class="center">
                  <?php echo JHtml::_('jgrid.published', $item->state, $i, 'itissueslist.', $canChange, 'cb'); ?>
               </td>
            <?php } ?>

            <?php if ($show_created_by == 1 ) : ?>
               <td>
                  <?php echo $item->created_by; ?>
               </td>
            <?php endif; ?>
            <?php if ($show_created_on == 1 ) : ?>
               <td>
                  <?php echo $item->created_on; ?>
               </td>
            <?php endif; ?>
            <?php if ($show_modified_by == 1 ) : ?>
               <td>
                  <?php echo $item->modified_by; ?>
               </td>
            <?php endif; ?>
            <?php if ($show_modified_on == 1 ) : ?>
               <td>
                  <?php echo $item->modified_on; ?>
               </td>
            <?php endif; ?>

            <?php if (isset($this->items[0]->ordering)) { ?>
               <td class="order">
               <?php if ($canChange) : ?>
                  <?php if ($saveOrder) :?>
                     <?php if ($listDirn == 'asc') : ?>
                         <span><?php echo $this->pagination->orderUpIcon($i, true, 'itissueslist.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                         <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'itissueslist.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                      <?php elseif ($listDirn == 'desc') : ?>
                         <span><?php echo $this->pagination->orderUpIcon($i, true, 'itissueslist.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                          <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'itissueslist.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                      <?php endif; ?>
                  <?php endif; ?>
                  <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                     <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
               <?php else : ?>
                  <?php echo $item->ordering; ?>
               <?php endif; ?>
               </td>
            <?php } ?>

            <?php if (isset($this->items[0]->id)) { ?>
               <td class="center">
                  <?php echo (int) $item->id; ?>
               </td>
            <?php } ?>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>

   <div>
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
      <?php echo JHtml::_('form.token'); ?>
   </div>
</form>