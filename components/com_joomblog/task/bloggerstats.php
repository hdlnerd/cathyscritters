<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: bloggerstats.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage bloggerstats.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class JbblogBloggerstatsTask extends JbblogBaseController
{
	function JbblogBloggerstatsTask()
	{
		$this->toolbar	= JB_TOOLBAR_BLOGGER;
	}
	
	function display()
	{
		$mainframe	=& JFactory::getApplication();
	
		$user	=& JFactory::getUser();
		$db		=& JFactory::getDBO();
		$pathway =& $mainframe->getPathway();
		
		$db->setQuery("SELECT `description` FROM #__joomblog_user WHERE `user_id` = '{$user->id}'");
		$desc	= $db->loadResult();
		
		if(!class_exists('JoomblogTemplate'))
		{
			require_once( JB_COM_PATH.DS.'template.php' );
		}
		
		$pathway->addItem(JText::_('COM_JOOMBLOG_ADMIN_MENU_STATS'),'');
		
		JbAddEditorHeader();
        
		$tpl	= new JoomblogTemplate();

		$tpl->set('num_entries', JbCountUserEntry($user->id));
				
		// Need to check if integrations with jomcomment is enabled.
		if(JbGetJomComment())
		{
		    $tpl->set('jomcomment',true);
		    $tpl->set('num_comments', JbCountUserComment($user->id));
		}
		
		$tpl->set('num_hits', JbCountUserHits($user->id));
		$tpl->set('tags', JbGetUsedTags($user->id));
		$tpl->set('Jbitemid', JbGetItemId());
		$tpl->set('description', $desc);
		$tpl->set('postingRights', JbGetUserCanPost());
		$html = $tpl->fetch(JB_TEMPLATE_PATH."/admin/blogger_stats.html");
		return $html;
	}
}