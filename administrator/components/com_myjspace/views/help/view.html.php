<?php
/**
* @version $Id: view.html.php $
* @version		2.0.3 20/10/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'legacy.php';

jimport('joomla.application.component.view');

class MyjspaceViewHelp extends JViewLegacy
{
	/**
	 * display method of BSbanner view
	 * @return void
	 **/
	function display($tpl = null)
	{	
		require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
		require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';
		
	// Menu bar
		JToolBarHelper::title( JText::_('COM_MYJSPACE_HOME') .': <small>'.JText::_('COM_MYJSPACE_HELP').'</small>', 'help_header.png');

	// Content
		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$file_max_size = $pparams->get('file_max_size', 204800);
		$editor_selection = $pparams->get('editor_selection', 'myjsp');
		$nb_max_page = $pparams->get('nb_max_page', 1);
		$link_folder = $pparams->get('link_folder', 1);
		$nb_max_page = $pparams->get('nb_max_page', 1);
		
		// Page root folder
		$dirname = JPATH_ROOT.DS.BSHelperUser::getFoldername();
		if (is_writable($dirname))
			$iswritable = 1;
		else
			$iswritable = 0;
			
		// Check all index format (=version)
		$nb_index_ko = BSHelperUser::CheckVersionIndexPage();

		// Report
		$report_js = "
		window.addEvent('domready', function(){
				$('link_sel_all').addEvent('click', function(e){
					$('report').select();
				});
			});
		";
		$report = configuration_report();
		$report .= ' [quote]';
		$report .= '[b]Editor selection:[/b] '.$editor_selection;
		$report .= ' | [b]Index Format:[/b] ';
		if ($nb_index_ko == 0)
			$report .= ' ok';
		else
			$report .= ' ko';

		$report .= ' | [b]Link as folder:[/b] ';			
		if ($link_folder == 1)
			$report .= ' yes';
		else
			$report .= ' no';
	
		$report .= ' | [b]Root Page dir:[/b] '.BSHelperUser::getFoldername();
		
		$report .= ' | [b]Root Page dir writable:[/b] ';
		if ($iswritable == 1)
			$report .= ' ok';
		else
			$report .= ' ko';
			
		$report .= ' | [b]Max. pages per user:[/b] '.$nb_max_page;
			
		$report .= '[/quote]';
				
		// ACL J1.6+ (user.pages) Migration to Myjspace 2.0.0
		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			$query = "SELECT COUNT(`rules`) FROM `#__assets` WHERE `title` = 'com_myjspace' AND `name` = 'com_myjspace' AND `rules` LIKE '%user.pages%'";
			$db	= JFactory::getDBO();
			$db->setQuery($query);
			$db->query();
			$count = $db->loadResult();
		}
		if (!isset($count))
			$count = 0;

		if ($count == 0)
			$acl_rules_2000 = false;
		else
			$acl_rules_2000 = true;

		// GD
		if (function_exists("gd_info"))
			$gd_support = true;
		else
			$gd_support = false;
	
		$this->assignRef('file_max_size', $file_max_size);
		$this->assignRef('iswritable', $iswritable);
		$this->assignRef('link_folder', $link_folder);
		$this->assignRef('editor_selection', $editor_selection);
		$this->assignRef('nb_index_ko', $nb_index_ko);
		$this->assignRef('report_js', $report_js);
		$this->assignRef('report', $report);
		$this->assignRef('nb_max_page', $nb_max_page);
		$this->assignRef('acl_rules_2000', $acl_rules_2000);
		$this->assignRef('gd_support', $gd_support);
		
		parent::display($tpl);
	}
}

?>
