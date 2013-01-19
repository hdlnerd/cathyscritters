<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: viewtags.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage viewtags.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'base.php' );

jimport( 'joomla.html.pagination' );

class JbblogViewtagsTask extends JbblogBaseController{
	
	function JbblogViewtagsTask(){
		$this->toolbar = JB_TOOLBAR_BLOGGER;
	}
	
	function display(){
	
		global $_JB_CONFIGURATION, $Itemid;
	
		$mainframe	=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		
		$post = JRequest::get('post');
		$option = 'com_joomblog';$trigger = 0;
		$sort = $mainframe->getUserStateFromRequest( $option.'sort', 'sort', 'name', 'word' );
		$order = $mainframe->getUserStateFromRequest( $option.'order', 'order', 'asc', 'word' );
		
		if ($sort == 'weight')
		{
			$sort = 'name';
			$trigger = 1;
		}
		
		$like = '';
		if (isset($post['filter-tags']) && $post['filter-tags'])
		{
			$like = " WHERE t.name LIKE '%".$post['filter-tags']."%'";
		}
		
		$db->setQuery(	"SELECT a.*, p.posts, ct.contentid, COUNT(ct.tag) as count, t.name " .
						" FROM #__joomblog_content_tags as ct " .
						" LEFT JOIN #__joomblog_posts as a ON a.id = ct.contentid " .
						" LEFT JOIN `#__joomblog_privacy` AS `p` ON `p`.`postid`=`a`.`id` AND `p`.`isblog`=0 ".
						" LEFT JOIN #__joomblog_tags as t ON t.id = ct.tag ".$like." " .
                        " WHERE t.name <> '' " .
						" GROUP BY (ct.tag) ORDER BY t.".$sort." ".$order."");
//		echo 'SMT DEBUG: <pre>'; print_R($db->getQuery()); echo '</pre>';
		
		$tags = $db->loadObjectList();
		$this->_getBlogs($tags);
		$tags = $this->getRowsByPrivacyFilter($tags);
	//echo "<hr /> SMT Debug:<pre>"; print_R($tags); echo "</pre><hr />";
	//die;
		if (count($tags) && $trigger){
			usort($tags, array("JbblogViewtagsTask", "cmpSort"));
			if ($order == 'asc'){
				ksort($tags);
			} else if ($order=='desc') {
				krsort($tags);
			}
		}
		
		$tag = array();
		$tag[0] = $tags;
		
		$template		= new JoomblogTemplate();
		$template->set( 'Itemid' , $Itemid );
		$template->set( 'tags' , $tag );
		$template->set( 'filtertags' , isset($post['filter-tags'])?$post['filter-tags']:'');
		
		$content = $template->fetch( $this->_getTemplateName('viewtags') );
		$content = '<div id="jblog-section">'.JText::_('COM_JOOMBLOG_TAGS').'</div><div id="tags-list">'.$content.'</div>';
		
		return $content;
		
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
				if (!isset($post->blogtitle)) {$rows[$i]->count--;continue;}
				switch ( $post->posts ) 
				{
					case 0:	break;
					case 1:	
						if (!$user->id) 
						{
							$rows[$i]->count--;
						}
					break;
					case 2:	
						if (!$user->id) 
						{
							unset($rows[$i]);
						}else
						{
							if (!$this->isFriends($user->id, $post->created_by) && $user->id!=$post->created_by)
							{
								$rows[$i]->count--;
							}	
						}						
					break;
					case 3:	
						if (!$user->id) 
						{
							$rows[$i]->count--;
						}else
						{
							if ($user->id!=$post->created_by)
							{
								$rows[$i]->count--;
							}	
						}						
					break;
					case 4:	
								$rows[$i]->count--;											
					break;
				}
			}
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
		
		$db			=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		
		if(count($rows)){
			for($i =0; $i < count($rows); $i++){
				$row    =& $rows[$i];
			
				$db->setQuery(" SELECT b.blog_id, lb.title,p.posts, p.comments, p.jsviewgroup   " .
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
							if (!jbInJSgroup($user->id, $blogs[0]->jsviewgroup))
							{
								$row->posts = $blogs[0]->posts;
							}	else $row->posts = 0;
						}
					break;
				}					
				$row->blogid = $blogs[0]->blog_id;
				$row->blogtitle = $blogs[0]->title;
				}
			}
		}
	}
	
	function cmpSort($a, $b){
				
		if ($a->count == $b->count) {
			return 0;
		}
			
			return ($a->count < $b->count) ? -1 : +1;
		}
}

