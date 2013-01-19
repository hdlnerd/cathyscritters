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
			<td valign="top" width="100%" >				<form action="<?php echo JRoute::_('index.php?option=com_joomblog&view=comments'); ?>" method="post" name="adminForm" >
					<?php if ($this->comments) { ?>
					<fieldset id="filter-bar">
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
					</fieldset>
					<?php } ?>
					<table class="adminlist">
						<thead>
							<tr>
								<th width="1%" >
									<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
								</th>
								<th>
									<?php echo JHtml::_('grid.sort', 'COM_JOOMBLOG_FIELD_HEADING_COMMENT', 'i.comment', $listDirn, $listOrder); ?>
								</th>
								<th>
									<?php echo JHtml::_('grid.sort', 'COM_JOOMBLOG_FIELD_HEADING_POST', 'c.title', $listDirn, $listOrder); ?>
								</th>
								<th>
									<?php echo JHtml::_('grid.sort', 'COM_JOOMBLOG_FIELD_HEADING_AUTHOR', 'i.name', $listDirn, $listOrder); ?>
								</th>
								<th>
									<?php echo JHtml::_('grid.sort', 'COM_JOOMBLOG_FIELD_HEADING_DATE', 'i.created', $listDirn, $listOrder); ?>
								</th>
								<th width="5%">
									<?php echo JHtml::_('grid.sort', 'JSTATUS', 'i.published', $listDirn, $listOrder); ?>
								</th>
								<th width="1%" class="nowrap">
									<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'i.id', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
							</tr>
						</tfoot>
						<tbody>
						<?php if ($this->comments) {foreach($this->comments as $i => $item) {
							?>
							<tr class="row<?php echo $i % 2; ?>">
								<td class="center" >
									<?php echo JHtml::_('grid.id', $i, $item->id); ?>
								</td>
								<td>
								<?php if ($this->canDo->get('core.admin')) { ?>
									<a href="<?php echo JRoute::_('index.php?option=com_joomblog&task=comment.edit&id='.$item->id);?>" ><?php echo JText::_('COM_JOOMBLOG_FIELD_HEADING_COMMENT'); ?>:</a>
									<?php echo $this->crop($item->comment); ?>
								<?php } else { ?>
									<?php echo $this->crop($item->comment); ?>
								<?php } ?>
								</td>
								<td>
									<a href="<?php echo JRoute::_('index.php?option=com_joomblog&task=post.edit&id='.$item->contentid);?>">
										<?php echo $item->post_title; ?>
									</a>
								</td>
								<td>
									<?php if ($item->user_id) { ?>
									<a href="<?php echo JRoute::_('index.php?option=com_joomblog&task=user.edit&id='.$item->user_id); ?>" >
										<?php echo $item->author; ?>
									</a>
									<?php } else { ?>
										<?php echo ucfirst($item->name); ?>
									<?php } ?>
								</td>
								<td>
									<?php echo JHtml::_('date', $item->created); ?>
								</td>
								<td class="center">
									<?php echo JHtml::_('jgrid.published', $item->published, $i, 'comments.', $this->canDo->get('core.admin'), 'cb'); ?>
								</td>
								<td>
									<?php echo $item->id; ?>
								</td>
							</tr>
						<?php }} else { ?>
							<tr>
								<td colspan="7" align="center" >
									<?php echo JText::sprintf('COM_JOOMBLOG_FIELD_NONE', 'comments'); ?>
									<a href="<?php echo JRoute::_('index.php?option=com_joomblog&task=comment.add'); ?>" >
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
