<?php
/**
* @version $Id: default.php $ 
* @version		2.0.2 03/09/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'components/com_myjspace/assets/myjspace.css');
?>
<div class="myjspace myjsp-w-100">
<?php if ($this->link_folder == 1) { ?>
		
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MYJSPACE_ADMIN_SYNCHRO_DB_FOLDER').': '.JText::_('COM_MYJSPACE_ADMIN_DELETE_FOLDER');?></legend>
			<form method="post" action="<?php echo JRoute::_('index.php'); ?>">
				<input name="option" type="hidden" value="com_myjspace" />
				<input name="view" type="hidden" value="tools" />
				<input name="task" type="hidden" value="adm_delete_folder" />
				<input type="submit" class="button mjp-config" value="<?php echo JText::_('COM_MYJSPACE_ADMIN_DELETE_FOLDER'); ?>" />
				<?php echo JText::_('COM_MYJSPACE_ADMIN_DELETE_FOLDER_2');?>
			</form>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MYJSPACE_ADMIN_SYNCHRO_DB_FOLDER').': '.JText::_('COM_MYJSPACE_ADMIN_CREATE_FOLDER');?></legend>
			<form method="post" action="<?php echo JRoute::_('index.php'); ?>">
				<input name="option" type="hidden" value="com_myjspace" />
				<input name="view" type="hidden" value="tools" />
				<input name="task" type="hidden" value="adm_create_folder" />
				<input type="submit" class="button mjp-config" value="<?php echo JText::_('COM_MYJSPACE_ADMIN_CREATE_FOLDER'); ?>" />
			</form>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MYJSPACE_ADMIN_SYNCHRO_DB_FOLDER').': '.JText::_('COM_MYJSPACE_ADMIN_DELETE_EMPTY_PAGES');?></legend>
			<form method="post" action="<?php echo JRoute::_('index.php'); ?>">
				<input name="option" type="hidden" value="com_myjspace" />
				<input name="view" type="hidden" value="tools" />
				<input name="task" type="hidden" value="adm_delete_empty_pages" />
				<input type="submit" class="button mjp-config" value="<?php echo JText::_('COM_MYJSPACE_ADMIN_DELETE_EMPTY_PAGES'); ?>" />
			</form>
		</fieldset>
<?php } else echo JText::_('COM_MYJSPACE_ADMIN_LINK_MSG'); ?>

<br />
</div>
