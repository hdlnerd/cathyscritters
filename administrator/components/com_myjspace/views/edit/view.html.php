<?php
/**
* @version $Id: view.html.php $
* @version		2.0.3 20/10/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'legacy.php';

jimport('joomla.application.component.view');
jimport('joomla.html.parameter');

class MyjspaceViewEdit extends JViewLegacy
{
	function display($tpl = null)
	{
		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		
		// Tags buttons
		$e_name = JRequest::getVar('e_name', 'mjs_editable');
		$allow_tag_myjsp_iframe = $pparams->get('allow_tag_myjsp_iframe', 1);
		$allow_tag_myjsp_include = $pparams->get('allow_tag_myjsp_include', 1);
			
		$this->assignRef('e_name', $e_name);
		$this->assignRef('allow_tag_myjsp_iframe', $allow_tag_myjsp_iframe);
		$this->assignRef('allow_tag_myjsp_include', $allow_tag_myjsp_include);

		parent::display($tpl);
	}
	
}

?>
