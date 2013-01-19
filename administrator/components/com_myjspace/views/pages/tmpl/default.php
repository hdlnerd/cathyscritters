<?php
/**
* @version $Id: default.php $
* @version		2.0.3 23/10/2012
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

?>
<div class="myjspace">
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
	<table>
		<tr>
			<td style="width:100%">
				<?php echo JText::_( 'COM_MYJSPACE_FILTER' ); ?>
				<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_type').value='0';this.form.getElementById('filter_logged').value='0';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td style="white-space:nowrap">
				<?php echo $this->lists['type'];?>
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
				<th style="width:15%;white-space:nowrap" class="title">
					<?php echo JHTML::_('grid.sort', JText::_('COM_MYJSPACE_LABELUSERNAME'), 'b.username', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('COM_MYJSPACE_LABELENABLEUSER'), 'b.block', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
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
					<?php echo JHTML::_('grid.sort', JText::_('COM_MYJSPACE_TITLEMODEEDIT'), 'a.blockEdit', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th style="width:5%;white-space:nowrap;" class="title">
					<?php echo JHTML::_('grid.sort', JText::_('COM_MYJSPACE_TITLEMODEVIEW'), 'a.blockView', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="11">
					<?php echo $this->pagination->getListFooter(); ?>
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
				if (!$row->username)
					$row->block = 1	; // Look coherence :-)
				$userblock_img = $row->block? 'active_no.png' : 'active_yes.png';
				$userblock_alt = $row->block? JText::_( 'Blocked' ) : JText::_( 'Enabled' );
				
				if ($row->blockEdit == 1)
					$blockEdit_img = 'active_su.png';
				else if ($row->blockEdit == 2)
					$blockEdit_img = 'active_no.png';
				else
					$blockEdit_img = 'active_yes.png';
				
				$blockEdit_alt = $row->blockEdit? JText::_( 'COM_MYJSPACE_TITLEMODEEDIT1' ) : JText::_( 'COM_MYJSPACE_TITLEMODEEDIT0' );

				if ($row->blockView == 1)
					$blockView_img = "publish_g.png";
				else if ($row->blockView == 0)
					$blockView_img = "publish_r.png";
				else if ($row->blockView == 2)
					$blockView_img = "publish_y.png";
				else
					$blockView_img = "publish_x.png";
	
				$blockView_alt = get_assetgroup_label($row->blockView);
					
				if (version_compare(JVERSION, '1.6.0', 'ge'))
					$id_link = 'index.php?option=com_users&task=user.edit&id=' . $row->userid. '';
				else
					$id_link = 'index.php?option=com_users&view=user&task=edit&cid[]='. $row->userid. '';
				
				$page_link = 'index.php?option=com_myjspace&view=page&id='. $row->id. '';
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
						if ($this->share_page != 0 && $row->access > 0) {
							$link_pre = "../components/com_myjspace/images/";
							echo ' <img src="'.$link_pre.'share_nb.png" style="width:12px; height:12px; border:none" alt="access" title="'.JText::_('COM_MYJSPACE_TITLESHAREEDIT').': '.get_assetgroup_label($row->access).'" />';
						}
					?>
				</td>
				<td>
<?php
					if ($row->username)
						echo '<a href="'.Jroute::_($id_link).'">'.$row->username.'</a>';
					else
						echo '<img src="'.$link_pre.'active_no.png" style="width:16px; height:16px; border:none" alt="" />';
?>
				</td>				
				<td>
					<img src="<?php echo $link_pre.$userblock_img;?>" style="width:16px; height:16px; border:none" alt="<?php echo $userblock_alt; ?>" title="<?php echo $userblock_alt; ?>" />
				</td>
				<td><?php echo $row->create_date; ?></td>
				<td><?php echo $row->hits; ?></td>
				<td><?php echo $row->size; ?></td>
				<td style="text-align:center">
					<img src="<?php echo $link_pre.$blockEdit_img;?>" style="width:16px; height:16px; border:none" alt="<?php echo $blockEdit_alt; ?>" title="<?php echo $blockEdit_alt; ?>" />
				</td>
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

	<input type="hidden" name="option" value="com_myjspace" />
	<input type="hidden" name="view" value="pages" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
