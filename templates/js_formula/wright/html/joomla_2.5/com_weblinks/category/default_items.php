<?php
// no direct access
defined('_JEXEC') or die;
// Code to support edit links for weblinks
// Create a shortcut for params.
$params = &$this->item->params;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtmlBehavior::framework();
// Get the user object.
$user = JFactory::getUser();
// Check if user is allowed to add/edit based on weblinks permissinos.
$canEdit = $user->authorise('core.edit', 'com_weblinks');
$canCreate = $user->authorise('core.create', 'com_weblinks');
$canEditState = $user->authorise('core.edit.state', 'com_weblinks');

$n = count($this->items);
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>

<?php if (empty($this->items)) : ?>
	<p> <?php echo JText::_('COM_WEBLINKS_NO_WEBLINKS'); ?></p>
<?php else : ?>

<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($this->params->get('show_pagination_limit')) : ?>
		<fieldset class="filters">
		<legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend>
		<div class="display-limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		</fieldset>
	<?php endif; ?>

	<table class="category">
		<?php if ($this->params->get('show_headings')==1) : ?>
		<thead>
			<tr>
				<th class="title">
					<?php echo JHtml::_('grid.sort',  'COM_WEBLINKS_GRID_TITLE', 'title', $listDirn, $listOrder); ?>
				</th>
			
				<?php if ($this->params->get('show_link_hits')) : ?>
				<th class="hits">
					<?php echo JHtml::_('grid.sort',  'JGLOBAL_HITS', 'hits', $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>
		</tr>
	</thead>
	<?php endif; ?>
	
	<tbody>
	<?php foreach ($this->items as $i => $item) : ?>
		<?php if ($this->items[$i]->state == 0) : ?>
			<tr class="system-unpublished cat-list row<?php echo $i % 2; ?>">
		<?php else: ?>
			<tr class="cat-list row<?php echo $i % 2; ?>" >
		<?php endif; ?>

				<td class="title">
					<?php if ($this->params->get('link_icons') <> -1) : ?>
					<span class="jicons-icons">
						<?php echo JHtml::_('image','system/'.$this->params->get('link_icons', 'weblink.png'), JText::_('COM_WEBLINKS_LINK'), NULL, true);?>
					</span>
					<?php endif; ?>
				<p>
					<?php
						// Compute the correct link
						$menuclass = 'category'.$this->pageclass_sfx;
						$link = $item->link;
						$width	= $item->params->get('width');
						$height	= $item->params->get('height');
						if ($width == null || $height == null) {
							$width	= 600;
							$height	= 500;
						}

						switch ($item->params->get('target', $this->params->get('target')))
						{
							case 1:
								// open in a new window
								echo '<a href="'. $link .'" target="_blank" class="'. $menuclass .'" rel="nofollow">'.
									$this->escape($item->title) .'</a>';
								break;

							case 2:
								// open in a popup window
								$attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width='.$this->escape($width).',height='.$this->escape($height).'';
								echo "<a href=\"$link\" onclick=\"window.open(this.href, 'targetWindow', '".$attribs."'); return false;\">".
									$this->escape($item->title).'</a>';
								break;
							case 3:
								// open in a modal window
								JHtml::_('behavior.modal', 'a.modal'); ?>
								<a class="modal" href="<?php echo $link;?>"  rel="{handler: 'iframe', size: {x:<?php echo $this->escape($width);?>, y:<?php echo $this->escape($height);?>}}">
									<?php echo $this->escape($item->title). ' </a>' ;
								break;

							default:
								// open in parent window
								echo '<a href="'.  $link . '" class="'. $menuclass .'" rel="nofollow">'.
									$this->escape($item->title) . ' </a>';
								break;
						}
					?>
					<?php // Code to add the edit link for the weblink. ?>

							<?php if ($canEdit) : ?>
								<ul class="actions">
									<li class="edit-icon">
										<?php echo JHtml::_('icon.edit',$item, $params); ?>
									</li>
								</ul>
							<?php endif; ?>
				</p>

				<?php if (($this->params->get('show_link_description')) AND ($item->description !='')): ?>
					<p>
					<?php echo nl2br($item->description); ?>
					</p>
				<?php endif; ?>
			</td>
			<?php if ($this->params->get('show_link_hits')) : ?>
			<td class="hits">
				<?php echo $item->hits; ?>
			</td>
			<?php endif; ?>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php if ($this->params->get('show_pagination')) : ?>
 <div class="pagination">
	<?php if ($this->params->def('show_pagination_results', 1)) : ?>
		<p class="counter">
			<?php echo $this->pagination->getPagesCounter(); ?>
		</p>
	<?php endif; ?>
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
<?php endif; ?>

		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	</form>
<?php endif; ?>