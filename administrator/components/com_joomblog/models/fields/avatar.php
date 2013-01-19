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

class JFormFieldAvatar extends JFormField
{
	function getInput() {
		$input = '';
		
		$input .= '<div style="float:left;" >';
		if ($this->value) {
			$input .= '<p style="float:none;" ><img src="'.JURI::root().'images/joomblog/avatar/'.$this->value.'" alt="avatar" /></p>';
		} else {
			$input .= '<p style="float:none;" ><img src="'.JURI::root().'administrator/components/com_joomblog/assets/images/user.png" alt="avatar" /></p>';
		}
		$input .= '<p style="float:none;" ><input type="file" name="avatarFile" /></p>';
		$input .= '<p style="float:none;" ><input type="checkbox" name="resetAvatar" id="resetAvatar" value="1" /><label for="resetAvatar" style="clear: none;margin: 0px 0px 10px 5px;" >Reset Avatar</label></p>';
		$input .= '</div>';
		
		return $input;
	}
}
