<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: users.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage users.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'base.php' );

jimport( 'joomla.html.pagination' );

class JbblogUsersTask extends JbblogBaseController{
	
	function JbblogUsersTask(){
		$this->toolbar = JB_TOOLBAR_BLOGGER;
	}
	
	function display(){
		
		global $JBBLOG_LANG, $_JB_CONFIGURATION, $Itemid;
		
		$option = 'com_joomblog';
		$mainframe	=& JFactory::getApplication();
		jbAddPathway(JText::_('COM_JOOMBLOG_ALL_BLOGS_TITLE'));	
		jbAddPageTitle(JText::_('COM_JOOMBLOG_ALL_BLOGS_TITLE'));
		$use_tables = ",#__joomblog_tags as b,#__joomblog_content_tags as c ";
		$total = 0;
		
		$limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
		
		$limit		= intval($limit);
		$limitstart = intval($limitstart);
		$sections	= implode(',',jbGetCategoryArray($_JB_CONFIGURATION->get('managedSections')));
		$db			=& JFactory::getDBO();
		
		
		$query = "SELECT count(u.id) FROM #__users as u WHERE u.block = 0";
		$db->setQuery($query);
		$total = $db->loadResult();
		
		$pageNav = new JPagination($total, $limitstart, $limit);	
		
		$db->setQuery("SELECT u.username, u.name, ub.* FROM #__joomblog_user as ub LEFT JOIN #__users as u ON u.id = ub.user_id WHERE u.block = 0 LIMIT $limitstart, $limit");
		$users = $db->loadObjectList();
				
		$bloggersHTML = '';
		if (!empty($users)){
			foreach($users as $user){
				
				if ($_JB_CONFIGURATION->get('avatar') == 'jomsocial'){
					$db->setQuery("SELECT thumb FROM #__community_users WHERE userid = ".$user->user_id);
					$avatar = $db->loadResult();
					$user->src			= (!$avatar) ? JUri::root()."components/com_joomblog/images/user_thumb.png" : JUri::root().$avatar;
				} else if ($_JB_CONFIGURATION->get('avatar') == 'juser'){
					if ($user->avatar)
					{
						$user->src = JURI::root()."images/joomblog/avatar/thumb_".$user->avatar;
					} else {
						$user->src = JURI::root()."components/com_joomblog/images/user_thumb.png";
					}
				}
				
				$user->link = JRoute::_("index.php?option=com_joomblog&task=profile&id={$user->user_id}&Itemid=$Itemid");
				
				$date =& JFactory::getDate();
				$searchWhere = " AND a.created_by = $user->user_id";
				
				$query = "SELECT DISTINCT COUNT(*) FROM #__joomblog_posts as a " .
						" INNER JOIN #__joomblog_blogs AS `bl` ON `bl`.`content_id`=`a`.`id`  " .
						" WHERE a.state=1 AND a.publish_up < '" . $date->toMySQL() . "' 
						  and a.catid in ($sections) 
						  $searchWhere";				
				$db->setQuery($query);
				$totalArticle = $db->loadResult();				
				$this->totalEntries = $totalArticle;
				$query = " SELECT DISTINCT a.*,p.posts, p.comments  FROM #__joomblog_posts as a " .
						" LEFT JOIN `#__joomblog_privacy` AS `p` ON `p`.`postid`=`a`.`id`  AND `p`.`isblog`=0 ".
						" WHERE a.state=1 AND a.publish_up < '" . $date->toMySQL() . "' 
						and a.catid in ($sections) 
						$searchWhere ORDER BY a.created DESC LIMIT 0, ".$_JB_CONFIGURATION->get('BloggerRecentPosts');
				$db->setQuery($query);
				$bloggers = $db->loadObjectList();
				JbblogUsersTask::_getBlogs($bloggers);	
				
				$bloggers = $this->getRowsByPrivacyFilter($bloggers);				
				
				$template		= new JoomblogTemplate();
				$man = array(); $recent = array();
				$man[0] = $user;
				$recent[0] = $bloggers;
				
				$template->set( 'recent' , $recent );
				$template->set( 'Itemid' , $Itemid );
				$template->set( 'man' ,  $man);
				$template->set( 'totalArticle' ,  $this->totalEntries);
				$template->set('categoryDisplay' , $_JB_CONFIGURATION->get('categoryDisplay') );
								
				$bloggersHTML	.= $template->fetch( $this->_getTemplateName('users') );
				
				unset( $template );
			}
				
			$content = '<div id="jblog-section">'.JText::_('COM_JOOMBLOG_BLOGGERS').'</div><div id="bloggers">'.$bloggersHTML.'</div>';
			
			$content .= '<div class="jb-pagenav">' . $pageNav->getPagesLinks() . '</div>';
			return $content;
			
		}
	}
	
