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
?>
<form action="<?php echo JRoute::_('index.php?option=com_joomblog&view=sampledata'); ?>" method="post" name="adminForm" id="adminform" class="adminform">
	<table class="admin" style="" >
		<tr>
			<td valign="top" class="lefmenutd" >
				<?php echo $this->loadTemplate('menu');?>
			</td>
			<td valign="top" style="width: 100%;" >
				<table class="adminlist" style="border:1px solid #cccccc;" >
					<tbody>
						<?php if ($this->canDo->get('core.admin') or $this->canDo->get('core.create')) { ?>
						<tr>
							<td width="100%">
								Will be created 2 demonstration blogs and 6 demonstration blog posts. Do you want to install it for sure?
							</td>
						</tr>
						<tr>
							<td>
								<div>
									<div class="button2-left">
										<div class="blank">
											<a onclick="Joomla.submitbutton('installSampleData')" href="#">Yes</a>
										</div>
									</div>
									&nbsp;&nbsp;
									<div class="button2-left">
										<div class="blank">
											<a onclick="window.location.href = 'index.php?option=com_joomblog&amp;view=posts';" href="#">No</a>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<?php } else { ?>
						<tr>
							<td width="100%">
								You have no rights to perform this action!
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</td>
		</tr>
	</table>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>