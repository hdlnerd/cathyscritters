<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: captcha.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage captcha.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_LIBRARY_PATH . DS . 'captcha.php' );

class JbblogCaptchaTask
{
	function JbblogCaptchaTask()
	{
		getCaptcha();
		exit();
	}	
}
