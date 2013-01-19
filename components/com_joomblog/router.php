<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: router.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage router.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once('functions.joomblog.php' );

function JoomblogBuildRoute( &$query )
{
	$mainframe	=& JFactory::getApplication();
	
	$segments = array();
	$admintask = array(
		'adminhome',
		'bloggerpref',
		'bloggerstats',
		'showcomments');
		
	//$endWithSlash = array('rss', 'feed', 'tagslist', 'blogs', 'profile', 'search', 'archive');
	$endWithSlash = array();


	if (isset($query['task']) && $query['task'] != 'tag') {
		if($query['task'] == 'tagslist'){
			$query['task'] = 'tagslist';
		}
		
		if($query['task'] == 'categorylist'){
			$query['task'] = 'categorylist';
		}
		
		if($query['task'] == 'rss'){
			$query['task'] = 'feed';
		}
		
		if ($query['task'] == 'profile') {
			$segments[] = 'profile';
			$segments[] = jbUserGetName($query['id']);
			unset($query['id']);
			unset($query['task']);
		}
		
		if(isset($query['task']) && in_array($query['task'], $admintask)){	
			if (isset($query['amp;Itemid'])) {
				unset($query['amp;Itemid']);
			}
		}
		
		if($mainframe->getCfg('sef') && $mainframe->getCfg('sef_suffix')){
			if(isset($query['task']) && strlen($query['task']) > 5 && (substr($query['task'], -5) == '.html')){
				$query['task'] = substr($query['task'], 0, -5);
			}
		}
		
		if(isset($query['task']) && (in_array($query['task'], $endWithSlash) || 
			in_array($query['task'], $admintask))){
			$query['task'] /*.= '/' */;
		}
		
		if(isset($query['task'])){
			$segments[] = $query['task'];
			unset($query['task']);
		}
	} else
	
	if (isset($query['show'])) {
		$segments[] = 'post';
		$segments[] = jbGetPost($query['show'])->alias /*.'/' */;
		unset($query['show']);
	} else
	
	if (isset($query['category'])) {
		$segments[] = 'category';
		$segments[] = jbGetCategory($query['category'])->alias /*."/" */;
		unset($query['category']);
		if(isset($query['task'])) unset($query['task']);
	} else
	
	if (isset($query['tag'])) {
		$segments[] = 'tag';
		$query['tag'] = str_replace(' ', '-', $query['tag']);
		$segments[] = $query['tag'] /*. '/' */;
		unset($query['tag']);
		if(isset($query['task'])) unset($query['task']);
	} else

	if (isset($query['user'])) {
		$segments[] = 'user';
		$segments[] = $query['user'] /*.'/' */;
		unset($query['user']);
	} else
	
	if(in_array('delete', $segments)){
		$segments[] = $query['id'];
		unset($query['id']);
	} else {
	
	}
	
	if (isset($query['view']))
	{
		if ($query['view']=="default") $segments[] = 'mainpage';
		unset($query['view']);
	}
	
	if (isset($query['blogid']))
	{
		$segments[] = 'blog';
		$db	=& JFactory::getDBO();
		$sql = "SELECT `title` FROM #__joomblog_list_blogs WHERE id = '".(int)$query['blogid']."' ";
		$db->setQuery( $sql );
		$result=$db->loadResult();
		$blogsef = JFilterOutput::stringURLSafe($result);
		$segments[] = $blogsef;
		unset($query['blogid']);
	}

	return $segments;
}

function JoomblogParseRoute( $segments )
{
	$db	=& JFactory::getDBO();

	$vars = array();

	$admintask = array(
		'adminhome',
		'bloggerpref',
		'bloggerstats',
		'showcomments'
	);
	
	$actions = array(
		'cpage',
		'tagslist',
		'categorylist',
		'archive',
		'feed',
		'search',
		'blogs'
	);
  
	if(isset($segments[0])){
		for($i = 0; $i < count($segments); $i++){
			if(strlen($segments[$i]) > 5 && substr($segments[$i], -5) == '.html'){
				$segments[$i] = substr($segments[$i], 0, -5);
			}
		}
		
		if($segments[0] == 'post' && isset($segments[1])){
			$vars['task']	= 'show';
			$segments[1] = str_replace(':', '-', $segments[1]); 
			$query = "SELECT a.id FROM #__joomblog_posts AS a, #__categories AS c WHERE c.extension = 'com_joomblog' AND c.id = a.catid AND a.alias = '{$segments[1]}' ";
			$db->setQuery( $query );
			$vars['show'] = $db->loadResult();
		} else
		
		if($segments[0] == 'tagslist' && !isset($segments[1])){
			$vars['task'] = 'tagslist';
		} else
		
		if($segments[0] == 'categorylist' && !isset($segments[1])){
			$vars['task'] = 'categorylist';
		} else
		
		if($segments[0] == 'category' && isset($segments[1])){
			$vars['task']	= 'tag';
			$segments[1] = str_replace(':', '-', $segments[1]);
			$db	=& JFactory::getDBO();
			$db->setQuery("SELECT `id` FROM #__categories WHERE extension = 'com_joomblog' AND `alias`='{$segments[1]}'"); 
			$vars['category'] = $db->loadResult();
		} else
		
		if($segments[0] == 'tag' && isset($segments[1])){
			$vars['task']	= 'tag';
			$segments[1] = str_replace(':', '-', $segments[1]); 
			$segments[1] = str_replace('-', ' ', $segments[1]);
			$vars['tag'] = $segments[1];
		} else
		
		if($segments[0] == 'archive' && isset($segments[1])){
			$vars['archive'] = $segments[1];
		} else 
		
		if($segments[0] == 'delete'){
			$vars['task'] = 'delete';
		} else 
		
		if($segments[0] == 'user' && isset($segments[1])){
			$vars['user'] = $segments[1];
		} else
		
		if($segments[0] == 'profile' && isset($segments[1])){
			$vars['task'] = 'profile';
			$userid = jbGetAuthorId($segments[1]);
			$vars['id'] = $userid;
		} else
				
		if($segments[0] == 'feed'){
			$vars['task'] = 'rss';
		}else
		
		if($segments[0] == 'search'){
			$vars['task'] = 'search';
		}
		
		if(in_array($segments[0], $admintask) || empty($vars)){
			$vars['task'] = $segments[0];
		} 
		
		
		if($segments[0] == 'blog' && isset($segments[1])){
			
			$vars['task'] = '';
			$vars['view'] = 'blogger';
			$segments[1] = str_replace(':', '-', $segments[1]); 
			$db	=& JFactory::getDBO();
			$db->setQuery("SELECT `id` FROM #__joomblog_list_blogs WHERE `alias`='{$segments[1]}'"); 
			$vars['blogid'] = $db->loadResult();
		} else
		
		if($segments[0] == 'mainpage')
		{
			$vars['task'] = '';
			$segments[0]='';
			$vars['view'] = 'default';
		}
		
	}

	return $vars;
}

