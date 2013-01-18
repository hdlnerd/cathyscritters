<?php
/**
* @version		2.0.3 21/10/2012
* @package		myjspacesearch.php
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/
defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

jimport('joomla.plugin.plugin');
jimport('joomla.html.parameter');

class plgSearchMyjspacesearch extends JPlugin
{
	// Add language
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage('plg_search_myjspacesearch', JPATH_ADMINISTRATOR);
	}

	// Search function J!1.5
	function onSearch($text, $phrase = '', $ordering = '', $areas = null) {
		return($this->onContentSearch($text, $phrase, $ordering, $areas));
	}

	// Search Areas function J!1.5
	function onSearchAreas() {
		return($this->onContentSearchAreas());
	}

	// Function to return an array of search areas.
	function onContentSearchAreas() {
		static $areas = array();

		// BS Myjspace component ACL
		if (version_compare(JVERSION,'1.6.0','ge') && ($this->params->get('use_com_acl', 0) && !JFactory::getUser()->authorise('user.search', 'com_myjspace'))) 
			return array();
		
		if (empty($areas)) {
			$areas['myjspace'] = JText::_('PLG_MYJSPACESEARCH_PAGE');
		}
		return $areas;
	}
	
	// Search function
	function onContentSearch($text, $phrase = '', $ordering = '', $areas = null) {
		
		$db = JFactory::getDBO();
		$user_actual = JFactory::getuser();

		// BS MyJspace component not installed
		if (!file_exists(JPATH_ROOT.DS.'components'.DS.'com_myjspace'.DS.'myjspace.php'))
			return array();
		
		// BS Myjspace component ACL
		if (version_compare(JVERSION,'1.6.0','ge') && ($this->params->get( 'use_com_acl', 0) && !JFactory::getUser()->authorise('user.search', 'com_myjspace') )) 
			return array();

		require_once JPATH_ROOT.DS.'components'.DS.'com_myjspace'.DS.'helpers'.DS.'util.php';

		// If the array is not correct, return it:
		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
				return array();
			}
		}

		// Define the parameters
		$limit = $this->params->get('search_limit', 50);
		$contentLimit = $this->params->get('content_limit', 150);
		// URL display mode
		$param_url_mode = $this->params->get('param_url_mode', 0);

		// Cleaning searching terms
		$text = trim($text);

		// Return empty array when nothing was filled in
		if ($text == '') {
			return array();
		}

		// Search for direct characters or for html equivalent for text with accent
		if ($this->params->get('search_html', 1))
			$text = htmlentities($text,ENT_QUOTES,'UTF-8');
			
		// Search
		$wheres = array();
		switch ($phrase) {

			// Search exact
			case 'exact' :
				if (version_compare(JVERSION, '1.6.0', 'ge'))
					$text = $db->Quote('%'.$db->escape($text, true).'%', false);
				else
					$text = $db->Quote('%'.$db->getEscaped($text, true).'%', false);
				$wheres2 = array();
				$wheres2 [] = '`pagename` LIKE ' . $text . ' OR `content` LIKE ' . $text;
				$where = '(' . implode(') OR (', $wheres2) . ')';
				break;

			// Search all or any
			case 'all' :
			case 'any' :

			// Set default
			default :
				$words = explode(' ', $text);
				$wheres = array();
				foreach ($words as $word) {
					if (version_compare(JVERSION, '1.6.0', 'ge'))
						$word = $db->Quote('%'.$db->escape($word, true).'%', false);
					else
						$word = $db->Quote('%'.$db->getEscaped($word, true).'%', false);
					$wheres2 = array();
					$wheres2 [] = '`pagename` LIKE ' . $word . ' OR `content` LIKE ' . $word;
					$wheres [] = implode(' OR ', $wheres2);
				}
				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
				break;
		}

		// Ordering of the results
		switch ($ordering) {

			// Oldest first
			case 'oldest' :
				$order = '`create_date` ASC';
				break;

			// Popular first
			case 'popular' :
				$order = '`hits` ASC, `create_date` DESC';
				break;

			// Newest first
			case 'newest' :
				$order = '`create_date` DESC';
				break;

			// Alphabetic, ascending
			case 'alpha' :
			// Default setting: hit, create_date descending
			default :
				$order = '`hits` ASC, `create_date` DESC';
		}
		
		$query = "SELECT `pagename` AS title, `content` AS text, `create_date` AS created FROM `#__myjspace` WHERE `blockView` != 0 AND `content` != '' AND
				`publish_up` < NOW() AND (`publish_down` >= NOW() OR `publish_down` = '0000-00-00 00:00:00')
				AND {$where} ORDER BY {$order}";

		// Query
		$db->setQuery($query, 0, $limit);
		$rows = $db->loadObjectList();

		// Search for folder
		if ($param_url_mode == 1) {
			$pparams = JComponentHelper::getParams('com_myjspace');
			$repertoire = $pparams->get('foldername', 'myjsp') . '/';
			$id_itemid = '?Itemid=';
		} else {
			$repertoire = 'index.php?option=com_myjspace&view=see&pagename=';
			$id_itemid = '&Itemid=';
		}
		
		// Itemid
		$itemid = $this->params->get('forced_itemid', '');
		if ($itemid == '') {
			if (($itemid = JRequest::getInt('Itemid', 0)) == 0) { // If not into the parameter
				$itemid = JSite::getMenu()->getDefault()->id; // Get the default menu value
			}
		}
		$itemid = get_menu_itemid('index.php?option=com_myjspace&view=see', $itemid);

		foreach ($rows as $key => $row) {
			$rows[$key]->section = JText::_('PLG_MYJSPACESEARCH_PAGE');	
			$rows[$key]->href = Jroute::_($repertoire . $row->title . $id_itemid. $itemid);
			$rows[$key]->browsernav = '2';
			
			// Workaround for preg_replace
			if (strlen($row->text) > 92160) // 90 ko (real limit is little bit biggger)
				$row->text = substr($row->text, 0, 92160);		
			// Html tags
			$row->text = strip_tags($row->text);
	//		$row->text = preg_replace( '#<[^>]*>#i', '', $row->text);
			// Hide #Tags
			$search  = array('#userid', '#name', '#username', '#id', '#pagename', '#access', '#access_edit', '#lastupdate', '#lastaccess', '#createdate', '#description', '#category', '#bsmyjspace', '#fileslist', '#cbprofile', '#hits', '#jomsocial-profile', '#jomsocial-photos', '#inf', '#sup');
			$replace = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			$row->text = str_replace($search, $replace, $row->text);
			// BBCode [register]
			if ($user_actual->id != 0) // if the user is registered
				$row->text = preg_replace('!\[register\](.+)\[/register\]!isU', '$1', $row->text);
			else // if not registered
				$row->text = preg_replace('!\[register\](.+)\[/register\]!isU', '', $row->text); // Keep it secret :-)
			// {} tags (enleves par la fct d'affichage de search)
			// Length
			$row->text = substr($row->text, 0, $contentLimit) . '...';
		}

		// Return the search results in an array	
		return $rows;
	}

}
