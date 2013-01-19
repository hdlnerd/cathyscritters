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
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

JHTML::_('behavior.modal', 'a.modal_jform_created_by');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'components/com_myjspace/assets/myjspace.css');
$document->addScript(JURI::root() . 'media/system/js/mootools-more.js');
	
if ($this->publish_mode == 2)
	JHTML::_('behavior.calendar'); 

?>
<div class="myjspace">
	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="col myjsp-w-45 fltlft">

		<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_MYJSPACE_LABELUSERDETAILS' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_PAGELINK'); ?></label>
					</td>
					<td>
						<a href="<?php echo $this->link ?>"><?php echo $this->link ?></a>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_TITLENAME'); ?></label>
					</td>
					<td>
						<input type="text" name="mjs_pagename" id="mjs_pagename" class="inputbox" size="40" value="<?php echo $this->pagename; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELPAGEID'); ?></label>
					</td>
					<td>
						<?php echo $this->id; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
<?php if (version_compare(JVERSION, '1.6.0', 'ge')) { ?>
						<label><?php echo  JText::_('COM_MYJSPACE_LABELNAME'); ?></label>
<?php } else { ?>
						<label><?php echo  JText::_('COM_MYJSPACE_LABELUSERNAME'); ?></label>
<?php } ?>
					</td>
					<td>
<?php if (version_compare(JVERSION, '1.6.0', 'ge')) { ?>
						<input type="text" name="mjs_username2" id="mjs_username2" class="inputbox" size="40" value="<?php echo $this->username; ?>" disabled="disabled" />
<?php } else { ?>
						<input type="text" name="mjs_username" id="mjs_username" class="inputbox" size="40" value="<?php echo $this->username; ?>" />
<?php } ?>
						<input type="hidden" name="mjs_userid" id="mjs_userid" value="0" />
<?php if (version_compare(JVERSION, '1.6.0', 'ge')) { ?>
					<div class="button2-left">
						<div class="blank">
							<a class="modal_jform_created_by" title="Select User" href="index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=jform_created_by" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><?php echo  JText::_('COM_MYJSPACE_LABELSELECTUSER'); ?></a>
						</div>
					</div>
<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELMETAKEY'); ?></label>
					</td>
					<td>
						<input type="text" name="mjs_metakey" id="mjs_metakey" class="inputbox" size="40" value="<?php echo $this->metakey; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELCREATIONDATE'); ?></label>
					</td>
					<td>
						<?php echo $this->create_date; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELLASTUPDATEDATE'); ?></label>
					</td>
					<td>
						<?php echo $this->last_update_date; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELLASTACCESSDATE'); ?></label>
					</td>
					<td>
						<?php echo $this->last_access_date; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELHITS'); ?></label>
					</td>
					<td>
						<?php echo $this->hits;
							if ($this->hits > 0) { ?>
						&nbsp;<input name="reset_hits" type="submit" class="button" value="<?php echo JText::_('COM_MYJSPACE_LABELHITSRESET'); ?>" onclick="document.getElementById('resethits').value='yes';this.form.submit();" />
						<input name="resethits" id="resethits" type="hidden" value="no" />
						<?php } ?>
					</td>
				</tr>
<?php				
	if ($this->publish_mode != 0) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELPUBLISHUP'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('calendar', $this->publish_up, "publish_up", "publish_up", $this->date_fmt_pub, array('size'=>'10')) .' '. $this->img_publish_up;; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELPUBLISHDOWN'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('calendar', $this->publish_down, "publish_down", "publish_down", $this->date_fmt_pub, array('size'=>'10')) .' '. $this->img_publish_down; ?>
					</td>
				</tr>						
<?php
	}
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_MYJSPACE_TITLEMODEEDIT' ); ?></label>
					</td>
					<td>
						<select name="mjs_mode_edit" id="mjs_mode_edit">
							<option value="0" <?php if ($this->blockedit == 0) echo " selected='selected'"; ?> ><?php echo JText::_('COM_MYJSPACE_TITLEMODEEDIT0') ?></option>
							<option value="1" <?php if ($this->blockedit == 1) echo " selected='selected'"; ?> ><?php echo JText::_('COM_MYJSPACE_TITLEMODEEDIT1') ?></option>
							<option value="2" <?php if ($this->blockedit == 2) echo " selected='selected'"; ?> ><?php echo JText::_('COM_MYJSPACE_TITLEMODEEDIT2') ?></option>						</select>
					</td>
				</tr>
<?php
				if ($this->group_list) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_MYJSPACE_TITLESHAREEDIT' ); ?></label>
					</td>
					<td>
						<select name="mjs_share" id="mjs_share">
<?php
						if ($this->access == 0)
							echo "<option value=\"0\" selected=\"selected\">&nbsp;-</option>\n";
						else
							echo "<option value=\"0\">&nbsp;-</option>\n";

						foreach ($this->group_list as $value) {
							if ($value->value != 1) {
								if ($value->value == $this->access)
									echo '<option value="'.$value->value.'" selected="selected">'.'&nbsp;'.$value->text."</option>\n";
								else
									echo '<option value="'.$value->value.'">'.'&nbsp;'.$value->text."</option>\n";
							}
						}
?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_MYJSPACE_TITLEUPDATENAME' ); ?></label>
					</td>
					<td>
						<?php echo $this->modified_by; ?>
					</td>
				</tr>
<?php
				}
