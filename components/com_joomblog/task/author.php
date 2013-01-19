<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: author.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage author.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'browse.base.php' );

class JbblogAuthorTask extends JbblogBrowseBase
{
	var $author = null;
	var $authorId = 0;
	
	function JbblogAuthorTask()
	{
		parent::JbblogBrowseBase();
		$this->toolbar = JB_TOOLBAR_BLOGGER;

		$authorId	= JRequest::getVar( 'user' , '' , 'REQUEST' );
				
		$authorId	= is_string($authorId) ? jbGetAuthorId(urldecode($authorId)) : intval($authorId); 
		
		$view = JRequest::getVar( 'view' , '' , 'GET' );
		if (isset($view) && $view == 'user')
			{
				$menu =& JSite::getMenu();
				$item   = $menu->getActive();
				$params   =& $menu->getParams($item->id);
				$authorId = intval($params->get('user'));
				$user = Jfactory::getUser($authorId);
				
				jbAddPathway(JText::_('COM_JOOMBLOG_ALL_BLOGS_TITLE'),JURI::base().JRoute::_('index.php?option=com_joomblog&user='.$blogger));
				jbAddPathway($title);
			}
		
		$this->authorId = $authorId;
		
		$this->author	=& JTable::getInstance('BlogUsers','Table');

		$this->author->load($authorId);
	}
	
	function _header()
	{
		$html = parent::_header();

		return $html;
	}
		
	function setData()
	{
		$searchby = array(); 
		$searchby['authorid'] = $this->authorId;

		$category	= JRequest::getVar( 'category' , '' , 'REQUEST' );
		if( !empty( $category ) )
		{
			$category = strval(urldecode( $category ));
			$category = str_replace("+", " ", $category);
			$searchby['category'] = $category;
		}
		
		$this->filters = $searchby;		
	}
}
