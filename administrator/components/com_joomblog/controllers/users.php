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

class JoomBlogControllerUsers extends JControllerAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('block', 'changeBlock');
		$this->registerTask('unblock', 'changeBlock');
	}

	public function getModel($name = 'User', $prefix = 'UsersModel') 
	{
		JModel::addIncludePath(JPATH_SITE.DS.'administrator/components'.DS.'com_users/models');
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	public function changeBlock()
	{
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('block' => 1, 'unblock' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('COM_USERS_USERS_NO_ITEM_SELECTED'));
		} else {
			$model = $this->getModel('User', 'UsersModel');

			if (!$model->block($ids, $value)) {
				JError::raiseWarning(500, $model->getError());
			} else {
				if ($value == 1){
					$this->setMessage(JText::plural('COM_USERS_N_USERS_BLOCKED', count($ids)));
				} else if ($value == 0){
					$this->setMessage(JText::plural('COM_USERS_N_USERS_UNBLOCKED', count($ids)));
				}
			}
		}

		$this->setRedirect('index.php?option=com_joomblog&view=users');
	}
}
