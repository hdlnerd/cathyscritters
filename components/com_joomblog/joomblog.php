<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: joomblog.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage joomblog.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

global $_JOOMBLOG, $_JB_CONFIGURATION, $Itemid;

jimport('joomla.html.parameter');

require_once( JPATH_ROOT.DS.'components'.DS.'com_joomblog'.DS.'defines.joomblog.php' );
require_once( JB_COM_PATH.DS.'task'.DS.'base.php' );
require_once( JB_COM_PATH.DS.'functions.joomblog.php' );
require_once( JB_LIBRARY_PATH.DS.'datamanager.php' );
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_joomblog'.DS.'config.joomblog.php' );
require_once( JB_COM_PATH.DS.'template.php' );

JTable::addIncludePath(JB_COM_PATH.DS.'tables');

$_JB_CONFIGURATION = new JB_Configuration();

$mainframe	=& JFactory::getApplication();

if(JRequest::getInt('Itemid') != 0) {
  $Itemid = JRequest::getInt('Itemid');
}else{
  $Itemid = 0;
}

$sectionid 	= $_JB_CONFIGURATION->get('postSection');
$catid 	= $_JB_CONFIGURATION->get('catid');
$sections	= $_JB_CONFIGURATION->get('managedSections');

if ($sections == ""){
	$sections = "-1";
}


function jbfPublishedcomment(){
	global $_JB_CONFIGURATION;
	
	$mainframe	=& JFactory::getApplication();
	$my	=& JFactory::getUser();
	$db	=& JFactory::getDBO();
	
	$params = JRequest::getVar('params','');
	$contentid = JRequest::getVar('contentid',0);
	$id = JRequest::getVar('id',0);
	
	if(!$my->get('id') || !$_JB_CONFIGURATION->get('useComment')){
		$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&show='.$contentid.'&Itemid='.jbGetItemId(),false),JText::_('COMMENT NOT CHANGING COMMENT'));
	}
	
	if($_JB_CONFIGURATION->get('useComment')){
		$date =& JFactory::getDate();
		$strSQL	= "UPDATE #__joomblog_comment SET `published`='".$params."', `modified`='".$date->toMySql()."' WHERE `id`= $id AND `contentid` = $contentid ";
		$db->setQuery($strSQL);
		$db->query();
	}
	
	if($params == '1'){
		$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&show='.$contentid.'&Itemid='.jbGetItemId(),false),JText::_('COMMENT PUBLISHED COMMENT'));
	}else{
		$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&show='.$contentid.'&Itemid='.jbGetItemId(),false),JText::_('COMMENT UNPUBLISHED COMMENT'));
	}
}

function jbfSavecomment(){
	global $_JB_CONFIGURATION;
	
	$mainframe =& JFactory::getApplication(); 
	$row =& JTable::getInstance( 'Comments' , 'Table' );
	
	$contentid = JRequest::getVar('contentid',0);
	
	$user = & JFactory::getUser();
	
	$comments = JRequest::getVar( 'editcomment' , 0 , 'REQUEST' );
	
	if(!$user->get('id') || !$_JB_CONFIGURATION->get('useComment')){
		$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&show='.$contentid.'&Itemid='.jbGetItemId(),false),JText::_('COMMENT NOT SAVING COMMENT'));
	}
	
	if (!class_exists('HTML_BBCodeParser') AND !function_exists('BBCode')) {
		include_once (JB_LIBRARY_PATH.DS."bbcodeparser.php");
	}
	
	if($comments){
		
		$date =& JFactory::getDate();
		
		foreach($comments as $key => $value){
			$row->load($key);
			if($row->id && $row->user_id == $user->get('id')){
				$row->comment = BBCode($value);
				$row->modified = $date->toMySql();
				$row->modified_by = $user->get('id');
				$row->store();
			}
		}
	}
	
	$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&show='.$contentid.'&Itemid='.jbGetItemId(),false),JText::_('COMMENT UPDATE COMMENT'));
}

