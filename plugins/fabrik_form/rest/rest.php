<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Fabrik.form.rest
 * @copyright   Copyright (C) 2005 Fabrik. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Require the abstract plugin class
require_once COM_FABRIK_FRONTEND . '/models/plugin-form.php';

/**
 * Submit or update data to a REST service
 *
 * @package     Joomla.Plugin
 * @subpackage  Fabrik.form.rest
 * @since       3.0
 */

class plgFabrik_FormRest extends plgFabrik_Form
{

	/**
	 * Run right at the end of the form processing
	 * form needs to be set to record in database for this to hook to be called
	 *
	 * @param   object  $params      plugin params
	 * @param   object  &$formModel  form model
	 *
	 * @return	bool
	 */

	public function onBeforeProcess($params, &$formModel)
	{
		$w = new FabrikWorker;
		if (!function_exists('curl_init'))
		{
			JError::raiseError(500, 'CURL NOT INSTALLED');
			return;
		}
		// The username/password
		$config_userpass = $params->get('username') . ':' . $params->get('password');

		// Set where we should post the REST request to

		// POST new records PUT exisiting records
		$method = $formModel->_formData['rowid'] == 0 ? 'POST' : 'PUT';

		$endpoint = $method === 'PUT' ? $params->get('put') : $params->get('endpoint');
		$endpoint = $w->parseMessageForPlaceholder($endpoint);

		// What is the root node for the xml data we are sending
		$xmlParent = $params->get('xml_parent', 'ticket');
		$xmlParent = $w->parseMessageForPlaceholder($xmlParent);

		// Request headers
		$headers = array();

		// Set up CURL object
		$chandle = curl_init();

		// Set which fields should be included in the data.
		$dataMap = $params->get('include_list', 'milestone-id, status, summary');
		$include = $w->parseMessageForPlaceholder($dataMap, null, false);

		// Get the foreign key element
		$fkElement = $formModel->getElement($params->get('foreign_key'), true);
		$fkElementKey = $fkElement->getFullName();

		$fkData = array();
		if ($method === 'PUT')
		{
			$fkData = json_decode(JArrayHelper::getValue($formModel->_formData, $fkElementKey));
			$fkData = JArrayHelper::fromObject($fkData);
		}
		$endpoint = $w->parseMessageForPlaceHolder($endpoint, $fkData);


		$output = $this->buildOutput($formModel, $include, $xmlParent, $headers);
		$curlOpts = $this->buildCurlOpts($method, $headers, $endpoint, $params, $output);

		foreach ($curlOpts as $key => $value)
		{
			curl_setopt($chandle, $key, $value);
		}
		$output = curl_exec($chandle);
		$jsonOutPut = FabrikWorker::isJSON($output) ? true : false;

		if (!$this->handleError($output, $formModel, $chandle))
		{
			return false;
		}

		curl_close($chandle);

		// Set FK value in Fabrik form data
		if ($method === 'POST')
		{
			if ($jsonOutPut)
			{
				$fkVal = json_encode($output);
			}
			else
			{
				$fkVal = $output;
			}
			$formModel->updateFormData($fkElementKey , $fkVal, true, true);
		}
	}

	/**
	 * Create the data structure containing the data to send
	 *
	 * @param   object  $formModel  Form Model
	 * @param   string  $include    list of fields to include
	 * @param   xml     $xmlParent  Parent node if rendering as xml
	 * @param   array   &$headers   Headeres
	 *
	 * @return mixed
	 */
	private function buildOutput($formModel, $include, $xmlParent, &$headers)
	{
		$postData = [];
		if (FabrikWorker::isJSON($include))
		{
			$include = json_decode($include);

			$include = JArrayHelper::fromObject($include[0]);
			if ($xmlParent != '')
			{
				$postData[$xmlParent] = $include;
			}
			else
			{
				$postData = $include;
			}
		}
		else
		{
			$include = explode(',', $include);
			foreach ($include as $i)
			{
				if (array_key_exists($i, $formModel->_formData))
				{
					$postData[$i] = $formModel->_formData[$i];
				}
				elseif (array_key_exists($i, $formModel->_fullFormData, $i))
				{
					$postData[$i] = $formModel->_fullFormData[$i];
				}
			}
		}

		$postAsXML = false;
		if ($postAsXML)
		{
			$xml = new SimpleXMLElement('<' . $xmlParent . '></' . $xmlParent . '>');
			$headers = array('Content-Type: application/xml', 'Accept: application/xml');
			foreach ($postData as $k => $v)
			{
				$xml->addChild($k, $v);
			}
			$output = $xml->asXML();
		}
		else
		{
			$output = http_build_query($postData);
		}
		return $output;
	}

