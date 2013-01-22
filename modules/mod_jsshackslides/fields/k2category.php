<?php
/**
* @version   1.x
* @package   ShackSlides
* @copyright (C) 2010 Joomlashack / Meritage Assets Corp
* @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldK2category extends JFormFieldList
{
	public $type = 'K2category';

	protected function getInput()
	{
		$k2_element_path = JPATH_SITE.'/administrator/components/com_k2/elements/categories.php';		
		
		if (file_exists($k2_element_path)) :
			include_once($k2_element_path);
			return JFormFieldCategories::getInput();
		else :
			return JText::_('K2_NOT_INSTALLED');
		endif;
	}
}
