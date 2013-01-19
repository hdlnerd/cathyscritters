<?php
/**
* JoomBlog component for Joomla
* @version $Id: tags.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage tags.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class JBTags {
	
	var $_errMsg;
	var $tags; 
	var $insertId;
		
	function _prepNewtag($newtag)
	{
		
		$newtag = trim($newtag);

		return $newtag;
	}
	 	
	function strip( $tag )
	{
		$tag = preg_replace('/[`~!@#$%\^&*\(\)\+=\{\}\[\]|\\<">,\\/\^\*;:\?\'\\\]/', '', $tag);
		
		return $tag;
	}
	
	function getTagCloud(){
	}
		
	function add(&$newtag)
	{
		$addOk  = false;
		
		$db		=& JFactory::getDBO();
			
		$newtag	= $db->getEscaped( $newtag );
		
		$slug	= $this->_prepNewTag($newtag);
		$slug	= $this->strip($slug);
		
		if($newtag == '' || $slug == '')
			return false;

		$strSQL     = "SELECT COUNT(*) FROM `#__joomblog_tags` WHERE `name`='{$newtag}' ";
		$db->setQuery( $strSQL );
		$totalMatch = $db->loadResult();
	
		if($totalMatch == 0)
		{
		    $strSQL = "INSERT INTO `#__joomblog_tags` (`name`) VALUES ('{$newtag}')";
		    $db->setQuery($strSQL);
		    $db->query();
		    
		    $this->insertId = $db->insertId();
		    $addOk = true;
		}
		else
		{
			$strSQL	= "SELECT `id` FROM `#__joomblog_tags` WHERE `name`='{$newtag}'";
			$db->setQuery($strSQL);
			$this->insertId	= $db->loadResult();
		}
		
		return $addOk;
	}
	 	
	function getInsertId()
	{
		return $this->insertId;
	}
}
