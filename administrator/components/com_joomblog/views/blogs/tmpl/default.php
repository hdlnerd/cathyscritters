<?php

/**
* JoomBlog component for Joomla 1.6
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$search 	= $this->escape($this->state->get('filter.search'));
?>
<table class="admin">
	<tbody>
		<tr>
			<td valign="top" class="lefmenutd" >
				<?php echo $this->loadTemplate('menu');?>
			</td>
			<td valign="top" width="100%" >				<form action="<?php echo JRoute::_('index.php?option=com_joomblog&view=blogs'); ?>" method="post" name="adminForm" >
					<?php if (isset($this->blogs) or isset($this->categories)) { ?>
					<fieldset id="filter-bar">
						<?php if ($this->blogs or $search) { ?>
						<div class="filter-search fltlft">
							<label class="filter-search-lbl" for="filter_search">
								<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
							</label>
							<input type="text" name="filter_search" id="filter_search" value="<?php echo $search; ?>" title="" />
							<button type="submit">
								<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
							</button>
							<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
								<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
							</button>
						</div>
						<?php } ?>
						<?php if ($this->users) { ?>
						<div class="filter-select fltrt">
							<?php
								$addcaption	= array(JHtml::_('select.option', '', JText::_('COM_JOOMBLOG_OPTION_SELECT_AUTHOR'), 'id', 'name'));
								$this->users ? $users = array_merge($addcaption, $this->users) : $users = $addcaption;
								echo JHTML::_('select.genericlist',  $users, 'filter_author_id', 'class="inputbox" onchange="this.form.submit()" ', 'id', 'name', $this->state->get('filter.author_id'));
							?>
						</div>
						<?php } ?>
					</fieldset>
					<?php } ?>
					<table class="adminlist">
						<thead>
							<tr>
								<th width="1%">
									<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
								</th>
								<th>
									<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'i.title', $listDirn, $listOrder); ?>
								</th>
								<th>
									<?php echo JText::_('JGLOBAL_DESCRIPTION'); ?>
								</th>
								<th>
									<?php echo JHtml::_('grid.sort', 'COM_JOOMBLOG_FIELD_HEADING_AUTHOR', 'i.user_id', $listDirn, $listOrder); ?>
								</th>
								<th>
									<?php echo JHtml::_('grid.sort', 'COM_JOOMBLOG_FIELD_HEADING_DATE', 'i.create_date', $listDirn, $listOrder); ?>
								</th>
								<th width="1%" class="nowrap">
									<?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'i.hits', $listDirn, $listOrder); ?>
								</th>
								<th width="5%">
									<?php echo JHtml::_('grid.sort', 'JSTATUS', 'i.published', $listDirn, $listOrder); ?>
								</th>
								<th width="5%">
									<?php echo JHtml::_('grid.sort', 'COM_JOOMBLOG_APPROVEBLOG', 'i.approved', $listDirn, $listOrder); ?>
								</th>
								<th width="1%" class="nowrap">
									<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'i.id', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
							</tr>
						</tfoot>
						<tbody>
						<?php if ($this->blogs) {foreach($this->blogs as $i => $item) {
							?>
							<tr class="row<?php echo $i % 2; ?>">
								<td class="center">
									<?php echo JHtml::_('grid.id', $i, $item->id); ?>
								</td>
								<td>
								<?php if ($this->canDo->get('core.edit') or ($this->canDo->get('core.edit.own') and $item->user_id == $this->user->get('id'))) { ?>
									<a href="<?php echo JRoute::_('index.php?option=com_joomblog&task=blog.edit&id='.$item->id);?>">
										<?php echo $this->escape($item->title); ?>
									</a>
								<?php } else { ?>
									<?php echo $this->escape($item->title); ?>
								<?php } ?>
								</td>
								<td>
									<?php echo $item->description; ?>
								</td>
								<td>
									<a href="<?php echo JRoute::_('index.php?option=com_joomblog&task=user.edit&id='.$item->user_id); ?>" >
										<?php echo $item->author; ?>
									</a>
								</td>
								<td class="center">
									<?php echo JHtml::_('date', $item->create_date); ?>
								</td>
								<td class="center">
									<?php echo (int)$item->hits; ?>
								</td>
								<td class="center">
									<?php echo JHtml::_('jgrid.published', $item->published, $i, 'blogs.', $this->canDo->get('core.edit'), 'cb'); ?>
								</td>
								<td class="center">
									<?php if ($item->approved) {$p1 = 'unpublish';$p2 = 'publish'; $titl = JText::_('COM_JOOMBLOG_UNAPPROVED'); } else {$p1 = 'publish';$p2 = 'unpublish'; $titl = JText::_('COM_JOOMBLOG_APPROVED'); }?>
										
										<a title="<?php echo $titl; ?>" onclick="return listItemTask('cb<?php echo $i;?>','blogs.<?php echo $p1; ?>_approve')" href="#" title="<?php echo $titl; ?>" class="jgrid">
											<span class="state <?php echo $p2; ?>">
												<span class="text"><?php echo $titl; ?></span>
											</span>
										</a>
								</td>
								<td>
									<?php echo $item->id; ?>
								</td>
							</tr>
						<?php }} else { ?>
							<tr>
								<td colspan="9" align="center" >
									<?php echo JText::sprintf('COM_JOOMBLOG_FIELD_NONE', 'blogs'); ?>
									<a href="<?php echo JRoute::_('index.php?option=com_joomblog&task=blog.add'); ?>" >
										<?php echo JText::_('COM_JOOMBLOG_FIELD_NONE_A'); ?>
									</a>
								</td>
							</tr>
						<?php } ?>
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
			</td>
		</tr>
	</tbody>
</table>
