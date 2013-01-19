<?php

/**
* JoomBlog component for Joomla 1.6
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.tooltip');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$search 	= $this->escape($this->state->get('filter.search_plugin'));
$type 		= $this->escape($this->state->get('filter.search_plugin_type'));
$saveOrder	= ($listOrder == 'a.name');
?>
<table class="admin">
	<tbody>
		<tr>
			<td valign="top" class="lefmenutd" >
				<?php echo $this->loadTemplate('menu');?>
			</td>
			<td valign="top" width="100%" >				<form action="<?php echo JRoute::_('index.php?option=com_joomblog&view=plugins'); ?>" method="post" name="adminForm" >
					<fieldset id="filter-bar">
						<div class="filter-search fltlft">
							<label class="filter-search-lbl" for="filter_search">
								<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
							</label>
							<input type="text" name="filter_search_plugin" id="filter_search_plugin" value="<?php echo $search; ?>" title="" />
							<button type="submit">
								<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
							</button>
							<button type="button" onclick="document.id('filter_search_plugin').value='';this.form.submit();">
								<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
							</button>
						</div>
						<div class="filter-select fltrt">
							<?php 
								$state = array();
									$state[] = JHTML::_('select.option', 0, JText::_( 'COM_JOOMBLOG_PLUGINS_SELECT_TYPE' ));
									$state[] = JHTML::_('select.option','content', JText::_( 'content' ) );
									$state[] = JHTML::_('select.option','editors-xtd', JText::_( 'editors-xtd' ) );
								echo JHTML::_('select.genericlist',  $state, 'filter_plugin_type', 'onchange="this.form.submit()"', 'value', 'text', $type );
							
							?>
						</div>
					</fieldset>
					<table class="adminlist">
						<thead>
							<tr>
								<th width="1%">
									<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
								</th>
								<th>
									<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
								</th>
								<th width="10%" >
									<?php echo JHtml::_('grid.sort', 'COM_JOOMBLOG_PLUGINS_TYPE', 'a.folder', $listDirn, $listOrder); ?>
								</th>
								<th width="5%">
									<?php echo JHtml::_('grid.sort', 'JSTATUS', 'b.published', $listDirn, $listOrder); ?>
								</th>
								<th width="1%" class="nowrap">
									<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
							</tr>
						</tfoot>
						<tbody>
						<?php 
						
						if ($this->plugins) 
						{
							foreach($this->plugins as $i => $item) {
							?>
							<tr class="row<?php echo $i % 2; ?>">
								<td class="center">
									<?php echo JHtml::_('grid.id', $i, $item->id); ?>
								</td>
								<td>								
									<?php echo $this->escape($item->name); ?>
								</td>
								<td><?php echo $item->folder; ?></td>
								<td class="center">
									<?php echo JHtml::_('jgrid.published', $item->published, $i, 'plugins.', $this->canDo->get('core.edit'), 'cb'); ?>
								</td>
								<td>
									<?php echo $item->id; ?>
								</td>
							</tr>
						<?php }
						} else { ?>
							<tr>
								<td colspan="9" align="center" >
									<?php echo JText::sprintf('COM_JOOMBLOG_FIELD_NONE', 'plugins'); ?>
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
