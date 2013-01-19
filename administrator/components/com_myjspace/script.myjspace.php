<?php
/**
* @version $Id: script.myjspace.php $
* @version		2.0.3 20/10/2012
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class com_myjspaceInstallerScript
{
	public function __construct($installer)
	{
		$this->installer = $installer;
	}
 
	public function postflight( $type, $parent ) {

		// Installation & migrations
		define('__ROOTINSTALL__', dirname(__FILE__));
		require_once(__ROOTINSTALL__.'/install.myjspace.php');
		jimport('joomla.access.rules');

		$db	= JFactory::getDBO();
		
		// J! >= 1.6 ACL
		$query = "SELECT COUNT(*) FROM `#__assets` WHERE `title` = 'com_myjspace' AND `name` = 'com_myjspace' AND `rules` LIKE '%user.%'";
		$db->setQuery($query);
		$db->query();
		$count = $db->loadResult();	
		if (!isset($count))
			return false;
			
		if ($count == 0) { // No Rules => Store the default ACL rules into the database
			$defaultRules = array(
				'core.admin' => array(),
				'core.manage' => array(),
				'user.config' => array('2' => 1),
				'user.delete' => array('2' => 1),
				'user.edit' => array('2' => 1),
				'user.myjspace' => array('1' => 1, '2' => 1),
				'user.search' => array('1' => 1, '2' => 1),
				'user.see' => array('1' => 1, '2' => 1),
				'user.pages' => array('1' => 1, '2' => 1)
			);

			if (version_compare(JVERSION, '2.5.6', 'ge'))
				$rules	= new JAccessRules($defaultRules);
			else
				$rules	= new JRules($defaultRules);

			$asset	= JTable::getInstance('asset');

			if (!$asset->loadByName('com_myjspace')) {
				$root = JTable::getInstance('asset');
				$root->loadByName('root.1');
				$asset->name = 'com_myjspace';
				$asset->title = 'com_myjspace';
				$asset->setLocation($root->id, 'last-child');
			}
			$asset->rules = (string)$rules;
			
	        if (! $asset->check() || ! $asset->store()) { 
                $this->setError($asset->getError());
				return false;
			}
		} 

		// Migration to Myjspace 2.0.0 from older version with ACL but not with 'user.pages'
		$query = "SELECT COUNT(`rules`) FROM `#__assets` WHERE `title` = 'com_myjspace' AND `name` = 'com_myjspace' AND `rules` LIKE '%user.pages%'";
		$db->setQuery($query);
		$db->query();
		$count = $db->loadResult();	
		if (!isset($count))
			return false;

		if ($count == 0) {
			echo "Added the new ACL rules (since 2.0.0) 'user.pages' (for pages list) to allow users to use it.<br/><br>";

			$asset	= JTable::getInstance('asset');
			if (!$asset->loadByName('com_myjspace')) {
				return false;
			}
			$new_rules = '"user.pages":{"1":1,"2":1}';
			$asset->rules = str_replace('}}', '},'.$new_rules.'}', $asset->rules);

	        if (! $asset->check() || ! $asset->store()) { 
                $this->setError($asset->getError());
				return false;
			}
		}

		return true;
	}
}

?>
