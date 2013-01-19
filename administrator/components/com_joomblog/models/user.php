<?php

/**
* JoomBlog component for Joomla 1.6 & 1.7
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

// For security reasons use build in user model class
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_users'.DS.'models'.DS.'user.php');
jimport('joomla.utilities.date');

class JoomBlogModelUser extends UsersModelUser
{
	public function getForm($data = array(), $loadData = true) 
	{
		JForm::addFormPath(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_users'.DS.'models'.DS.'forms');
		$form = $this->loadForm('com_users.user', 'user', array('control' => 'jform', 'load_data' => false));
		$file = JoomBlogHelper::getDataFile('custom.xml');
		$form->loadFile($file, 'custom');

		$form->bind($this->getItem());
		$form->setValue('password', null, '');

		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}

	public function getItem($pk = null) {
		$item = parent::getItem($pk);
		
		if ($item->id) {
			$db = &JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__joomblog_user');
			$query->where('user_id='.(int)$item->id);
			
			$db->setQuery($query);
			$result = $db->loadObject();
			
			if ($result) {
				$custom = new JObject();
				foreach ($result as $key=>$value) {
					if ($value) {
						$custom->set($key, $value);
					}
				}
				$item->set('custom', $custom);
			}
			
			if (isset($item->custom->birthday)) {
				$date = new JDate($item->custom->birthday);
				$item->custom->birthday = getdate($date->toUnix());
			}
		}
		
		return $item;
	}
	
	public function save($data) {
		if (parent::save($data)) {
			$pk = $this->getState('user.id');			
			$custom = $data['custom'];
			$custom['user_id'] = $pk;

			$date = new JDate($custom['birthday']['year'].'-'.$custom['birthday']['mon'].'-'.$custom['birthday']['mday']);
			$custom['birthday'] = $date->toSql();

			$custom['resetAvatar'] = JRequest::getVar('resetAvatar', false, 'post', 'bool');
			$custom['avatarFile'] = JRequest::getVar('avatarFile', array(), 'files', 'array');

			if (!empty($custom['avatarFile']['name'])) {
				$custom['avatar'] = $this->uploadAvatar($pk);
			}

			if($custom['resetAvatar']) {
				$custom['avatar'] = $this->resetAvatar($pk);
			}

			$table = JTable::getInstance('Profile', 'JoomBlogTable');
			$table->save($custom);

			return true;
		}

		return false;
	}

	function resetAvatar($cid)
	{
		$db = JFactory::getDBO();
		jimport('joomla.filesystem.file');
		jimport('joomla.utilities.utility');

		$db->setQuery("SELECT avatar FROM #__joomblog_user WHERE user_id=".$cid);
		$avatar = $db->loadResult();
		$fileName = JPATH_ROOT.DS.'images'.DS.'joomblog'.DS.'avatar'.DS.$avatar;
		$thumbName = JPATH_ROOT.DS.'images'.DS.'joomblog'.DS.'avatar'.DS.'thumb_'.$avatar;

		if (JFile::exists($fileName) and JFile::exists($thumbName)) {
			JFile::delete($fileName);
			JFile::delete($thumbName);
		}
		
		return '';
	}

	function uploadAvatar($cid)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.utilities.utility');	

		$app  = JFactory::getApplication();
		$file		= JRequest::getVar('avatarFile', '', 'files', 'array');
		$url		= 'index.php?option=com_joomblog&view=users';
		$params 	= JComponentHelper::getParams('com_joomblog');

		if( !isset($file['tmp_name']) || empty( $file['tmp_name'])) {
			return true;
		} else {
			$media_config = JComponentHelper::getParams('com_media');
			$uploadLimit	= (double)$params->get('maxFileSize', $media_config->get('upload_maxsize', 2));
			$uploadLimit	= ($uploadLimit*1024*1024);

			if(filesize($file['tmp_name']) > $uploadLimit && $uploadLimit != 0) {
				$app->enqueueMessage( JText::_('IMAGE FILE SIZE EXCEEDED') , 'error');
				$app->redirect($url);
			}

            if(!JBImageHelper::isValidType($file['type'])) {
				$app->enqueueMessage(JText::_('IMAGE FILE NOT SUPPORTED'), 'error');
				$app->redirect($url);
            }
			
			// Crash Apache?
			/*if(!JBImageHelper::isValid($file['tmp_name'])) {
				$app->enqueueMessage(JText::_('IMAGE_FILE_NOT_SUPPORTED'), 'error');
				$app->redirect($url);
			}*/

			$imageMaxWidth	= $params->get('avatarWidth', 200);

			// Get a hash for the file name.
			$fileName		= JUtility::getHash($file['tmp_name'].time());
			$hashFileName	= JString::substr($fileName,0,24);

			//@todo: configurable path for avatar storage?
			$storage			= JPATH_ROOT.DS.'images'.DS.'joomblog'.DS.'avatar';
			$storageImage		= $storage.DS.$hashFileName.JBImageHelper::getExtension($file['type']);
			$storageThumbnail	= $storage.DS.'thumb_'.$hashFileName.JBImageHelper::getExtension($file['type']);
			$image				= 'images/joomblog/avatar/'.$hashFileName.JBImageHelper::getExtension($file['type']);
			$thumbnail			= 'images/joomblog/avatar/'.'thumb_'.$hashFileName.JBImageHelper::getExtension($file['type']);

			// Only resize when the width exceeds the max.
			if(!JBImageHelper::resizeProportional($file['tmp_name'], $storageImage, $file['type'], $imageMaxWidth)) {
				$app->enqueueMessage(JText::sprintf('ERROR_MOVING_UPLOADED_FILE', $storageImage), 'error');
				$app->redirect($url);
			}

			// Generate thumbnail
			if(!JBImageHelper::createThumb($file['tmp_name'], $storageThumbnail, $file['type'])) {
				$app->enqueueMessage(JText::sprintf('ERROR_MOVING_UPLOADED_FILE' , $storageThumbnail), 'error');
				$app->redirect($url);
			}
			
			return $hashFileName.JBImageHelper::getExtension($file['type']);
		}
	}
}
