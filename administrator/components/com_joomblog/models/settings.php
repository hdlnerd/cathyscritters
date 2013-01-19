<?php
/**
* JoomPortfolio component for Joomla 1.6
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Settings model.
 *
 */
class JoomBlogModelSettings extends JModelAdmin
{
	protected $context = 'com_joomblog';

	protected function allowEdit($data = array(), $key = 'id')
	{
		return JFactory::getUser()->authorise('core.admin', 'com_joomblog');
	}

	public function getTable($type = 'settings', $prefix = 'JoomblogTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	
	protected function loadFormData()
	{
			$data = $this->getItem();
		return $data;
	}
	
	public function getItem($pk = null)
	{
		if (!isset($this->item)) {
			$db	=& JFactory::getDBO();
			$db->setQuery("SELECT `extension_id`, `params` FROM #__extensions WHERE name='com_joomblog' AND element='com_joomblog'");
			$config = $db->loadObject();
			
			$configString = $config->params;
			$cfg = json_decode($configString);
			$item = null;
			foreach ($cfg as $key=>$val) {
				$item->$key = $val;
			}
			$item->id = $config->extension_id;
		}
		return $item;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		$app	= JFactory::getApplication();

		$form = $this->loadForm('com_joomblog.settings', 'settings', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}	
	
	
}