function jbfAddcomment(){
	global $_JB_CONFIGURATION;

	$mainframe =& JFactory::getApplication(); 
	$sessions =& JFactory::getSession();
	$db	=& JFactory::getDBO();
	$my	=& JFactory::getUser();
	$row =& JTable::getInstance( 'Comments' , 'Table' );
	
	$comment = JRequest::getString('comment','');
	$contentid = JRequest::getVar('contentid',0);
	$data = JRequest::get('post');
	
	if (!class_exists('HTML_BBCodeParser') AND !function_exists('BBCode')) {
		include_once (JB_LIBRARY_PATH.DS."bbcodeparser.php");
	}
	
	if($_JB_CONFIGURATION->get('useCommentreCaptcha')){
		require_once( JB_LIBRARY_PATH . DS . 'recaptchalib.php' );
		$privatekey = $_JB_CONFIGURATION->get('recaptcha_privatekey');
		if (empty($privatekey))
		{
			$privatekey = "6LefN9USAAAAAPKe9gjZt5SS9dhblDtuROBQUcMe";
		}
		$resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
		if (!$resp->is_valid) {
			$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&show='.$contentid.'&Itemid='.jbGetItemId(),false),JText::_('COMMENT CAPTCHA NOT CORRECT'));
		}
	}
	elseif($_JB_CONFIGURATION->get('useCommentCaptcha')){
		$captcha = JRequest::getVar('captchacode','');
		if($captcha != $sessions->get("captcha")){
			$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&show='.$contentid.'&Itemid='.jbGetItemId(),false),JText::_('COMMENT CAPTCHA NOT CORRECT'));
		}
	}
	
	if($_JB_CONFIGURATION->get('useComment')){
		if($_JB_CONFIGURATION->get('useCommentOnlyRegisteredUsers')){
			if(!$my->get('id')){
				$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&show='.$contentid.'&Itemid='.jbGetItemId(),false),JText::_('COMMENT ONLY REGISTERED USERS'));
			}
		}
		
		$row->bind( $data , true);
			
		jimport('joomla.mail.helper');	
			
		if(!JMailHelper::isEmailAddress($row->email)){
			$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&show='.$contentid.'&Itemid='.jbGetItemId(),false),JText::_('COMMENT NOT CORRECT EMAIL'));
		}
			
		$row->comment = BBCode($row->comment);
		
		if($my->get('id')){
			$row->user_id = $my->get('id');
		}
		
		$row->ip = $_SERVER['REMOTE_ADDR'] != getenv('SERVER_ADDR') ? $_SERVER['REMOTE_ADDR'] : getenv('HTTP_X_FORWARDED_FOR');

    $date =& JFactory::getDate();
    $row->created = $date->toMySql();

		if($_JB_CONFIGURATION->get('allowModerateComment')){
			$row->published = 1;
		}
		$row->store();
		
		$query = "SELECT * FROM `#__joomblog_posts` WHERE `id`='{$row->contentid}' ";
    $db->setQuery( $query );
    $content = $db->loadObject();
		
		if($_JB_CONFIGURATION->get('notifyCommentAdmin') && !array_diff($my->groups, array('Super User')) ){
      jbNotifyCommentAdmin($row->id, $data['name'], $data['title'], $comment);
		}
	
		if($_JB_CONFIGURATION->get('notifyCommentAuthor') && $my->get('id') != $content->created_by ){
      jbNotifyCommentAuthor($row->id, $data['name'], $data['title'], $comment);
		}


		if(!$_JB_CONFIGURATION->get('allowModerateComment')){
			$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&show='.$contentid.'&Itemid='.jbGetItemId(),false),JText::_('COMMENT ADDING COMMENT AND MODERATE'));
		}else{
			$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&show='.$contentid.'&Itemid='.jbGetItemId(),false),JText::_('COMMENT ADDING COMMENT'));
		}
		
	}else{
		$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&show='.$contentid.'&Itemid='.jbGetItemId(),false),JText::_('COMMENT NOT ADDING COMMENT'));
	}
}

function jbfCancelblog()
{
  $mainframe =& JFactory::getApplication(); 
  $mainframe->redirect(JRoute::_('index.php?option=com_joomblog&task=adminhome&Itemid='.jbGetItemId(),false));
}

