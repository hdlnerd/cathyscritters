<?php

/**
* JoomBlog component for Joomla 1.6 & 1.7
* @package JoomBlog
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');


class JoomBlogController extends JController
{
	function display($cachable = false) 
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'administrator/components/com_joomblog/assets/css/joomblog.css');
		$document->addScript(JURI::root().'administrator/components/com_joomblog/assets/js/base.js');

		$viewName = JRequest::getCmd('view', 'about');
		$this->default_view = $viewName;

		JoomBlogHelper::addSubmenu($viewName);

		parent::display($cachable);
	}

	function latestVersion()
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/Snoopy.class.php' );
		$tm_version= JoomBlogHelper::getVersion();
		$s = new Snoopy();
		$s->read_timeout = 90;
		$s->referer = JURI::root();
		@$s->fetch('http://www.joomplace.com/version_check/componentVersionCheck.php?component=joomblog&current_version='.urlencode($tm_version));
		$version_info = $s->results;
		$version_info_pos = strpos($version_info, ":");
		if ($version_info_pos === false) {
			$version = $version_info;
			$info = null;
		} else {
			$version = substr( $version_info, 0, $version_info_pos );
			$info = substr( $version_info, $version_info_pos + 1 );
		}
		if ($s->error || $s->status != 200) {
			echo '<font color="red">Connection to update server failed: ERROR: ' . $s->error . ($s->status == -100 ? 'Timeout' : $s->status).'</font>';
		} else if($version == $tm_version) {
			echo '<font color="green">' . $version . '</font>' . $info;
		} else {
			echo '<font color="red">' . $version . '</font>&nbsp;<a href="http://www.joomplace.com/members-area.html" target="_blank">(Upgrade to the latest version)</a>' ;
		}
		exit();
	}
	
	public function latestNews()
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/Snoopy.class.php' );

		$s = new Snoopy();
		$s->read_timeout = 10;
		$s->referer = JURI::root();
		@$s->fetch('http://www.joomplace.com/news_check/componentNewsCheck.php?component=joomblog');
		$news_info = $s->results;
		
		if ($s->error || $s->status != 200) {
			echo '<font color="red">Connection to update server failed: ERROR: ' . $s->error . ($s->status == -100 ? 'Timeout' : $s->status).'</font>';
		} else {
			echo $news_info;
		}
		exit();
	}

	public function history()
	{
		echo '<h2>'.JText::_('COM_JOOMBLOG_VERSION_HISTORY').'</h2><br/>';
		jimport ('joomla.filesystem.file');
		if (!JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.'/changelog.txt')) {
			echo 'History file not found.';
		} else {
			echo '<textarea class="editor" rows="30" cols="50" style="width:100%">';
			echo JFile::read(JPATH_COMPONENT_ADMINISTRATOR.'/changelog.txt');
			echo '</textarea>';
		}
		exit();
	}
	
	function installSampleData() {
		$link = 'index.php?option=com_joomblog&view=posts';
		$model = $this->getModel('sampledata');

		if (!$model->makeInstall()) {
			$this->setRedirect($link, implode(',', $model->getErrors()), 'error');
			return;
		}

		$this->setRedirect($link, JText::_('COM_JOOMBLOG_SAMPLEDATA_INSTALL_SUCCESS'), 'msg');
	}
}
