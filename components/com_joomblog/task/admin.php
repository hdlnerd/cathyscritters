<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: admin.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage admin.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once(JB_COM_PATH.DS.'task'.DS.'browse.base.php');

class JbblogAdminTask extends JbblogBrowseBase
{		
	function JbblogAdminTask()
	{		
		$this->toolbar	= JB_TOOLBAR_HOME;	
	}
	
	function display()
	{
		$db =& JFactory::getDBO();
		$mainframe =& JFactory::getApplication(); 
		
		$action		= JRequest::getVar( 'operation' , '' , 'GET' );
		$sid		= JRequest::getVar( 'sid','','GET' );
		
		$strSQL	= "SELECT * FROM #__joomblog_admin WHERE `sid`='{$sid}'";
		$db->setQuery( $strSQL );
		$admin	= $db->loadObject();
		
		$cid = $admin->cid;
		$type	= $admin->type;
		
		if( $cid )
		{
			if(method_exists( $this , $action))
				$content	= $this->$action($cid,$type);
			else
				$content	= $this->_error();
		}
		else
		{
			$content	= $this->_error();
		}		
		
		$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&view=default'),$content);
	}
	
	function publish($cid, $type)
	{
		$db		=& JFactory::getDBO();
		
		if(!$type){
      $strSQL	= "UPDATE #__joomblog_posts SET `state`='1' WHERE `id`='{$cid}'";
      $db->setQuery( $strSQL );
      $db->query();
      
      $sid		= JRequest::getVar( 'sid','','GET' );
      	$strSQL	= "DELETE FROM #__joomblog_admin WHERE `sid`='{$sid}'";
		$db->setQuery($strSQL);
		$db->query();
      
      return 'Blog entry published.';
		}elseif($type == 1){
      $strSQL	= "UPDATE #__joomblog_comment SET `published`='1' WHERE `id`='{$cid}'";
      $db->setQuery( $strSQL );
      $db->query();
      
      return 'Comment published.';
		}
	}
	
	function unpublish($cid, $type)
	{
		$db		=& JFactory::getDBO();
		
		if(!$type){
      $strSQL	= "UPDATE #__joomblog_posts SET `state`='0' WHERE `id`='{$cid}'";
      $db->setQuery( $strSQL );
      $db->query();
      
      $sid		= JRequest::getVar( 'sid','','GET' );
      	$strSQL	= "DELETE FROM #__joomblog_admin WHERE `sid`='{$sid}'";
		$db->setQuery($strSQL);
		$db->query();
      
      return 'Blog entry unpublished.';
		}elseif($type == 1){
      $strSQL	= "UPDATE #__joomblog_comment SET `published`='0' WHERE `id`='{$cid}'";
      $db->setQuery( $strSQL );
      $db->query();
      
      return 'Comment unpublished.';
		}
	}

	function remove($cid, $type)
	{
		$db		=& JFactory::getDBO();
		
		if(!$type){
      $strSQL	= "DELETE FROM #__joomblog_posts WHERE `id`='{$cid}'";
      $db->setQuery( $strSQL );
      $db->query();
      
      $sid		= JRequest::getVar( 'sid','','GET' );
      	$strSQL	= "DELETE FROM #__joomblog_admin WHERE `sid`='{$sid}'";
		$db->setQuery($strSQL);
		$db->query();
      
      return 'Blog entry removed.';
		}elseif($type == 1){
      $strSQL	= "DELETE FROM #__joomblog_comment WHERE `id`='{$cid}' ";
      $db->setQuery( $strSQL );
      $db->query();
      
      return 'Comment removed.';
		}
	}
	
	function _error()
	{
		return "<p><b>On this blog post has made ​​the operation. Link is invalid.</b></p>";
	}

}