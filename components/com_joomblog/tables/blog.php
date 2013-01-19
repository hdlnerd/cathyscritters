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

class TableBlog extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__joomblog_list_blogs', 'id', $db);
	}
	
	protected function _getAssetName()
        {
                $k = $this->_tbl_key;
                return 'com_joomblog.blog.'.(int) $this->$k;
        }

        protected function _getAssetTitle()
        {
                return $this->title;
        }

        protected function _getAssetParentId($table = null, $id = null)
        {
               $assetId = null;
               $asset = JTable::getInstance('Asset');
               $asset->loadByName('com_joomblog');
                return $asset->id; 
        }
       


        public function bind($array, $ignore = '')
        {
                if (isset($array['params']) && is_array($array['params'])) {
                        $registry = new JRegistry();
                        $registry->loadArray($array['params']);
                        $array['params'] = (string)$registry;
                }

                if (isset($array['metadata']) && is_array($array['metadata'])) {
                        $registry = new JRegistry();
                        $registry->loadArray($array['metadata']);
                        $array['metadata'] = (string)$registry;
                }
               
                if (isset($array['rules']) && is_array($array['rules'])) {
                        $rules = new JRules($array['rules']);
                        $this->setRules($rules);
                }
                return parent::bind($array, $ignore);
        }

	public function delete($pk = null)
	{
		$db = $this->getDBO();

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__joomblog_blogs');
		$query->where('blog_id='.$pk);
		
		$db->setQuery($query);
		$ids = $db->loadObjectList();
		
		if ($ids) {
			$content = JTable::getInstance('Post', 'JoomBlogTable', array());
			foreach ($ids as $id) {
				$content->delete($id);
			}
		}

		return parent::delete($pk);
	}
	
	public function store($updateNulls = false)
	{
		if (parent::store($updateNulls)) 
		{
		if ($this->id)
			{
				$db	=& JFactory::getDBO();
				$posts = JRequest::getInt('viewcommrules');
				$comments = JRequest::getInt('viewpostrules');
				
				$jsviewposts = JRequest::getInt('jsgroups_view');
				$jspostposts = JRequest::getInt('jsgroups_post');
				
				$query	= "SELECT `id` FROM `#__joomblog_privacy` WHERE `isblog`=1 AND `postid`='".$this->id."' ";
					$db->setQuery( $query );
					$isset = $db->loadResult();
				if ($isset)
				{
					$query	= "UPDATE `#__joomblog_privacy` SET `jsviewgroup`='".$jsviewposts."',`jspostgroup`='".$jspostposts."', `posts`='".$posts."',`comments`='".$comments."' WHERE `isblog`=1 AND `postid`='".$this->id."' ";
					$db->setQuery( $query );
					$db->query();
				}else
				{
					$query	= "INSERT INTO `#__joomblog_privacy` (`id` ,`postid` ,`posts`,`comments`,`isblog`,`jsviewgroup`,`jspostgroup`) VALUES ( NULL , '".$this->id."', '".$posts."', '".$comments."','1','".$jsviewposts."','".$jspostposts."');";
					$db->setQuery( $query );
					$db->query();
				}		
			}
			return true;
		}
		return false;
	}
}
