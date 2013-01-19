<?php
/*
* @version $Id: helper.php $
* @version		2.0.3 21/10/2012
* @package		mod_viewmyjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Pas d'acces direct
defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

class modViewMyJspaceHelper {

	// Recupere la liste des pages & id avec contenu
	public static function getListPage($triemode = 0, $affmax = 0, $emptymode = 0, $nonvisiblemode = 0, $publish = 1, $resultmode = 0, $catid_list = '') {
		$db	= JFactory::getDBO();
		$result	= null;

		// Safety
		$resultmode = intval($resultmode);
		if ($resultmode < 0 || $resultmode > 127)
			$resultmode = 0;

		$query = "SELECT DISTINCT  mjs.id, mjs.userid, jos.userid AS connect, mjs.pagename, mjs.last_update_date";
		// id(username) = 1, pagename = 2, last_update_date = 16 for display (search)			
		if ($resultmode & 4)
			$query .= ", mjs.metakey";
		if ($resultmode & 8)
			$query .= ", mjs.create_date";
		if ($resultmode & 32)
			$query .= ", mjs.hits";
		// 64 for image (search)
		$query .= " FROM `#__myjspace` mjs LEFT JOIN `#__session` jos ON mjs.userid=jos.userid WHERE 1=1 ";

		if ($emptymode == 0)
			$query .= " AND mjs.content != '' ";
		
		if ($nonvisiblemode == 0)
			$query .= " AND blockView != 0";

		if ($publish == 1 && $nonvisiblemode == 0)
			$query .= " AND mjs.publish_up < NOW() AND (mjs.publish_down >= NOW() OR mjs.publish_down = '0000-00-00 00:00:00')";

		if ($catid_list != '')
			$query .= " AND mjs.catid IN (".$catid_list.")";
			
		if ($triemode == 0)
			$query .= " ORDER BY mjs.pagename ASC";
		else if ($triemode == 1)
			$query .= " ORDER BY mjs.pagename DESC";
		else if ($triemode == 2)
			$query .= " ORDER BY RAND()";
		else if ($triemode == 3)
			$query .= " ORDER BY mjs.create_date DESC";
		else if ($triemode == 4)
			$query .= " ORDER BY mjs.last_update_date DESC";
		else if ($triemode == 5)
			$query .= " ORDER BY mjs.hits DESC";
			
		if ($affmax != 0)
			$query .= " LIMIT $affmax";

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if ($db->getErrorNum()) {
			JError::raiseWarning( 500, $db->stderr() );
		}
		
		return $result;
	}

	// Recupere le nombre de pages
	public static function getNbPage($emptymode = 0, $nonvisiblemode = 0, $publish = 1) {
		$db = JFactory::getDBO();
		$result	= null;

		// Select page
		$query = "SELECT COUNT(*) FROM `#__myjspace` WHERE 1 = 1 ";

		if ($nonvisiblemode == 0) // Display all non visible if 1
			$query .= " AND `blockView` != 1";
			
		if ($emptymode == 0)
			$query .= " AND `content` != '' ";
			
		if ($publish == 1 && $nonvisiblemode == 0)
			$query .= " AND `publish_up` < NOW() AND (`publish_down` >= NOW() OR `publish_down` = '0000-00-00 00:00:00')";
			
		$db->setQuery($query);
		$result = $db->loadResult();
		if ($db->getErrorNum()) {
			JError::raiseWarning( 500, $db->stderr() );
		}
		
		if ($result == null)
			$result = 0;

		return $result;
	}
	
	// Image connecte/non connecté ou mise à jour depuis 'delais'
	public static function aff_img($connecte, $dateupdate, $delais, $affimgcon) {
		if ($affimgcon != 0) {

			$link_pre = 'modules/mod_viewmyjspace/images/';

			// Si connecté ou pas
			if ($connecte)
				$retour = '<img src="'.$link_pre.'tick.png" style="width:10px; border:none; margin-left:3px;margin-right:3px;" alt="" />';
			else
				$retour = '<img src="'.$link_pre.'rating_star_blank.png" style="width:10px; border:none; margin-left:3px;margin-right:3px;" alt="" />';

			// Si option pour Page mise à jour depuis 'delais'
			if ($delais != 0 && (time() - strtotime($dateupdate)) < $delais && ($connecte))
				$retour = '<img src="'.$link_pre.'rating_star_green.png" style="width:10px; border:none; margin-left:3px;margin-right:3px;" alt="" />';
			if ($delais != 0 && (time() - strtotime($dateupdate)) < $delais && $delais != 0 && !($connecte))
				$retour = '<img src="'.$link_pre.'rating_star.png" style="width:10px; border:none; margin-left:3px;margin-right:3px;" alt="" />';
		} else
			$retour = '';
	   
		return $retour;
	}

	// Test si une image existe, si oui retour le code HTML pour l'afficher, sinon null
	// $mode = 0 => affiche l'image
	// $mode = 1 => affiche un lien sur limage pour previsualisation avec Lytebox
	public static function exist_image_html($img_dir, $img_dir_prefix = JPATH_SITE, $mode = 0, $title = '', $max_width='100px', $max_height='100px', $img_name = 'preview.jpg' ) {

		$retour = null;
		$filename = $img_dir_prefix.DS.$img_dir.DS.$img_name;

		if (@file_exists($filename)) {
			if ($mode == 0)
				$retour = '<img src="'.$img_dir.'/'.$img_name.'" class="img_preview" title="'.$title.'" alt="'.$title.'" style=" max-width:'.$max_width.'; max-height:'.$max_height.';" />';
			else
				$retour = '<a href="'.$img_dir.'/'.$img_name.'" rel="lytebox"><img src="'.$img_dir.'/'.$img_name.'" class="img_preview" title="'.$title.'" alt="'.$title.'" style=" max-width:'.$max-width.'; max-height:'.$max-height.';"/></a>';
		}
	
		return $retour;
	}
	
	// Retourne l'itemid d'un menu via url
	// Prend le premier si plusieurs et 0 si pas trouvé
	public static function get_menu_itemid($url) {
	
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$menu_items = $menu->getItems('link', $url);
	
		if (count($menu_items) >= 1)
			return $menu_items[0]->id;
			
		return 0;
	}
}

?>
