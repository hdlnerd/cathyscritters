<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: tags.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage tags.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'browse.base.php' );

class JbblogTagsTask extends JbblogBrowseBase
{
	function JbblogTagsTask()
	{
		parent::JbblogBrowseBase();
		$this->toolbar = JB_TOOLBAR_HOME;
	}
	
	function setData()
	{
		$searchby = array(); 
		
		$category	= JRequest::getVar( 'category' , '' , 'REQUEST' );

		if( !empty( $category ) )
		{
			$category	= strval( urldecode( $category ) );
			$category	= str_replace("+", " ", $category);

			$searchby['category'] = $category;
		}
		
		$this->filters = $searchby;	
	}
	
}
