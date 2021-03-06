<?php
/**
* JoomBlog component for Joomla
* @version $Id: plugins.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage plugins.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

global $_JB_CONFIG;

class JBPluginsDB{

	var $table	= '#__joomblog_plugins';
	var $key	= '';
	var $db		= null;
	var $_plugins	= '';
	
	function JBPluginsDB()
	{
		$this->db	=& JFactory::getDBO();
		$this->_plugins	= '#__extensions';
	}
	
	function getPlugins($type = 'content', $published = true){
		
		if(is_array($type)){
			$type	= "AND a.folder IN ('".implode("','",$type)."') ";
		}else{
			if($type == 'content')
				$type	= "AND a.folder='content' ";
			else
				$type	= "AND a.folder='{$type}' ";	
		}

		if($published)
			$published	= "AND b.published='1' ";

		$strSQL	= "SELECT a.element, a.ordering "
				. "FROM {$this->_plugins} AS a, {$this->table} AS b "
				. "WHERE b.id=a.extension_id "
				. "AND a.enabled='1' "
				. $published
				. $type
				. 'AND a.type="plugin" '
				. "AND a.element !='jom_comment_bot' "
				. "ORDER BY a.ordering";

		$this->db->setQuery($strSQL);
		
		return $this->db->loadObjectList();
	}
	
	function getTotal($type = 'content')
	{
		if(is_array($type)){
			$type = "AND folder IN ('".implode("','",$type)."') ";
		}else{
			if($type == 'content')
				$type	= "AND folder='content' ";
			else
				$type	= "AND folder='{$type}' ";	
		}
	
		$query	= 'SELECT COUNT(*) FROM ' . $this->db->nameQuote( $this->_plugins ) . ' '
				. ' WHERE enabled=\'1\' '
				. 'AND type="plugin" '
				. $type
				. 'AND element != "jom_comment_bot"';
		$this->db->setQuery( $query );

		return $this->db->loadResult();
	}
	
	function get($limitstart , $limit, $type = 'content')
	{
		if(is_array($type)){
			$type	= "AND a.folder IN ('".implode("','",$type)."') ";
		}else{
			if($type == 'content')
				$type	= "AND a.folder='content' ";
			else
				$type	= "AND a.folder='{$type}' ";	
		}
		
		$limitQuery = '';
		if ($limit)
		{
			$limitQuery = "LIMIT {$limitstart}, {$limit}";
		}
		
		$strSQL	= "SELECT a.name, a.folder, a.element, b.id, b.published "
				. "FROM {$this->_plugins} AS a, {$this->table} AS b "
				. "WHERE b.id=a.extension_id "
				. "AND a.enabled='1' "
				. 'AND a.type="plugin" '
				. $type
				. "AND a.element!='jom_comment_bot' "
				. $limitQuery;
		$this->db->setQuery($strSQL);
		
		return $this->db->loadObjectList();
	}

	function initPlugins($type = 'content')
	{
		if(is_array($type)){
			$type	= "AND a.folder IN ('".implode("','",$type)."') ";
		}else{
			if($type == 'content')
				$type	= "AND a.folder='content' ";
			else
				$type	= "AND a.folder='{$type}' ";	
		}

		$strSQL	= "SELECT a.name, a.folder, a.extension_id AS id FROM {$this->_plugins} AS a "
				. "LEFT OUTER JOIN {$this->table} AS b "
				. "ON (a.extension_id=b.id) "
				. "WHERE b.id IS NULL "
				. $type
				. "AND a.enabled=1 "
				. "AND a.type='plugin' "
				. "AND a.element!='jom_comment_bot'";

		$this->db->setQuery($strSQL);
		$plugins	= $this->db->loadObjectList();
		
		if($plugins)
		{
			foreach($plugins as $plugin)
			{
				$strSQL	= "INSERT INTO {$this->table} SET id='{$plugin->id}'";
				$this->db->setQuery($strSQL);
				$this->db->query();
			}
		}
	}
}

class JBPlugins
{	

	var $_folder	= '';	
	var $_events	= null;

	function JBPlugins()
	{
		$this->_db	= new JBPluginsDB();

		$this->_folder	= JPATH_PLUGINS;
	}
		 	
	function load()
	{
		$mainframe	=& JFactory::getApplication();
		$plugins	= $this->_db->getPlugins();
		
		if($plugins)
		{
			foreach($plugins as $plugin)
			{

			    $plugin->folder 		= 'content';
			    $plugin->published 	= '1';
			    $plugin->params		= null; 
			
				JRequest::setVar( 'task' , 'view' , 'GET' );
				JRequest::setVar( 'option' , 'com_content' , 'GET' );
	
				JPluginHelper::importPlugin('content', $plugin->element);
				$plg	= JPluginHelper::getPlugin('content' , $plugin->element);
				
				
				$dispatcher	=& JDispatcher::getInstance();
				$plgObj		= 'plgContent' . ucfirst($plg->name);
				
				if( class_exists( $plgObj ) )
				{
					$instance = new $plgObj($dispatcher , (array) $plg);
					
					if( method_exists($instance , 'onContentPrepare') )
					{
						$this->register( 'onContentPrepare' , $plgObj , $plugin->params , $plugin->published );
					}
					
					if( method_exists($instance , 'onPrepareContent') )
					{
						$this->register( 'onPrepareContent' , $plgObj , $plugin->params , $plugin->published );
					}
					
					if( method_exists($instance , 'onBeforeDisplayContent') )
					{
						$this->register( 'onBeforeDisplayContent' , $plgObj , $plugin->params , $plugin->published );
					}

					if( method_exists($instance , 'onAfterDisplayContent') )
					{
						$this->register( 'onAfterDisplayContent' , $plgObj , $plugin->params , $plugin->published );
					}
				}
				else 
				{
					foreach($dispatcher->_observers as $observer)
					{
						if( is_array($observer) )
						{
							if($observer['event'] == 'onPrepareContent')
							{
								$this->register('onPrepareContent', $observer['handler'], $plugin->params, $plugin->published);
							}

							if($observer['event'] == 'onBeforeDisplayContent')
							{
								$this->register('onBeforeDisplayContent', $observer['handler'], $plugin->params, $plugin->published);
							}

							if($observer['event'] == 'onAfterDisplayContent')
							{
								$this->register('onAfterDisplayContent', $observer['handler'], $plugin->params, $plugin->published);
							}
						}
					}
				}
			}
		}
	}
	
	function register($event, $handler, $params, $published = 1)
	{		
		if(!isset($this->_events[$event])) 
			$this->_events[$event]	= array();
	
		if(!in_array($handler, $this->_events[$event]))
			$this->_events[$event][] = $handler;
	}
	
	function _callFunction($handler, &$row, &$params, $page, $event)
	{
		if( class_exists($handler) )
		{
			$dispatcher	=& JDispatcher::getInstance();

			$plugin =& JPluginHelper::getPlugin('content' , $handler);

			$instance = new $handler($dispatcher, (array)($plugin));

			
			return $instance->$event($row, $params , $page);
		}
		else if( function_exists($handler) )
		{
			return call_user_func_array($handler, array(&$row, &$params, $page));
		}
	}
	
	function trigger($event, &$row, &$params, $page = '0')
	{
		$result	= '';
		if(isset($this->_events[$event]))
		{
			foreach($this->_events[$event] as $handler)
			{
				$result	.= $this->_callFunction($handler, $row, $params, $page, $event);
			}
		}
 		return $result;
	}
		
	function init($type = 'content')
	{
		$this->_db->initPlugins($type);
	}
	
	function get($limitstart, $limit, $type = 'content')
	{
		return $this->_db->get($limitstart, $limit, $type);
	}
	
	function getTotal($type = 'content')
	{
		return $this->_db->getTotal($type);
	}
}