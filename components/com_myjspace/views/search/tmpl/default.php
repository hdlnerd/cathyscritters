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

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'components/com_myjspace/assets/myjspace.css');
if ($this->add_lightbox == 1) {
	$document->addStyleSheet(JURI::root() . 'components/com_myjspace/assets/lytebox/lytebox.css');
	$document->addScript(JURI::root() . 'components/com_myjspace/assets/lytebox/lytebox.js');
}
require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';

if ($this->aff_titre)
	echo '<h2>'.JText::_('COM_MYJSPACE_TITLESEARCH').'</h2>';

JHTML::_('behavior.tooltip');
?>
<div class="myjspace">
<br />
<?php
 if ($this->aff_select) {
 	// Search selector to print
?>
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_MYJSPACE_TITLESEARCH') ?><?php if ($this->url_rss_feed != '') {echo '&nbsp;&nbsp;'.$this->url_rss_feed;} ?></legend>
		    <form action="<?php echo JRoute::_('index.php?option=com_myjspace&view=search&Itemid='.$this->Itemid); ?>" method="post">
<?php
		echo '&nbsp;'.JText::_('COM_MYJSPACE_SEARCHSORT').' <select name="sort" onchange="this.form.submit()">';
		$sort_list = array(JText::_('COM_MYJSPACE_SEARCHSORT0'),JText::_('COM_MYJSPACE_SEARCHSORT1'),JText::_('COM_MYJSPACE_SEARCHSORT2'),JText::_('COM_MYJSPACE_SEARCHSORT3'),JText::_('COM_MYJSPACE_SEARCHSORT4'),JText::_('COM_MYJSPACE_SEARCHSORT5'));
		$nb = count($sort_list);
		for ($i = 0; $i < $nb ; ++$i ) {
			if ($i == $this->aff_sort )
				echo '<option selected="selected" value="'.$i.'">&nbsp;'.$sort_list[$i].'&nbsp;</option>';
			else
				echo '<option value="'.$i.'">&nbsp;'.$sort_list[$i].'&nbsp;</option>';
		}
		echo '</select>';
?>
				<?php echo JText::_('COM_MYJSPACE_SEARCHSEARCHPNAME') ?><input type="checkbox" name="check_search[]" <?php if (isset($this->check_search_asso['name'])) echo 'checked="checked"'; ?> value="name" onchange="this.form.submit()" />
				<?php echo JText::_('COM_MYJSPACE_SEARCHSEARCHCONTENT') ?><input type="checkbox" name="check_search[]" <?php if (isset($this->check_search_asso['content'])) echo 'checked="checked"'; ?> value="content" onchange="this.form.submit()" />
				<?php echo JText::_('COM_MYJSPACE_SEARCHSEARCHDESCRIPTION') ?><input type="checkbox" name="check_search[]" <?php if (isset($this->check_search_asso['description'])) echo 'checked="checked"'; ?> value="description" onchange="this.form.submit()" />
<?php
	$categories_count = count($this->categories);
	if ($categories_count > 0) {
		echo '&nbsp;'.JText::_( 'COM_MYJSPACE_LABELCATEGORY' );
?>
				<select name="catid" id="catid">
				<option value="0">&nbsp;-</option>
<?php
				for ($i = 0; $i < $categories_count; $i++) {
					if ($this->categories[$i]['value'] == $this->catid)
						echo '<option value="'.$this->categories[$i]['value'].'" selected="selected">'.'&nbsp;'.str_repeat('- ',$this->categories[$i]['level']).$this->categories[$i]['text']."</option>\n";
					else
						echo '<option value="'.$this->categories[$i]['value'].'">'.'&nbsp;'.str_repeat('- ',$this->categories[$i]['level']).$this->categories[$i]['text']."</option>\n";
				}
	}			
?>
				</select>				
				<input type="text" name="svalue" id="svalue" class="inputbox" size="10" value="<?php echo $this->svalue; ?>" />
				<input type="submit" id="bouton" name="bouton" value="<?php echo JText::_('COM_MYJSPACE_SEARCH'); ?>" class="button" />
<?php 
		if ($this->search_pagination == 1)
			echo '<div class="list-footer"><span class="limit">'.$this->pagination->getLimitBox(). '</span> '.$this->pagination->getPagesLinks().'</div>';
?>
			</form>
		</fieldset>
		<br />
<?php } ?>

	<div class="myjspace_result_search">
	<fieldset class="adminform">
