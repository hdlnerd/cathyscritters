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

class MyjspaceViewTools extends JViewLegacy
{
	/**
	 * display method of BSbanner view
	 * @return void
	 **/
	function display($tpl = null)
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'version.php';
	// Menu bar
		JToolBarHelper::title(JText::_('COM_MYJSPACE_HOME') .': <small>'.JText::_('COM_MYJSPACE_TOOLS').'</small>', 'config.png');

	// Content
	// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$link_folder = $pparams->get('link_folder', 1);
		$this->assignRef('link_folder', $link_folder);

		parent::display($tpl);
	}
}

?>
