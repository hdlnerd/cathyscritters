<?php

/**
* JoomBlog component for Joomla 1.6 & 1.7
* @package JoomBlog
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');


require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'tables'.DS.'content.php');

class JoomBlogTablePost extends JTableJoomblogPosts
{
	protected $tags;
	protected $blog_id;

	public function delete($pk = null)
	{
		if (parent::delete($pk)) {
			$db = JFactory::getDBO();

			$query = $db->getQuery(true);
			$query->delete();
			$query->from('#__joomblog_blogs');
			$query->where('content_id='.(int)$pk);
			$db->setQuery($query);
			$db->query();

			$query = $db->getQuery(true);
			$query->delete();
			$query->from('#__joomblog_content_tags');
			$query->where('contentid='.(int)$pk);
			$db->setQuery($query);
			$db->query();
			
			$query = $db->getQuery(true);
			$query->delete();
			$query->from('#__joomblog_multicats');
			$query->where('aid='.(int)$pk);
			$db->setQuery($query);
			$db->query();
			
			return true;
		}
		
		return false;
	}
	
	public function store($updateNulls = false)
	{
		if ($this->created == '')
		{
			$date = JFactory::getDate();
			jimport('joomla.version');
			$version = new JVersion();
			$joomla_version = $version->getShortVersion(); 
			if ($joomla_version <= 1.7) 
				{
					$this->created = $date->toMySQL();
				}
				else $this->created = $date->toSql();
		}
		$allcategs = array();
		$isNew = false;
		if (empty($this->id)) {
			$isNew = true;
		}
		if ($this->catid)
		{
			if (sizeof($this->catid))
			{
				$allcategs = $this->catid;
				if (is_array($this->catid)) $this->catid = $allcategs[0];
			}
		}
		if (parent::store($updateNulls)) {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			if ($isNew) {
				$query->insert('#__joomblog_blogs');
			} else {
				$query->update('#__joomblog_blogs');
				$query->where('`content_id`='.(int)$this->id);
			}

			$query->set('content_id='.(int)$this->id);
			$query->set('blog_id='.(int)$this->blog_id);

			$db->setQuery($query);
			$db->query();
			
			/*** MULTI CATS ***/
				if (sizeof($allcategs))
				{
					$query = $db->getQuery(true);
					$query->delete();
					$query->from('#__joomblog_multicats');
					$query->where('aid='.(int)$this->id);
					$db->setQuery($query);
					$db->query();
					
					foreach ( $allcategs as $alc ) 
					{
						$query = $db->getQuery(true);
						$query->insert('#__joomblog_multicats');
						$query->set('aid='.(int)$this->id);
						$query->set('cid='.(int)$alc);
						$db->setQuery($query);
						$db->query();
					}					
				}
								
			/*****************/
			
			if (isset($this->tags)) {
			
				$this->tags = explode(',', $this->tags);
				array_walk($this->tags, create_function('&$val', '$val = trim($val);'));
				
				$query = $db->getQuery(true);
				$query->delete();
				$query->from('#__joomblog_content_tags');
				$query->where('contentid='.(int)$this->id);
				$db->setQuery($query);
				$db->query();

				$query = $db->getQuery(true);
				$query->select('name');
				$query->from('#__joomblog_tags');
				$db->setQuery($query);
				$list = $db->loadResultArray();


				foreach ($this->tags as $tag) {
					$ttable = JTable::getInstance('Tag', 'JoomBlogTable');
					if (!in_array($tag, $list)) {
				
						$tag = trim ($tag);
						$data = array(
							'name' => $tag,
							'default' => null,
							'slug' => null
						);
						if ($tag) $ttable->save($data);
					} else {
						$ttable->loadByName($tag);
					}
					 if ($ttable->id)
					 {
						$query = $db->getQuery(true);
						$query->insert('#__joomblog_content_tags');
						$query->set('contentid='.(int)$this->id);
						$query->set('tag='.(int)$ttable->id);
						$db->setQuery($query);
						$db->query();
					 }
				}
				
			}
			
		if ($this->id)
		{
			$db	=& JFactory::getDBO();
			$jform = JRequest::getVar('jform');
			$posts = $jform['viewpostrules'];
			$comments = $jform['viewcommrules'];
			
			$query	= "SELECT `id` FROM `#__joomblog_privacy` WHERE `isblog`=0 AND `postid`='".$this->id."' ";
				$db->setQuery( $query );
				$isset = $db->loadResult();
			if ($isset)
			{
				$query	= "UPDATE `#__joomblog_privacy` SET `posts`='".$posts."',`comments`='".$comments."' WHERE `isblog`=0 AND `postid`='".$this->id."' ";
				$db->setQuery( $query );
				$db->query();
			}else
			{
				$query	= "INSERT INTO `#__joomblog_privacy` (`id` ,`postid` ,`posts`,`comments`,`isblog`) VALUES ( NULL , '".$this->id."', '".$posts."', '".$comments."','0');";
				$db->setQuery( $query );
				$db->query();
			}	
		}	
			return true;
		}
		
		return false;
	}

	public function load($keys = null, $reset = true) {
		if (parent::load($keys, $reset)) {
			$db = $this->getDbo();

			$query = $db->getQuery(true);
			$query->select('blog_id');
			$query->from('#__joomblog_blogs');
			$query->where('content_id='.(int)$this->id);
			$db->setQuery($query);
			$this->blog_id = $db->loadResult();

			$query = $db->getQuery(true);
			$query->select('GROUP_CONCAT(t.name) AS tags');
			$query->from('#__joomblog_content_tags AS ct');
			$query->join('left', '#__joomblog_tags AS t ON t.id=ct.tag');
			$query->where('contentid='.(int)$this->id);
			$db->setQuery($query);
			$this->tags = $db->loadResult();
		
			return true;
		}
		
		return false;
	}

	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		$tmp = $this->$k;
		return 'com_joomblog.article.'.(int)$this->$k;
	}

	protected function _getAssetTitle()
	{
		return $this->title;
	}

	protected function _getAssetParentId($table = null, $id = null)
	{
		$assetId = null;
		$db = $this->getDbo();
		if ($this->catid) {
			$query	= $db->getQuery(true);
			$query->select('asset_id');
			$query->from('#__categories');
			$query->where('id = '.(int)$this->catid);

			$this->_db->setQuery($query);
			if ($result = $this->_db->loadResult()) {
				$assetId = (int)$result;
			}
		}
		if ($assetId) {
			return $assetId;
		} else {
			return parent::_getAssetParentId($table, $id);
		}
	}
}
