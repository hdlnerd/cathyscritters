<?php

/**
 * @version   1.x
 * @package   ShackSlides
 * @copyright (C) 2010 Joomlashack / Meritage Assets Corp
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Direct access to files is not permitted');

require_once(JPATH_ROOT.'/'.'modules'.'/'.'mod_jsshackslides'.'/'.'helper.php');

class ModShackSlidesFolderHelper extends ModShackSlidesHelper
{
	private $directory;
	private $folder;
	private $limit;
	private $xml = false;

	public function  __construct($params) {
        parent::__construct($params);
		$this->folder = $params->get('folder_folder', 'modules'.'/'.'mod_jsshackslides'.'/'.'tmpl'.'/'.'demos');
		$this->directory =  JPATH_ROOT.'/'.$this->folder;
		$this->limit = $this->limit = $params->get('limit', '5');

		if (is_file($this->directory.'/'.'images.xml')) $this->xml = simplexml_load_file($this->directory.'/'.'images.xml');

		if ($this->xml)
		{
			$this->loadImagesFromXML();
		}
		else
		{
			$this->loadImagesFromDirectory();
		}

		$this->setBase(JURI::base().str_replace('\\', '/', $this->folder).'/');
	}

	private function loadImagesFromXML()
	{
		$i = 0;
		foreach ($this->xml->xpath('//slide') as $slide)
		{
			if ($i < $this->limit) {
				$this->images[] = $slide->image;
				$this->links[] = (isset($slide->link)) ? $slide->link : false;
				$this->titles[] = (isset($slide->title)) ? $slide->title : false;
			}
			$i++;
		}
	}

	private function loadImagesFromDirectory()
	{
		jimport('joomla.filesystem.folder');

		$images = JFolder::files($this->directory, '\.png$|\.gif$|\.jpg$|\.bmp$|\.jpeg$');

        sort($images);

		while (count($images) > $this->limit)
		{
			array_pop($images);
		}

		$this->images = $images;
	}
}