<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: usertag.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage usertag.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

include_once(JB_COM_PATH . '/task/base.php');
 
class JbblogUsertagTask extends JbblogBaseController{
	function JbblogUsertagTask(){
	}
	
	function display(){
		return "<h1>List all entry with the given tag from the given user</h1>";
	}
}
