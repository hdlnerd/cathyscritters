<?php
/**
* @version $Id: default.php $
* @version		2.0.3 20/10/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util_acl.php';

if (version_compare(JVERSION, '1.6.0', 'ge'))
	$prefix = 'Joomla.';
else
	$prefix = '';

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'components/com_myjspace/assets/myjspace.css');

?>
<div class="myjspace">
<h2><?php echo JText::_('COM_MYJSPACE_TITLEPAGES').' - '.$this->sub_title; ?></h2>
<fieldset class="adminform">
	<legend><?php echo JText::_('COM_MYJSPACE_TITLEPAGES') ?></legend>
	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
	<table>
		<tr>
			<td style="width:100%">
				<?php echo JText::_( 'COM_MYJSPACE_FILTER' ); ?>
				<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button class="btn" onclick="document.getElementById('search').value='';this.form.getElementById('filter_logged').value='0';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td style="white-space:nowrap">
				<?php echo $this->lists['logged'];?>
			</td>
		</tr>
	</table>

	<table class="adminlist">
		<thead>
			<tr>
				<th style="width:3%" class="title">#</th>
				<th  style="width:3%" class="title">  
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('COM_MYJSPACE_LABELID'), 'a.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th style="width:20%;white-space:nowrap" class="title">
					<?php echo JHTML::_('grid.sort', JText::_('COM_MYJSPACE_TITLENAME'), 'a.pagename', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('COM_MYJSPACE_LABELCREATIONDATE'), 'a.create_date', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('COM_MYJSPACE_LABELHITS'), 'a.hits', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('COM_MYJSPACE_LABELSIZE'), 'size', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th style="width:5%;white-space:nowrap;" class="title">
					<?php echo JHTML::_('grid.sort', JText::_('COM_MYJSPACE_TITLEMODEVIEW'), 'a.blockView', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php 
						echo '<div class="list-footer"><span class="limit">'.$this->pagination->getLimitBox(). '</span> '.$this->pagination->getPagesLinks().'</div>';
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$link_pre = "components/com_myjspace/images/";
			$k = 0;
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
				$row = $this->items[$i];
				if ($row->blockView == 1)
					$blockView_img = "publish_g.png";
				else if ($row->blockView == 0)
					$blockView_img = "publish_r.png";
				else if ($row->blockView == 2)
					$blockView_img = "publish_y.png";
				else
					$blockView_img = "publish_x.png";
	
				$blockView_alt = get_assetgroup_label($row->blockView);

				$page_link = 'index.php?option=com_myjspace&view='.$this->lview.'&id='.$row->id.'&Itemid='.$this->lItemid;
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<td>
					<input type="radio" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="<?php echo $prefix; ?>isChecked(this.checked);" />
				</td>
				<td>
					<a href="<?php echo Jroute::_($page_link); ?>"><?php echo $row->id; ?></a>
				</td>
				<td>
					<a href="<?php echo Jroute::_($page_link); ?>"><?php echo $row->pagename; ?></a>
					<?php
						if ($this->share_page != 0) {
							$link_pre = "components/com_myjspace/images/";
							if ($row->userid != $this->myuserid && $this->myuserid != 0) {
								$table = JUser::getTable();
								if ($table->load($row->userid)) {
									$user = JFactory::getUser($row->userid);
								} else {
									$user = new stdClass();
									$user->username = '-';
								}
								echo ' <img src="'.$link_pre.'share.png" style="width:12px; height:12px; border:none" alt="'.$user->username.'" title="'.JText::_('COM_MYJSPACE_LABELUSERNAME').': '.$user->username.'" />';
							} else if ($row->userid == $this->myuserid && $row->access > 0) {
								echo ' <img src="'.$link_pre.'share_nb.png" style="width:12px; height:12px; border:none" alt="access" title="'.JText::_('COM_MYJSPACE_TITLESHAREEDIT').': '.get_assetgroup_label($row->access).'" />';			
							}
						}
					?>
				</td>
				<td><?php echo $row->create_date; ?></td>
				<td><?php echo $row->hits; ?></td>
				<td><?php echo $row->size; ?></td>
				<td>
					<img src="<?php echo $link_pre.$blockView_img;?>" style="width:16px; height:16px; border:none" alt="<?php echo $blockView_alt; ?>" title="<?php echo $blockView_alt; ?>" />
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>

	<br />

<?php
 if (($this->uid > 0 && $this->uid == $this->userid) || ($this->uid == 0 && $this->userid > 0)) {
?>
	<table style="width: 100%;" class="noborder" ><tr>

		<td style="width: 2%;">
		</td>
		<td style="width: 15%;">
<?php
		if (version_compare(JVERSION, '1.6.0', 'lt') || (version_compare(JVERSION, '1.6.0', 'ge') && JFactory::getUser()->authorise('user.config', 'com_myjspace'))) {
?>
			<input name="bt_config" type="submit" class="button btn mjp-list" value="<?php echo JText::_('COM_MYJSPACE_TITLECONFIG1'); ?>" onclick="if (this.form.boxchecked.value==0){alert('<?php echo JText::_( 'COM_MYJSPACE_PAGELIST_ALERT'); ?>');}else{document.getElementById('Itemid').value='<?php echo $this->Itemid_config; ?>';document.getElementById('view').value='config';this.form.submit();}" />
<?php	} ?>
		</td>
		<td style="width: 15%;">
<?php
		if (version_compare(JVERSION, '1.6.0', 'lt') || (version_compare(JVERSION, '1.6.0', 'ge') && JFactory::getUser()->authorise('user.edit', 'com_myjspace'))) {
?>
			<input name="bt_edit" type="submit" class="button btn mjp-list" value="<?php echo JText::_('COM_MYJSPACE_TITLEEDIT1'); ?>" onclick="if (this.form.boxchecked.value==0){alert('<?php echo JText::_( 'COM_MYJSPACE_PAGELIST_ALERT'); ?>');}else{document.getElementById('Itemid').value='<?php echo $this->Itemid_edit; ?>';document.getElementById('view').value='edit';this.form.submit();}" />
<?php	} ?>
		</td>
		<td style="width: 15%;">
<?php
		if (version_compare(JVERSION, '1.6.0', 'lt') || (version_compare(JVERSION, '1.6.0', 'ge') && JFactory::getUser()->authorise('user.see', 'com_myjspace'))) {
?>
			<input name="bt_see" type="submit" class="button btn mjp-list" value="<?php echo JText::_('COM_MYJSPACE_TITLESEE1'); ?>" onclick="if (this.form.boxchecked.value==0){alert('<?php echo JText::_( 'COM_MYJSPACE_PAGELIST_ALERT'); ?>');}else{document.getElementById('Itemid').value='<?php echo $this->Itemid_see; ?>';document.getElementById('view').value='see';this.form.submit();}" />
<?php	} ?>
		</td>
		<td style="width: 15%;">
<?php
		if (version_compare(JVERSION, '1.6.0', 'lt') || (version_compare(JVERSION, '1.6.0', 'ge') && JFactory::getUser()->authorise('user.see', 'com_myjspace'))) {
?>
			<input name="bt_delete" type="submit" class="button btn mjp-list" value="<?php echo JText::_('COM_MYJSPACE_DELETE'); ?>" onclick="if (this.form.boxchecked.value==0){alert('<?php echo JText::_( 'COM_MYJSPACE_PAGELIST_ALERT'); ?>');}else{document.getElementById('Itemid').value='<?php echo $this->Itemid_delete; ?>';document.getElementById('view').value='delete';this.form.submit();}" />
<?php	} ?>
		</td>
		<td style="width: 15%;">
<?php
		if ($this->total < $this->nb_max_page && (version_compare(JVERSION, '1.6.0', 'lt') || (version_compare(JVERSION, '1.6.0', 'ge') && JFactory::getUser()->authorise('user.config', 'com_myjspace')))) {
?>
			<input name="bt_new" type="submit" class="button btn mjp-list" value="<?php echo JText::_('COM_MYJSPACE_CREATEPAGE'); ?>" onclick="document.getElementById('Itemid').value='<?php echo $this->Itemid_config; ?>';document.getElementById('view').value='config';document.getElementById('id').value='-1';this.form.submit();" />
<?php } ?>
		</td>
	</tr></table>
<?php } ?>

	<input type="hidden" name="option" value="com_myjspace" />
	<input type="hidden" name="view" id="view" value="pages" />
	<input type="hidden" name="id" id="id" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="uid" value="<?php echo $this->uid; ?>" />
	<input type="hidden" name="Itemid" id="Itemid" value="<?php echo $this->Itemid; ?>" />
	<input type="hidden" name="boxchecked" id="boxchecked"  value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</fieldset>

</div>
