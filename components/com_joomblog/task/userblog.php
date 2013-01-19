<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: userblog.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage userblog.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'author.php' );

class JbblogUserblogTask extends JbblogAuthorTask
{
	
	function JbblogUserblogTask()
	{
		parent::JbblogBrowseBase();
		
		$this->toolbar = JB_TOOLBAR_BLOGGER;
		
		$my				=& JFactory::getUser();
		$authorId = $author = $my->id; 
		
		$this->authorId = $authorId;
		
		$this->author =& JTable::getInstance( 'BlogUsers' , 'Table' );
		$this->author->load($authorId);
	}
	
	function display()
	{
		$my		=& JFactory::getUser();
		
		if( $my->id == 0 )
		{
			echo '<div id="fp-content">';
			echo JText::_('COM_JOOMBLOG_LOGIN_TO_VIEW_BLOG');
			echo '</div>';
		}
		else
		{
			$content	= parent::display();
			jbAddPageTitle( $my->name . "'s Blog");
			return $content;
		}
	}
}
