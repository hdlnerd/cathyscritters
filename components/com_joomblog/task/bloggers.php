<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: bloggers.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage bloggers.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

include_once(JB_COM_PATH . '/task/base.php');
 
class JbblogBloggersTask extends JbblogBaseController{
	
	function JbblogBloggersTask(){
		$this->toolbar = JB_TOOLBAR_BLOGGER;
	}
	
	function display(){
		return "List of all current bloggers";
	}
	
}
