<?php
/**
* JoomBlog component for Joomla
* @version $Id: datamanager.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage datamanager.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.file' );

function mb_get_entries(&$searchby)
{
	global $sectionid, $_JB_CONFIGURATION;

	$db			=& JFactory::getDBO();
	$mainframe	=& JFactory::getApplication();
	$user	=& JFactory::getUser();
	
	$limit 		= isset($searchby['limit']) 	 ? intval($searchby['limit']): 10;
	$limitstart = isset($searchby['limitstart']) ? intval($searchby['limitstart']): 0;
	$jcategory 	= isset($searchby['jcategory'])  ? intval($searchby['jcategory']): 0;
	
	$authorid 	= isset($searchby['authorid']) 	 ? $db->getEscaped($searchby['authorid']): "";
	$category 	= isset($searchby['category']) 	 ? $db->getEscaped($searchby['category']): "";
	$search 	= isset($searchby['search']) 	 ? $db->getEscaped($searchby['search']): "";
	$archive 	= isset($searchby['archive']) 	 ? $db->getEscaped($searchby['archive']): "";
	
	$sections = implode(',',jbGetCategoryArray($_JB_CONFIGURATION->get('managedSections')));
	$selectMore		= "";
	$searchWhere	= "";
	$primaryOrder	= "";
	$use_tables		= "";
	
	if (!empty ($category) && empty($jcategory))
	{
		$categoriesArray = explode(",", $category);
		$categoriesList = "0";
		foreach ($categoriesArray as $jbtag)
		{
			$jbtag	= $db->getEscaped( trim( $jbtag ) );

			$jbtag = str_replace(' ', '%', $jbtag);
			$db->setQuery("SELECT id FROM #__joomblog_tags WHERE name LIKE '$jbtag' ");
			$searchCategoryId = $db->loadResult();
			
			if ($searchCategoryId)
			{
				$categoriesList .= ",";
				$categoriesList .= "$searchCategoryId";
			}
		}
		
		$use_tables .= ",#__joomblog_tags as b,#__joomblog_content_tags as c ";
		$searchWhere .= " AND (b.id=c.tag AND c.contentid=a.id AND b.id IN ($categoriesList)) ";
	}
	
	if (!empty ($jcategory) && $jcategory > 0)
	{		
		$searchWhere .= " AND (a.catid='$jcategory') ";
	}
	
	if (!empty ($authorid) or $authorid == "0")
	{
		$searchWhere .= " AND a.created_by IN ($authorid)";
	}
	
	if (!empty ($search))
	{
		$searchWhere .= " AND match (a.title,a.fulltext,a.introtext) against ('$search' in BOOLEAN MODE) ";
	}
	
	if (!empty ($archive))
	{
		$searchWhere .= " AND a.created BETWEEN '$archive' AND date_add('$archive', INTERVAL 1 MONTH) ";
	}
	
	$date	=& JFactory::getDate();
	
	$lang = JFactory::getLanguage();

	$searchWhere .= " AND a.language IN ('*','".$lang->get('tag')."') ";
	
	$query = " SELECT count(*) FROM #__joomblog_posts as a $use_tables WHERE a.state=1 and a.publish_up < '" . $date->toMySQL() . "' and a.sectionid in ($sections) $searchWhere";
	$db->setQuery($query);
	$total = $db->loadResult();
	$searchby['total'] = $total;

	$query = " SELECT a.*, round(r.rating_sum/r.rating_count) as rating, r.rating_count, p.posts $selectMore 
		FROM (#__joomblog_posts as a $use_tables ) 
			left outer join #__joomblog_posts_rating as r 
				on (r.content_id=a.id) 
		 LEFT JOIN `#__joomblog_privacy` AS `p` ON `p`.`postid`=`a`.`id` AND `p`.`isblog`=0  
		WHERE a.state=1 and a.publish_up < '" . $date->toMySQL() . "' 
			and a.catid in ($sections) 
			$searchWhere ORDER BY $primaryOrder a.created DESC,a.id DESC";// LIMIT $limitstart,$limit";

	$db->setQuery($query);

	$rows = $db->loadObjectList();
	
	$v = 0;
	
	if($rows AND count($rows) > 0)
	{
		$url	= rtrim( JURI::root() , '/' );

		for($i = 0; $i < count($rows); $i++ )
		{
			$rows[$i]->permalink = jbGetPermalinkUrl($rows[$i]->id);
			$rows[$i]->introtext = str_replace('src="images', 'src="'. $url .'/images', $rows[$i]->introtext );
			$rows[$i]->fulltext = str_replace('src="images', 'src="'. $url .'/images',  $rows[$i]->fulltext );
			
			$row=&$rows[$i];
			
			$db->setQuery(" SELECT b.blog_id as bid, lb.title as btitle, p.posts, p.comments  " .
						      " FROM #__joomblog_blogs as b, #__joomblog_list_blogs as lb " .
						      " LEFT JOIN `#__joomblog_privacy` AS `p` ON `p`.`postid`=`lb`.`id` AND `p`.`isblog`=1 ".
						      " WHERE b.content_id=".$row->id." AND lb.id = b.blog_id AND lb.approved=1 AND lb.published=1 ");
			$blogs = $db->loadObjectList();
			if (sizeof($blogs)) 
				{
				switch ( $blogs[0]->posts ) 
				{
					case 0:	break;
					case 1:	
						if (!$user->id) 
						{
							unset($rows[$i]);
						}
					case 2:	
						if (!$user->id) 
						{
							unset($rows[$i]);
						}else
						{
							if (!isFriends($user->id, $row->created_by) && $user->id!=$row->created_by)
							{
								unset($rows[$i]);
							}	
						}
					break;
					case 3:	
						if (!$user->id) 
						{
								unset($rows[$i]);
						}else
						{
							if ($user->id!=$row->created_by)
							{
								unset($rows[$i]);
							}	
						}						
					break;
				}
			}	
			if (isset($rows[$i]))
			switch ( $row->posts ) 
			{
				case 0: break;
				case 1:	
					if (!$user->id) 
					{
						unset($rows[$i]);
					}
				break;
				case 2:	
					if (!$user->id) 
					{
						unset($rows[$i]);
					}else
					{
						if (!isFriends($user->id, $row->created_by) && $user->id!=$row->created_by)
						{
							unset($rows[$i]);
						} 
					}
				break;
				case 3:	
					if (!$user->id) 
					{
						unset($rows[$i]);
					}else
					{
						if ($user->id!=$row->created_by)
						{
							unset($rows[$i]);
						}
					}						
				break;
		}

		}
			$rows = array_values($rows);	
	}
	$n = sizeof( $rows );
	$ret = null;
	if ($n>$limit) $n=$limit;
	for ( $i = 0; $i < $n; $i++ ) 
	{
		$ret[$i]=$rows[$i];
	}
	
	return $ret;
}

