<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: delete.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage delete.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class JbblogDeleteTask extends JbblogBaseController
{
	function display()
	{
		$mainframe	=& JFactory::getApplication();
		$my	=& JFactory::getUser();		
		
		$id	= JRequest::getInt('id',0);
		
		$url = JRoute::_('index.php?option=com_joomblog&task=adminhome&Itemid=' . jbGetItemId() , false );

		if ($my->authorise('core.delete', 'com_joomblog.article.'.$id)) {
			$blog =& JTable::getInstance( 'Blogs' , 'Table' );
			$blog->load( $id );
					
			if( $blog->created_by != $my->id ){
				$mainframe->redirect( $url , JText::_('COM_JOOMBLOG_BLOG_ADMIN_NO_PERMISSIONS_TO_DELETE') );
				return;	
			}
			
			$blog->delete();
			
			$mainframe->redirect( $url ,  JText::_('COM_JOOMBLOG_BLOG_ENTRY_DELETED') );
		}else{
			$mainframe->redirect( $url , JText::_('COM_JOOMBLOG_BLOG_ADMIN_NO_PERMISSIONS_TO_DELETE') );
		}
	}
}