function jbfSavenewblog()
{
	global $JBBLOG_LANG, $_JB_CONFIGURATION;

	$db	=& JFactory::getDBO();
	$mainframe	=& JFactory::getApplication();
	$my	=& JFactory::getUser();
	$row =& JTable::getInstance( 'Blog' , 'Table' );
	
	$isNew = true;
	$data = JRequest::get('post');
	$row->bind( $data , true);
	
	if(!$my->authorise('post', 'com_joomblog')){
		$mainframe->redirect($_SERVER['HTTP_REFERER'],JText::_('COM_JOOMBLOG_BLOG_ADMIN_NO_PERMISSIONS_TO_POST'));
		return;
	}
		
	$title = $data['title'];
	$validation	= array();

	if( empty( $title ) || $title == JText::_('COM_JOOMBLOG_BLOG_TITLE') )
	{
		$validation['title'] = JText::_('COM_JOOMBLOG_TITLE_IS_REQUIRED');
	}

	$date =& JFactory::getDate();
	$row->create_date = $date->toFormat();
	
	$row->title = stripslashes($row->title);
	$row->user_id = $my->id;
	
	$row->published = $_JB_CONFIGURATION->get('autoapproveblogs');
	$row->approved = $_JB_CONFIGURATION->get('autoapproveblogs');
	
	/*
	if (!$_JB_CONFIGURATION->get('autoapproveblogs'))
	{
		$config	= JFactory::getConfig();
		$fromname	= $config->get('fromname');
		$mailfrom	= $config->get('mailfrom');
		$sitename	= $config->get('sitename');
		
		$id = JRequest::getInt('id');
		$subject = stripslashes(JText::_('COM_JOOMBLOG_MAIL_SUBJECT'));
		$message = nl2br(sprintf(stripslashes(JText::_('COM_JOOMBLOG_MAIL_NEW_MESSAGE')), JURI::base(), $row->title, $row->description, $my->name));
		
		JUtility::sendMail($mailfrom, $fromname, $mailfrom, $subject, $message, 1);
	}
	*/
	
	
	if(empty($validation))
	{
		 $row->store();
		 
		 jbBlogNotifyAdmin($row,$isNew);
		 
		if ($_JB_CONFIGURATION->get('autoapproveblogs'))
		{
			$url = 'index.php?option=com_joomblog&blogid='.$row->id.'&view=blogger&Itemid='.jbGetItemId();
			$ms = JText::_('COM_JOOMBLOG_BLOG_SUCCESSFULLY_SAVED');
		} 
		else 
		{
			$url = 'index.php?option=com_joomblog&task=blogs&Itemid='.jbGetItemId();
			$ms = JText::_('COM_JOOMBLOG_BLOG_SUCCESSFULLY_SAVED_NEED_APPROVE');
		}
		
		$mainframe->redirect(JRoute::_($url,false),$ms);
		
	}else{ 
		$errors			= '';
		foreach( $validation as $error )
		{
			$errors	.= '<div style="margin-bottom: 5px">' . $error . '</div>';
		}

		$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&task=blogs&Itemid='.jbGetItemId(),false),$errors."</br>");
	}
	
	return;
}


