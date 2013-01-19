<?php

/**
* JoomBlog component for Joomla 1.6
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		
		if (task == 'post.cancel') { Joomla.submitform(task, document.getElementById('item-form'));return;}
		if(document.formvalidator.isValid(document.id('item-form'))) {
			if (document.getElementById('jform_catid').value==0)
						{
							document.getElementById('jform_catid').focus();
							alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
							return false;
						}
			if (document.getElementById('jform_blog_id').value==0)
						{
							document.getElementById('jform_blog_id').focus();
							alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
							return false;
						}			
			
			<?php echo $this->form->getField('articletext')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<table class="admin">
	<tbody>
		<tr>
			<td valign="top" class="lefmenutd" >
				<?php echo $this->loadTemplate('menu');?>
			</td>
			<td valign="top" width="100%" >
				<form action="<?php echo JRoute::_('index.php?option=com_joomblog&view=post&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data" >
					<?php
						echo JHtml::_('tabs.start','item-tabs');
						foreach ($this->form->getFieldsets() as $fieldset) {
						$fields = $this->form->getFieldset($fieldset->name);
						if (count($fields) > 0) {
						echo JHtml::_('tabs.panel',JText::_($fieldset->label), 'item-'.$fieldset->name);
					?>
						<fieldset class="adminform" >
							<ul class="adminformlist">
							<?php
								foreach($this->form->getFieldset($fieldset->name) as $field) {
							?>
								<li><?php 
								echo $field->label;
								if ($field->type=='Editor'){ echo "<div class='clr'></div>"; }
								echo $field->input;
								if ($field->type=='Editor' || $field->type=='jbprivacy'){ echo "<div class='clr'></div>";}
								 ?></li>
							<?php } ?>
							</ul>
							<br class="clr" />
					<?php }} ?>
					<?php echo JHtml::_('tabs.end'); ?>
					<div>
						<input type="hidden" name="task" value="" />
						<?php echo JHtml::_('form.token'); ?>
					</div>
				</form>
			</td>
		</tr>
	</tbody>
</table>

