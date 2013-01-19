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

/**
 * HTML View pour la page 
 * @package myjspace
 */
 
class MyjspaceViewMyjspace extends JViewLegacy
{
	function display($tpl = null)
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'version.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';

	// Menu bar
		$doc = JFactory::getDocument();
		$style = ".icon-48-myjspace { background: url('components/com_myjspace/images/myjspace.png') 0 0 no-repeat; }";
		$doc->addStyleDeclaration($style);
		JToolBarHelper::title(JText::_('COM_MYJSPACE_HOME'), 'myjspace.png');

	// Content
        $version = BS_Helper_version::get_version('com_myjspace.manage');
        $this->assignRef('version', $version);
		
	// New version
		$newversion = BS_Helper_version::get_newversion('com_myjspace');
        $this->assignRef('newversion', $newversion);
		
	// Nb pages
		$nb_pages_total = BSHelperUser::myjsp_count_nb_page();
        $this->assignRef('nb_pages_total', $nb_pages_total);

	// Nb distinct users
		$nb_distinct_users = BSHelperUser::myjsp_count_nb_user();
        $this->assignRef('nb_distinct_users', $nb_distinct_users);

		parent::display($tpl);
	}
}

?>
