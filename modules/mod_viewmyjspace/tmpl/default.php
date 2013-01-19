<?php
/*
* @version $Id: default.php $
* @version		2.0.3 21/10/2012
* @package		mod_viewmyjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Pas d'acces direct
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'modules/mod_viewmyjspace/assets/viewmyjspace.css');

// Use the component ACL
if (version_compare(JVERSION,'1.6.0','ge') && ($params->get('use_com_acl', 0) && !JFactory::getUser()->authorise('user.search', 'com_myjspace'))) 
	return;

echo "<div class=\"mod_viewmyjspace\">\n";

if ($showmode != 1) {
    if ($nbpages > 1)
		echo '<div class="vmyjsp_nbpage">'.JText::sprintf('ECHOPAGES', $nbpages).'</div>';
	else
		echo '<div class="vmyjsp_nbpage">'.JText::sprintf('ECHOPAGE', $nbpages).'</div>';
}

if ($showmode != 0) {
	echo '<div class="vmyjsp_lstpage">'."\n";
	
	for ($i = 0; $i < count($names); ++$i) {

		echo '<div class="vmyjsp_onepage" style="text-align:'.$showmode0.'">';

		if ($showmode2 == 0) {
			$separ_l = '<span>';
			$separ_r = '</span> ';
		} else {
			$separ_l = '<div>';
			$separ_r = '</div>';
		}
	
		if ($showmode1 & 64) { // Image
			echo $separ_l.modViewMyJspaceHelper::exist_image_html($repertoire.'/'.$names[$i]->pagename, JPATH_SITE, 0, $names[$i]->pagename, $preview_width, $preview_height).$separ_r;
		}
	
		if ($showmode1 & 1) { // Pagename
			$pagename = $names[$i]->pagename;
			
			if ($showmode3 == 1) {
				$url = Jroute::_('index.php?option=com_myjspace&view=see&pagename='.$pagename.'&Itemid='.$itemid);
			} else {
				$url = Jroute::_(JURI::base(true).'/'.$repertoire.'/'.$pagename, true);
			}
			echo $separ_l.modViewMyJspaceHelper::aff_img($names[$i]->connect, $names[$i]->last_update_date, $delaisimgcon, $affimgcon) . '<a href="'. $url .'">'. $pagename ."</a>".$separ_r;
		}

		if ($showmode1 & 2) { // Username
			$table   = JUser::getTable();
			if($table->load($names[$i]->userid)) { // Test if user exist before to retrive info
				$user = JFactory::getUser($names[$i]->userid);
			} else { // User do no exist any more !
				$user = new stdClass();
				$user->username = '?';
			}
			echo $separ_l.$user->username.$separ_r;
		}

		if ($showmode1 & 8) { // Date created (8)
			echo $separ_l.date($date_fmt, strtotime($names[$i]->create_date)).$separ_r;
		}

		if ($showmode1 & 16) { // Date updated (16)
			echo $separ_l.date($date_fmt, strtotime($names[$i]->last_update_date)).$separ_r;
		}
		
		if ($showmode1 & 32) { // Hits (32)
			echo $separ_l.$names[$i]->hits.$separ_r;
		}

		if ($showmode1 & 4) { // Description (4)
			echo $separ_l.$names[$i]->metakey.$separ_r;
		}	
		echo "</div>\n";
	}
	echo "</div>\n";
}

echo "</div>\n";

?>
