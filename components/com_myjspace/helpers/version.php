<?php
/**
* @version $Id: version.php $
* @version		20/10/2012
* @package		BS
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Pas d'accès direct
defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

class BS_Helper_version
{
	// Info sur la version
	public static function get_version($droits = null) {
		// Nom du fichier de configuration et extraction des infos xml
		$option = JRequest::getCmd('option');
		$retour = null;
		if ($option) {
			$retour = BS_Helper_version::get_xml_item($option, 'name').' | '.BS_Helper_version::get_xml_item($option, 'author');
			$retour .= ' | <a href="'.BS_Helper_version::get_xml_item($option, 'authorUrl').'">'.BS_Helper_version::get_xml_item($option, 'authorUrl').'</a><br />';
			$retour .= BS_Helper_version::get_xml_item($option, 'copyright').' | '.BS_Helper_version::get_xml_item($option, 'license').'<br />';

			$retour_version = BS_Helper_version::get_xml_item($option, 'version').' | '.BS_Helper_version::get_xml_item($option, 'build').' | '.BS_Helper_version::get_xml_item($option, 'creationDate');

			$user = JFactory::getUser();
			$app = JFactory::getApplication();

			// Pour admin seulement & backend
			if ($app->isAdmin()) {
				if (version_compare(JVERSION, '1.6.0', 'ge')) {
					if ($user->authorise($droits))
						$retour .= $retour_version.'<br />';
				} else {
					if ($user->gid == 25 || $user->gid == 24)
						$retour .= $retour_version.'<br />';
				}
			}
		}
		return $retour;
	}
	
	// Get a specific item : authorUrl, build
	public static function get_xml_item($component_tmp = null, $item = null) {
		// Nom du fichier de configuration et extraction des infos xml
		$option_tmp = JRequest::getCmd('option');
		
		$retour = '';
		if ($option_tmp || $component_tmp) {
			if ($component_tmp)
				$my_componant = substr($component_tmp, 4, strlen($component_tmp)-4);
			else
				$my_componant = substr($option_tmp, 4, strlen($option_tmp)-4);
	
			$path = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_'.$my_componant;
			if (!@file_exists($path.DS.$my_componant.'.xml'))
				return '';
			else
				$file = $path.DS.$my_componant.'.xml';

			libxml_use_internal_errors(true);
			$xml = simplexml_load_file($file);
			$retour = (string)$xml->{$item};
		}
		
		return $retour;	
	}

	// Check if new version and get version info '' = no new version
	public static function get_newversion($component_tmp = null, $type_tmp = 'component') {
	
		if ($component_tmp == null)
			$component_tmp = JRequest::getCmd('option');

		// Si pas de test
		$pparams = JComponentHelper::getParams($component_tmp);
		if ($pparams->get('allowcheckversion', 1) == 0)
			return '';

		if ($component_tmp)
			$my_componant = substr($component_tmp,4, strlen($component_tmp)-4);
		else
			$my_componant = substr($option_tmp,4, strlen($option_tmp)-4);
				
		$path = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_'.$my_componant;
		if (!@file_exists($path.DS.$my_componant.'.xml'))
			return '';
		else
			$file = $path.DS.$my_componant.'.xml';
			
		// Données de la version actuelle: ou se trouvent le sinfo pour avoir la dernière version
		libxml_use_internal_errors(true);
		$xml = simplexml_load_file($file);
		
		$actual_version = (string)$xml->version;
		$actual_build = (string)$xml->build;
		$max_version = (string)$actual_version;
		$datalink = (string)$xml->updateservers->server;

		if (!$datalink)
			return '';
		
		// Recherche de la derniere version
		
		// Voir si pas dejà en cache
		$check_lastdate = $pparams->get('check_lastdate', 0);	
		$check_version = $pparams->get('check_version', '');
		$check_period = $pparams->get('check_period', 864000);

		if (abs($check_lastdate - time()) < $check_period) { // If use dada in cache
			if (version_compare($check_version, $actual_version, 'gt'))
				return $check_version;
			else
				return '';		
		}

		// Recup du fichier de maj.
		if ($datalink) { // Si lien de maj.
			$mon_serveur = JURI::root();		
			$datalink = $datalink."&type=".$type_tmp."&name=".$component_tmp."&version=".$actual_version.'b'.$actual_build."&joomla=".JVERSION."&server=".$mon_serveur;

			$contents = null;
			if (function_exists('curl_init')) 
				$contents = BS_Helper_version::getCURL($datalink); // First method
			else { // Second method
				$inputHandle = @fopen($datalink, "r");
				if (!$inputHandle) {
					// Set the config in memory & save in DB en return 
					$pparams->set('check_lastdate', time());	
					$pparams->set('check_version', $max_version);	
					BS_Helper_version::save_parameters($component_tmp);
					return '';
				}
				$contents = '';
				while (!feof($inputHandle)) {
					$contents .= fread($inputHandle, 4096);
					if ($contents === false) {
						// Set the config in memory & save in DB en return 
						$pparams->set('check_lastdate',time());	
						$pparams->set('check_version',$max_version);	
						BS_Helper_version::save_parameters($component_tmp);
						return '';
					}
				}
				fclose($inputHandle);			
			}
			if (!$contents) {
				// Set the config in memory & save in DB en return 
				$pparams->set('check_lastdate',time());	
				$pparams->set('check_version',$max_version);	
				BS_Helper_version::save_parameters($component_tmp);
				return '';
			}
			// Test la validé du xml du recu !
			$xml_test = @simplexml_load_string($contents);
			if ($xml_test===FALSE) {
				// Set the config in memory & save in DB en return 
				$pparams->set('check_lastdate',time());	
				$pparams->set('check_version',$max_version);	
				BS_Helper_version::save_parameters($component_tmp);
				return '';
			}

			// Traitement du fichier pour rechercher la derniere version
			$plateform = substr(JVERSION,0, -2);
			$xml = simplexml_load_string($contents);

			foreach( $xml->update as $update ) {
				if ((string)$update->element == $component_tmp && (string)$update->type == $type_tmp && $plateform == (string)$update->targetplatform['version']) {
					if (version_compare((string)$update->version ,$max_version, 'gt'))
						$max_version = (string)$update->version;
				}
			}

			// Set the config in memory & save in DB
			$pparams->set('check_lastdate',time());	
			$pparams->set('check_version',$max_version);	
			BS_Helper_version::save_parameters($component_tmp);

			// Return version if new one
			if ($actual_version != $max_version)
				return $max_version;
		}
		
		return '';
	}
	
	// Save de paramaters for memory to DB
	public static function save_parameters($component_tmp = null) {
			// Save the new config
			$db	= JFactory::getDBO();
			$pparams = JComponentHelper::getParams($component_tmp);
			
			if (version_compare(JVERSION, '1.6.0', 'ge')) {		
				$data = $pparams->toString('JSON');
				$db->setQuery('UPDATE `#__extensions` SET `params` = '.$db->Quote($data).' WHERE '.
					"`element` = ".$db->Quote($component_tmp)." AND `type` = 'component'");
			} else {		
				$data = $pparams->toString('INI');
				$db->setQuery('UPDATE `#__components` SET `params` = '.$db->Quote($data).' WHERE '.
					"`option` = ".$db->Quote($component_tmp)." AND `parent` = 0 AND `menuid` = 0");
			}
			$db->query();	
	}

	// Get data from url with curl_ functions
	function &getCURL($url = null, $fp = null, $nofollow = false)
	{
		$result = false;
		
		$ch = curl_init($url);
		
		if (!@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1) && !$nofollow) {
			// Safe Mode is enabled. We have to fetch the headers and
			// parse any redirections present in there.
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);

			// Get the headers
			$data = curl_exec($ch);
			curl_close($ch);
			
			// Init
			$newURL = $url;
			
			// Parse the headers
			$lines = explode("\n", $data);
			foreach($lines as $line) {
				if (substr($line, 0, 9) == "Location:") {
					$newURL = trim(substr($line,9));
				}
			}

			// Download from the new URL
			if ($url != $newURL) {
				return self::getCURL($newURL, $fp);
			} else {
				return self::getCURL($newURL, $fp, true);
			}
		} else {
			@curl_setopt($ch, CURLOPT_MAXREDIRS, 20);
		}

		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		// Pretend we are IE7, so that webservers play nice with us
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)');
		
		if (is_resource($fp)) {
			curl_setopt($ch, CURLOPT_FILE, $fp);
		}

		$result = curl_exec($ch);
		curl_close($ch);
	
		return $result;
	}	
	
}
?>
