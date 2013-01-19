<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: archive.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage archive.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'base.php' );

jimport( 'joomla.html.pagination' );

class JbblogArchiveTask extends JbblogBaseController{
	
	var $_calendar = null; 
	var $_calendar_date = 'now';
	
	function JbblogArchiveTask(){
		$this->toolbar = JB_TOOLBAR_BLOGGER;
	}
	
	function display(){
	
		global $_JB_CONFIGURATION, $Itemid;
	
		$mainframe	=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$document	= & JFactory::getDocument();
		$action = JRequest::getVar('action', '');
		$date = (JRequest::getInt('date')) ? JRequest::getInt('date') : time();
		
		
		JbblogArchiveTask::setCalendarDate($date);
		$calendar		=& JbblogArchiveTask::getCalendar();
		
		jimport('joomla.utilities.date');
		$jdate = new JDate(strtotime(JbblogArchiveTask::getCalendarDate()));
		$calendar_header = $jdate->toFormat(JText::_('%B %Y'));

		//$calendar_header = strftime('%B %Y', strtotime(JbblogArchiveTask::getCalendarDate()));
		
		$cal = array();
		$cal[0] = $calendar;
		
		$template		= new JoomblogTemplate();
		$template->set( 'Itemid' , $Itemid );
		$template->set( 'calendar' , $cal );
		$template->set( 'calendar_header' , $calendar_header );
		$template->set( 'prev_month' , strtotime(JbblogArchiveTask::getCalendarDate(). ' -1 month' ) );
		$template->set( 'next_month' , strtotime(JbblogArchiveTask::getCalendarDate(). ' +1 month' ) );
				
		$content = $template->fetch( $this->_getTemplateName('calendar') );
				
		if ( $action != 'refresh_calendar'){
			$document->addStyleSheet(JURI::root()."components/com_joomblog/templates/default/css/calendar.css");		
			$content = '<script language="javascript" src="'.JURI::root().'components/com_joomblog/templates/default/js/jquery.qtip.min.js"></script><div id="jblog-section">'.JText::_('COM_JOOMBLOG_ARCHIVES').'</div><div id="mt_calendar">'.$content.'</div>';
			return $content;
		} else {
			echo $content;
			die;
		}		
	}
	
	function getCalendarDate() {
		return $this->_calendar_date;
	}
	
	function setCalendarDate($date){
		$this->_calendar_date = date('Y-m-d H:i:s', $date);
	}
	
	function &getCalendar() {
		if (!$this->_calendar) {
			$this->_createCalendar();
		}
		
		return $this->_calendar; 
	}
	
	function _getEntries()
	{
		global $_JB_CONFIGURATION, $Itemid;
	
		$mainframe	=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$sections	= implode(',',jbGetCategoryArray($_JB_CONFIGURATION->get('managedSections')));
				
		$query = " SELECT DISTINCT a.created, a.id, p.posts, p.comments, a.created_by  FROM #__joomblog_posts as a " .
				" LEFT JOIN `#__joomblog_privacy` AS `p` ON `p`.`postid`=`a`.`id` AND `p`.`isblog`=0 ".
				" WHERE a.state=1 AND a.catid in ($sections) ORDER BY a.created DESC";
		$db->setQuery($query);
		$dates = $db->loadObjectList();
		$this->_getBlogs($dates);
		$dates = $this->getRowsByPrivacyFilter($dates);
		
		$temp_dates = array();
		if (!empty($dates)){
			foreach($dates as $date){		
				if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $date->created, $match))
				{
					$temp_dates[$date->id] = $match[0];
				}			
			}	
		}
		unset($dates);
		$dates = $temp_dates;
		
		return $dates;
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
				if (!isset($post->blogtitle)) {unset($rows[$i]);continue;}
				switch ( $post->posts ) 
				{
					case 0:	break;
					case 1:	
						if (!$user->id) 
						{
							$rows[$i]=null;
							unset($rows[$i]);
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
							if ($user->id!=$post->created_by)
							{
								unset($rows[$i]);
							}	
						}						
					break;
					case 4:	
							unset($rows[$i]);						
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
	
	function _getInfo($ids, $date)
	{
		global $_JB_CONFIGURATION, $Itemid;
	
		$mainframe	=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$sections	= implode(',',jbGetCategoryArray($_JB_CONFIGURATION->get('managedSections')));
		
		$html = "<div class='jbtip-in'><div class='jbtip-title'><small class='jbtip-title-small'>".$date."</small></div>";
		$search = (!empty($ids)) ? " AND a.id IN(".implode(',', $ids).")" : "";
		$db->setQuery("SELECT DISTINCT a.title, a.id, a.created FROM #__joomblog_posts as a WHERE a.state=1 AND a.catid in ($sections) $search ORDER BY a.created DESC");
		$info = $db->loadObjectList();
		
		if(!empty($info))
		{
			$html .= "<small class='amount-posts'>".JText::_('COM_JOOMBLOG_THEREARE').count($info).JText::_('COM_JOOMBLOG_BLOG_POST_AVAILABLE_ON_THIS_DATE')."</small><ul class='jbtip-entries-list ulrest'>";
			foreach($info as $i)
			{
				$html .= "<li><a href='".JRoute::_('index.php?option=com_joomblog&show='.$i->id.'&Itemid='.$Itemid)."'>".htmlspecialchars($i->title)."</a></li>";
			}
			$html .= "</ul>";
		}
		
		$html .= "</div>";
		return $html;
	}
	
	function _createCalendar() {
		
		// calendar
		//setlocale (LC_ALL, 'en_GB');
		
		$dates = $this->_getEntries();
	
		$date = $this->_calendar_date;
		$sundayfirst = false;
				
		$date = strtotime($date);
		
		$month = date('m', $date);
		$day   = date('d', $date);
		$year  = date('Y', $date);
		
		$wday = JDDayOfWeek(GregorianToJD($month, 1, $year), 0);
		
		if ($wday == 0) 
			$wday = 7;
		$n = - ($wday - ($sundayfirst? 1: 2));
		if ($sundayfirst && $n == -6)
			$n = 1;
			
		$cal = array();
		$nsat = ($sundayfirst? 6: 5);
		$nsun = ($sundayfirst? 0: 6);
	
		for ($y=0; $y < 6; $y++) {
			$row = array();
			$notEmpty = false;
			for ($x=0; $x < 7; $x++, $n++) {			
					
				if (checkdate($month, $n, $year)) {	
						
					$day = $n;
					$day = (intval($day) < 10) ? "0".$day: $day;
										
					$day_class = '';
					$day_tip = '';
					$day_style = '';
					$day_html = $day;	
					
					if (in_array($year.'-'.$month.'-'.$day, $dates)){
						$ids = array_keys($dates, $year.'-'.$month.'-'.$day);
						$timestamp = strtotime($year.'-'.$month.'-'.$day);
						$ndate = date("d F Y", $timestamp);
						$info = $this->_getInfo($ids, $ndate);
						$day_class = !$day_class? 'day yellow qtip': $day_class;
						$day_tip .= $info;
					}
					
					$day_class = !$day_class? 'day': $day_class;
					
					$row[] = array('html' => $day_html,
									'class' => $day_class,
									'style' => $day_style,
									'tip' => ' title="'.$day_tip.'" '
									);
					
					$notEmpty = true;
				} else {
					$row[] =  array('html' => '',
									'class' => 'day_empty',
									'style' => '',
									'tip' => ''
									);
					
				}
			}
			if (!$notEmpty) 
				break;
			$cal[] = $row;
		}
		$this->_calendar = $cal;
		return;
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
}