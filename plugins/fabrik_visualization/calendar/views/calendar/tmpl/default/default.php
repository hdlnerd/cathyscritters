<?php
/**
* @package		Joomla.Plugin
* @subpackage	Fabrik.visualization.calendar
* @copyright	Copyright (C) 2005 Fabrik. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

//@TODO if we ever get calendars inside packages then the ids will need to be
// Replaced with classes contained within a distinct id

$row = $this->row;
?>
<div id="<?php echo $this->containerId;?>" class="fabrik_visualization" style="border:1px sold;margin:5px;">
	<?php if ($this->params->get('show-title', 0))
	{?>
		<h1><?php echo $row->label;?></h1>
	<?php }?>
	<div class='calendar-message'>

	</div>
	<?php echo $this->loadTemplate('filter'); ?>
		<?php if ($this->canAdd)
		{
		?>
		<a href="#" class="addEventButton" title="Add an event"><?php echo JText::_('PLG_VISUALIZATION_CALENDAR_ADD') ?></a>
	<?php }
	?>
	<?php if ($row->intro_text != '')
	{?>
	<div><?php echo $row->intro_text;?></div>
	<?php }
	?>

</div>