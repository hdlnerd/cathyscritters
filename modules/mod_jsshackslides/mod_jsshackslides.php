<?php
/**
* @version   1.x
* @package   ShackSlides
* @copyright (C) 2010 Joomlashack / Meritage Assets Corp
* @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Direct access to files is not permitted');

require_once(dirname(__FILE__).'/'.'helpers'.'/'.$params->get('source','folder').'.php');

$helperClass = 'ModShackSlides'.ucfirst($params->get('source','folder')).'Helper';
$helper = new $helperClass($params);

$images = $helper->getImages();
$links = $helper->getLinks();
$titles = $helper->getTitles();
$base = $helper->getBase();

require(JModuleHelper::getLayoutPath('mod_jsshackslides'));