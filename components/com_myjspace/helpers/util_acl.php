<?php
/**
* @version $Id: util_acl.php $
* @version		2.0.3 20/10/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Pas d'accès direct
defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

function get_assetgroup_list() {

	$pparams = JComponentHelper::getParams('com_myjspace');

	$group_list[0] = new stdClass();
	$group_list[0]->value = 0;
	$group_list[0]->text = JText::_('COM_MYJSPACE_TITLEMODEVIEW1');
	
	if (version_compare(JVERSION, '1.6.0', 'ge') && $pparams->get('user_mode_view_acl', 0) == 1) {
		$group_list = array_merge($group_list, JHtml::_('access.assetgroups'));
	} else {
		$group_list[1] = new stdClass();
		$group_list[1]->value = 1;
		$group_list[1]->text = JText::_('COM_MYJSPACE_TITLEMODEVIEW0');
		$group_list[2] = new stdClass();
		$group_list[2]->value = 2;
		$group_list[2]->text = JText::_('COM_MYJSPACE_TITLEMODEVIEW2');
	}

	return $group_list;
}

function get_assetgroup_label($access = 0) {

	$group_list = get_assetgroup_list();

	foreach ($group_list as $value) {
		if ($value->value == $access) {
			return $value->text;
		}
	}

	return '';
}

?>
