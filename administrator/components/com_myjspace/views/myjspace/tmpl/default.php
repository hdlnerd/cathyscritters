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
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_MYJSPACE_TITLE');?></legend>
		<?php echo $this->version; ?>
	</fieldset>
	
<?php if ($this->newversion != '' ) { ?>
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_MYJSPACE_NEWVERSION');?></legend>
		<?php echo '<span style="color:orange">'.JText::_('COM_MYJSPACE_NEWVERSION').'</span> '.$this->newversion; ?>
	</fieldset>
<?php } ?>

	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_MYJSPACE_STATISTICS');?></legend>
		
		<table class="admintable">
			<tr>
				<td class="key">
					<label><?php echo  JText::_('COM_MYJSPACE_NBPAGESTOTAL'); ?></label>
				</td>
				<td>
					<?php echo $this->nb_pages_total; ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label><?php echo  JText::_('COM_MYJSPACE_NBDISTINCTUSERS'); ?></label>
				</td>
				<td>
					<?php echo $this->nb_distinct_users; ?>
				</td>
			</tr>
		</table>
		
	</fieldset>

<br />
</div>

