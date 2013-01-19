<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: profile.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage profile.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'base.php' );

class JbblogProfileTask extends JbblogBaseController
{
	function JbblogProfileTask()
	{
		$this->toolbar	= JB_TOOLBAR_ACCOUNT;
	}

	function display()
	{
		global $_JB_CONFIGURATION;

		$mainframe	=& JFactory::getApplication();
		$document =& JFactory::getDocument();

		$my	=& JFactory::getUser();
		$db	=& JFactory::getDBO();
		
		$id = JRequest::getInt('id',0);
		$user =& JTable::getInstance( 'BlogUsers' , 'Table' );
		$user->load($id);
		
		if ($_JB_CONFIGURATION->get('avatar') == 'jomsocial'){
			$db->setQuery("SELECT avatar FROM #__community_users WHERE userid = ".$user->user_id);
			$avatar = $db->loadResult();
			$user->src			= (!$avatar) ? JUri::root().'components/com_joomblog/images/user.png' : JUri::root().$avatar;
		} else if ($_JB_CONFIGURATION->get('avatar') == 'juser'){
			$user->src			= (!$user->avatar) ? JUri::root().'components/com_joomblog/images/user.png' : JUri::root().'images/joomblog/avatar/'.$user->avatar;
		}else 
		{
			$user->src = JUri::root().'components/com_joomblog/images/user.png';
		}
		
		$pathway =& $mainframe->getPathway();

		$pathway->addItem(JText::_( 'COM_JOOMBLOG_USER_PROFILE_DETAILS'),'');

		jbAddEditorHeader();

		$tpl = new JoomblogTemplate();
		$tpl->set('user', array($user));
		$tpl->set('jbitemid', jbGetItemId());
		
		$path = $this->_getTemplateName( 'profile' );
		$content = $tpl->fetch( $path );
		return $content;
	}
}