<?php
	$nb = count($this->result);

	if ($this->separ == 1) {
		$separ_l = '';
		$separ_l_img = '';
		$separ_r = ' ';
		$separ_end = '&nbsp;-&nbsp;';
	} else if ($this->separ == 2) {
		$separ_l = '';
		$separ_l_img = '';
		$separ_r = ' ';
		$separ_end = '<br />';
	} else { // == 0 (tab)
		$separ_l = '<td>';
		$separ_l_img = '<td class="mjsp_search_img">';
		$separ_r = '</td>';
		$separ_end = '';
	}

	$nb_metakey = 0;
	if ($this->search_aff_add & 4) {
		for ($i = 0; $i < $nb ; ++$i ) {
			if ($this->result[$i]['metakey'] != '' )
				$nb_metakey++;
		}
	}
	
	if ($this->separ == 0)
		echo '<table class="mjsp_search_tab">'."\n";
		
	for ($i = 0; $i < $nb ; ++$i) {
		if ($this->separ == 0)
			echo '<tr>';
			
		if ($this->search_aff_add & 64) { // Image (64)
			echo $separ_l_img.exist_image_html($this->foldername.'/'.$this->result[$i]['pagename'], JPATH_SITE, $this->add_lightbox, $this->result[$i]['pagename']).$separ_r;
		}			

		if ($this->search_aff_add & 1) { // Pagename (1)
			if ($this->link_folder_print == 1)
				$url = Jroute::_($this->foldername.'/'.$this->result[$i]['pagename']);
			else
				$url = Jroute::_('index.php?option=com_myjspace&view=see&pagename='.$this->result[$i]['pagename'].'&Itemid='.$this->Itemid_see);
			echo $separ_l.'<a href="'.$url.'">'.$this->result[$i]['pagename'].'</a>'.$separ_r;
		}
			
		if ($this->search_aff_add & 2) { // Username (2)
			$table   = JUser::getTable();
			if ($table->load($this->result[$i]['userid'])) { // Test if user exists before retreiving info
				$user = JFactory::getUser($this->result[$i]['userid']);
			} else { // User does no exist any more !
				$user = new stdClass();
				$user->username = '';
			}
			echo $separ_l.$user->username.$separ_r;
		}

		if ($this->search_aff_add & 8) { // Date created (8)
			echo $separ_l.date($this->date_fmt, strtotime($this->result[$i]['create_date'])).$separ_r;
		}
		
		if ($this->search_aff_add & 16) { // Date updated (16)
			echo $separ_l.date($this->date_fmt, strtotime($this->result[$i]['last_update_date'])).$separ_r;
		}
		
		if ($this->search_aff_add & 32) { // Hits (32)
			echo $separ_l.$this->result[$i]['hits'].$separ_r;
		}

		if ($this->search_aff_add & 128) { // Category (128)
			if (isset($this->categories_label[$this->result[$i]['catid']]))
				echo $separ_l.$this->categories_label[$this->result[$i]['catid']].$separ_r;
			else
				echo $separ_l.'-'.$separ_r;
		}
		
		if ($this->search_aff_add & 4 && $nb_metakey > 0) { // Description (4)
			echo $separ_l.$this->result[$i]['metakey'].$separ_r;
		}
			
		if ($this->separ == 0)
			echo "</tr>\n";
		else if ($i < ($nb-1))
			echo "<br />\n";
	}
	if ($this->separ == 0)
		echo '</table>';
?>
	</fieldset>


	</div>
</div>
