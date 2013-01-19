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

require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'components/com_myjspace/assets/myjspace.css');
$document->addScriptDeclaration($this->report_js);
?>
<div class="myjspace myjsp-w-100">
<fieldset class="adminform">
<legend><?php echo JText::_('COM_MYJSPACE_ADMIN_SOME_HELP'); ?></legend>
<br />
<div><strong><?php echo JText::_('COM_MYJSPACE_ADMIN_INFO_0'); ?></strong></div>
<ul>
<?php
	$span_fin_ko = 'style="color:red">';

	$span_fin = '>';
	if (ini_get('file_uploads') != 1)
		$span_fin = $span_fin_ko;
	echo '<li>PHP file_uploads = <span '.$span_fin.ini_get('file_uploads')."</span></li>\n";

	echo '<li>PHP upload_tmp_dir = '.ini_get('upload_tmp_dir')."</li>\n";

	$span_fin = '>';
	if (convertBytes(ini_get('upload_max_filesize')) < $this->file_max_size)
		$span_fin = $span_fin_ko;
	echo '<li>PHP upload_max_filesize = <span '.$span_fin.convertBytes(ini_get('upload_max_filesize')).'</span> ('.JText::_('COM_MYJSPACE_ADMIN_SUPERIOR').' '.$this->file_max_size.' : '. JText::_('COM_MYJSPACE_LABELUSAGE2') . ")</li>\n";

	$span_fin = '>';
	if (convertBytes(ini_get('post_max_size')) < $this->file_max_size)
		$span_fin = $span_fin_ko;
	echo '<li>PHP post_max_size = <span '.$span_fin.convertBytes(ini_get('post_max_size')).'</span> ('.JText::_('COM_MYJSPACE_ADMIN_SUPERIOR').' '.$this->file_max_size. ' : '. JText::_('COM_MYJSPACE_LABELUSAGE2') . ")</li>\n";

	/*
   	file_uploads= On/Off permet d'autoriser ou non l'envoi de fichiers.
	upload_tmp_dir = répertoire permet de définir le répertoire temporaire permettant d'accueillir le fichier uploadé.
	upload_max_filesize = 2M permet de définir la taille maximale autorisée pour le fichier. 
		Si cette limite est dépassée, le serveur enverra un code d'erreur.
	post_max_size indique la taille maximale des données envoyées par un formulaire. 
		Cette directive prime sur upload_max_filesize, il faut donc s'assurer d'avoir post_max_size supérieure à upload_max_filesize 
*/
?>
</ul>
<div><strong><?php echo JText::_('COM_MYJSPACE_ADMIN_INFO_OTHER'); ?></strong></div>
<ul>
<?php
	// Editor
	echo '<li>';
	$check_editor = check_editor_selection($this->editor_selection);
	$span_fin = '> ';
	if ($check_editor == false)
		$span_fin = $span_fin_ko;
	echo JText::_('COM_MYJSPACE_ADMIN_EDITOR'). ' <span '.$span_fin.$this->editor_selection.'</span>';		
	if ($check_editor == false) // Use the Joomla default editor
		echo JText::_('COM_MYJSPACE_ADMIN_EDITOR_SELECTION');
	echo '</li>';
	
	if ($this->link_folder == 1) {
		// Root folder
		echo '<li>'.JText::_('COM_MYJSPACE_ADMIN_ISWRITABLE');
		if ($this->iswritable)
			echo JText::_('COM_MYJSPACE_ADMIN_FOLDER_OK');
		else
			echo JText::_('COM_MYJSPACE_ADMIN_FOLDER_KO');
		echo "</li>\n";
				
		// Index
		if ($this->nb_index_ko >= 0) {
			echo '<li>';
			if ($this->nb_index_ko == 0)
				echo JText::_('COM_MYJSPACE_ADMIN_INDEX_FORMAT_OK');
			else
				echo JText::sprintf('COM_MYJSPACE_ADMIN_INDEX_FORMAT_KO', '<span '.$span_fin_ko.$this->nb_index_ko.'</span>');
			echo '</li>';
		}
	}
	
	// ACL 2.0.0+ for J1.6+
	if (version_compare(JVERSION, '1.6.0', 'ge') && $this->nb_max_page > 1 && $this->acl_rules_2000 == false)
		echo '<li>'.JText::_('COM_MYJSPACE_ADMIN_ACL_MSG').'</li>';	
	
	// GD
		echo '<li>'.JText::_('COM_MYJSPACE_ADMIN_GD');
		if ($this->gd_support == true)
			echo JText::_('COM_MYJSPACE_ADMIN_OK');
		else
			echo JText::_('COM_MYJSPACE_ADMIN_KO').JText::_('COM_MYJSPACE_LABELUSAGE4');
		echo "</li>\n";
?>
</ul>
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_('COM_MYJSPACE_ADMIN_REPORT'); ?></legend>
	<div><a href="#" id="link_sel_all" ><?php echo JText::_('COM_MYJSPACE_ADMIN_REPORT_SELECT'); ?></a></div>
	<textarea id="report" name="report" cols="" rows="" style="width:100%; height:120px;"><?php echo htmlspecialchars($this->report, ENT_COMPAT, 'UTF-8'); ?></textarea>
</fieldset>

<br />
</div>
