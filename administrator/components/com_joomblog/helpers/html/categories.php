<?php
// no direct access
defined('_JEXEC') or die;
?>
<table class="admin">
	<tbody>
		<tr>
			<td valign="top" class="lefmenutd" >
				<?php echo $this->loadTemplate('menu');?>
			</td>
			<td valign="top" width="100%" >
				<?php include('components/com_categories/views/categories/tmpl/default.php'); ?>
			</td>
		</tr>
	</tbody>
</table>