function jbfSaveblog()
{
	global $JBBLOG_LANG, $_JB_CONFIGURATION;

	$db	=& JFactory::getDBO();
	$mainframe	=& JFactory::getApplication();
	$my	=& JFactory::getUser();
	$row =& JTable::getInstance( 'Blogs' , 'Table' );
	
	$isNew = true;
	
	$data = JRequest::get('post');
	$metadata_array = array('metakey' => $data['metakey'], 'metadesc' => $data['metadesc'], 'page_image' => $data['page_image'], 'ogdesc' => $data['ogdesc']);
	$metadata_json = json_encode ($metadata_array);
	$data['metadata']= $metadata_json ;	
	$data['fulltext'] = stripslashes($_POST['fulltext']);
	$data['language'] = stripslashes($_POST['language']);


	$row->bind( $data , true);
	
	if($row->id != '0' || $row->id != 0)
		$isNew	= false;

	if ($data['catid'])
		{
			if (sizeof($data['catid']))
			{
				$allcategs = $data['catid'];
				$row->catid = $allcategs[0];
			}
		}
					
	$categoryId	= $row->catid;//$data['catid'];
	
	if($isNew && !jbGetUserCanPost()){
		$mainframe->redirect($_SERVER['HTTP_REFERER'],JText::_('COM_JOOMBLOG_BLOG_ADMIN_NO_PERMISSIONS_TO_POST'));
		return;
	}
	
	
	if(!$my->authorise('core.create', 'com_joomblog.category.'.$categoryId)){
		$mainframe->redirect($_SERVER['HTTP_REFERER'],JText::_('COM_JOOMBLOG_BLOG_ADMIN_NO_PERMISSIONS_TO_POST_IN_CATEGORY'));
		return;
	}

	if($isNew){
		$params = new JRegistry();
		$params->def('show_title', "");
		$params->def('link_titles', "");
		$params->def('show_intro', "");
		$params->def('show_category', "");
		$params->def('link_category', "");
		$params->def('show_parent_category', "");
		$params->def('link_parent_category', "");
		$params->def('show_author', "");
		$params->def('link_author', "");
		$params->def('show_create_date', "");
		$params->def('show_modify_date', "");
		$params->def('show_publish_date', "");
		$params->def('show_item_navigation', "");
		$params->def('show_icons', "");
		$params->def('show_print_icon', "");
		$params->def('show_email_icon', "");
		$params->def('show_vote', "");
		$params->def('show_hits', "");
		$params->def('show_noauth', "");
		$params->def('alternative_readmore', "");
		$params->def('article_layout', "");
		$params->def('alternative_readmore',$data['alternative_readmore']);
		$row->attribs = (string)$params;
	}else{
		$params = new JRegistry();
		$params->loadJSON($row->attribs);
		$params->set('alternative_readmore',$data['alternative_readmore']);
		$row->attribs = (string)$params;
	}
		
	$title = trim($data['title']);
	$validation	= array();

	if( empty( $title ) || $title == JText::_('COM_JOOMBLOG_BLOG_TITLE') )
	{
		$validation['title'] = JText::_('COM_JOOMBLOG_TITLE_IS_REQUIREDD');
	}

	$fulltext = $data['fulltext'];

	if( empty( $fulltext ) )
	{
		$validation['fulltext'] = JText::_('COM_JOOMBLOG_CANNOT_BE_EMPTY');
	}

	if(empty( $categoryId ) )
	{
		$validation['catid'] = JText::_('COM_JOOMBLOG_CATEGORY_MUST_BE_SELECTED');
	}
	
	$createdDate = $data['publish_up'];

	if(isset($createdDate) && !empty($createdDate))
	{
		$date =& JFactory::getDate( $row->publish_up );
		$row->created = $date->toFormat();
	}
	else
	{
		$date =& JFactory::getDate();
		$row->created = $date->toFormat();
	}
	
	if(!$isNew){
		$row->modified = $date->toMySQL();	
		$row->modified_by = $my->id;
	}
	
	$row->publish_up = $row->created;

	$jcStatus		= isset( $data['jcState'] ) && !empty( $data['jcState'] ) ? $data['jcState'] : false;
	$row->fulltext 	= stripslashes($row->fulltext);
	$row->introtext = stripslashes($row->introtext);
	$row->title 	= stripslashes($row->title);
	
	if( $jcStatus !== false )
	{
		if($jcStatus == 'enabled')
		{
			$row->fulltext  .= '{jomcomment}';
		}
		else if($jcStatus == 'disabled')
		{
		    $row->fulltext  .= '{!jomcomment}';
		}
	}
	
	if(empty($validation))
	{
		$row->store();
		$query = 'SELECT id FROM #__joomblog_blogs WHERE content_id = "'.$row->id.'"';
		$db->setQuery($query);
		$exists = $db->loadResult();
		
		if (!$exists){
			$query = 'INSERT INTO #__joomblog_blogs (`id`, `content_id`, `blog_id`) VALUES ("", "'.$row->id.'", "'.$data['blog_id'].'")';
			$db->setQuery($query);
			$db->query();
		} else {
			$query = 'UPDATE #__joomblog_blogs SET blog_id = "'.$data['blog_id'].'" WHERE content_id="'.$row->id.'"';
			$db->setQuery($query);
			$db->query();
		}
		
		$row->load($row->id);

		if( JRequest::getString('tags','') )
		{
			//add tags
			$query	= "DELETE FROM #__joomblog_content_tags WHERE contentid=".$row->id." ";
			$db->setQuery( $query );
			$db->query();
				
			$tags = explode(',',JRequest::getString('tags',''));	

			if( is_array( $tags ) ){
				foreach($tags as $tag){
					$tagid = jbfAddtag($tag);
			
					$query	= "INSERT INTO #__joomblog_content_tags "
							. "(`contentid`,`tag`) VALUES (".$row->id.", $tagid)";
					$db->setQuery( $query );
					$db->query();
				}
			}else{
				$tagid = jbfAddtag($tag);
			
				$query	= "INSERT INTO #__joomblog_content_tags "
						. "(`contentid`,`tag`) VALUES (".$row->id.", $tagid)";
				$db->setQuery( $query );
				$db->query();
			}
		}
		
	/*** MULTI CATS ***/
		if (sizeof($allcategs))
		{
			$query = $db->getQuery(true);
			$query->delete();
			$query->from('#__joomblog_multicats');
			$query->where('aid='.(int)$row->id);
			$db->setQuery($query);
			$db->query();
			
			foreach ( $allcategs as $alc ) 
			{
				$query = $db->getQuery(true);
				$query->insert('#__joomblog_multicats');
				$query->set('aid='.(int)$row->id);
				$query->set('cid='.(int)$alc);
				$db->setQuery($query);
				$db->query();
			}					
		}								
	/*****************/
		
		jbNotifyAdmin($row->id, jbGetAuthorName($row->created_by, $_JB_CONFIGURATION->get('useFullName')), $row->title, $row->introtext . $row->fulltext, $isNew);
		
		jbSortOrder($row);

		$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&show='.$row->id.'&Itemid='.jbGetItemId(),false),JText::_('COM_JOOMBLOG_BLOG_ENTRY_SAVED'));
	}else{ 
		$errors			= '';
		foreach( $validation as $error )
		{
			$errors	.= '<div style="margin-bottom: 5px">' . $error . '</div>';
		}

		$mainframe->redirect(JRoute::_('index.php?option=com_joomblog&task=write&id='.$row->id.'&Itemid='.jbGetItemId(),false),$errors."</br>");
	}
	
	return;
}

