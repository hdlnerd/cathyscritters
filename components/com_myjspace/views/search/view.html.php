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

class MyjspaceViewSearch extends JViewLegacy
{
	function display($tpl = null)
	{		
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';
		
		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
	  	$user = JFactory::getuser();
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();

		// Param
		$Itemid = JRequest::getInt('Itemid', 0);
		$Itemid_see = get_menu_itemid('index.php?option=com_myjspace&view=see', $Itemid); // compatibility old install
		$Itemid_see = get_menu_itemid('index.php?option=com_myjspace&view=see&id=&pagename=', $Itemid_see);
		$aff_titre = JRequest::getInt('title', 1); 		// print the title
		$aff_select = JRequest::getInt('select', 1);	// print the search selector
		$aff_sort = JRequest::getInt('sort', 4); 		// sort order
		$separ = JRequest::getInt('separ', 0);			// tab or space or \n between each space
		$svalue = JRequest::getVar('svalue', '');		// Search key for search content value
		$catid = JRequest::getInt('catid', 0);			// catid
		
		// Pagination & limit
		$search_pagination = $pparams->get('search_pagination', 1);
		$search_max_line = intval($pparams->get('search_max_line', 100));
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getInt('limitstart', 0); 
		if ($limit > $search_max_line)
			$limit = $search_max_line;

		// Result display
		$search_aff_add = intval($pparams->get('search_aff_add', 69));
		if ($search_aff_add <= 0 || $search_aff_add > 255)
			$search_aff_add = 1;
		
		// Folder root dir
		$link_folder_print = $pparams->get('link_folder_print', 1);
		if ($link_folder_print == 1) {
			$foldername = BSHelperUser::getFoldername();
		} else
			$foldername = null;
		
		// Selection checked
		$check_search = JRequest::getVar('check_search', array('name', 'content', 'description'));
		foreach ($check_search as $i => $value) {
			$check_search_asso[$value] = '1';
		}
	
		// Autorisation & search
		if ($limit >= 0) {
			jimport('joomla.html.pagination');
			$total = BSHelperUser::loadPagename($aff_sort, 0, 1, 1, 1, $check_search_asso, $svalue, $search_aff_add, 0, true, $catid);
			$result = BSHelperUser::loadPagename($aff_sort, $limit, 1, 1, 1, $check_search_asso, $svalue, $search_aff_add, $limitstart, false, $catid);
			$pagination = new JPagination( $total, $limitstart, $limit);
		} else {
			$result = array();
			$aff_select = 0;
			$aff_titre = 0;
			$pagination = new stdClass();
		}

        // Web page title
		if ($pparams->get('pagetitle', 1) == 1) {
			$title = JText::_('COM_MYJSPACE_TITLESEARCH');
			if (empty($title)) {
				$title = $app->getCfg('sitename');
			} elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
				$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
			} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
				$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
			}
			if ($title)
				$document->setTitle($title);
		}

		// Breadcrumbs
		$pathway = $app->getPathway();
		if (($pathid = count($pathway->getPathwayNames())) > 1)
			$pathway->setItemName($pathid-2, JText::_('COM_MYJSPACE_PAGE'));
		$pathway->addItem(JText::_('COM_MYJSPACE_TITLESEARCH', ''));
		
		// Lightbox usage & preview
		$add_lightbox = $pparams->get('add_lightbox', 1);
		
		// Date format
		$date_fmt = $pparams->get('date_fmt', 'Y-m-d H:i:s');
		$date_fmt_tab = explode(' ',$date_fmt);
		$date_fmt = $date_fmt_tab[0];

		// Categories
		$categories = BSHelperUser::GetCategories(1);
		$categories_label = BSHelperUser::GetCategoriesLabel($categories);
		
		// Rss feef
		$url_rss_feed = '';
		if (intval($pparams->get('rss_feed', 50)) > 0) {
			$url = 'index.php?option=com_myjspace&view=search&format=feed';
			if ($catid != '')
				$url .= '&catid='.$catid;
			if ($svalue != '')
				$url .= '&svalue='.$svalue;
			if ($aff_sort != 4)
				$url .= '&sort='.$aff_sort;

			$url_rss_feed = '<a href="'.JRoute::_($url).'"><img src="'.JURI::root().'components/com_myjspace/images/rss.gif" alt="rss" title="rss" /></a>';
			
			$document->addHeadLink(JRoute::_($url.'&type=rss'), 'alternate', 'rel', array('type' => 'application/rss+xml', 'title' => 'RSS 1.0'));
			$document->addHeadLink(JRoute::_($url.'&type=atom'), 'alternate', 'rel', array('type' => 'application/atom+xml', 'title' => 'Atom'));
		}
		
		// Var assign
		$this->assignRef('Itemid', $Itemid);
		$this->assignRef('Itemid_see', $Itemid_see);
		$this->assignRef('aff_titre', $aff_titre);
		$this->assignRef('aff_select', $aff_select);
		$this->assignRef('aff_sort', $aff_sort);
		$this->assignRef('svalue', $svalue);
		$this->assignRef('separ', $separ);		
		$this->assignRef('result', $result);
		$this->assignRef('search_aff_add', $search_aff_add);
		$this->assignRef('add_lightbox', $add_lightbox);	
		$this->assignRef('date_fmt', $date_fmt);
		$this->assignRef('link_folder_print', $link_folder_print);
		$this->assignRef('foldername', $foldername);
		$this->assignRef('check_search_asso', $check_search_asso);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('search_pagination', $search_pagination);
		$this->assignRef('catid', $catid);
		$this->assignRef('categories', $categories);
		$this->assignRef('categories_label', $categories_label);
		$this->assignRef('url_rss_feed', $url_rss_feed);
		
		parent::display($tpl);
	}
}
?>
