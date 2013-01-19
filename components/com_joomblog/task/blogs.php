<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @package JoomBlog
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'base.php' );
require_once( JB_LIBRARY_PATH . DS . 'avatar.php' );

jimport( 'joomla.html.pagination' );

class JbblogBlogsTask extends JbblogBaseController
{

	function JbblogBlogsTask(){
		parent::JbblogBaseController();
	
		$this->toolbar = JB_TOOLBAR_BLOGS;
	}

	function display($styleid = '', $wrapTag = 'div')
	{
		global $JBBLOG_LANG, $_JB_CONFIGURATION, $Itemid;

		$mainframe	=& JFactory::getApplication();
		$user	=& JFactory::getUser();
		
		jbAddPathway(JText::_('COM_JOOMBLOG_ALL_BLOGS_TITLE'));	
		jbAddPageTitle(JText::_('COM_JOOMBLOG_ALL_BLOGS_TITLE'));

		$total = 0;
		
		$limit		= JRequest::getVar( 'limit' , 10 , 'GET' );
		$limitstart	= JRequest::getVar( 'limitstart' , 0 , 'GET' );
		
		$limit		= intval($limit);
		$limitstart = intval($limitstart);

		//$sections	= $_JB_CONFIGURATION->get( 'managedSections' );
		$sections	= implode(',',jbGetCategoryArray($_JB_CONFIGURATION->get('managedSections')));
		
		$db			=& JFactory::getDBO();
		
		$query		= "SELECT distinct c.created_by from #__joomblog_posts c left outer join #__joomblog_user m on (m.user_id=c.created_by) WHERE m.user_id IS NULL and catid in ($sections)";
		$db->setQuery( $query );

		if( $db->loadObjectlist() )
		{
			$not_in_joomblog = $db->loadObjectlist();
			foreach($not_in_joomblog as $to_insert)
			{
				$db->setQuery("INSERT INTO #__joomblog_user SET user_id='" . $to_insert->created_by . "',description=''");
				$db->query();
			}
		}

		$db->setQuery("SELECT distinct(u.id) as user_id, `lb`.description,u.username, u.name, `lb`.title, `lb`.id, ub.avatar,p.posts, p.comments, p.jsviewgroup  "
			." FROM #__users u, #__joomblog_user as ub,#__joomblog_list_blogs AS `lb` " .
			" LEFT JOIN `#__joomblog_privacy` AS `p` ON `p`.`postid`=`lb`.`id` AND `p`.`isblog`=1 "
			." WHERE `lb`.user_id=u.id AND ub.user_id = u.id AND `lb`.published = 1 AND `lb`.approved =1 limit $limitstart,$limit");
		$blogs = $db->loadObjectList();
		
		//echo 'SMT DEBUG: <pre>'; print_R(str_replace('#__', $db->getprefix(),$db->getQuery())); echo '</pre>';
		
		$db->setQuery("SELECT count(distinct(u.id)) "
			." FROM #__users u, #__joomblog_list_blogs mu, #__joomblog_posts c "
			." WHERE mu.user_id=u.id");
			
		$total = $db->loadResult();
		$bloggerHTML	= '';
		
		$tmpl = JRequest::getVar('tmpl');
		if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
		
		if ($blogs)
		{
			foreach ($blogs as $blog)
			{		
				
				switch ( $blog->posts ) 
				{
					case 0:	break;
					case 1:	
						if (!$user->id) 
						{
							$total--;
							unset($blog);
						}
					break;	
					case 2:	
						if (!$user->id) 
						{
							$total--;
							unset($blog);
						}else
						{
							if (!$this->isFriends($user->id, $blog->user_id) && $user->id!=$blog->user_id)
							{
								$total--;
								unset($blog);
							}	
						}
					break;
					case 3:	
						if (!$user->id) 
						{
							$total--;unset($blog);
						}else
						{
							if ($user->id!=$blog->user_id)
							{
								$total--;unset($blog);
							}	
						}						
					break;
					case 4:	
						if (!$user->id) 
						{
							$total--;unset($blog);
						}else
						{
							if (!jbInJSgroup($user->id, $blog->jsviewgroup))
							{
								$total--;unset($blog);
							}
						}
					break;
				}					
				if (!isset($blog)) continue;				
				$blog->title = $blog->title?$blog->title:$blog->username."'s blog";
								
				$blog->numEntries	= jbCountUserEntry($blog->user_id, "1");
				$blog->numHits		= jbCountUserHits($blog->user_id);
				$blog->blogLink		= JRoute::_("index.php?option=com_joomblog&blogid=" . $blog->id . "&view=blogger&Itemid=".$Itemid.$tmpl);
				
				if($_JB_CONFIGURATION->get('avatar') == 'jomsocial'){
					
					$db->setQuery("SELECT thumb FROM #__community_users WHERE userid = ".$blog->user_id);
					$thumb = $db->loadResult();
					$blog->src			= (!$thumb) ? JUri::root().'components/com_joomblog/images/user_thumb.png' : JUri::root().$thumb;
	
				} else {
					$blog->src			= (!$blog->avatar) ? JUri::root().'components/com_joomblog/images/user_thumb.png' : JUri::root().'images/joomblog/avatar/thumb_'.$blog->avatar;
				}
				$blog->authorLink	= JRoute::_("index.php?option=com_joomblog&task=profile&id=" . $blog->user_id . "&Itemid=".$Itemid.$tmpl);
				//$blog->description	= strip_tags($blog->description, '<u> <i> <b>');
				$blog->description	= $blog->description;
				$db->setQuery("SELECT datediff(curdate(),MAX(created)) from #__joomblog_posts WHERE sectionid IN ($sections) AND created_by='" . $blog->user_id . "' and state='1' and publish_up < now()");
				$lastUpdated		= $db->loadResult();

				if(!empty( $lastUpdated ) )
				{
					if( $lastUpdated > 0 )
					{
						$lastUpdated	= ( $lastUpdated == 1 ? JText::_('COM_JOOMBLOG_BLOG_UPDATED_YESTERDAY') : JText::sprintf( 'COM_JOOMBLOG_BLOG_UPDATED_DAYS_AGO' , $lastUpdated ) );
					}
					else
					{
						$lastUpdated	= JText::_('COM_JOOMBLOG_BLOG_UPDATED_TODAY');
					}

					$blog->last_updated = $lastUpdated;
				}
				else
				{
					$blog->last_updated	= JText::_('COM_JOOMBLOG_BLOG_WAS_NEVER_UPDATED');
				}

				$categories		= jbGetUserTags($blog->user_id);
				
				$tmpArray		= array();
				$blogs[0]		= $blog;
				
				$template		= new JoomblogTemplate();
				$template->set( 'avatarWidth' , $_JB_CONFIGURATION->get('avatarWidth' ) );
				$template->set( 'avatarLeftPadding' , ($_JB_CONFIGURATION->get('avatarWidth' ) + 30 ) );
				$template->set( 'useFullName' , $_JB_CONFIGURATION->get('useFullName') );
				$template->set( 'blogs' , $blogs );
				$template->set( 'categories' , $categories );
				$template->set( 'Itemid' , $Itemid );
				
				$bloggerHTML	.= $template->fetch( $this->_getTemplateName('blogs_blogger') );
				
				unset( $template );
			}
		}

		$buttonHTML='';
		/*
		if (jbCanBlogCreate())
		{
			$createlink = JRoute::_('index.php?option=com_joomblog&task=newblog');
			$buttonHTML='<div class="joomBlog-crlnk"><a class="blogcreatelink" href="'.$createlink.'" title="">'.JText::_('COM_JOOMBLOG_CREATEBLOG_BUTTON_TEXT').'</a></div>';
		}
		*/
		$template	= new JoomblogTemplate();
		$template->set( 'buttonHTML' , $buttonHTML );
		$template->set( 'bloggerHTML' , $bloggerHTML );
		$content	= $template->fetch($this->_getTemplateName('blogs'));
		
		$queryString = $_SERVER['QUERY_STRING'];
		$queryString = preg_replace("/\&limit=[0-9]*/i", "", $queryString);
		$queryString = preg_replace("/\&limitstart=[0-9]*/i", "", $queryString);
		$pageNavLink = $_SERVER['REQUEST_URI'];
		$pageNavLink = preg_replace("/\&limit=[0-9]*/i", "", $pageNavLink);
		$pageNavLink = preg_replace("/\&limitstart=[0-9]*/i", "", $pageNavLink);

		$pageNav		= new JPagination($total, $limitstart, $limit);		
		$content .= '<div class="my-pagenav">' . $pageNav->getPagesLinks('index.php?' . $queryString) . '</div>';

		return $content;
	}
	protected function isFriends($id1=0,$id2=0)
	{
		$db	=& JFactory::getDBO();
		$db->setQuery(	" SELECT `connection_id` FROM `#__community_connection` " .
						" WHERE connect_from=".(int)$id1." AND connect_to=".(int)$id2." AND `status`=1 ");
		$frindic = $db->loadResult();
		if ($frindic) return true; else return false;				
	}
}