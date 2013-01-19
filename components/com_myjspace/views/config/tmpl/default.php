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
		
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'components/com_myjspace/assets/myjspace.css');

if ($this->publish_mode == 2)
	JHTML::_('behavior.calendar');
?>
<h2><?php echo JText::_('COM_MYJSPACE_TITLECONFIG'); ?></h2>
<div class="myjspace">
	<br />
<?php if ($this->blockedit != 2 && $this->alert_root_page == 0) {

?> 
		<form action="<?php echo JRoute::_('index.php'); ?>" method="post">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_MYJSPACE_LABELUSERDETAILS' ); ?></legend>
			<table class="admintable">
				<tr>
					<td class="key">
						<label><?php echo  JText::_('COM_MYJSPACE_PAGELINK'); ?></label>
					</td>
					<td>
						<a href="<?php echo $this->link; ?>"><?php echo $this->link; ?></a>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo  JText::_('COM_MYJSPACE_PAGEBBCODE'); ?></label>
					</td>
					<td>
						[url]<?php echo $this->link ?>[/url]
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_TITLENAME'); ?></label>
					</td>
					<td>
					<?php if ($this->pagename_username == 1) { ?>
						<?php echo $this->pagename; ?>
						<input type="hidden" name="mjs_pagename" id="mjs_pagename"  value="<?php echo $this->pagename; ?>" /> <?php echo $this->msg_tmp; ?>
					<?php } else { ?>
						<input type="text" name="mjs_pagename" id="mjs_pagename" class="inputbox" size="40" value="<?php echo $this->pagename; ?>" /> <?php echo $this->msg_tmp; ?>
					<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELUSERNAME'); ?></label>
					</td>
					<td>
						<?php echo $this->username; ?>
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
<?php
				$model_page_list_count = count($this->model_page_list);
				if ($this->msg_tmp != '' && $model_page_list_count >= 2) { // If several (2 pages + text_to_choixe) model page list
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_MYJSPACE_TITLEMODEL' ); ?></label>
					</td>
					<td>
						<select name="mjs_model_page" id="mjs_model_page">
<?php
							for ($i = 0; $i < $model_page_list_count; $i++) {
								if ($this->model_page_list[$i])
									echo '<option value="'.$i.'">'.$this->model_page_list[$i]."</option>\n";
							}
?>							
						</select>
					</td>
				</tr>
<?php				
				}
?>			
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
<?php				
	if ($this->page_increment == 1) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELHITS'); ?></label>
					</td>
					<td>
						<?php echo $this->hits;
							if ($this->hits > 0) { ?>
						&nbsp;<input name="reset_hits" type="submit" class="button btn" value="<?php echo JText::_('COM_MYJSPACE_LABELHITSRESET'); ?>" onclick="document.getElementById('resethits').value='yes';this.form.submit();" />
						<input name="resethits" id="resethits" type="hidden" value="no" />
						<?php } ?>
					</td>
				</tr>
<?php
	}
	if ($this->publish_mode == 2) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELPUBLISHUP'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('calendar', $this->publish_up, "publish_up", "publish_up", $this->date_fmt_pub, array('size'=>'10')) .' '. $this->img_publish_up; ?>
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
				if ($this->share_page != 0) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_MYJSPACE_TITLESHAREEDIT' ); ?></label>
					</td>
					<td>
<?php
					if ($this->share_page == 2) {
						echo "<select name=\"mjs_share\" id=\"mjs_share\">\n";
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
						echo "</select>\n";
					} else
						echo get_assetgroup_label($this->access);
?>
					</td>
				</tr>
<?php
				}
				if ($this->share_page != 0 && $this->access > 0) {
?>
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
						<option value="">-</option>
						<?php
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
	if ($this->user_mode_view == 1) {
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
			<input name="Itemid" type="hidden" value="<?php echo $this->Itemid_config; ?>" />
			<input name="option" type="hidden" value="com_myjspace" />
			<input name="task" type="hidden" value="save_config" />
			<input name="id" type="hidden" value="<?php echo $this->id; ?>" />
			<input type="submit" class="button btn mjp-config" value="<?php echo JText::_('COM_MYJSPACE_SAVE'); ?>" />
		</fieldset>
		</form>
<?php
	if ($this->msg_tmp == '') {
?>
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_MYJSPACE_TITLEACTION') ?></legend>
		<table style="width: 100%;" class="noborder" ><tr>
		<td style="width: 20%;">
<?php
		if (version_compare(JVERSION, '1.6.0', 'lt') || (version_compare(JVERSION, '1.6.0', 'ge') && JFactory::getUser()->authorise('user.edit', 'com_myjspace'))) {
?>
			<form method="post" action="<?php echo Jroute::_('index.php?option=com_myjspace&view=edit&id='.$this->id.'&Itemid='.$this->Itemid_edit); ?>">
				<input type="submit" class="button btn mjp-config" value="<?php echo JText::_('COM_MYJSPACE_TITLEEDIT1'); ?>" />
			</form>
<?php	} ?>
		</td>
		<td style="width: 20%;">
<?php
		if (version_compare(JVERSION, '1.6.0', 'lt') || (version_compare(JVERSION, '1.6.0', 'ge') && JFactory::getUser()->authorise('user.see', 'com_myjspace'))) {
?>
			<form method="post" action="<?php echo Jroute::_('index.php?option=com_myjspace&view=see&id='.$this->id.'&Itemid='.$this->Itemid_see); ?>">
				<input type="submit" class="button btn mjp-config" value="<?php echo JText::_('COM_MYJSPACE_TITLESEE1'); ?>" />
			</form>
<?php	} ?>
		</td>
		<td style="width: 20%;">
<?php
		if (version_compare(JVERSION, '1.6.0', 'lt') || (version_compare(JVERSION, '1.6.0', 'ge') && JFactory::getUser()->authorise('user.delete', 'com_myjspace'))) {
?>
			<form method="post" action="<?php echo Jroute::_('index.php?option=com_myjspace&view=delete&id='.$this->id.'&Itemid='.$this->Itemid_delete); ?>">
				<input type="submit" class="button btn mjp-config" value="<?php echo JText::_('COM_MYJSPACE_DELETE'); ?>" />
			</form>
<?php	} ?>
		</td>
		<td style="width: 20%;">
<?php
		if ($this->nb_max_page > 1 && (version_compare(JVERSION, '1.6.0', 'lt') || (version_compare(JVERSION, '1.6.0', 'ge') && JFactory::getUser()->authorise('user.pages', 'com_myjspace')) )) {
?>
			<form method="post" action="<?php echo Jroute::_('index.php?option=com_myjspace&view=pages&Itemid='.$this->Itemid_pages); ?>">
				<input type="submit" class="button btn mjp-config" value="<?php echo JText::_('COM_MYJSPACE_NEW'); ?>" />
			</form>
<?php	} ?>
		</td>
		</tr></table>
	</fieldset>
<?php
		}
		if ($this->uploadadmin && ($this->uploadimg > 0 || $this->uploadmedia > 0)) {
?>
	<fieldset class="adminform ">
		<legend><?php echo JText::_('COM_MYJSPACE_UPLOADTITLE') ?></legend>
		<table style="width: 100%;" class="noborder"><tr>
			<td style="width: 10%;"></td>
			<td>
			<form method="post" action="<?php echo JRoute::_('index.php'); ?>" enctype="multipart/form-data" >
				<input name="Itemid" type="hidden" value="<?php echo $this->Itemid_config; ?>" />
				<input name="option" type="hidden" value="com_myjspace" />
				<input name="task" type="hidden" value="upload_file" />
				<input name="id" type="hidden" value="<?php echo $this->id; ?>" />
				<input type="file" name="upload_file" />
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $this->file_max_size; ?>" />
				<br />
				<input type="submit" class="button btn mjp-config" value="<?php echo JText::_('COM_MYJSPACE_UPLOADUPLOAD') ?>" onclick="document.getElementById('progress_div').style.visibility='visible';" />
				<div id="progress_div" style="visibility: hidden;"><img src="<?php echo JURI::root(); ?>components/com_myjspace/assets/progress.gif" alt="wait..." style="padding-top: 5px;" /></div>
			</form>
			</td>
		<?php
		if (1) { // No list = not list for deleting ... :-) Can be a separate option in the futur 	?>
			<td>
			<form  method="post" action="<?php echo JRoute::_('index.php'); ?>" >
				<input name="Itemid" type="hidden" value="<?php echo $this->Itemid_config; ?>" />
				<input name="option" type="hidden" value="com_myjspace" />
				<input name="task" type="hidden" value="delete_file" />
				<input name="id" type="hidden" value="<?php echo $this->id; ?>" />
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
			<td style="width: 10%;"></td>
		</tr></table>
	</fieldset>
<?php
		}
   } else if ($this->alert_root_page == 1)
 		echo JText::_('COM_MYJSPACE_ALERTYOURADMIN');  
	else if ($this->blockedit == 1)
		echo JText::_('COM_MYJSPACE_EDITBLOCKED');
   else
		echo JText::_('COM_MYJSPACE_EDITLOCKED');

	if ($this->display_myjspace_ref == 1) {
 ?>
 	<div class="bsfooter">
		<a href="<?php echo Jroute::_('index.php?option=com_myjspace&view=myjspace&Itemid='.$this->Itemid); ?>">BS MyJspace</a>
	</div>
 <?php } ?>
 
</div>
