<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

// For security reasons use build in content model class
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_joomblog'.DS.'models'.DS.'article.php');

class JoomBlogModelPost extends ContentModelArticle
{
	protected $context = 'com_joomblog';

	protected function canDelete($data, $key = 'id') {
		return JFactory::getUser()->authorise('core.delete', 'com_joomblog.article.'.((int) isset($data->$key) ? $data->$key : 0));
		return true;
	}

	public function getTable($type = 'Post', $prefix = 'JoomBlogTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = false) 
	{
		$form = $this->loadForm('com_joomblog.post', 'post', array('control' => 'jform', 'load_data' => $loadData));
		$item = $this->getItem();
		
		$form->setFieldAttribute('catid', 'extension', 'com_joomblog');

		if (empty($item->id)) {
			$app = &JFactory::getApplication();
			$item->set('catid', $app->getUserStateFromRequest('com_joomblog.filter.category_id', 'filter_author_id'));
			$item->set('blog_id', $app->getUserStateFromRequest('com_joomblog.filter.blog_id', 'filter_author_id'));
			$item->set('created_by', $app->getUserStateFromRequest('com_joomblog.filter.author_id', 'filter_author_id'));
		}

		$form->bind($item);

		if (empty($form)) {
			return false;
		}
		return $form;
	}
}