function jbfAddtag($newtag){
	$db	=& JFactory::getDBO();
	
	$id = 0;
	
	$newtag	= $db->getEscaped( trim($newtag) );
	
	if(!$newtag) return false;

	$query = "SELECT COUNT(*) FROM `#__joomblog_tags` WHERE `name`='{$newtag}' ";
	$db->setQuery( $query );
	$count_tags = $db->loadResult();

	if($count_tags == 0){
		$query  = "INSERT INTO `#__joomblog_tags` (`name`) VALUES ('{$newtag}')";
		$db->setQuery($query);
		$db->query();
		echo $db->getErrorMsg();
		$id = $db->insertId();
	}else{
		$query 	= "SELECT `id` FROM `#__joomblog_tags` WHERE `name`='{$newtag}'";
		$db->setQuery($query);
		$id = $db->loadResult();
	}
	
	return $id;
}

function isValidMember()
{
	$my	=& JFactory::getUser();
		
	if($my->id == '0')
		return false;
	
	return true;
}

function isEditable($contentId)
{
	$db	=& JFactory::getDBO();
	$my	=& JFactory::getUser();
	$strSQL	= "SELECT `created_by` FROM #__joomblog_posts WHERE `id`='{$contentId}'";
	
	$db->setQuery($strSQL);
	$creator = $db->loadResult();
	
	if($my->id != $creator && $my->id != '42')
		return false;
	
	return true;
}

function jbfTogglepublish()
{
	$mainframe =& JFactory::getApplication(); 
	
	$id = JRequest::getVar('id',0);
	
	$db	=& JFactory::getDBO();	
	$my	=& JFactory::getUser();
	
	if( !jbGetUserCanPublish() || !$my->authorise('core.edit.state', 'com_joomblog.article.'.$id) ){
		$mainframe->redirect($_SERVER['HTTP_REFERER'],JText::_('COM_JOOMBLOG_BLOG_ADMIN_NO_PERMISSIONS_TO_PUBLISH'));
		return;
	}
	
	while (@ ob_end_clean());
	$db->setQuery("SELECT state FROM #__joomblog_posts WHERE id=$id");
	$publish = $db->loadResult();
	
	$publish = intval(!($publish));
	$db->setQuery("UPDATE #__joomblog_posts SET state='$publish' WHERE id=$id");
	$db->query();
	
	$mainframe->redirect($_SERVER['HTTP_REFERER'], JText::_('COM_JOOMBLOG_BLOG_UPDATED'));
	
	return true;
}

function jbfAddvote()
{
	$mainframe =& JFactory::getApplication(); 
	$my	=& JFactory::getUser();
	$db	=& JFactory::getDBO();
	
	$vote = JRequest::getInt('vote',0);
	$id = JRequest::getInt('id',0);
	
	if($vote != 1 && $vote != -1){
		echo '{"msg":"Error"}';
		return;
	}
	$mid = $my->get('id');
	
	//joomplace hack
	//if(!$mid){	if (isset($_COOKIE['bl'])) 	{ $mid=$_COOKIE['bl']; } }
	//
	$registry = new JRegistry();
	
	$msg = "";
	
	$ip = $_SERVER['REMOTE_ADDR'] != getenv('SERVER_ADDR') ? $_SERVER['REMOTE_ADDR'] : getenv('HTTP_X_FORWARDED_FOR');
	
	$query	= 'SELECT id FROM #__joomblog_posts WHERE created_by = '.$mid.' AND id = '.$id;
	$db->setQuery( $query );
	$isOwner = $db->loadResult();
	
	if(!$mid){
		$msg = JText::_("VOTES NOT USER REGISTERED");
	}elseif($isOwner){
		$msg = JText::_("VOTES NOT ADDED");
	}else{
		if($isOwner){
			$msg = JText::_("VOTES NOT ADDED");
		}else

		$query	= 'SELECT * FROM #__joomblog_votes WHERE contentid = '.$id.' AND userid = '.$mid;
		$db->setQuery( $query );
		$isVote = $db->loadObject();
		
		
		if($isVote){
			if($isVote->vote != $vote){
				$vote = $isVote->vote+$vote;
				
				if(!$vote){
					$db->setQuery("DELETE FROM #__joomblog_votes WHERE userid = ".$mid." AND contentid = ".$id);
				}else{
					$db->setQuery("UPDATE #__joomblog_votes SET vote = $vote  WHERE userid = ".$mid." AND contentid = ".$id);
				}
				
				if(!$db->query()){
					$msg = $db->getErrorMsg();
				}else{
					if(!$vote){
						$msg = JText::_("VOTES REMOVED");
					}else{
						$msg = JText::_("VOTES ADDING");
					}
				}
				
				$query	= 'SELECT COUNT(vote) FROM #__joomblog_votes WHERE vote = -1 AND contentid = '.$id;
				$db->setQuery( $query );
				$notlike = $db->loadResult();
				
				$query	= 'SELECT COUNT(vote) FROM #__joomblog_votes WHERE vote = 1 AND contentid = '.$id;
				$db->setQuery( $query );
				$like = $db->loadResult();
				
				$registry->set('sumvote',$like-$notlike);
				
			}else{
				$msg = JText::_("VOTES YET ADDED");
			}
		}else{
			$query	= "INSERT INTO #__joomblog_votes "
				. "(`userid`,`contentid`,`vote`) VALUES (".$mid.", ".$id.", ".$vote.")";
			$db->setQuery( $query );

			if(!$db->query()){
				$msg = $db->getErrorMsg();
			}else{
				$msg = JText::_("VOTES ADDING");
			}
			
			$query	= 'SELECT COUNT(vote) FROM #__joomblog_votes WHERE vote = -1 AND contentid = '.$id;
			$db->setQuery( $query );
			$notlike = $db->loadResult();
			
			$query	= 'SELECT COUNT(vote) FROM #__joomblog_votes WHERE vote = 1 AND contentid = '.$id;
			$db->setQuery( $query );
			$like = $db->loadResult();
			
			$registry->set('sumvote',$like-$notlike);
		}
	}
	
	$registry->set('msg',$msg);
	
	echo (string)$registry;
	
	return;
}

