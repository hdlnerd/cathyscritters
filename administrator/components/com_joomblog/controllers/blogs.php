<?php

/**
* JoomBlog component for Joomla 1.6 & 1.7
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class JoomBlogControllerBlogs extends JControllerAdmin
{
	
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Define standard task mappings.
			$this->registerTask('unpublish_approve',	'publish_approve');	// value = 0
	}
		
	
	public function getModel($name = 'Blog', $prefix = 'JoomBlogModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	public function publish_approve()
       {
       	  	// Check for request forgeries
       	  	JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		$session	= JFactory::getSession();
		$registry	= $session->get('registry');

		// Get items to publish from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		$data	= array('publish_approve' => 1, 'unpublish_approve' => 0);
		$task 	= $this->getTask();
		$value	= JArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid)) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);

			// Publish the items.
			if (!$model->publish_approve($cid, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
			else {
				if ($value == 1) {
					$ntext = $this->text_prefix.'_N_ITEMS_APPROVED';
				}
				else if ($value == 0) {
					$ntext = $this->text_prefix.'_N_ITEMS_UNAPPROVED';
				}
				else if ($value == 2) {
					$ntext = $this->text_prefix.'_N_ITEMS_ARCHIVED';
				}
				else {
					$ntext = $this->text_prefix.'_N_ITEMS_TRASHED';
				}
				$this->setMessage(JText::plural($ntext, count($cid)));
			}
		}
		$extension = JRequest::getCmd('extension');
		$extensionURL = ($extension) ? '&extension=' . JRequest::getCmd('extension') : '';
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$extensionURL, false));
	}
}
