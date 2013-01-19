<?php
/**
* @version $Id: user.php $
* @version		2.0.3 20/10/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Pas d'accés direct
defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// Component Helper
jimport('joomla.application.component.helper');

// -----------------------------------------------------------------------------

class BSHelperUser
{
	var $id = 0; // V 2.0.0
	var $userid = 0;
	var $catid = 0; // V 2.0.1
	var $modified_by = 0; // V 2.0.1
	var $access = 0; // V 2.0.1
	var $pagename = null;
	var $content = null;
	var $blockEdit = 0;
	var $blockView = 1; // V 2.0.2 = 1
	var $foldername = null;
	var $create_date = null;
	var $last_access_date = null;
	var $last_update_date = null;
	var $last_access_ip = 0; // crc32b value
	var $hits = 0;
	var $publish_up = null;
	var $publish_down = null;
	var $metakey = null;
	var $template = null; // J! template
	var $index_format_version = 5; // V 2.0.2

// Constructeur
	function bshelperuser() {
		$this->foldername = self::getFoldername();	
	}

// DB : Page new create 'empty' content for the current user
//		return the page id
	function createPage($pagename = '', $catid = 0) {
	  	$db	= JFactory::getDBO();
		$query = "INSERT INTO `#__myjspace` (`userid`, `pagename`, `content`, `blockEdit`, `blockView`, `metakey`, `template`, `catid`, `modified_by`, `access`) VALUES (".$db->Quote(intval($this->userid)).", ".$db->Quote($pagename).",'', '0', '1', '', '', ".$db->Quote(intval($catid)).", ".$db->Quote(intval($this->userid)).", '0')";
		$db->setQuery($query);
		$db->query();
		$id = $db->insertid();

		return $id;
	}

// DB : Set conf parameter : pagename, blockView, blockEdit, publish_up, publish_down ... (for a page id) for a page
	function SetConfPage($choice = 255) {
		$choice = intval($choice);
	  	$db	= JFactory::getDBO();

		$query = 'UPDATE `#__myjspace` SET ';
		$query .= '`last_update_date` = CURRENT_TIMESTAMP' . ',';
		if ($choice & 1)
			$query .= ' `pagename` = '.$db->Quote($this->pagename).',';
		if ($choice & 2)
			$query .= ' `blockView` = '.$db->Quote(intval($this->blockView)).',';
		if ($choice & 4)
			$query .= ' `blockEdit` = '.$db->Quote(intval($this->blockEdit)).',';
		if ($choice & 8)
			$query .= ' `publish_up` = '.$db->Quote($this->publish_up).',';
		if ($choice & 16)
			$query .= ' `publish_down` = '.$db->Quote($this->publish_down).',';
		if ($choice & 32)
			$query .= ' `metakey` = '.$db->Quote($this->metakey).',';
		if ($choice & 64)		
			$query .= ' `template` = '.$db->Quote($this->template).',';
		if ($choice & 128)		
			$query .= ' `catid` = '.$db->Quote(intval($this->catid)).',';
		if ($choice & 256)		
			$query .= ' `userid` = '.$db->Quote(intval($this->userid)).',';
		if ($choice & 512)		
			$query .= ' `access` = '.$db->Quote(intval($this->access)).',';
		if ($choice & 1024)		
			$query .= ' `modified_by` = '.$db->Quote(intval($this->modified_by)).',';

		$query = substr($query, 0, -1); // remove the last comma
		$query .= ' WHERE `id` = '.$db->Quote(intval($this->id));
	
		$db->setQuery($query);
		if ($db->query())
			return 1;
		return 0;
	}

// DB & FS : Delete page & folder content
	function deletePage($link_folder = 1, $forced = 1) {
		$filedir = JPATH_SITE.DS.$this->foldername.DS.$this->pagename;

		// Important :-)
		if ($this->pagename == '' || ($this->foldername == '' && $link_folder == 1))
		   return 0;

		if ($link_folder == 1) {
			$oldfolder = getcwd();
			if (!@chdir($filedir))
				return 0;		

			// Delete all files in the folder
			$projectsListIgnore = array('.', '..'); // safety
			$handle = @opendir('.');
			while (false !== ($file = @readdir($handle))) {
				if (!@is_dir($file) && !in_array($file,$projectsListIgnore)) {
					if ($forced == 0 && $file != 'index.php')
						return 0;
				
					if ($file != 'index.php' && !@unlink($file) ) {
						@chdir($oldfolder);
						return 0;
					}
				}
			}
			if (!@unlink('index.php') ) {
				@chdir($oldfolder);
				return 0;
			}
			
			@closedir($handle);
			@chdir(JPATH_SITE.DS.$this->foldername);

			if (!(@rmdir($filedir) || @rename($filedir, JPATH_SITE.DS.$this->foldername.DS.'#garbage'))) {
				@chdir($oldfolder);	
				return 0;
			}
		}

		$db	= JFactory::getDBO();
		$query = "DELETE FROM `#__myjspace` WHERE `id` = ".$db->Quote(intval($this->id));
		$db->setQuery($query);
		if ($db->query()) {
			if ($link_folder == 1)
				@chdir($oldfolder);
			return 1;
		}
		
		if ($link_folder == 1)
			@chdir($oldfolder);		
		return 0;
	}
	
// DB : Load all user page info (with content)
// $this->id need to be set before call
  	function loadPageInfo($choix = 0, $getcontent_bs = true) {

		$this->userid = 0;
		$this->content = null;
		$this->blockEdit = 0;
		$this->blockView = 1;
		$this->create_date = null;
		$this->last_access_date = null;
		$this->last_update_date = null;
		$this->last_access_ip = 0;
		$this->hits = 0;
		$this->publish_up = null;
		$this->publish_down = null;
		$this->metakey = null;
		$this->template = null;	
		$this->catid = 0;
		$this->access = 0;
		$this->modified_by = 0;
		
		if (($this->id > 0 && $choix == 0) || ($this->pagename != '' && $choix ==1)) {
		  	$db	= JFactory::getDBO();
			$result_set	= null;
			
			if ($choix == 1)
				$where = "WHERE `pagename` = ".$db->Quote($this->pagename);
			else
				$where = "WHERE `id` = ".$db->Quote(intval($this->id));
				
			$query = "SELECT `id`, `userid`, `pagename`, `blockEdit`, `blockView`";
			if ($getcontent_bs == true)
				$query .= ",`content`";
			$query .= ",`create_date`, `last_update_date`, `last_access_date`, `last_access_ip`, `hits`, `publish_up`, `publish_down`, `metakey`, `template`, `catid`, `access`, `modified_by` FROM `#__myjspace` ".$where;
			
			$db->setQuery($query);
			$result_set = $db->loadObjectList();
			// Voir code + mieux si une ligne et forcer une ligne ...
			$this->id = 0;
			$this->pagename = null;
			
			if ($result_set != null) {
				foreach( $result_set as $result) {
					$this->id = $result->id;
					$this->userid = $result->userid;
					$this->pagename = $result->pagename;
					if ($getcontent_bs == true)
						$this->content = $result->content;
					$this->blockEdit = $result->blockEdit;
					$this->blockView = $result->blockView;
					$this->create_date = $result->create_date;
					$this->last_update_date = $result->last_update_date;
					$this->last_access_date = $result->last_access_date;
					$this->last_access_ip = $result->last_access_ip;
					$this->hits = $result->hits;
					$this->publish_up = $result->publish_up;
					$this->publish_down = $result->publish_down;
					$this->metakey = $result->metakey;
					$this->template = $result->template;
					$this->catid = $result->catid;
					$this->access = $result->access;
					$this->modified_by = $result->modified_by;
				}
				return 1;
			}
		}
		return 0;	
	}
	
// DB : Load user info (without content)
  	function loadPageInfoOnly($choix = 0) {
		$this->loadPageInfo($choix, false);
	}

// DB : Update content (= personal page)
	function updateUserContent() {
	  	$db	= JFactory::getDBO();
		$query = "UPDATE `#__myjspace` SET `content` = ".$db->Quote($this->content).",`modified_by` = ".$db->Quote($this->modified_by).", `last_update_date` = CURRENT_TIMESTAMP WHERE `id` = ".$db->Quote(intval($this->id));
		$db->setQuery($query);
		if ($db->query())
			return 1;
		return 0;
	}
	
// DB : Update Date and hit for the last acess if not same ip addr compare to the last (too simple mais efficient)
	function updateLastAccess($last_access_ip = '') {
	  	$db	= JFactory::getDBO();
		$query = "UPDATE `#__myjspace` SET `last_access_date` = CURRENT_TIMESTAMP, `last_access_ip` = ".$db->Quote($last_access_ip).", `hits` = `hits` + 1 WHERE `id` = ".$db->Quote(intval($this->id))." AND `last_access_ip` <> ".$db->Quote($last_access_ip);
		$db->setQuery($query);
		if ($db->query())
			return 1;
		return 0;
	}

// DB : Reset Hits & Update Date
	function ResetLastAccess() {
	  	$db	= JFactory::getDBO();
		$query = "UPDATE `#__myjspace` SET `last_access_date` = '0000-00-00 00:00:00', `last_access_ip` = '0', `hits` = 0 WHERE `id` = ".$db->Quote(intval($this->id));
		$db->setQuery($query);
		if ($db->query())
			return 1;
		return 0;
	}
	
// DB : Check if pagename already exist by name
	function ifExistPageName($pagename = '') {
	  	$db	= JFactory::getDBO();
		$query = "SELECT `pagename` FROM `#__myjspace` WHERE `pagename` = ".$db->Quote($pagename);
		$db->setQuery($query);
		return $db->loadResult();
	}

// DB : Select a specific content by Page id
	function GetContentPageId($id = 0) {
	  	$db	= JFactory::getDBO();
		$query = "SELECT `content` FROM `#__myjspace` WHERE `id` = ".$db->Quote($id);
		$db->setQuery($query);
		return $db->loadResult();
	}

// DB : Get the list of id, pagename for a specific user
//		If id specified, select only the concerned page (if owned by the user)
// 		If array() $access specified include share pages for the user list group
	function GetListPageId($userid = 0, $id = 0, $access = null) {
	  	$db	= JFactory::getDBO();
		$query = "SELECT `id`, `pagename` FROM `#__myjspace` WHERE ";
		if ($access == null)
			$query .= " `userid` = ".$db->Quote($userid);
		else
			$query .= " ( `userid` = ".$db->Quote($userid)." OR `access` IN (".implode(',', $access).") )";
		if ($id > 0)
			$query .= " AND `id` = ".$db->Quote($id);
		$db->setQuery($query);
		return $db->loadAssocList();
	}

// DB : Get categories list	
	public static function GetCategories($published = null) {

		if (version_compare(JVERSION, '1.6.0', 'lt'))
			return array();

	  	$db	= JFactory::getDBO();
		$query  = "SELECT a.id AS value, a.title AS text, a.level, a.published FROM `#__categories` AS a";
		$query .= " LEFT JOIN `#__categories` AS b ON a.lft > b.lft";
		$query .= " AND a.rgt < b.rgt";
		$query .= " WHERE ( a.extension = 'com_myjspace' )";
		if ($published != null)
			$query .= " AND a.published = ".$db->Quote($published);
		else
			$query .= " AND a.published IN ( 0, 1 )";
		$query .= " GROUP BY a.id, a.title, a.level, a.lft, a.rgt, a.extension, a.parent_id, a.published";
		$query .= " ORDER BY a.lft ASC";
		$db->setQuery($query);
		return $db->loadAssocList();
	}
	
// DB : Get category label
	public static function GetCategory($catid = 0) {

		if ($catid == 0 || version_compare(JVERSION, '1.6.0', 'lt'))
			return '';

	  	$db	= JFactory::getDBO();
		$query  = "SELECT `title` FROM `#__categories` WHERE `id` = ".$db->Quote($catid);
		$db->setQuery($query);
		return $db->loadResult();
	}

// DB : Get Gategories label into an indexed array
	public static function GetCategoriesLabel($cat = null) {
		if ($cat == null)
			$cat = self::GetCategories(1);

		$nb_cat = count($cat);
		$cat_index = array();
		for ($i = 0 ; $i < $nb_cat ; $i++) {
			$cat_index[$cat[$i]['value']] = $cat[$i]['text'];
		}

		return $cat_index;
	}

// DB : Get user page(s) URL: Page URL if one & page list URL if more than one
//		if only one : can choose if display link as folder
// return : tab of pages id, url

	public static function GetUserUrl($userid = 0, $link_folder_print = 0, $Itemid = 0, $xhtml = true) {

        require_once JPATH_ROOT.DS.'components'.DS.'com_myjspace'.DS.'helpers'.DS.'util.php';

		$url = '';
		$user_page = New BSHelperUser();
		$list_page_tab = $user_page->GetListPageId($userid);
		$nb_page = count($list_page_tab);

		if ($nb_page == 1) {
			$Itemid = get_menu_itemid('index.php?option=com_myjspace&view=see', $Itemid);
			$Itemid = get_menu_itemid('index.php?option=com_myjspace&view=see&id=&pagename=', $Itemid);		

			if ($link_folder_print == 1)
				$url = JURI::base().self::getFoldername().'/'.$list_page_tab[0]['pagename'].'/';
			else
				$url = str_replace(JURI::base(true).'/', '', JURI::base()).Jroute::_('index.php?option=com_myjspace&view=see&id='.$list_page_tab[0]['id'].'&Itemid='.$Itemid, $xhtml);
		} else if ($nb_page > 1) {
			$Itemid = get_menu_itemid('index.php?option=com_myjspace&view=pages', $Itemid);
			$url = str_replace(JURI::base(true).'/', '', JURI::base()).Jroute::_('index.php?option=com_myjspace&view=pages&uid='.$userid.'&Itemid='.$Itemid, $xhtml);
		}
		
		return (array($list_page_tab, $url));
	}

// DB : Get a new free pagename with a number as suffix
//		$prefix : page nameprefix, $fin : max number of try to find a name
	function GetPagenameFree($prefix = '', $fin = 1000) {

	  	$db	= JFactory::getDBO();
		if (version_compare(JVERSION, '1.6.0', 'ge'))
			$searchEscaped = "(".$db->Quote('^'.$db->escape($prefix, true).'[0-9]*$') . ")";
		else
			$searchEscaped = "(".$db->Quote('^'.$db->getEscaped($prefix, true).'[0-9]*$') . ")";
		$query = "SELECT `pagename` FROM `#__myjspace` WHERE `pagename` RLIKE ".$searchEscaped;
		$db->setQuery($query);
		$list_pages = $db->loadAssocList();	
		$nb_list = count($list_pages);

		// If no page with this prefix use the prefix as pagename
		if ($nb_list == 0)
			return $prefix;
		
		// To do not have suffix = 1 if a pagename = $prefix exists
		$debut = 1;
		for ($j = 0 ; $j < $nb_list ; $j++) {
			if ($list_pages[$j]['pagename'] == $prefix) {
				$debut = 2;
				break;
			}
		}
	
		for ( $i = $debut ; $i <= $fin ; $i++ ) {
			$ok = true;
			for ($j = 0 ; $j < $nb_list ; $j++) {
				if ($list_pages[$j]['pagename'] == $prefix.$i) {
					$ok = false;
					break;
				}
			}

			if ($ok == true)
				return $prefix.$i;
		}
		
		// Too much existing number ... choose it yourself
		return $prefix;
	}
		
// DB : List of all username (if $resultmode = 1 add metakey)
//		or count the number of line for the same criterias
	public static function loadPagename($triemode = -1, $affmax = 0, $blocked = 0, $publish = 0, $content = 0, $check_search = null, $scontent = '', $resultmode = 0, $limitstart = 0, $count = false, $catid = 0, $extra_query = null) {
	  	$db	= JFactory::getDBO();

		// Safety
		$resultmode = intval($resultmode);
		if ($resultmode < 0 || $resultmode > 255)
			$resultmode = 0;
		if ($affmax < 0)
			return null;

		if ($count == true)
			$query = "SELECT COUNT(*)";
		else {
			// Columns to 'display'
			$query = "SELECT `id`, `userid`, `pagename`"; // id(username) = 1, pagename = 2 for display (search)
				
			if ($resultmode & 4)
				$query .= ", `metakey`";
			if ($resultmode & 8)
				$query .= ", `create_date`";
			if ($resultmode & 16)
				$query .= ", `last_update_date`";
			if ($resultmode & 32)
				$query .= ", `hits`";
			if ($resultmode & 128)
				$query .= ", `catid`";
			// 64 for image (search)
		}
		
		$query .= " FROM `#__myjspace` WHERE 1=1";
		
		// Criterias
		if ($blocked)
			$query .= " AND `blockView` != 0";

		if ($publish)
			$query .= " AND `publish_up` < CURRENT_TIMESTAMP AND (`publish_down` >= CURRENT_TIMESTAMP OR `publish_down` = '0000-00-00 00:00:00')";

		if ($content == 1)
			$query .= " AND `content` != ''";

		if ($content == -1)
			$query .= " AND `content` = ''";

		if ($catid != 0)
			$query .= " AND `catid` = ".$db->Quote($catid);
			
		if ($check_search != null && count($check_search) > 0 && $scontent != '') {
			$query .= " AND ( 1=0 ";
			
			$pparams = JComponentHelper::getParams('com_myjspace');
			if ($pparams->get('search_html', 1)) // Search into html content
				$scontent = htmlentities($scontent,ENT_QUOTES, 'UTF-8');

			$tab_scontent = explode (' ', $scontent);
			if (count($tab_scontent >= 1)) {
				$scontent = '';
				foreach ( $tab_scontent as $word ) {
					if (version_compare(JVERSION, '1.6.0', 'ge'))
						$scontent .= '%'.$db->escape($word, true);
					else
						$scontent .= '%'.$db->getEscaped($word, true);
				}
				$scontent .= '%';
			}
			
			if (isset($check_search['name']))
				$query .= " OR `pagename` LIKE ".$db->Quote($scontent, false);

			if (isset($check_search['description']))
				$query .= " OR `metakey` LIKE ".$db->Quote($scontent, false);

			if (isset($check_search['content']))
				$query .= " OR `content` LIKE ".$db->Quote($scontent, false);
				
			$query .= " ) ";
		}	
		
		// Extra query
		if ($extra_query)
			$query .= $extra_query;
		
		// Sort order
		if ($triemode == 0)
			$query .= " ORDER BY `pagename` ASC";
		else if ($triemode == 1)
			$query .= " ORDER BY `pagename` DESC";
		else if ($triemode == 2)
			$query .= " ORDER BY RAND()";
		else if ($triemode == 3)
			$query .= " ORDER BY `create_date` DESC";
		else if ($triemode == 4)
			$query .= " ORDER BY `last_update_date` DESC";
		else if ($triemode == 5)
			$query .= " ORDER BY `hits` DESC";

		$db->setQuery($query, $limitstart, $affmax);
			
		if ($count == true)
			$row = $db->loadResult();
		else
			$row = $db->loadAssocList();
		
		return $row;
	}

// DB : count the total number of pages
	public static function myjsp_count_nb_page() {
	  	$db	= JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM `#__myjspace`";
		$db->setQuery($query);
		$db->query();
		return $db->loadResult();
	}

// Count th enumber of distinct users
	public static function myjsp_count_nb_user() {
	  	$db	= JFactory::getDBO();
		$query = "SELECT COUNT(DISTINCT `userid`) FROM `#__myjspace`";
		$db->setQuery($query);
		$db->query();
		return $db->loadResult();
	}

// FS : Page Create Folder & file to redirect	
	function CreateDirFilePage($pagename = '', $choix = 1, $id = 0) {
		
		$filedir = JPATH_SITE.DS.$this->foldername.DS.$pagename;
		$link = JURI::root();

		if ($choix == 1)
			$content_id = 'pagename='.$pagename;
		else {
			if ($id != 0)
				$userid = $id;
			else
				$userid = $this->id;
			$content_id = 'id='.$userid;
		}

$content = "<?php
// com_myjspace
// Format:".$this->index_format_version."
//
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', '".str_replace('\\', '\\\\', JPATH_SITE)."');
require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');

\$app = JFactory::getApplication('site');
\$app->initialise();

\$config = new JConfig();
\$menu = \$app->getMenu();
\$defaultMenu = \$menu->getDefault();
\$itemid = \$defaultMenu->id;
\$itemid = get_menu_itemid('index.php?option=com_myjspace&view=see', \$itemid);
\$itemid = get_menu_itemid('index.php?option=com_myjspace&view=see&id=&pagename=', \$itemid);

\$url_tmp = \"index.php?option=com_myjspace&view=see&".$content_id."\";
if (\$itemid != 0)
	\$url_tmp .= '&Itemid='.\$itemid;

\$url_tmp2 = '';
if (\$config->sef_rewrite == 0 && substr_count(JRoute::_('index.php'), 'index.php', false))
	\$url_tmp2 = 'index.php';
	
\$a_supp = JRoute::_('index.php',false);
if (\$config->sef_rewrite == 1)
	\$a_supp = str_replace('index.php', '', \$a_supp);
	
\$url = \"".$link."\".\$url_tmp2.str_replace(\$a_supp, '', JRoute::_(\$url_tmp, false));

if (!headers_sent())
	header(\"location: \$url\");
echo \"<html><body><a href=\".\$url.\">".$pagename."</a><script type=\\\"text/javascript\\\">window.location.href='\".\$url.\"'</script></body></html>\";

function get_menu_itemid(\$url = '', \$default = 0) {
	\$app = JFactory::getApplication();
	\$menu = \$app->getMenu();
	\$menu_items = \$menu->getItems('link', \$url);

	if (count(\$menu_items) >= 1)
		return \$menu_items[0]->id;

	return \$default;
}
?>
";
		// Folder (may already exist)
		@mkdir($filedir);
		@chmod($filedir, 0755);

		// File index.php
		$file = $filedir.DS.'index.php';
		$handle = @fopen($file,"w");
		if ($handle) {
			@fwrite( $handle, $content );
			@chmod($file, 0755);
			return 1;
		}
		
		return 0;
	}
	
	// Retreive the version info the index file
	function VersionIndexPage($pagename = '') {
	
		$file_index = JPATH_SITE.DS.$this->foldername.DS.$pagename.DS.'index.php';
		$contenu = @fread(fopen($file_index, "r"), 80); 
		$sortie = null;
		preg_match('#// Format:(.*)\n#Us', $contenu, $sortie);

		if (isset($sortie[1]))
			$version = trim($sortie[1]);
		else
			$version = 0;
	
		return $version;
	}

	// Check the number of index page with NOT the actual version for all pages
	public static function CheckVersionIndexPage() {
		$nb_index_ko = -1;
		$pparams = JComponentHelper::getParams('com_myjspace');
		$pparams->get('link_folder', 1);
		if ($pparams->get('link_folder', 1) == 1 ) {

			$user_page = New BSHelperUser();
			$user_page->foldername = self::getFoldername();
			$username_list = self::loadPagename();
			
			$nb_page = count($username_list);
			$nb_index_ko = 0;
			if ($nb_page > 0) {
				for ($i = 0; $i < $nb_page; $i++) {
					if ((int)$user_page->VersionIndexPage($username_list[$i]['pagename']) != (int)$user_page->index_format_version)
						$nb_index_ko = $nb_index_ko + 1;
				}
			}
		}
		return $nb_index_ko;
	}
	
// FOLDERNAME

// CFG : Get foldername
	public static function getFoldername() {
		$pparams = JComponentHelper::getParams('com_myjspace');
		$foldername = $pparams->get('foldername', 'myjsp');
		return $foldername;
	}

// FS : test if the 'real' foldername exist
	public static function ifExistFoldername($foldername = '') {
		$oldfolder = getcwd();
		@chdir(JPATH_SITE);
		$retour = @is_dir($foldername);
		@chdir($oldfolder);
		return($retour);
	}

// FS & CFG : create or update page ROOT folder name
	function updateFoldername($foldername = '', $link_folder = 1, $keep = 0) {
	    require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user_event.php';
	    require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'version.php';
		
		// Check
		if ($foldername == '')
			return 0;

		if ($link_folder == 1) {
			// Rename (or create + chmod) folder or move subfolders on file system too
			if ($this->foldername != $foldername && self::ifExistFoldername(JPATH_SITE.DS.$foldername) && BSUserEvent::adm_rename_folders(JPATH_SITE.DS.$this->foldername, JPATH_SITE.DS.$foldername)) { // rename = move in one existing
				if ($keep == 0)
					@rmdir(JPATH_SITE.DS.$this->foldername);
			} else if ($keep == 1 && @mkdir(JPATH_SITE.DS.$foldername) && @chmod(JPATH_SITE.DS.$foldername, 0755) && BSUserEvent::adm_rename_folders(JPATH_SITE.DS.$this->foldername, JPATH_SITE.DS.$foldername)) { // Create a new one and move
				// rien :-)
			} else if ($keep == 0 && !@rename(JPATH_SITE.DS.$this->foldername, JPATH_SITE.DS.$foldername)) { // if error try to create
				if (!@mkdir(JPATH_SITE.DS.$foldername) || !@chmod(JPATH_SITE.DS.$foldername, 0755))
					return 0;
			} // => rename folder ok
			
			// If no file index.html in the forder, create it 
			$file = JPATH_SITE.DS.$foldername.DS.'index.html';
			if (!@file_exists($file)) {
				$content = '<html><body></body></html>';
				$handle = @fopen($file,"w");
				if ($handle) {
					@fwrite( $handle, $content );
					@chmod($file, 0755);
				}
			}
		}

	    if ($this->foldername != $foldername) {
			$pparams = JComponentHelper::getParams('com_myjspace');
			$pparams->set('foldername',$foldername);
			BS_Helper_version::save_parameters('com_myjspace');
		}
		return 1;
	}

// Check foldername caracteres	
	public static function checkFoldername($foldername = '', $allowed = '#^[a-zA-Z0-9/]+$#') {
		if (preg_match($allowed, $foldername))
			return 1;
		return 0;
	}

// Check valid char & keep only valid char for a pagename, for example to take only the valid char from a user name
	function only_valid_char($chaine = null, $allowed = '/[^A-Za-z0-9]/') {

		$retour = preg_replace($allowed, '', $chaine);
		return $retour;
	}
	
// PAGE CONTENT fct

// Substitute # tags with they contents (dadabase contents for a user page)
// Reserved words: #userid, #name, #username, #pagename, #id, #access, #acces_edit', #lastupdate, #lastaccess, #createdate, #fileslist, #hits ... and a specific one #bsmyjspace :-)
// pos = 0 for page content, 1 for prefix, 2 for suffix
	function traite_prefsuf(&$atraiter = '', &$user = null, $page_increment = 0, $date_fmt = 'Y-m-d H:i:s', $chaine_files = '', $replace_inf_sup = 0, $Itemid = 0) {

		if ($atraiter == null || $atraiter == '')
			return '';

        require_once JPATH_ROOT.DS.'components'.DS.'com_myjspace'.DS.'helpers'.DS.'util_acl.php';
		
		// 'Complex' tag: myjsp iframe
		$pparams = JComponentHelper::getParams('com_myjspace');
		if ($pparams->get('allow_tag_myjsp_iframe', 1) == 1) {
			// Tag {myjsp iframe URL}
			$chaine_iframe = '<iframe src="$1" id="myjsp-iframe" frameborder="0" ></iframe>';
			$atraiter = preg_replace('!{myjsp iframe (.+)\}!isU', $chaine_iframe, $atraiter);
		}
		
		// 'Complex' tag: myjsp include
		if ($pparams->get('allow_tag_myjsp_include', 1) == 1) {
			// Tag {myjsp include URL} (only the first url will be taking into account: to be used once per page + head + foot)
			if (preg_match('!{myjsp include (.+)\}!isU', $atraiter, $sortie)) {
				if (count($sortie) >= 2) {
					$fichier_sortie = @file_get_contents(trim($sortie[1]));
					preg_match('#<body>(.*)</body>#Us', $fichier_sortie, $fichier_sortie);
					if (count($fichier_sortie) >= 2)
						$atraiter = preg_replace('!{myjsp include (.+)\}!isU', $fichier_sortie[1], $atraiter);
				}
			}
		}

		// CB
		$chaine_cb = '<iframe src="'.Jroute::_('index.php?option=com_comprofiler&task=userProfile&user='.$user->id.'&tmpl=component').'" id="cbprofile" frameborder="0" ></iframe>';
		// Joomsocial
		$chaine_jsocial_profile = '<iframe src="'.Jroute::_('index.php?option=com_community&view=profile&userid='.$user->id.'&tmpl=component').'" id="jomsocial-profile" frameborder="0" ></iframe>';
		$chaine_jsocial_photos  = '<iframe src="'.Jroute::_('index.php?option=com_community&view=photos&task=myphotos&userid='.$user->id.'&tmpl=component').'" id="jomsocial-photos" frameborder="0" ></iframe>';
		// MyJspace string
		$chaine_bsmyjspace = '<span class="bsfooter"><a href="'.Jroute::_('index.php?option=com_myjspace&amp;view=myjspace').'">BS MyJspace</a></span>';
		// Category
		$category = self::GetCategory($this->catid);
		// Reverved words to replace
		$search  = array('#userid', '#name', '#username', '#id', '#pagename', '#access', '#acces_edit', '#lastupdate', '#lastaccess', '#createdate', '#description', '#category', '#bsmyjspace', '#fileslist', '#cbprofile','#jomsocial-profile','#jomsocial-photos');
		$replace = array($user->id, $user->name, $user->username, $this->id, $this->pagename, get_assetgroup_label($this->blockView), get_assetgroup_label($this->access), date($date_fmt, strtotime($this->last_update_date)), date($date_fmt, strtotime($this->last_access_date)),date($date_fmt, strtotime($this->create_date)), $this->metakey, $category, $chaine_bsmyjspace, $chaine_files, $chaine_cb, $chaine_jsocial_profile, $chaine_jsocial_photos);
		
		if ($replace_inf_sup == 1) {
			$search  = array_merge($search, array('#inf', '#sup')); // because html code ot allowed any more in 1.6 & 1.7 configuration
			$replace = array_merge($replace, array('<', '>'));
		}
		if ($page_increment == 1) {
			$search[] = '#hits';
			$replace[] = $this->hits;
		}
		
		// Replace
		$atraiter = str_replace($search, $replace, $atraiter);
	
		return $atraiter;
	}

	// Function to have 'API' for component & plugins
	
	// Return the user pagename content if exist (with all tags replaced aa right check)
	public static function mjsp_exist_page_content($id = 0, $pagebreak = 0, $Itemid = 0) {
		$retour = '';
	
		// User & component
		$pparams = JComponentHelper::getParams('com_myjspace');
		$user_actual = JFactory::getuser();
		
		// Personnal page info
		$user_page = New BSHelperUser(); // For simple call from outside
		$user_page->id = $id;
		$user_page->loadPageInfo();
		$user = JFactory::getuser($user_page->userid);

        // Content & complete with prefix & suffix and remplacing # tags
		$page_increment = $pparams->get('page_increment', 1);
		$date_fmt = $pparams->get('date_fmt', 'Y-m-d H:i:s');
		
        // Content
		$uploadadmin = $pparams->get('uploadadmin', 1);
		$uploadimg = $pparams->get('uploadimg', 1);
		$chaine_files = '';
		if ($uploadadmin == 1 && $uploadimg == 1) { // May be add optional in the futur
			require_once JPATH_ROOT.DS.'components'.DS.'com_myjspace'.DS.'helpers'.DS.'util.php';
			$forbiden_files = array('.', '..', 'index.html', 'index.htm', 'index.php');
			$tab_list_file = list_file_dir(JPATH_ROOT.DS.$user_page->foldername.DS.$user_page->pagename, '*', $forbiden_files, 1);
			$nb = count($tab_list_file);
			for ($i = 0 ; $i < $nb ; ++$i)
				$chaine_files .= '<a href="'.JURI::base().$user_page->foldername.'/'.$user_page->pagename.'/'.$tab_list_file[$i].'">'.$tab_list_file[$i].'</a> '; 
		}

		if ($pparams->get('allow_user_content_var', 1))
			$content = $user_page->traite_prefsuf($user_page->content, $user, $page_increment, $date_fmt, $chaine_files, 0, $Itemid);
		else
			$content = $user_page->content;
			
		// [register]
		if ($pparams->get('editor_bbcode_register', 0) == 1 && strlen($content) <= 92160) { // Allow to use the dynamic tag [register]
			$uri = JFactory::getURI();
			$return = $uri->toString();
			if ($pparams->get('url_login_redirect', '')) 
				$url = $pparams->get('url_login_redirect', '');
			else {
				if (version_compare(JVERSION, '1.6.0', 'ge'))
					$url = 'index.php?option=com_users&view=login';
				else
					$url = 'index.php?option=com_user&view=login';
				$url .= '&return='.base64_encode($return); // To redirect to the originaly call page
				$url = Jroute::_($url, false);
			}
 
			if ($user_actual->id != 0)// if not registered
				$content = preg_replace('!\[register\](.+)\[/register\]!isU', '$1', $content);
			else // If registered
				$content = preg_replace('!\[register\](.+)\[/register\]!isU', JText::sprintf('COM_MYJSPACE_REGISTER', $url), $content);		
		}			

		$prefix = '';
		$suffix = '';
		
		// Force default dates
		if ($pparams->get('publish_mode',2) == 0) { // do not take into account the dates
			$user_page->publish_up = '0000-00-00 00:00:00';
			$user_page->publish_down = '0000-00-00 00:00:00';
		}
		if ($user_page->publish_down == '0000-00-00 00:00:00')
			$user_page->publish_down = date('Y-m-d 00:00:00',strtotime("+1 day"));		
				
		// Specific context
		$aujourdhui = time();
		if ($user_page->blockView == null) {
//			$content = JText::_('COM_MYJSPACE_PAGENOTFOUND');
			$content = '';
		} else if ($user_page->blockView == 0 && $user_actual->id != $user_page->userid) {
//			$content = JText::_('COM_MYJSPACE_PAGEBLOCK');
			$content = '';
		} else if ($user_page->blockView == 2 && $user_actual->username == "") {
//        require_once JPATH_ROOT.DS.'components'.DS.'com_myjspace'.DS.'helpers'.DS.'util_acl.php';
//			$content = JText::sprintf('COM_MYJSPACE_PAGERESERVED', get_assetgroup_label($user_page->blockView));
			$content = '';
		} else if ($user_page->content == null) {
//			$content = JText::_('COM_MYJSPACE_PAGEEMPTY');
			$content = '';
		} else if (strtotime($user_page->publish_up) > $aujourdhui || strtotime($user_page->publish_down) <= $aujourdhui) {
//			$content = JText::_('COM_MYJSPACE_PAGEUNPLUBLISHED');
			$content = '';
		} else {
		
		// Top and bottom
			if ($pparams->get('page_prefix', ''))
				$prefix = '<span class="top_myjspace">'.$user_page->traite_prefsuf($pparams->get('page_prefix', ''), $user, $page_increment, $date_fmt, $chaine_files, 1, $Itemid).'</span><br />';
			if ($pparams->get('page_suffix', '#bsmyjspace'))
				$suffix = '<span class="bottom_myjspace">'.$user_page->traite_prefsuf($pparams->get('page_suffix', '#bsmyjspace'), $user, $page_increment, $date_fmt, $chaine_files, 1, $Itemid).'</span><br />';
		}			
		
		if ($pagebreak == 0) {
			$regex = '#<hr([^>]*?)class=(\"|\')system-pagebreak(\"|\')([^>]*?)\/*>#iU';
			$content = preg_replace( $regex, '<br />', $content );
		}
		
		if ($content)
			$retour = '<div class="myjspace-prefix">'.$prefix.'</div><div class="myjspace-content"></div>'.$content.'<div class="myjspace-suffix">'.$suffix.'</div>';
	
		return $retour;
	}
	
}
?>