function jbfAddcommentvote()
{
	$mainframe =& JFactory::getApplication(); 
	$my	=& JFactory::getUser();
	$db	=& JFactory::getDBO();
	
	$vote = JRequest::getInt('vote',0);
	$id = JRequest::getInt('id',0);
	
	if($vote != 1 && $vote != -1){
		echo '{"msg":"Error"}';
		return;
	}
	
	$registry = new JRegistry();
	
	$msg = "";
	
	$ip = $_SERVER['REMOTE_ADDR'] != getenv('SERVER_ADDR') ? $_SERVER['REMOTE_ADDR'] : getenv('HTTP_X_FORWARDED_FOR');
	
	if($my->get('id')){
		$query	= 'SELECT id FROM #__joomblog_comment WHERE user_id = '.$my->get('id').' AND id = '.$id;
	}else{
		$query	= 'SELECT id FROM #__joomblog_comment WHERE ip = '.$ip.' AND id = '.$id;
	}
	$db->setQuery( $query );
	$isOwner = $db->loadResult();
	
	if(!$my->get('id')){
		$msg = JText::_("COMMENT VOTES NOT USER REGISTERED");
	}elseif($isOwner){
		$msg = JText::_("VOTES NOT ADDED");
	}else{
		$query	= 'SELECT * FROM #__joomblog_comment_votes WHERE commentid = '.$id.' AND userid = '.$my->get('id');
		$db->setQuery( $query );
		$isVote = $db->loadObject();

		if($isVote){
			if($isVote->vote != $vote){
				$voted = $isVote->vote+$vote;

				if(!$voted){
					$db->setQuery("DELETE FROM #__joomblog_comment_votes WHERE userid = ".$my->get('id')." AND commentid = ".$id);
				}else{
					$db->setQuery("UPDATE #__joomblog_comment_votes SET vote = $voted  WHERE userid = ".$my->get('id')." AND commentid = ".$id);
				}

				if(!$db->query()){
					$msg = $db->getErrorMsg();
				}else{
					if(!$vote){
						$msg = JText::_("VOTES REMOVED");
					}else{
						$msg = JText::_("VOTES ADDING");
					}
				}

				$db->setQuery("UPDATE #__joomblog_comment SET voted = voted + $vote  WHERE id ='$id' ");
				if(!$db->query()){
					$msg = $db->getErrorMsg();
				}
				
				$query	= "SELECT voted FROM #__joomblog_comment WHERE id ='$id' ";
				$db->setQuery( $query );
				
				$registry->set('sumcommentvote',$db->loadResult());
				
			}else{
				$msg = JText::_("VOTES YET ADDED");
			}
		}else{
			$query	= "INSERT INTO #__joomblog_comment_votes "
				. "(`userid`,`commentid`,`vote`) VALUES (".$my->get('id').", ".$id.", ".$vote.")";
			$db->setQuery( $query );

			if(!$db->query()){
				$msg = $db->getErrorMsg();
			}else{
				$msg = JText::_("COMMENT VOTES ADDING");
			}
			
			$db->setQuery("UPDATE #__joomblog_comment SET voted = voted + $vote  WHERE `id`='$id'");
			$db->query();
			
			$query	= 'SELECT voted FROM #__joomblog_comment WHERE id = '.$id;
			$db->setQuery( $query );
			
			$registry->set('sumcommentvote',$db->loadResult());
		}
	}
	
	$registry->set('msg',$msg);
	
	echo (string)$registry;
	
	return;
}