	/**
	 * Create the CURL options when sending
	 *
	 * @param   string  $method  POST/PUT
	 * @param   array   &$headers  Headers
	 *
	 * @return  array
	 */
	private function buildCurlOpts($method, &$headers, $endpoint, $params, $output)
	{
		// The username/password
		$config_userpass = $params->get('username') . ':' . $params->get('password');
		$curlOpts = array();
		if ($method === 'POST')
		{
			$curlOpts[CURLOPT_POST] = 1;
		}
		else
		{
			$curlOpts[CURLOPT_PUT] = 1;

			/**
			 * // Not working for apparty:
			 *
			 * CURLOPT_CUSTOMREQUEST => $method,
			 */
		}

		$curlOpts[CURLOPT_URL] = $endpoint;
		$curlOpts[CURLOPT_SSL_VERIFYPEER] = 0;
		$curlOpts[CURLOPT_POSTFIELDS] = $output;
		$curlOpts[CURLOPT_HTTPHEADER] = $headers;
		$curlOpts[CURLOPT_RETURNTRANSFER] = 1;
		$curlOpts[CURLOPT_HTTPAUTH] = CURLAUTH_ANY;
		$curlOpts[CURLOPT_USERPWD] = $config_userpass;
		return $curlOpts;
	}

	/**
	 * Handle any error generated
	 *
	 * @return boolean
	 */
	private function handleError(&$output, $formModel, $chandle)
	{
		if (FabrikWorker::isJSON($output))
		{
			$output = json_decode($output);

			// @TODO make this more generic - currently only for apparty
			if (isset($output->errors))
			{
				// Have to set something in the errors array otherwise form validates
				$formModel->_arErrors['dummy___elementname'][] = 'woops!';
				$formModel->getForm()->error = implode(', ', $output->errors);
				return false;
			}
		}
		$httpCode = curl_getinfo($chandle, CURLINFO_HTTP_CODE);
		echo $httpCode;exit;
		switch ($httpCode)
		{
			case '400':
				echo "Bad Request";
				break;
			case '401':
				echo "Unauthorized";
				break;
			case '404':
				echo "Not found";
				break;
			case '405':
				echo "Method Not Allowed";
				break;
			case '406':
				echo "Not Acceptable";
				break;
			case '415':
				echo "Unsupported Media Type";
				break;
			case '500':
				echo "Internal Server Error";
				break;
		}
		if (curl_errno($chandle))
		{
			JError::raiseNotice($httpCode, curl_error($chandle));
			return false;
		}
		return true;
	}

}


/* $e['name'] = 'robs test';
 $e['cancelled'] = null;
$e['description'] = 'desc';
$e['itunes_link'] = '';
$e['keyword_list'] = 'keywords';
$e['presale_price'] = '19.00';
$e['published'] = null;
$e['regular_price'] = '25.00';
$e['sold_out'] = null;
$e['soundcloud_link'] = null;
$e['youtube_link'] = null;
$e['open_at'] = 1342444076;
$e['start_at'] = 1342451276;
$e['end_at'] = 1342462076;
$e['venue_id'] = 1;
$e['status'] = 'draft';
$e['cover_artwork'] = null;


$postData['event'] = $e;*/
