<?php

/**
* JoomPortfolio component for Joomla 1.6
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldBirthday extends JFormField
{
	protected $type = 'Option';

	function getInput() {
		$input = '';

		$input .= JHTML::_('select.integerlist', 1, 31, 1, $this->name.'[mday]', $attribs = null, $this->value['mday'], $format = "");
		
		$arr = array(
			JHTML::_('select.option', 1, JText::_('JANUARY') ),
			JHTML::_('select.option', 2, JText::_('FEBRUARY') ),
			JHTML::_('select.option', 3, JText::_('MARCH') ),
			JHTML::_('select.option', 4, JText::_('APRIL') ),
			JHTML::_('select.option', 5, JText::_('MAY') ),
			JHTML::_('select.option', 6, JText::_('JUNE') ),
			JHTML::_('select.option', 7, JText::_('JULY') ),
			JHTML::_('select.option', 8, JText::_('AUGUST') ),
			JHTML::_('select.option', 9, JText::_('SEPTEMBER') ),
			JHTML::_('select.option', 10, JText::_('OCTOBER') ),
			JHTML::_('select.option', 11, JText::_('NOVEMBER') ),
			JHTML::_('select.option', 12, JText::_('DECEMBER') )
		);
		$input .= JHTML::_('select.genericlist', $arr, $this->name.'[mon]', null, 'value', 'text', $this->value['mon']);
		
		$input .= JHTML::_('select.integerlist', 1901, 2011, 1, $this->name.'[year]', $attribs = null, $this->value['year'], $format = "");
		
		return $input;
	}
}