function jbfTogglecommentpublish()
{
	global $JBBLOG_LANG, $_JB_CONFIGURATION;

	$mainframe =& JFactory::getApplication(); 
	$db	=& JFactory::getDBO();
	$my	=& JFactory::getUser();

	$id = JRequest::getVar('id',0);
	
	if($_JB_CONFIGURATION->get('useJomComment')){
		$query	= 'SELECT contentid FROM #__jomcomment WHERE `id`=' . $db->Quote( $id );
		$db->setQuery( $query );
		$cid	= $db->loadResult();
	}else{
		$query	= 'SELECT contentid FROM #__joomblog_comment WHERE `id`=' . $db->Quote( $id );
		$db->setQuery( $query );
		$cid	= $db->loadResult();
	}

	$query	= 'SELECT created_by FROM #__joomblog_posts WHERE `id`=' . $db->Quote( $cid );
	$db->setQuery( $query );

	$author = $db->loadResult();
	
	if($author != $my->id){
		return;
	}
	
	if($_JB_CONFIGURATION->get('useJomComment')){
		$db->setQuery("SELECT published FROM #__jomcomment WHERE `id`='$id'");
		$publish = $db->loadResult();

		$publish = intval(!($publish));
		$db->setQuery("UPDATE #__jomcomment SET published='$publish' WHERE `id`='$id'");
		$db->query();
	}else{
		$db->setQuery("SELECT published FROM #__joomblog_comment WHERE `id`='$id'");
		$publish = $db->loadResult();

		$publish = intval(!($publish));
		$db->setQuery("UPDATE #__joomblog_comment SET published='$publish' WHERE `id`='$id'");
		$db->query();
	}
	
	$mainframe->redirect($_SERVER['HTTP_REFERER']);
	
	return;
}

function jbfCommentapproveall()
{	
	global $JBBLOG_LANG, $_JB_CONFIGURATION;

	$mainframe =& JFactory::getApplication(); 
	$db	=& JFactory::getDBO();
	$my	=& JFactory::getUser();
	
	$strSQL	= "SELECT id FROM #__joomblog_posts WHERE created_by='{$my->id}'";
	$db->setQuery( $strSQL );
	$result	= $db->loadObjectList();
	$rows	= array();
	
	foreach($result as $row)
	{
		$rows[]	= $row->id;
	}
	$rows	= implode(',', $rows);
	
	if($_JB_CONFIGURATION->get('useJomComment')){
		$strSQL	= "UPDATE #__jomcomment SET `published`='1' WHERE `contentid` IN({$rows}) "
				. "AND `published`='0'";
		$db->setQuery($strSQL);
		$db->query();
	}else{
		$strSQL	= "UPDATE #__joomblog_comment SET `published`='1' WHERE `contentid` IN({$rows}) "
				. "AND `published`='0'";
		$db->setQuery($strSQL);
		$db->query();
	}
	
	$itemId	= jbGetItemId();
	$link	= JRoute::_('index.php?option=com_joomblog&task=showcomments&Itemid=' . $itemId , false);
	
	$mainframe->redirect($link, JText::_('COM_JOOMBLOG_UNPBL_COMNTS'));
		
	return;
}

function jbfCommentremoveunpublished()
{
	global $JBBLOG_LANG, $_JB_CONFIGURATION;
	
	$mainframe =& JFactory::getApplication(); 
	$db	=& JFactory::getDBO();
	$my	=& JFactory::getUser();
	
	$strSQL	= "SELECT id FROM #__joomblog_posts WHERE created_by='{$my->id}'";
	$db->setQuery($strSQL);
	$result	= $db->loadObjectList();
	$rows	= array();
	
	foreach($result as $row)
	{
		$rows[]	= $row->id;
	}
	$rows	= implode(',', $rows);
	
	if($_JB_CONFIGURATION->get('useJomComment')){
		$strSQL	= "DELETE FROM #__jomcomment WHERE `published`='0' AND `contentid` IN({$rows})";
		$db->setQuery($strSQL);
		$db->query();
	}else{
		$strSQL	= "DELETE FROM #__joomblog_comment WHERE `published`='0' AND `contentid` IN({$rows})";
		$db->setQuery($strSQL);
		$db->query();
	}

	$link	= JRoute::_('index.php?option=com_joomblog&task=showcomments&Itemid='.jbGetItemId() , false);

	$mainframe->redirect($link, JText::_('COM_JOOMBLOG_UNPBL_COMNTS_REMOVE'));
	
	return ;
}

