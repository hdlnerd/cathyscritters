<?php
/**
* @version $Id: router.php $
* @version		2.0.0 21/07/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * @param	array	A named array
 * @return	array
 */
function MyjspaceBuildRoute(&$query = null)
{
	$segments = array();

	if (isset($query['view'])) {
		$segments[] = $query['view'];
		unset($query['view']);
	}
	if (isset($query['id'])) {
		$segments[] = $query['id'];
		unset($query['id']);
	}
	if (isset($query['pagename'])) {
		$segments[] = $query['pagename'];
		unset($query['pagename']);
	}
	if (isset($query['uid'])) {
		$segments[] = $query['uid'];
		unset($query['uid']);
	}
	
	return $segments;
}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/myjspace/view/id/Itemid
 *
 * index.php?/myjspace/id/Itemid
 */
function MyjspaceParseRoute($segments = null)
{
	$vars = array();

	// view is always the first element of the array
	$count = count($segments);
	
	if ($count) {
		$count--;
		$segment = array_shift($segments);
		if (is_numeric($segment)) {
			$vars['id'] = $segment;
		} else {
			$vars['view'] = $segment;
		}
	}

	if ($count && isset($vars['view']) && $vars['view'] == 'pages') {	
		$count--;
		$segment = array_shift($segments);
		
		if (is_numeric($segment)) {
			$vars['uid'] = $segment;
		}
	}
	
	if ($count) {	
		$count--;
		$segment = array_shift($segments);
		
		if (is_numeric($segment)) {
			$vars['id'] = $segment;
		} else
			$vars['pagename'] = $segment;
		
	}

	return $vars;
}

?>
