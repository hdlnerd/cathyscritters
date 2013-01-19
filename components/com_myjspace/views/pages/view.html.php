<?php
/**
* @version $Id: view.php $
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
require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util_acl.php';

jimport('joomla.application.component.view' );

class MyjspaceViewPages extends JViewLegacy
{
	function display($tpl = null)
	{
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';

		$db	= JFactory::getDBO();
		$app = JFactory::getApplication();
		$user = JFactory::getuser();
		$pparams = JComponentHelper::getParams('com_myjspace');

		$nb_max_page = $pparams->get('nb_max_page', 1);
		$share_page = $pparams->get('share_page', 0);
		
		$Itemid = JRequest::getInt('Itemid', 0);
		$option = JRequest::getCmd('option');
		$lview = JRequest::getCmd('lview', 'see');
		$uid = JRequest::getInt('uid', 0);

		$Itemid_config = get_menu_itemid('index.php?option=com_myjspace&view=config', $Itemid);
		$Itemid_edit = get_menu_itemid('index.php?option=com_myjspace&view=edit', $Itemid);
		$Itemid_see = get_menu_itemid('index.php?option=com_myjspace&view=see', $Itemid); // Compatibility old install
		$Itemid_see = get_menu_itemid('index.php?option=com_myjspace&view=see&id=&pagename=', $Itemid_see);
		$Itemid_delete = get_menu_itemid('index.php?option=com_myjspace&view=delete', $Itemid);
		
		$lItemid = $Itemid;
		if ($lview == 'config')
			$lItemid = $Itemid_config;		
		else if ($lview == 'edit')
			$lItemid = $Itemid_edit;
		else if ($lview == 'see')
			$lItemid = $Itemid_see;
		else if ($lview == 'delete')
			$lItemid = $Itemid_delete;
			
		$filter_order		= $app->getUserStateFromRequest( "$option.filter_order", 'filter_order', 'a.pagename', 'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest( "$option.filter_order_Dir", 'filter_order_Dir', '', 'word');
		
		$filter_logged		= $app->getUserStateFromRequest( "$option.filter_logged", 'filter_logged', -1, 'int');
		$search				= $app->getUserStateFromRequest( "$option.search", 'search', '', 'string');
		
		if (strpos($search, '"') !== false)
			$search = str_replace(array('=', '<'), '', $search);
		$search = JString::strtolower($search);

		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getInt('limitstart', 0); 
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0); // Case limitstart was changed

		$where = array();
		if (isset( $search ) && $search!= '') {
			if (version_compare(JVERSION, '1.6.0', 'ge'))
				$searchEscaped = $db->Quote('%'.$db->escape($search, true).'%', false);
			else
				$searchEscaped = $db->Quote('%'.$db->getEscaped($search, true).'%', false);
			$where[] = 'a.pagename LIKE '.$searchEscaped;
		}

		if (isset($filter_logged) && $filter_logged > -1)
			$where[] = ' a.blockView = '.$filter_logged.' ';

		$where = ( count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '' );

		if (strlen($where))
			$where .= ' AND';
		else
			$where .= ' WHERE';

		if ($share_page != 0 && ($lview == 'edit' || $lview == 'see') && $uid <= 0 && $user->id != 0) { // List with shared pages whith me
			$where .= " ( a.userid = ".$db->Quote($user->id)." OR a.access IN (".implode(',', $user->getAuthorisedViewLevels()).") )"; // only my pages
		} else {
			if ($uid > 0)
				$where .= ' a.userid = '.$uid;
			else
				$where .= ' a.userid = '.$db->Quote($user->id); // only my pages
		}
		
		$query = 'SELECT COUNT(a.id)'
		. ' FROM `#__myjspace` AS a'
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
	
		$query = 'SELECT a.id, a.access, a.userid, a.pagename, a.blockView, a.hits, a.create_date, LENGTH(content) AS size'
			. ' FROM `#__myjspace` AS a'
			. $where
			. $orderby
		;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();

		// get list of Log Status for dropdown filter
		$logged[] = JHTML::_('select.option', -1, '- '. JText::_('COM_MYJSPACE_TITLEMODEVIEW' ) .' -');
		$group_list = get_assetgroup_list();
		for ($i = 0 ; $i < count($group_list) ; $i++) {
			$logged[] = JHTML::_('select.option', $group_list[$i]->value, $group_list[$i]->text);
		}
		$lists['logged'] = JHTML::_('select.genericlist', $logged, 'filter_logged', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_logged" );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']	= $filter_order;

		// search filter
		$lists['search'] = $search;

		// Breadcrumbs
		$sub_title = '';
		if ($lview == 'config')
			$sub_title = JText::_('COM_MYJSPACE_TITLECONFIG1');
		else if ($lview == 'edit')
			$sub_title = JText::_('COM_MYJSPACE_TITLEEDIT1');
		else if ($lview == 'delete')
			$sub_title = JText::_('COM_MYJSPACE_DELETE');
		else
			$sub_title = JText::_('COM_MYJSPACE_TITLESEE1');
		
		$pathway = $app->getPathway();
		if (($pathid = count($pathway->getPathwayNames())) > 1)
			$pathway->setItemName($pathid-2, JText::_('COM_MYJSPACE_TITLEPAGES'));
		$pathway->addItem($sub_title);		
		
		$this->assignRef('Itemid', $Itemid);
		$this->assignRef('Itemid_config', $Itemid_config);
		$this->assignRef('Itemid_edit', $Itemid_edit);
		$this->assignRef('Itemid_see', $Itemid_see);
		$this->assignRef('Itemid_delete', $Itemid_delete);
		$this->assignRef('lItemid', $lItemid);
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $rows);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('uid',	$uid);
		$this->assignRef('userid', $user->id);
		$this->assignRef('lview', $lview);
		$this->assignRef('sub_title', $sub_title);
		$this->assignRef('total', $total);
		$this->assignRef('nb_max_page', $nb_max_page);
		$this->assignRef('myuserid', $user->id);
		$this->assignRef('share_page', $share_page);		
		
		parent::display($tpl);
	}
}

?>
