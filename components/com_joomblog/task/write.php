<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: write.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage write.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class JbblogWriteTask extends JbBlogBaseController{
	
	function _header(){
		
	}
	
	function _footer(){
	}
	
	function display()
	{
		global $_JB_CONFIGURATION, $Itemid;

		$mainframe	=& JFactory::getApplication();
		$my	=& JFactory::getUser();
		$doc =& JFactory::getDocument();
		
		$pathway =& $mainframe->getPathway();

		$tpl = new JoomblogTemplate();

		$id	= JRequest::getVar('id','','GET');
		$postid	= JRequest::getVar('id',0,'POST');

		if(!empty($postid)){
			$id = $postid;
		}

		$row =& JTable::getInstance( 'Blogs' , 'Table' );
		$row->load($id);

		$isNew	= true;

		if($id) 
		{
		$isNew	= false;
		}
		if($isNew && !jbGetUserCanPost()){
			$mainframe->redirect($_SERVER['HTTP_REFERER'],JText::_('COM_JOOMBLOG_BLOG_ADMIN_NO_PERMISSIONS_TO_POST'));
			return;
		}

		if(!$isNew && (!$my->authorise('core.edit', 'com_joomblog.article.'.$id ))){
			
			if($row->created_by == $my->get('id') && (!$my->authorise('core.edit.own', 'com_joomblog.article.'.$id ))){
				$mainframe->redirect($_SERVER['HTTP_REFERER'],JText::_('COM_JOOMBLOG_BLOG_ADMIN_NO_PERMISSIONS_TO_POST'));
				return;
			}
		}

		if($isNew){
		  $pathway->addItem(JText::_( 'COM_JOOMBLOG_NEW_ENTRIES'),'');
		  jbAddPageTitle(JText::_( 'COM_JOOMBLOG_NEW_ENTRIES'));
		}else{
		  $pathway->addItem($row->title,JRoute::_('index.php?option=com_joomblog&show='.$row->id.'&Itemid='.$Itemid));
		  $pathway->addItem(JText::_('COM_JOOMBLOG_EDIT_ENTRIES'),'');
		  jbAddPageTitle(JText::_( 'COM_JOOMBLOG_EDIT_ENTRIES'));
		}

		if(!jbGetUserCanPublish()){
		    $row->state = 0;
		}

		$userCreateTag  = (boolean) $_JB_CONFIGURATION->get('enableUserCreateTags');
		
		$userImageUpload	= (boolean) $_JB_CONFIGURATION->get('useImageUpload');

		$my	=& JFactory::getUser();

		if($my->id == '42')
		{
		    $userCreateTag  = true;
		}
		
		$db	=& JFactory::getDBO();
		if($row->id != 0){
			$query	= "SELECT b.name FROM #__joomblog_content_tags AS a, #__joomblog_tags AS b "
					. "WHERE b.id=a.tag AND a.contentid='{$row->id}' ";
			$db->setQuery( $query );

			$arr = $db->loadResultArray();
			if($arr){
        $tags = count($arr)>1?implode(",",$arr):$arr[0];
			}else{
        $tags = "";
			}
		}elseif($_JB_CONFIGURATION->get('allowDefaultTags')){
			$query	= "SELECT t.name FROM #__joomblog_tags as t WHERE t.default = 1 ";
			$db->setQuery( $query );

			$arr = $db->loadResultArray();
			if($arr){
        $tags = count($arr)>1?implode(",",$arr):$arr[0];
			}else{
        $tags = "";
			}
		}

		if( !empty( $row->created ) ) {
			$date =& JFactory::getDate( $row->created );
		}else{
			$date =& JFactory::getDate();
		}

		//$date->setOffSet( $mainframe->getCfg( 'offset' ) );
		
		$jcDashboard    = false;
		$enableDashboard    = $_JB_CONFIGURATION->get('enableJCDashboard');
		$enableJC           = $_JB_CONFIGURATION->get('useComment');
		$jcFile				= JPATH_ROOT . DS . 'components' . DS . 'com_jomcomment' . DS . 'jomcomment.php';
				
		if($enableDashboard && $enableJC && file_exists($jcFile))
			$jcDashboard    = true;
				
		$validation_msg = array();
		$message = "";
		
		$saving		= JRequest::getVar( 'saving' , '' , 'POST' );

		$readmoreTag = '<hr id="system-readmore" />';
		$readmore = (!empty($row->introtext) && !empty($row->fulltext))? $readmoreTag : '';
		$row->fulltext = $row->introtext . $readmore. $row->fulltext; 
		
		$jcState = 'disabled';
		if($jcDashboard){
			if(stristr($row->fulltext, '{jomcomment}') !== false){
				$jcState = 'enabled';
				$row->fulltext = str_replace('{jomcomment}','',$row->fulltext);
			}else if(stristr($row->fulltext, '{!jomcomment}') !== false){
				$jcState = 'disabled';
				$row->fulltext = str_replace('{!jomcomment}','',$row->fulltext);
			}else{
				$jcState = 'default';
			}
		}

		$tpl->set('imageUpload', $userImageUpload);
		$tpl->set('userCreateTag',$userCreateTag);
		$tpl->set('jcState',$jcState);
		$tpl->set('jcDashboard',$jcDashboard);
		$tpl->set('validation_msg', $validation_msg);
		$tpl->set('date', $date->toFormat() );
		$tpl->set('publishRights', jbGetUserCanPublish());
		$tpl->set('publishStatus', true);
		$tpl->set('disableReadMoreTag' , $_JB_CONFIGURATION->get('disableReadMoreTag') );
        
        $db->setQuery('SELECT publish_up FROM #__joomblog_posts WHERE id=' . $id);
        $publish_up = $db->loadResult();
       
        // if ($publish_up == '') $publish_up = JFactory::getDate()->toSql();
        if ($publish_up == '')
        {
        	$date =& JFactory::getDate();
        	$publish_up = $date->toFormat();
        	
        }
        $db->setQuery('SELECT publish_down FROM #__joomblog_posts WHERE id=' . $id);
        $publish_down = $db->loadResult();
        
        $tpl->set('publish_up', $publish_up);
        $tpl->set('publish_down', $publish_down);
		
		
		$db->setQuery('SELECT blog_id FROM #__joomblog_blogs WHERE content_id='.$id);
		$blog_id = $db->loadResult();
		$blog_id = ($blog_id)?$blog_id:0;
		
		$blogs = jbGetBlogsListPrivate($my->id);$options = array();
		
		$options[] = JHTML::_('select.option',  0, JText::_('COM_JOOMBLOG_FORM_SELECT_A_BLOG_LIST'));
		foreach($blogs as $blog)
		{
			$user = JFactory::getUser($blog->user_id);
			$blog->title = ($blog->title!='')?$blog->title:$user->username."'s blog";
			$options[] = JHTML::_('select.option',  $blog->id, $blog->title );
		}
		
		$bloglist = JHTML::_('select.genericlist',   $options, 'blog_id', 'class="selectlist"', 'value', 'text', $blog_id );
		
		$contentcat = '<label><strong>Category</strong><select name="esrt" id="esrt">';
		$cats = jbGetCategoryList($_JB_CONFIGURATION->get('postSection'));
		foreach($cats as $c){
		  $contentcat .= '<option value="'.$c->id.'">'.$c->title.'</option>';
		}	
		$contentcat .= '</select></label>';
			
		$row->id = intval($row->id);
		if($row->id == 0)
			$row->state = $_JB_CONFIGURATION->get('defaultPublishStatus');
		$meta_array = json_decode($row->metadata);
		($meta_array->metakey !== '') ? $tpl->set('metakey', $meta_array->metakey) : $tpl->set('metakey', $row->metakey);
		($meta_array->metadesc !== '') ? $tpl->set('metadesc', $meta_array->metadesc) : $tpl->set('metadesc', $row->metadesc);
		$tpl->set('page_image', $meta_array->page_image);
		$tpl->set('ogdesc', $meta_array->ogdesc);
		$tpl->set('state', JHTML::_('select.booleanlist', 'state', '', $row->state));
		$tpl->set('categories', $contentcat);
		//$tpl->set('blogs', $lblogs);
		$tpl->set('videobot',$_JB_CONFIGURATION->get('enableAllVideosBot'));
		$tpl->set('tags', isset($tags)?$tags:'');
		$tpl->set('fulltext', $row->fulltext);
		$tpl->set('id', $row->id);
		$tpl->set('title', $row->title);
		$tpl->set('use_mce', $_JB_CONFIGURATION->get('useMCEeditor') ? "true" : "false");

		$registry = new JRegistry;
		$registry->loadJSON($row->attribs);
		$attribs = $registry->toArray();
		
		$tpl->set('alternative_readmore', @$attribs['alternative_readmore']?$attribs['alternative_readmore']:$_JB_CONFIGURATION->get('readMoreLink'));
		
		require_once(JB_LIBRARY_PATH.DS.'plugins.php');
	
		$plugins = new JBPlugins();
		$plugins->init('editors-xtd');
		$plugins_rows = $plugins->get(0, 999, 'editors-xtd');

		$not_use_editors_xtd = array();
		if($plugins_rows){
			foreach($plugins_rows as $value){
				if(!$value->published)
					$not_use_editors_xtd[] = $value->element;
			}
		}

		$tpl->set('editorsxtd',$not_use_editors_xtd);

		jimport('joomla.form.form');

		$form = new JForm('languageform');
		//$form->loadFile(JB_ADMIN_COM_PATH . DS . 'form.xml');
		//$form->setValue("jblanguage", null, $row->language);
		
//		$formlist = array();
//		$formlist['input']= $form->getInput("jblanguage");
//		$formlist['label']= $form->getLabel("jblanguage");

		$form->loadFile(JB_ADMIN_COM_PATH . DS . 'models'.DS.'forms'.DS.'post.xml');
		$form->setValue("language", null, $row->language);
		$formlist = array();
		$formlist['input']= $form->getInput("language");
		$formlist['label']= $form->getLabel("language");
		
		$tpl->set('form',$formlist);
		
		/*CATFORM*/
		$form = new JForm('catform');
		$form->loadFile(JB_ADMIN_COM_PATH . DS . 'models'.DS.'forms'.DS.'post.xml');
		$form->setValue("catid", null, $cats);
		$formlist = array();
		$formlist['input']= $form->getInput("catid");
		$formlist['label']= $form->getLabel("catid");
		$tpl->set('catform',$formlist);
		
		
		//$form->setValue("blog_id", null, $blog_id);
		$tpl->set('blogslist',$bloglist);
		/**/
		
		$query = "SELECT id FROM #__assets WHERE name = 'com_joomblog' ";
		$db->setQuery($query);
		$assets = $db->loadResult();
		$tpl->set('assets', $assets);
		$tpl->set('author', $my->get('id'));

		$sections   = $_JB_CONFIGURATION->get('managedSections');

		$query = "SELECT cid FROM #__joomblog_multicats WHERE aid = ".$row->id;
		$db->setQuery($query);
		$selCat = $db->loadResultArray();

		//$query = 'SELECT * FROM #__categories WHERE parent_id IN (' . $sections . ') AND published = 1 ';
		//$query = 'SELECT * FROM #__categories WHERE extension ="com_joomblog" AND published = 1 ';
		/*
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

			$query->select('DISTINCT(mc.cid), c.title, c.id');
			$query->from('#__joomblog_multicats AS mc');
			$query->join('LEFT','#__categories AS c ON c.id=mc.cid');
			$db->setQuery($query);

		$categories = $db->loadObjectList();

		$cats = '<select id="catid" name="catid[]" size="3" multiple="multiple" style="height:50px;">';
		if (sizeof($categories))
		foreach($categories as $row){
			$rowName	= $row->title;
			if ($rowName)
			{
				if(in_array($row->id, $selCat)){
					$cats    .= '<option value="' . $row->id . '" selected="selected">' . $rowName . '</option>';
				}else{
					$cats    .= '<option value="' . $row->id . '">' . $rowName .'</option>';
				}
			}
		}
		$cats   .= '</select>';		
		$tpl->set('cats', $cats);
		*/
		/*Privacy settings*/
		$rules = jbGetCurrentPrivacy($id);
		$privacy = jbGetPrivacyList((isset($rules->posts)?$rules->posts:0), 'viewpostrules');
		$tpl->set('postprivacy', $privacy);
		
		$privacy = jbGetPrivacyList((isset($rules->comments)?$rules->comments:0), 'viewcommrules');
		$tpl->set('commentprivacy', $privacy);
		/**/
		
			
		$user=& JTable::getInstance( 'Blogs' , 'Table' );
		$user->load( $my->id );
		
		$doc->addStyleSheet(rtrim( JURI::base() , '/' )."/components/com_joomblog/css/style.css");
		
		
		$html = $tpl->fetch(JB_TEMPLATE_PATH."/admin/write.html");
		
		$html = str_replace("src=\"icons", "src=\"" . rtrim( JURI::base() , '/' ) . "/components/com_joomblog/templates/admin/icons", $html);
	
		echo $html;
	}
}
