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
// For security reasons use build in content table class
// require_once(JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table'.DS.'extension.php');

class JoomBlogTableSettings extends JTable
{	
	function __construct(&$db) {
		parent::__construct('#__extensions', 'extension_id', $db);

		// $this->_trackAssets = true;
	}
	
	protected function _getAssetName()
	{
		return 'com_joomblog';
	}
	
	public function bind($array, $ignore = '')
        {
               
                if (isset($array['rules']) && is_array($array['rules'])) {
                        $rules = new JRules($array['rules']);
                        $this->setRules($rules);
                }
                return parent::bind($array, $ignore);
        }
	
	function store()
	{
		$options = array();

		$jform = JRequest::getVar('jform', array(), '', '', JREQUEST_ALLOWRAW);

		if (sizeof($jform))
		{
			foreach ( $jform as $key => $jv ) 
			{
				if ($key!='rules')
				{
					$options[$key]=$jv;
				}
			}
		}
		$extension_rules = json_encode($jform['rules']);
		if (!empty($extension_rules))
		{
			$db	=& JFactory::getDBO();
			$extension_rules = mysql_real_escape_string($extension_rules);
			$query	= "UPDATE `#__assets` SET `rules`='".$extension_rules."' WHERE `name`='com_joomblog' AND `title`='com_joomblog'";
			$db->setQuery( $query );
			$db->query();
		}	
		$this->params = json_encode($options);
		$this->extension_id = JRequest::getInt('id',0);
	 return parent::store();
	}
}
