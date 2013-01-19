<?php 
/**
* JoomBlog component for Joomla
* @version $Id: helper.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage helper.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JbDisqusHelper {

	// Load Includes
	function loadHeadIncludes($headIncludes){
		global $loadDisqusPluginIncludes;
		$document = & JFactory::getDocument();
		if(!$loadDisqusPluginIncludes){
			$loadDisqusPluginIncludes=1;
			$document->addCustomTag($headIncludes);
		}
	}
		
	// Path overrides
	function getTemplatePath($pluginName,$file){
		 $mainframe	= &JFactory::getApplication();
		$p = new JObject;
		if(file_exists(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$pluginName.DS.str_replace('/',DS,$file))){
			$p->file = JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$pluginName.DS.$file;
			$p->http = JURI::base()."templates/".$mainframe->getTemplate()."/html/{$pluginName}/{$file}";
		} else {
			$p->file = JPATH_SITE.DS.'components'.DS.'com_joomblog'.DS.'libraries'.DS.$pluginName.DS.'tmpl'.DS.$file;
			$p->http = JURI::base()."components/com_joomblog/libraries/{$pluginName}/tmpl/{$file}";
		}
		return $p;
	}

} // end class
