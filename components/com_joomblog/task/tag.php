<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: tag.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage tag.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'browse.base.php' );

class JbblogTagTask extends JbblogBrowseBase
{
	var $category;
	
	function JbblogTagTask()
	{
		parent::JbblogBrowseBase();
		$this->toolbar = JB_TOOLBAR_HOME;
	}
	
	
	function _header()
	{
		echo parent::_header();
		
		$category	= JRequest::getVar( 'tag' , '' , 'REQUEST' );
		
		if(!$category){
			$category = JRequest::getVar( 'category' , '' , 'REQUEST' );
		}
		
				
		if(is_numeric($category)){
			$this->category = intval(urldecode( $category ));
	
			jbAddPathway( JText::_('COM_JOOMBLOG_SHOW_CATEGORIES_TITLE') , JRoute::_('index.php?option=com_joomblog&task=categorylist&Itemid='.jbGetItemId()));
			
			jbAddPageTitle(htmlspecialchars(jbGetJoomlaCategoryName($this->category)));
			
			jbAddPathway(htmlspecialchars(jbGetJoomlaCategoryName($this->category)));
		}else{
			$this->category = strval(urldecode( $category ) );
			$this->category = str_replace("+", " ", $category);

			jbAddPathway( JText::_('COM_JOOMBLOG_SHOW_TAGS_TITLE') , JRoute::_('index.php?option=com_joomblog&task=tagslist&Itemid='.jbGetItemId()));
			
			jbAddPageTitle(htmlspecialchars(jbGetTagName($this->category)));

			jbAddPathway(htmlspecialchars(jbGetTagName($this->category)));
		}
	}
	
	function setData()
	{
		$searchby = array();
    
		$category	= JRequest::getVar( 'category' , '' , 'REQUEST' );
		
		
		$view = JRequest::getVar( 'view' , '' , 'GET' );
		if (isset($view) && $view == 'category')
		{
			$menu =& JSite::getMenu();
			$item   = $menu->getActive();
			$params   =& $menu->getParams($item->id);
			$category = intval($params->get('category'));
		}
		
		if( !empty( $category ) && is_numeric($category)){
			$searchby['jcategory'] = $category;
		}else{
			$searchby['category'] = $this->category;
			$searchby['category'] = str_replace('-and-', '&', $searchby['category']);
			$searchby['category'] = str_replace(' and ', '&', $searchby['category']);
		}
		
		$this->filters = $searchby;	
	}
	
}
