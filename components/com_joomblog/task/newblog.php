<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: write.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage write.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class JbblogNewBlogTask extends JbBlogBaseController{
	
	function _header(){
		JHTML::_('behavior.mootools');		
	}
	
	function _footer(){
		
	}
	
	function display()
	{
		global $_JB_CONFIGURATION, $Itemid;

		$mainframe	=& JFactory::getApplication();
		$my	=& JFactory::getUser();
		$doc =& JFactory::getDocument();
		
		if(!jbCanBlogCreate()){
			$mainframe->redirect($_SERVER['HTTP_REFERER'],JText::_('COM_JOOMBLOG_BLOG_ADMIN_NO_PERMISSIONS_TO_CREATE'));
			return;
		}
		
		$pathway =& $mainframe->getPathway();
				
		$tpl = new JoomblogTemplate();
	
		/*Privacy settings*/
		$privacy = jbGetPrivacyList(0, 'viewpostrules',1, 'view');
		$tpl->set('postprivacy', $privacy);
		
		$privacy = jbGetPrivacyList(0, 'viewcommrules',1,'post');
		$tpl->set('viewprivacy', $privacy);
		/**/
				
		$doc->addStyleSheet(rtrim( JURI::base() , '/' )."/components/com_joomblog/css/style.css");
		
		$html = $tpl->fetch(JB_TEMPLATE_PATH."/admin/newblog.html");
		
		$html = str_replace("src=\"icons", "src=\"" . rtrim( JURI::base() , '/' ) . "/components/com_joomblog/templates/admin/icons", $html);
	
		echo $html;
	}
}
