<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: search.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage search.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'base.php' );
require_once( JB_LIBRARY_PATH . DS . 'plugins.php' );

class JbblogSearchTask extends JbblogBaseController
{
	var $_resultLength	= 250;
	var $_plugins		= null;
	
	function JbblogSearchTask()
	{
		$this->toolbar	= JB_TOOLBAR_SEARCH;
		$this->_plugins	= new JBPlugins();
	}
	
	function display()
	{
		global $Itemid, $JBBLOG_LANG, $_JB_CONFIGURATION;
		
		$mainframe	=& JFactory::getApplication();
		$my			=& JFactory::getUser();
		$pathway =& $mainframe->getPathway();
		
		jbAddPageTitle( JText::_( 'COM_JOOMBLOG_SEARCH_BLOG_ENTRY_TITLE') );
		
		$pathway->addItem(JText::_( 'COM_JOOMBLOG_SEARCH_BLOG_ENTRY_TITLE'),'');
		
		$template	= new JoomblogCachedTemplate(time() . $my->usertype . $_JB_CONFIGURATION->get('template'));
		
		$blogger		= JRequest::getVar('blogger','');
		$keyword		= JRequest::getVar('keyword','');
		$tags			= JRequest::getVar('tags','');
		$limitstart		= JRequest::getInt( 'limitstart' , 0  );

		$searchURL	= JRoute::_('index.php?option=com_joomblog&task=search&Itemid='.jbGetItemId());
		
		$template->set('searchURL', $searchURL);
		$template->set('Itemid', jbGetItemId());
		$results	= false;
		if((!empty($blogger) && isset($blogger))|| (!empty($keyword) && isset($keyword)) || (!empty($tags) && isset($tags)) )
		{
			$results	= $this->_search(array('blogger' => $blogger, 'keyword' => $keyword, 'tags' => $tags));
		}
		
		$template->set('pagination', $results['pagination']);
		unset($results['pagination']);
		$template->set('categoryDisplay' , $_JB_CONFIGURATION->get('categoryDisplay') );
		$template->set('limitstart' ,$limitstart);
		$template->set('blogger', $blogger);
		$template->set('keyword', $keyword);
		$template->set('tags', $tags);
		$template->set('results', $results);
		$content = $template->fetch($this->_getTemplateName('search'));
		
		return $content;
	}
	 	 	
