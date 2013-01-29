<?php

/**
 * @version   1.x
 * @package   ShackSlides
 * @copyright (C) 2010 Joomlashack / Meritage Assets Corp
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldColorpicker extends JFormField
{
	protected $type = 'Colorpicker';

	protected function getInput()
	{
		$doc = JFactory::getDocument();
		$doc->addScript(str_replace('/administrator/', '/', JURI::base()).'modules/mod_jsshackslides/fields/jscolor/jscolor.js');

		$size = ( $this->element['size'] ? 'size="'.$this->element['size'].'"' : '' );
        $value = htmlspecialchars_decode($this->value, ENT_QUOTES);

		$html = '<input type="text" name="'.$this->name.'" id="'.$this->name.'" value="'.$value.'" class="color" '.$size.' /> ';

		return $html;
	}
}