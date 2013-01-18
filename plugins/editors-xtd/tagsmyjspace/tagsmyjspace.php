<?php
/**
* @version $Id: tagsmyjspace.php $
* @version		2.0.3 21/10/2012
* @package		plg_tagsmyjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.html.parameter'); // >= J1.6

/**
 * Editor Tags MyJspace buton
 *
 */
class plgButtonTagsMyjspace extends JPlugin
{
	/**
	 * Constructor
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Display the button
	 *
	 * @return array A two element array of (imageName, textToInsert)
	 */
	public function onDisplay($name)
	{
		$component_name = JRequest::getVar('option', '');

		$button = new JObject;
		
		if ($component_name == 'com_myjspace') {

			// Get Config
			$app = JFactory::getApplication();
			$template = $app->getTemplate();
		
			if (version_compare(JVERSION,'1.6.0','lt'))
				JPlugin::loadLanguage('plg_editors-xtd_tagsmyjspace', JPATH_ADMINISTRATOR);

			JHtml::_('behavior.modal');
			
			$link = 'index.php?option=com_myjspace&amp;view=edit&amp;layout=tags&amp;tmpl=component&amp;e_name='.$name;

			$button->set('modal', true);
			$button->set('link', $link);
			$button->set('text', JText::_('PLG_EDITORSXTD_MYJSPACE_BUTTON_TAGS'));
			$button->set('name', 'tagsmyjspace readmore'); // Point to use the readmore class !
			$button->set('options', "{handler: 'iframe', size: {x: 400, y: 200}}");
		}

		return $button;
	}
}

?>
