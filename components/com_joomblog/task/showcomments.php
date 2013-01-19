<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: showcomments.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage showcomments.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.html.pagination' );
class JbblogShowcommentsTask extends JbblogBaseController
{
	function JbblogShowcommentsTask()
	{
		$this->toolbar	= JB_TOOLBAR_BLOGGER;
	}
	
	function display()
	{
		global $_JB_CONFIGURATION;
		
		$mainframe =& JFactory::getApplication(); 
		
		// get List of content id by this blogger
		$cats = implode(',',jbGetCategoryArray($_JB_CONFIGURATION->get('managedSections')));
		$pathway =& $mainframe->getPathway();
		
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();

		$search = $mainframe->getUserStateFromRequest("search{com_joomblog}{profilecomments}", 'filter_search', '');
		$search = $db->getEscaped(JString::trim(JString::strtolower($search)));

		$search_query = "";
		if($search){
			$search_query = " AND comment LIKE '%$search%' ";
		}

		$db->setQuery( "SELECT `id` FROM #__joomblog_posts WHERE `created_by`='{$user->id}' AND catid IN ({$cats}) " );
		$contents = $db->loadObjectList();
		$sections = array();

		foreach($contents as $row){
			$sections[] = $row->id;
		}
			
		$limitComment = $_JB_CONFIGURATION->get('limitComment');
			
		// Make sure that there are indeed some article written by the author
		if(!empty($sections))
		{
			$limitstart	= JRequest::getVar( 'limitstart' , '' , 'GET' );
			$limit		= $limitstart ? "LIMIT $limitstart, ".$limitComment : 'LIMIT '.$limitComment;
			
			if($_JB_CONFIGURATION->get('useJosComment')){
				$db->setQuery("SELECT * FROM #__jomcomment WHERE (`option`='com_joomblog') AND `contentid` IN (". implode(',', $sections).") ".$search_query." ORDER BY `date` DESC $limit");
				$comments = $db->loadObjectList();
				
				// Add pagination
				$db->setQuery( "SELECT count(*) FROM #__jomcomment WHERE (`option`='com_joomblog') AND `contentid` IN (". implode(',', $sections).")   ".$search_query );
				$total		= $db->loadResult();
			}else{
				$query = "SELECT *, created AS date FROM #__joomblog_comment WHERE  contentid IN (". implode(',', $sections).") ".$search_query." ORDER BY created DESC $limit ";
				$db->setQuery( $query );
				$comments = $db->loadObjectList();
			 
				// Add pagination
				$query = "SELECT COUNT(*) FROM #__joomblog_comment WHERE  contentid IN (". implode(',', $sections).") ".$search_query;
				$db->setQuery( $query );
				$total = $db->loadResult();     
			}
				
			$pagination	= new JPagination( $total , $limitstart , $limit );
			$pagination	= $pagination->getPagesLinks();
		}
		else
		{
			$pagination = '';
			$comments = array();
		}
		
		for($i = 0; $i < count($comments); $i ++)
		{
			if( !isset($comments[$i]->referer) || $comments[$i]->referer == '')
			{
				$comments[$i]->referer	= jbGetPermalinkURL($comments[$i]->contentid) . '#comment' . $comments[$i]->id;
			}
		}
		
		jbAddEditorHeader();
		
		$pathway->addItem(JText::_('COM_JOOMBLOG_ADMIN_COMMENTS'),'');
		
		$tpl = new JoomblogTemplate();
		$tpl->set('filter_search', $search);
		$tpl->set('myitemid', jbGetItemId());
		$tpl->set('pagination', $pagination);
		$tpl->set('comments', $comments);
		$tpl->set('postingRights', jbGetUserCanPost());
		$html = $tpl->fetch(JB_TEMPLATE_PATH."/admin/comments.html");
		
		return $html;
	}
}