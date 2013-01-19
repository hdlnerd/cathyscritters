<?php

/**
* JoomBlog component for Joomla 1.6 & 1.7
* @package JoomBlog
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');

class JoomBlogTableComment extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__joomblog_comment', 'id', $db);
	}

	public function delete($pk = null)
	{
		$db = $this->getDBO();

		$db->setQuery('DELETE FROM #__joomblog_comment_votes WHERE commentid='.$pk);
		$db->query();

		return parent::delete($pk);
	}
}
