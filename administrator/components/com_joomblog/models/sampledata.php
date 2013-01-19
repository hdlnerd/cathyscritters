<?php

/**
* JoomBlog component for Joomla 1.6 & 1.7
* @package JoomBlog
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jimport('joomla.application.component.helper');

class JoomBlogModelSampledata extends JModel
{
	protected $xmlStore = array();

	public function makeInstall()
	{
		jimport('joomla.installer.helper');
		jimport('joomla.filesystem.file' );
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.archive');

		$url = 'http://www.joomplace.com/media/joomblog103.zip';
		//$url = JURI::root().'tmp/joomblog.zip';
		$sampleDataFile = JInstallerHelper::downloadPackage($url);

		if (!$sampleDataFile) {
			$this->setError(JText::_('COM_JOOMBLOG_SAMPLEDATA_ERROR_DOWNLOAD'));
			return false;
		}

		$config = &JFactory::getConfig();
		$tmp_path = $config->getValue('config.tmp_path');

		$source = $tmp_path.DS.$sampleDataFile;
		$sampleDataDir = $tmp_path.DS.'joomblog';

		if (JFile::exists($sampleDataDir)) {
			JFile::delete($sampleDataDir);
		}

		if (!JArchive::extract($source, $tmp_path)) {
			$this->setError(JText::_('COM_JOOMBLOG_SAMPLEDATA_ERROR_ARCHIVE'));
			return false;
		} else {
			JFile::delete($source);
		}

		$imageDir = JPATH_SITE.DS.'media'.DS.'com_joomblog'.DS.'images';
		if (!JFile::exists($imageDir)) {
			JFolder::create($imageDir);
		}

		$jpg_files = JFolder::files($sampleDataDir.DS.'images');
		if ($jpg_files) {
			foreach ($jpg_files as $file) {
				if (!JFile::exists($imageDir.DS.$file)) {
					if (!JFile::move($sampleDataDir.DS.'images'.DS.$file, $imageDir.DS.$file)) {
						$this->setError(JText::_('COM_JOOMBLOG_SAMPLEDATA_ERROR_MOVE'));
						return false;
					}
				}
			}
		}

		$sqlFile = $sampleDataDir.DS.'install.sql';
		if (JFile::exists($sqlFile)) {
			$db = $this->getDBO();
			$sqlBuffer = file_get_contents($sqlFile);

			$queries = JInstallerHelper::splitSql($sqlBuffer);
			if (count($queries) > 0) {
				foreach ($queries as $query) {
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->query()) {
							$this->setError(JText::_('COM_JOOMBLOG_SAMPLEDATA_ERROR_SQL'));
							return false;
						}
					}
				}
			}
		}

		$xmlFile = $sampleDataDir.DS.'install.xml';
		if (JFile::exists($xmlFile)) {
			$xmlData = simplexml_load_file($xmlFile);

			foreach ($xmlData as $item) {
				$tblName = (string)$item['table'];
				$tblPrefix = (string)$item['prefix'];
				$tblNested = (bool)$item['nested'];

				if ($tblName) {
					foreach ($item->item as $element) {
						$table = $this->getTable($tblName, $tblPrefix);

						if ($table) {
							$elReturn = (string)$element['return'];

							$data = $this->traverseXML($element);

							if ($tblNested) {
								$parent_id = isset($data['parent_id']) ? (int)$data['parent_id'] : 0;
								$table->setLocation($parent_id, 'last-child');
							}

							$user =& JFactory::getUser();

							
							
							if (isset($data['created_user_id']) & $data['created_user_id'] !== $user->id)
							{
								$data['created_user_id'] = $user->id;
							}
							
							if (isset($data['modified_user_id']) & $data['modified_user_id'] !== $user->id)
							{
								$data['modified_user_id'] = $user->id;
							}	

							if (isset($data['user_id']) & $data['user_id'] !== $user->id)
							{
								$data['user_id'] = $user->id;
							}	

							if (isset($data['created_by']) & $data['created_by'] !== $user->id)
							{
								$data['created_by'] = $user->id;
							}	
							
																					
							$table->bind($data);

							if ($this->checkIssetData($tblName,$data))	
							{
								
								$table->store();
							}

							else if ($tblName=='category')
							{
								$db = $this->getDBO();
								$query = 'SELECT `id` FROM `#__categories` WHERE `title`="'.$data['title'].'" AND `published`<>-2 ';
								$db->setQuery($query);
								$cid = $db->loadResult();
								$table->load($cid); 
							}							

							if ($elReturn) {
								$this->xmlStore[$elReturn] = $table->id;
							}
						}
					}
				}
			}
		}

		$customFile = $sampleDataDir.DS.'custom.xml';
		if (JFile::exists($customFile)) {
			$xml = JoomBlogHelper::loadData('custom.xml');

			if ($xml) {
				$custom_xml = simplexml_load_file($customFile);
				$fields = $custom_xml->xpath('//field');

				foreach ($fields as $field) {
					$name = $field['name'];
					list($child) = $xml->xpath('//field[@name="'.$name.'"]');

					if (!$child) {
						$child = $xml->fields->fieldset[0]->addChild('field');
					}

					foreach ($field->attributes() as $name=>$value) {
						$child[$name] = $value;
					}
				}

				JoomBlogHelper::saveData($xml);
			} else {
				JFile::move($customFile, JPATH_SITE.DS.'media'.DS.'com_joomblog'.DS.'custom.xml');
			}
		}

		return true;
	}
	
	/***
	 * NEW SMT protected function STOP DUBLICATES 
	 * ***/
	protected function checkIssetData($tblName,$data)
	{
		if (isset($tblName) && isset($data['title']))
		{
		$db = $this->getDBO();
		switch ( $tblName ) {
			case 'category':
				$query = 'SELECT `id` FROM `#__categories` WHERE `title`="'.$data['title'].'" AND `published`<>-2 ';
			break;
			case 'Blog':
				$query = 'SELECT `id` FROM `#__joomblog_list_blogs` WHERE `title`="'.$data['title'].'"';
			break;
			case 'Post':
			$query = 'SELECT `id` FROM `#__joomblog_posts` WHERE `title`="'.$data['title'].'"';
				$db->setQuery($query);
			if ($db->loadResult()) { 
				$content_id = (int)$db->loadResult();
				$query = 'UPDATE `#__joomblog_posts` SET `catid`='.(int)$data["catid"].' WHERE `title`="'.$data['title'].'"';
				$db->setQuery($query);
				$db->query();
				$query = 'UPDATE `#__joomblog_multicats` SET `cid`='.(int)$data["catid"].' WHERE `aid`="'.$content_id.'"';
				$db->setQuery($query);
				$db->query();
				return false;
			}
			break;
		}
		
		$db->setQuery($query);
		if ($db->loadResult()) return false; else return true;
		}
		return true;
	}
	
	protected function traverseXML($data)
	{
		$out = array();

		foreach ($data->children() as $key=>$element) {
			if (count($element->children())) {
				$out[$key][] = $this->traverseXML($element);
			} else {
				$elUse = (string)$element['use'];
				if ($elUse) {
					$element = isset($this->xmlStore[$elUse]) ? $this->xmlStore[$elUse] : '';
				}

				$value = (string)$element;
				$elUnique = false;
				if (isset($element['unique'])) $elUnique = (bool)$element['unique'];
				if ($elUnique) {
					$value = uniqid($value.'_');
				}

				$out[$key][] = $value;
			}
		}
		
		foreach ($out as $key=>$value) {
			if (count($value) == 1) {
				$out[$key] = $value[0];
			}
		}
		
		return $out;
	}
}