class Joomblog
{
	var $task;
	var $adminTask ;
	
	function Joomblog()
	{	
		$this->adminTask = array('adminhome', 'edit', 'delete', 'write', 'showcomments', 'bloggerpref', 'bloggerstats', 'media');
	}
	
	function init()
	{
		global $Itemid;
		$task = JRequest::getVar( 'task' , '' , 'REQUEST' );
		if ($task =='mainpage') $task='';
		$this->task	= $task;
		if(JRequest::getVar( 'view' , '' , 'REQUEST' )=='adminhome'){
			$this->task	= 'adminhome';
		}
		
		$view = JRequest::getVar( 'view' , '' , 'GET' );		
		if(empty($this->task))
		{
			$this->task = 'browse';
			
			$show 	= false;
			$show	= JRequest::getVar( 'show' , '' , 'GET' );

			if(!empty($show))
				$this->task = 'show';

			$author	= JRequest::getVar( 'user' , '' , 'GET' );
			
			if (isset($view) && $view == 'user')
			{
				$menu =& JSite::getMenu();
				$item   = $menu->getActive();
				$params   =& $menu->getParams($item->id);
				$user = JFactory::getUser($params->get('user'));
				
				$author = $user->username;
			}
			
			if (isset($view) && $view == 'category')
			{				
				$this->task = 'tag';
			}
			
			if (isset($view) && $view == 'archives')
			{				
				$this->task = 'archive';
			}
			
			if (isset($view) && $view == 'tags')
			{				
				$this->task = 'viewtags';
			}
			
			if(!empty($author) && $show == '')
				$this->task = 'author';
		}		
	}
	 	
	function index()
	{
		if(in_array($this->task, $this->adminTask))
		{
			jimport( 'joomla.filesystem.file' );
			
			$file	= JB_COM_PATH . DS . 'task' . DS . strtolower( $this->task ) . '.php';
			
			if( !JFile::exists($file ) )
			{
				JError::raiseError( 404 , JText::_('COM_JOOMBLOG_INV_TASK' ) );
			}
			require_once( $file );
			
			$cName	= 'Jbblog' . ucfirst($this->task) . 'Task';
			$obj	= new $cName();
			$obj->execute();
		}else{
			$this->browse();
		}
	}
	
	function view()
	{
		$this->show();
	}
	
	function printblog()
	{
		$this->show();
	}
	
	function userblog()
	{
		$my		=& JFactory::getUser();
		
		if ($my->id == "0")
		{
			echo '<div id="fp-content">';
			echo JText::_('COM_JOOMBLOG_LOGIN_TO_VIEW_BLOG');
			echo '</div>';
		}
		else
		{
			echo '<div id="joomBlog-wrap">';
			mb_showViewerToolbar("home");
			$frontview = new MB_Frontview();
			$frontview->attachHeader();
			echo $frontview->browse('userblog');
			echo '</div>';
			echo getPoweredByLink();
		}
	}
	
	function execute()
	{
		global $_JB_CONFIGURATION;

		$mainframe =& JFactory::getApplication(); 
		
		if(in_array($this->task, $this->adminTask))
		{
			$session	= JRequest::getVar( 'session' , '' , 'GET' );
			$my			=& JFactory::getUser();
			
			if ( ($my->id == "0" || !empty($session) )&& $this->task == 'write')
			{
				$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login',false));
				return;
			}
			
			if ($my->id == "0")
			{
				echo '<div id="fp-content">';
				echo JText::_('COM_JOOMBLOG_DONTPERM');
				echo '</div>';
				return;
			}
		}
		
    jimport( 'joomla.filesystem.file' );
    
    $file	= JB_COM_PATH . DS . 'task' . DS . JString::strtolower( $this->task ) . '.php';
    if( JFile::exists( $file ) )
    {
      require_once( $file );
      $cname = 'Jbblog'.ucfirst($this->task).'Task';
      $obj = new $cname();
      if (method_exists($obj,'execute')) $obj->execute();
    }
    else
    {	
      $func = 'jbf'.ucfirst($this->task);
      if (function_exists($func)) {
        call_user_func($func);
      }else{
        echo JText::_('COM_JOOMBLOG_INV_TASK');
      }
    }	
	}
}

$Joomblog = new Joomblog();
$Joomblog->init();
$task = $Joomblog->task;

$Joomblog->execute();	