	function _search($filter)
	{
		global $_JB_CONFIGURATION;
		
		$limit = JRequest::getVar( 'limit' , $_JB_CONFIGURATION->get('numEntry') , 'GET');
		$limitstart	= JRequest::getInt( 'limitstart' , 0  );
		
		$db			=& JFactory::getDBO();
		
		$sections = implode(',',jbGetCategoryArray($_JB_CONFIGURATION->get('managedSections')));
		
		$query = '';
    
    //$query .= " LEFT JOIN #__categories AS c ON c.id = a.catid ";

		$blogger = isset( $filter['blogger'] ) ? $db->getEscaped( $filter['blogger'] ) : '';
		$keyword = isset( $filter['keyword'] ) ? $db->getEscaped( $filter['keyword'] ) : '';
		$tag = isset( $filter['tags'] ) ? $db->getEscaped( $filter['tags'] ) : '';
		
		$query		.= (!empty( $filter['blogger']) || !empty( $filter['keyword']) || !empty( $filter['tags']) ) ? ' WHERE ' : '';
		
		if(!empty( $tag ))
		{
			$tagId	= jbGetTagId( $tag );
			
			$query	.= ' b.tag="' . $tagId . '" AND a.id=b.contentid';
		}
		
		if(!empty($blogger))
		{
			if( !empty( $tag ) )
			{
				$query	.= "AND a.created_by='" . jbGetAuthorId($blogger) ."'";
			}
			else
			{
				$query	.= "a.created_by='" . jbGetAuthorId($blogger) ."'";
			}
		}

		if(!empty($keyword))
		{
			if(!empty($blogger) || !empty( $tag ) )
			{
				$query		.= " AND (a.title LIKE '%{$keyword}%' "
							 . "OR a.introtext LIKE '%{$keyword}%' "
							 . "OR a.fulltext LIKE '%{$keyword}%')";
			}
			else
			{
				$query		.= "(a.title LIKE '%{$keyword}%' "
							 . "OR a.introtext LIKE '%{$keyword}%' "
							 . "OR a.fulltext LIKE '%{$keyword}%')";
			}
		}

		$query	.= " AND `catid` IN ({$sections})";

		$lang = JFactory::getLanguage();

		$query	.= " AND a.language IN ('*','".$lang->get('tag')."') ";

		jimport( 'joomla.html.pagination' );
    
		$db->setQuery( "SELECT COUNT(DISTINCT a.id) FROM #__joomblog_posts AS a LEFT JOIN #__joomblog_content_tags AS b ON a.id = b.contentid ".$query );
		$total = $db->loadResult();
		$this->totalEntries=$total;
		$pageNav	= new JPagination( $total , $limitstart , $limit  );

		$db->setQuery( "SELECT DISTINCT a.*, p.posts, p.comments FROM #__joomblog_posts AS a " .
				" LEFT JOIN `#__joomblog_privacy` AS `p` ON `p`.`postid`=`a`.`id`  AND `p`.`isblog`=0".
				" LEFT JOIN #__joomblog_content_tags AS b ON a.id = b.contentid ".$query, $pageNav->limitstart, $pageNav->limit );
		$results	= $db->loadObjectList();
		$this->_format($results);
		$results = $this->getRowsByPrivacyFilter($results);
		$total=$this->totalEntries;
		
		$entrySession = array(); $_SESSION['entrySession'] = array();
		foreach($results as $entry){
				array_push($entrySession, $entry->id);
		}
		$_SESSION['entrySession'] = (!empty($entrySession)) ? $entrySession : array();		
		
		$results['pagination'] = '<div class="jb-pagenav">' . preg_replace('#(href)="([^:"]*)(?:")#','$1="$2'.($tag?'&tags='.$tag:'').($blogger?'&blogger='.$blogger:'').($keyword?'&keyword='.$keyword:'').'"',$pageNav->getPagesLinks()) . '</div>';
		return $results;
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
				if (!isset($post->blogtitle)) {unset($rows[$i]); $this->totalEntries--;}
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
	
		
	function _format(&$rows)
	{
		global $_JB_CONFIGURATION;
		$db			=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$this->_plugins->load();
		
		for($i =0; $i < count($rows); $i++){
			$row    =& $rows[$i];
			
			$row->text		= $row->introtext . $row->fulltext;
			$row->text		= JString::substr($row->text, 0, $this->_resultLength) . '...';
			$row->text		= strip_tags($row->text);
			$row->text		= str_replace(array('{jomcomment lock}','{!jomcomment}','{jomcomment}'),'',$row->text);
			$row->text		= preg_replace('#\s*<[^>]+>?\s*$#','',$row->text);
			$row->user		= jbGetAuthorName($row->created_by, $_JB_CONFIGURATION->get('useFullName'));
			$row->user		= $row->user;
			$row->link		= jbGetPermalinkURL($row->id);
			$row->jcategory		= '<a class="category" href="' . rtrim( JURI::base() , '/' ).JRoute::_('index.php?option=com_joomblog&task=tag&category=' . $row->catid ) . '">' . jbGetJoomlaCategoryName( $row->catid ) . '</a>';
			$row->userlink	= JRoute::_('index.php?option=com_joomblog&user=' . jbGetAuthorName($row->created_by));
			
			$db->setQuery(" SELECT b.blog_id, lb.title, p.posts, p.jsviewgroup " .
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
			$date			=& JFactory::getDate( $row->created );
			//$date->setOffSet( $_JB_CONFIGURATION->get('dateFormat') );
			$row->date		= $date->toFormat();
		}
	}
}
