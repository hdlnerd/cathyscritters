<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: adminhome.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage adminhome.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'base.php' );

class JbblogAdminhomeTask extends JbblogBaseController
{
	function JbblogAdminhomeTask()
	{
		$this->toolbar	= JB_TOOLBAR_ACCOUNT;
	}

	function display()
	{
		global $_JB_CONFIGURATION;

		$mainframe	=& JFactory::getApplication();
		$document =& JFactory::getDocument();

		$my	=& JFactory::getUser();
		$db	=& JFactory::getDBO();
		$pathway =& $mainframe->getPathway();
		$limitstart	= JRequest::getVar( 'limitstart' , 0 , 'REQUEST' );
 		$limitstart = intval($limitstart);
 		
 		$search = $mainframe->getUserStateFromRequest("search{com_joomblog}{profileposts}", 'filter_search', '');
		$search = $db->getEscaped(JString::trim(JString::strtolower($search)));
 		
 		$search_query = "";
 		if ($search){
			$search_query = " AND ( 
				c.title LIKE '%$search%' 
				OR c.introtext LIKE '%$search%'
				OR c.fulltext LIKE '%$search%' 
				) ";
		}
 		
 		$deflimit = $_JB_CONFIGURATION->get('numEntry');
 		
		$limit = JRequest::getVar( 'limit' , $deflimit , 'REQUEST' );

		$secid = implode(',',jbGetCategoryArray($_JB_CONFIGURATION->get('managedSections')));
		
		$limit = $limitstart ? "LIMIT $limitstart, ".$deflimit : 'LIMIT '.$deflimit;
		
		$pathway->addItem(JText::_( 'COM_JOOMBLOG_ADMIN_MY_ENTRIES'),'');
		
		$query		= "SELECT c.* FROM #__joomblog_posts AS c WHERE c.created_by ='{$my->id}' "
					. $search_query
					. "AND c.catid IN ({$secid}) "
					. "ORDER BY c.created  "
					. $limit;

		$db->setQuery($query);
		$entries	= $db->loadObjectList();

		jimport( 'joomla.filesystem.file' );
		$jomcommentExists	= JFile::exists( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_jomcomment' . DS . 'config.jomcomment.php' );
		
		for($i = 0; $i < count($entries); $i++)
		{
			$entries[$i]->canEditState = $my->authorise('core.edit.state', 'com_joomblog') && $my->authorise('core.edit.state', 'com_joomblog.article.'.$entries[$i]->id);
			$entries[$i]->canDelete = $my->authorise('core.delete', 'com_joomblog') && $my->authorise('core.delete', 'com_joomblog.article.'.$entries[$i]->id);
			$entries[$i]->canEdit   = $my->authorise('core.edit', 'com_joomblog') && $my->authorise('core.edit', 'com_joomblog.article.'.$entries[$i]->id);
			
			
			$entries[$i]->title    = htmlspecialchars($entries[$i]->title);
			$entries[$i]->action = '[ edit | delete ]';
			
			
			if( $jomcommentExists && $_JB_CONFIGURATION->get('useComment')  && $_JB_CONFIGURATION->get('useJomComment'))
			{
				$query	= "SELECT COUNT(*) FROM #__jomcomment AS a WHERE a.contentid='" .$entries[$i]->id . "' AND a.option='com_joomblog'";
				$db->setQuery( $query );
				$count	= $db->loadResult();
				$entries[$i]->commentCount = $count;
			}elseif($_JB_CONFIGURATION->get('useComment')){
				$query	= "SELECT COUNT(*) FROM #__joomblog_comment AS a WHERE a.contentid='" .$entries[$i]->id . "' ";
				$db->setQuery( $query );
				$count	= $db->loadResult();
				$entries[$i]->commentCount = $count;
			}
			
			$entries[$i]->cats = $this->getMulticats($entries[$i]->id);
		}

		$config = array();

		$query	= "SELECT count(*) FROM #__joomblog_posts AS c, #__categories AS cat WHERE  c.catid = cat.id AND c.created_by='{$my->id}' ".$search_query." AND catid IN ({$secid}) ORDER BY created";
		$db->setQuery( $query );
		$total	= $db->loadResult();

		$pagination	= jbPagination( $total , $limitstart , $deflimit );

		echo $db->getErrorMsg();

		jbAddEditorHeader();

		$tpl = new JoomblogTemplate();
		$tpl->set('filter_search', $search);
		$tpl->set('postingRights', jbGetUserCanPost());
		$tpl->set('publishRights', jbGetUserCanPublish());
		$tpl->set('jbitemid', jbGetItemId());
		$tpl->set('pagination', $pagination->links );
		$tpl->set('jbentries', $entries);
		$tpl->set( 'limit' , $limit);
		$html = $tpl->fetch(JB_TEMPLATE_PATH."/admin/home.html");
		return $html;
	}
	
	public function getMulticats($id=0)
	{
		$db	=& JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('mc.cid, c.title ');
		$query->from('#__joomblog_multicats AS mc');
		$query->join('LEFT', '#__categories AS c ON c.id=mc.cid');
		$query->where('aid='.$id);
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		return $items;
	}
}