?>				
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_MYJSPACE_TITLEMODEVIEW' ); ?></label>
					</td>
					<td>
						<select name="mjs_mode_view" id="mjs_mode_view">
<?php
						foreach ($this->blockview_list as $value) {
							if ($value->value == $this->blockview)
								echo '<option value="'.$value->value.'" selected="selected">'.'&nbsp;'.$value->text."</option>\n";
							else
								echo '<option value="'.$value->value.'">'.'&nbsp;'.$value->text."</option>\n";
						}
?>
						</select>
					</td>
				</tr>
<?php
	$categories_count = count($this->categories);
	if ($categories_count > 0) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_MYJSPACE_LABELCATEGORY' ); ?></label>
					</td>
					<td>
						<select name="mjs_categories" id="mjs_categories">
<?php
							for ($i = 0; $i < $categories_count; $i++) {
								if ($this->categories[$i]['value'] == $this->catid)
									echo '<option value="'.$this->categories[$i]['value'].'" selected="selected">'.'&nbsp;'.str_repeat('- ',$this->categories[$i]['level']).$this->categories[$i]['text']."</option>\n";
								else
									echo '<option value="'.$this->categories[$i]['value'].'">'.'&nbsp;'.str_repeat('- ',$this->categories[$i]['level']).$this->categories[$i]['text']."</option>\n";
							}
?>
						</select>
					</td>
				</tr>
<?php
	}

	if ($this->tab_template != null) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_MYJSPACE_TEMPLATE' ); ?></label>
					</td>
					<td>
						<select name="mjs_template" id="mjs_template">
						<?php
						if ('' == $this->template)
							echo "<option value=\"\" selected=\"selected\">-</option>\n";
						else
							echo "<option value=\"\">-</option>\n";

						foreach ($this->tab_template as $i => $value) {
							if ($value == $this->template)
								echo '<option value="'.$value.'" selected="selected">'.$value."</option>\n";
							else
								echo '<option value="'.$value.'">'.$value."</option>\n";
						}
						?>
						</select>
					</td>
				</tr>
<?php
	}

	if ($this->link_folder == 1 && ($this->uploadimg > 0 || $this->uploadmedia > 0)) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELUSAGE0'); ?></label>
					</td>
					<td>
						<?php echo JText::sprintf('COM_MYJSPACE_LABELUSAGE1',$this->page_size,$this->dir_max_size,$this->page_number); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELUSAGE2'); ?></label>
					</td>
					<td>
						<?php echo $this->file_img_size; ?>
					</td>
				</tr>
<?php
	}	
?>				
			</table>
			
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
			<input name="option" type="hidden" value="com_myjspace" />
			<input name="task" type="hidden" value="adm_save_page" /><br />

		</fieldset>
	</div>
	
	<div class="col myjsp-w-55 fltlft">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_MYJSPACE_PAGE' ); ?></legend>
<?php
	$editor = JFactory::getEditor($this->editor_selection);
	echo $editor->display('mjs_content', $this->content, $this->edit_x, $this->edit_y, null, null, $this->editor_button);
?>
	<br />
		</fieldset>
	</div>

	</form>

<?php
	if ($this->uploadadmin && ($this->uploadimg > 0 || $this->uploadmedia > 0)) {
?>
	<div class="col myjsp-w-100 fltlft">
	<fieldset class="adminform ">
		<legend><?php echo JText::_('COM_MYJSPACE_UPLOADTITLE') ?></legend>
		<table style="width: 100%;" class="noborder"><tr>
			<td style="width: 10%;"></td>
			<td>
			<form method="post" action="<?php echo JRoute::_('index.php'); ?>" enctype="multipart/form-data" >
				<input name="option" type="hidden" value="com_myjspace" />
				<input name="task" type="hidden" value="upload_file" />
				<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
				<input type="file" name="upload_file" />
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $this->file_max_size; ?>" />
				<br />
				<input type="submit" class="button btn mjp-config" value="<?php echo JText::_('COM_MYJSPACE_UPLOADUPLOAD') ?>" onclick="document.getElementById('progress_div').style.visibility='visible';" />
				<div id="progress_div" style="visibility: hidden;"><img src="<?php echo str_replace('/administrator', '', JURI::root()); ?>components/com_myjspace/assets/progress.gif" alt="wait..." style="padding-top: 5px;" /></div>
			</form>
			</td>
		<?php
		if (1) { // No list = not list for deleting ... :-) Can be a separate option in the futur 	?>
			<td>
			<form  method="post" action="<?php echo JRoute::_('index.php'); ?>" >
				<input name="option" type="hidden" value="com_myjspace" />
				<input name="task" type="hidden" value="delete_file" />
				<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
				<select name="delete_file" id="delete_file">
					<option value="" selected="selected"><?php echo JText::_('COM_MYJSPACE_UPLOADCHOOSE') ?></option>
					<?php
						$nb = count($this->tab_list_file);
						for ($i = 0 ; $i < $nb ; ++$i ) 
							echo '<option value="'.$this->tab_list_file[$i].'">'.$this->tab_list_file[$i]."</option>\n";
					?>
				</select>
				<br />
				<input type="submit" value="<?php echo JText::_('COM_MYJSPACE_UPLOADDELETE') ?>" class="button btn mjp-config" />
				<div>&nbsp;</div>			
			</form>
			</td>
		<?php } ?>
		</tr></table>
	</fieldset>
	</div>
<?php
		}
?>	

	<div class="clr"></div>
</div>
