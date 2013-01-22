<?php

/**
 * @version   1.x
 * @package   ShackSlides
 * @copyright (C) 2010 Joomlashack / Meritage Assets Corp
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Direct access to files is not permitted');

require_once(JPATH_ROOT.'/'.'modules'.'/'.'mod_jsshackslides'.'/'.'helper.php');

class ModShackSlidesK2Helper extends ModShackSlidesHelper
{
	private $content;
	private $category_id;
	private $ordering;
	private $ordering_direction;
	private $limit;
	private $featured;

	public function  __construct($params) {
        parent::__construct($params);
		$this->category_id = $params->get('k2_category', 0);
		$this->ordering = $params->get('ordering', 'ordering');
		$this->ordering_direction = $params->get('ordering_direction', 'ASC');
		$this->limit = $params->get('limit', '5');
		$this->featured = $params->get('featured', 'include');

		$this->getContentFromDatabase();

		$this->parseContentIntoProperties();
	}

	private function getContentFromDatabase()
	{
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$featured_items		= '';
		$contentConfig      = JComponentHelper::getParams( 'com_content' );
		$aid		        = $user->get('aid', 0);
		$now                = date('Y-m-d H:i:s');
		$nullDate           = $database->getNullDate();

		// check for new ACL on Joomla! from 1.6 and above
		jimport('joomla.version');
		$version = new JVersion();
		if (version_compare($version->RELEASE, "1.6", "ge")) {
			$aid = max ($user->getAuthorisedViewLevels());
		}

		$query = 'SELECT * FROM #__k2_items'.
		' WHERE catid ='.$this->category_id.
		(($this->featured !== 'include') ? ' AND featured '.(($this->featured == 'exclude') ? '!=' : '=').' 1' : '').
		' AND published = 1'.
		' AND access <= '.$aid.
		' AND trash = 0'.
		' AND (publish_up = ' . $database->Quote($nullDate) . ' OR publish_up <= ' . $database->Quote($now) . ')'.
		' AND (publish_down = ' . $database->Quote($nullDate) . ' OR publish_down >= ' . $database->Quote($now) . ')'.
		' ORDER BY '.$this->ordering.' '.$this->ordering_direction.
		' LIMIT '.$this->limit;
		$database->setQuery($query);
		$this->content = $database->loadObjectList();
	}

	private function parseContentIntoProperties()
	{
		foreach ($this->content as $item)
		{
			$this->images[] = $this->getFirstImageFromContent($item->introtext);
			$this->titles[] = $this->getTitleFromContent($item->introtext);
			$this->links[] = $this->buildLink($item->id);
		}
	}

    private function buildLink($id)
	{
		$fields = array(	'option' => 'com_k2',
							'view' => 'item',
                            'layout' => 'item',
							'id' => $id);
        $index = $this->compareQuery($fields);

        if ($index != false) $link = $this->menu[$index]->link.'&Itemid='.$this->menu[$index]->id;
        else $link = 'index.php?option=com_k2&view=item&layout=item&id='.$id;

		return JRoute::_($link);
	}
}
