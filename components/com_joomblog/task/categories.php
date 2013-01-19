<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: categories.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage categories.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'base.php' );

jimport( 'joomla.html.pagination' );

class JbblogCategoriesTask extends JbblogBaseController{
	
	function JbblogCategoriesTask(){
		$this->toolbar = JB_TOOLBAR_BLOGGER;
	}
	
	function display(){
	
		global $_JB_CONFIGURATION, $Itemid;
	
		$mainframe	=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$total = 0;
		
		$option = 'com_joomblog';
		$limit		= $mainframe->getUserStateFromRequest( $option.'category.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'category.limitstart', 'limitstart', 0, 'int' );		
		
		$rows = array();
		$sections = implode(',',jbGetCategoryArray($_JB_CONFIGURATION->get('managedSections')));
		$rows = jbGetCategoryArray($_JB_CONFIGURATION->get('managedSections'));
		$categoriesHTML = '';
		
		jbAddPageTitle( JText::_('COM_JOOMBLOG_SHOW_CATEGORIES_TITLE') );
		jbAddPathway( JText::_('COM_JOOMBLOG_SHOW_CATEGORIES_TITLE') );
		
		$total = count(jbGetCategoryArray($_JB_CONFIGURATION->get('managedSections')));
		$pageNav = new JPagination($total, $limitstart, $limit);
		
		$query = "SELECT c.* , COUNT(mc.aid) as count  
			FROM #__categories AS c " .
			" INNER JOIN #__joomblog_multicats AS mc ON  mc.cid = c.id " .
			" WHERE c.published = 1 AND c.id IN (".$sections.") GROUP BY (c.id) LIMIT $limitstart, $limit";
		$db->setQuery($query);
		$categories = $db->loadObjectList();
		$content = "";
		
		if (!empty($rows))
		{
			for ( $i = 0, $n = sizeof( $categories ); $i < $n; $i++ ) {
				$category=$categories[$i];
				$category->link = JRoute::_('index.php?option=com_joomblog&task=tag&category='.$category->id.'&Itemid='.$Itemid);
				
				$db->setQuery("SELECT a.*,p.posts, p.comments FROM #__joomblog_multicats as mc " .
						" LEFT JOIN #__joomblog_posts as a ON a.id = mc.aid " .
						" LEFT JOIN `#__joomblog_privacy` AS `p` ON `p`.`postid`=`a`.`id`  AND `p`.`isblog`=0".
						" LEFT JOIN #__categories as c ON c.id = mc.cid " .
						" WHERE a.state = 1 AND c.published = 1 AND mc.cid = {$category->id} " .
						//" ORDER BY (a.created) DESC");
						" ORDER BY (a.created) DESC LIMIT 0, ".$_JB_CONFIGURATION->get('CategoriesRecentPosts'));
				/*
				$db->setQuery("SELECT a.*,p.posts, p.comments FROM #__joomblog_posts as a " .
						" LEFT JOIN `#__joomblog_privacy` AS `p` ON `p`.`postid`=`a`.`id`  AND `p`.`isblog`=0".
						" LEFT JOIN #__categories as c ON c.id = a.catid " .
						" WHERE a.state = 1 AND c.published = 1 AND c.id = {$category->id} " .
						" ORDER BY (a.created) DESC LIMIT 0, ".$_JB_CONFIGURATION->get('CategoriesRecentPosts'));
				*/
				$articles = $db->loadObjectList();		
				JbblogCategoriesTask::_getCategories($articles);
				JbblogCategoriesTask::_getBlogs($articles);
				$articles = $this->getRowsByPrivacyFilter($articles);
				$db->setQuery("SELECT COUNT(a.id) FROM #__joomblog_multicats as mc " .
						" LEFT JOIN #__joomblog_posts as a ON a.id = mc.aid " .
						" LEFT JOIN `#__joomblog_privacy` AS `p` ON `p`.`postid`=`a`.`id`  AND `p`.`isblog`=0".
						" LEFT JOIN #__categories as c ON c.id = mc.cid " .
						" WHERE a.state = 1 AND c.published = 1 AND mc.cid = {$category->id} " .
						" ORDER BY (a.created) DESC");
				$count = $db->loadResult();		
				$categories[$i]->count =$count-$this->deltotal;
				
				$article = array();
				$article[0] = $articles;
				$cat = array();
				$cat[0] = $category;
				
				$template		= new JoomblogTemplate();
				$template->set( 'Itemid' , $Itemid );
				$template->set( 'articles' , $article );
				$template->set( 'category' , $cat );
				
				$categoriesHTML .= $template->fetch( $this->_getTemplateName('category_list') );
				
				unset( $template );
				
			}
			
			$content .= '<div id="jblog-section">'.JText::_('COM_JOOMBLOG_SHOW_CATEGORIES_TITLE').'</div><div id="categories">'.$categoriesHTML.'</div>';
			$content .= '<div class="jb-pagenav">' . $pageNav->getPagesLinks() . '</div>';
			
			return $content;
		}
		
	}
	
	protected function getRowsByPrivacyFilter($rows=null)
	{
		$this->deltotal=0;
		$user	=& JFactory::getUser();
		if (sizeof($rows))
		{
			for ( $i = 0, $n = sizeof( $rows ); $i < $n; $i++ ) 
			{
				$post = &$rows[$i];
				$post->posts?$post->posts:$post->posts=0;
				if (!isset($post->blogtitle)) {unset($rows[$i]); $this->deltotal++;continue;}
				
				switch ( $post->posts ) 
				{
					case 0:	break;
					case 1:	
						if (!$user->id) 
						{
							$rows[$i]=null;
							unset($rows[$i]);
							$this->deltotal++;
						}
					break;
					case 2:	
						if (!$user->id) 
						{
							unset($rows[$i]);
							$this->deltotal++;
							
						}else
						{
							if (!$this->isFriends($user->id, $post->created_by) && $user->id!=$post->created_by)
							{
								unset($rows[$i]);
								$this->deltotal++;
								
							}	
						}						
					break;
					case 3:	
						if (!$user->id) 
						{
							unset($rows[$i]);
							$this->deltotal++;
							
						}else
						{
							if ($user->id!=$post->created_by)
							{
								unset($rows[$i]);
								$this->deltotal++;
								
							}	
						}						
					break;
					case 4:
						unset($rows[$i]);
						$this->deltotal++;
					break;
				}
				
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
	
	function _getCategories(&$rows)
	{
		global $Itemid;
		if(count($rows)){
			for($i =0; $i < count($rows); $i++){
				$row    =& $rows[$i];
				$row->categories	= jbCategoriesURLGet($row->id, true);
			}
		}
	}
	
	function _getBlogs(&$rows){
		
		$db			=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		
		if(count($rows)){
			for($i =0; $i < count($rows); $i++){
				$row    =& $rows[$i];
			
				$db->setQuery(" SELECT b.blog_id, lb.title,p.posts, p.comments, p.jsviewgroup  " .
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
							$row->posts = $blogs[0]->posts;
						}
					break;
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
				$row->blogid = $blogs[0]->blog_id;
				$row->blogtitle = $blogs[0]->title;
				}
			}
		}
	}
	
}