<?php

/**
 * @version   1.x
 * @package   ShackSlides
 * @copyright (C) 2010 Joomlashack / Meritage Assets Corp
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldDirectory extends JFormFieldList
{
	public $type = 'Directory';

	protected function getOptions()
	{
		// path to images directory
		$path		= JPATH_ROOT.'/'.$this->element['directory'];
		$filter		= $this->element['filter'];
		$exclude	= $this->element['exclude'];
		$folders	= JFolder::folders($path, $filter, true, true);

		$options = array ();
		foreach ($folders as $folder)
		{
			$folder = str_replace(JPATH_ROOT.'/', '', $folder);
			$options[] = JHTML::_('select.option', $folder, $folder);
		}

		array_unshift($options, JHTML::_('select.option', '', '- '.JText::_('Use demo').' -'));

		return $options;
	}
}