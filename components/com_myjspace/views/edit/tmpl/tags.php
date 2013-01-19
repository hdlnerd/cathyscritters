<?php
/**
* @version $Id: tags.php $
* @version		2.0.1 06/08/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'components/com_myjspace/assets/myjspace.css');
$document->addStyleSheet(JURI::root() . 'media/system/css/adminlist.css');

// Js code
$js = "window.addEvent('load', function() {
			new JCaption('img.caption');
		});

function insertTags(valeur_choix) {
	valeur_choix += ' ';
	window.parent.jInsertEditorText(valeur_choix, '".$this->e_name."');
	window.parent.SqueezeBox.close();
	return false;
}
	";

// Tags list
$tag_list = array('#userid', '#name', '#username', '#pagename', '#id', '#access', '#lastupdate', '#lastaccess', '#createdate', '#description', '#fileslist');
$tag_label = array(JText::_('COM_MYJSPACE_TAG_USERID'), 
					JText::_('COM_MYJSPACE_TAG_NAME'),
					JText::_('COM_MYJSPACE_TAG_USERNAME'),
					JText::_('COM_MYJSPACE_TAG_PAGENAME'),
					JText::_('COM_MYJSPACE_TAG_ID'),
					JText::_('COM_MYJSPACE_TAG_ACCESS'),					
					JText::_('COM_MYJSPACE_TAG_LASTUPDATE'),
					JText::_('COM_MYJSPACE_TAG_LASTACCESS'),
					JText::_('COM_MYJSPACE_TAG_CREATEDATE'),
					JText::_('COM_MYJSPACE_TAG_DESCRIPTION'),
					JText::_('COM_MYJSPACE_TAG_FILESLIST'));
		
if (@file_exists(JPATH_ROOT.DS.'components'.DS.'com_comprofiler')) { // Add CB
	$tag_list = array_merge($tag_list, array('#cbprofile'));
	$tag_label = array_merge($tag_label, array(JText::_('COM_MYJSPACE_TAG_CBPROFILE')));
}

if (@file_exists(JPATH_ROOT.DS.'components'.DS.'com_community')) { // Add Jomsocial
	$tag_list = array_merge($tag_list, array('#jomsocial-profile','#jomsocial-photos'));
	$tag_label = array_merge($tag_label, array(JText::_('COM_MYJSPACE_TAG_JOOMSOCIALPROFILE'), JText::_('COM_MYJSPACE_TAG_JOOMSOCIALPHOTOS')));
}

if ($this->allow_tag_myjsp_iframe == 1) { // Allow Tag myjsp iframe
	$tag_label = array_merge($tag_label, array(JText::_('COM_MYJSPACE_TAG_MYJSP_IFRAME')));
	$tag_list = array_merge($tag_list, array('{myjsp iframe URL}'));
}

if ($this->allow_tag_myjsp_include == 1) {  // Allow Tag myjsp include
	$tag_label = array_merge($tag_label, array(JText::_('COM_MYJSPACE_TAG_MYJSP_INCLUDE')));
	$tag_list = array_merge($tag_list, array('{myjsp include URL}'));
}

$document->addScript(JURI::root() . 'media/system/js/mootools-core.js');
$document->addScript(JURI::root() . 'media/system/js/core.js');
$document->addScript(JURI::root() . 'media/system/js/caption.js');
$document->addScriptDeclaration($js);

?>
<div class="myjspace">
	<fieldset class="addtags">
		<table class="adminlist">
		<tbody>
<?php
	for($i=0;$i<sizeof($tag_list);$i++) {
		$pos=$i%2;
		echo '<tr class="row'.$pos.'"><td>';
		echo '<a class="pointer" href="#" onclick="insertTags(\''.$tag_list[$i].'\');">'.$tag_label[$i]."</a>\n"; 
		echo '</td></tr>';
    } 
?>
		</tbody>
		</table>
	</fieldset>

</div>
