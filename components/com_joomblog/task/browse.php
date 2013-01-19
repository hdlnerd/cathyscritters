<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: browse.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage browse.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

include_once(JB_COM_PATH . '/task/browse.base.php');

class JbblogBrowseTask extends JbblogBrowseBase
{
	
	function JbblogBrowseTask()
	{
		parent::JbblogBrowseBase();
		$this->toolbar = JB_TOOLBAR_HOME;
	}
	
}
