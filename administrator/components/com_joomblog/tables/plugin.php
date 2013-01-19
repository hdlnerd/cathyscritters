<?php

/**
* JoomBlog component for Joomla 1.6 & 1.7
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');

class JoomBlogTablePlugin extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__joomblog_plugins', 'id', $db);
	}
}