	protected function getRowsByPrivacyFilter($rows=null)
	{
		$user	=& JFactory::getUser();
		if (sizeof($rows))
		{
			for ( $i = 0, $n = sizeof( $rows ); $i < $n; $i++ ) 
			{
				
				$post = &$rows[$i];
				$post->posts?$post->posts:$post->posts=0;
				switch ( $post->posts ) 
				{
					case 0:	break;
					case 1:	
						if (!$user->id) 
						{
							$rows[$i]=null;
							unset($rows[$i]);
							$this->totalEntries--;
						}
					break;
					case 2:	
						if (!$user->id) 
						{
							unset($rows[$i]);
							$this->totalEntries--;
						}else
						{
							if (!$this->isFriends($user->id, $post->created_by) && $user->id!=$post->created_by)
							{
								unset($rows[$i]);
								$this->totalEntries--;
							}	
						}						
					break;
					case 3:	
						if (!$user->id) 
						{
							unset($rows[$i]);
							$this->totalEntries--;
						}else
						{
							if ($user->id!=$post->created_by)
							{
								unset($rows[$i]);
								$this->totalEntries--;
							}	
						}						
					break;
					case 4:
						unset($rows[$i]);
						$this->totalEntries--;
					break;	
				}
					if (!isset($post->btitle) && isset($rows[$i])) {unset($rows[$i]); $this->totalEntries--;}
			}
			$rows = array_values($rows);
		}
		return $rows;
	}
	
	protected function isFriends($id1=0,$id2=0)
	{
		$db	=& JFactory::getDBO();
		$db->setQuery(	" SELECT `connection_id` FROM `#__community_connection` " .
						" WHERE connect_from=".(int)$id1." AND connect_to=".(int)$id2." AND `status`=1 ");
		$frindic = $db->loadResult();
		if ($frindic) return true; else return false;				
	}
	
	
	function _getBlogs(&$rows){
		
		global $Itemid;
		$db			=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		if(count($rows)){
			for($i =0; $i < count($rows); $i++){
				$row    =& $rows[$i];
			
				$db->setQuery("SELECT b.blog_id as bid, lb.title as btitle, p.posts, p.jsviewgroup " .
						" FROM #__joomblog_blogs as b, #__joomblog_list_blogs as lb " .
						" LEFT JOIN `#__joomblog_privacy` AS `p` ON `p`.`postid`=`lb`.`id` AND `p`.`isblog`=1 ".
						" WHERE b.content_id=".$row->id." AND lb.id = b.blog_id");
				$blogs = $db->loadObjectList();
				if (sizeof($blogs)) 
				{
				switch ( $blogs[0]->posts ) 
				{
					case 0:	break;
					case 1:	
						if (!$user->id) 
						{
							$row->posts = $blogs[0]->posts;
						}
					case 2:	
						if (!$user->id) 
						{
							$row->posts = $blogs[0]->posts;
						}else
						{
							if (!$this->isFriends($user->id, $row->created_by) && $user->id!=$row->created_by)
							{
								$row->posts = $blogs[0]->posts;
							}	
						}
					break;
					case 3:	
						if (!$user->id) 
						{
							$row->posts = $blogs[0]->posts;
						}else
						{
							if ($user->id!=$post->created_by)
							{
								$row->posts = $blogs[0]->posts;
							}	
						}						
					break;
					case 4:	
						if (!$user->id) 
						{
							$row->posts = $blogs[0]->posts;
						}else
						{
							if (!jbInJSgroup($user->id, $blog[0]->jsviewgroup))
							{
								$row->posts = $blogs[0]->posts;
							}else $row->posts = 0;
						}
					break;
				}
				$row->bid = $blogs[0]->bid;
				$row->btitle = $blogs[0]->btitle;
				}
				
				$row->multicats = false;
				$cats = jbGetMultiCats($row->id);
				
				if (sizeof($cats))
				{
					$jcategories = array();
					foreach ( $cats as $cat ) 
					{
						$catlink = JRoute::_('index.php?option=com_joomblog&task=tag&category='.$cat.'&Itemid='.$Itemid );
						$jcategories []= ' <a class="category" href="' .$catlink. '">' . jbGetJoomlaCategoryName($cat).'</a> ';	
					}
					if (sizeof($jcategories)>1) $row->multicats = true;
					if (sizeof($jcategories)) $row->jcategory = implode(',', $jcategories);
					
				}else $row->jcategory	= '<a class="category" href="' . JRoute::_('index.php?option=com_joomblog&task=tag&category=' . $row->catid.'&Itemid='.$Itemid.$tmpl ). '">' . jbGetJoomlaCategoryName( $row->catid ) . '</a>';
				
				
				$row->categories	= jbCategoriesURLGet($row->id, true);
			}
		}
	}
	
}
