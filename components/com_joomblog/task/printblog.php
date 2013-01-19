<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: printblog.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage printblog.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH . DS . 'task' . DS . 'show.base.php' );
require_once( JB_COM_PATH . DS . 'libraries' . DS . 'plugins.php' );

class JbblogPrintblogTask extends JbblogShowBase
{
	var $_plugins	= null;
	var $row = null;
	var $uid = null;
	
	function JbblogPrintblogTask()
	{
		$this->_plugins	= new JBPlugins();
		$this->toolbar = JB_TOOLBAR_HOME;
		
		$db			=& JFactory::getDBO();
		$show		= JRequest::getVar( 'show' , '' , 'GET' );
		$id			= JRequest::getVar( 'id' , '' , 'GET' );
		$this->uid	= (!empty( $show ) ) ? $show : $id;

		$uid		= $this->uid;
		
		if (is_numeric($uid))
		{
			$date	=& JFactory::getDate();
			
			$query	= "SELECT c.*,p.permalink, '". $date->toMySQL() ."' as curr_time, r.rating_sum/r.rating_count as rating, r.rating_count from (#__joomblog_posts as c,#__joomblog_permalinks as p) left outer join #__joomblog_posts_rating as r on (r.content_id=c.id) WHERE c.id=p.contentid and c.id='$uid'";
			$db->setQuery( $query );
			$row	= $db->loadObject();

			if( !$row )
			{
				$row	=& JTable::getInstance( 'Blogs' , 'Table' );
				$row->load( $uid );
			}
		}
		else
		{	
			$uid = stripslashes($uid);
			$uid = urldecode($uid);
			$uid = $db->getEscaped($uid);

			$row	=& JTable::getInstance( 'Blogs' , 'Table' );
			$row->load($uid);
		}
		$this->row = &$row;
	}
	
	function _header()
	{
		return;
	}
	
	function isJbEntry()
	{
		$my		=& JFactory::getUser();
		
		return ($this->row->created_by == $my->id );
	}

	function display($styleid = '', $wrapTag = 'div')
	{
		global $JBBLOG_LANG, $_JB_CONFIGURATION;

		$doc = JFactory::getDocument();
		
		if(!jbAllowedGuestView('entry'))
		{
			$template		= new JoomblogTemplate();
			$content		= $template->fetch($this->_getTemplateName('permissions'));
			return $content;
		}
		
		$Itemid		= jbGetItemId();
		$row		= null;
		$task		= '';
		$task_url	= "";

		if ($task!="")
		{
			$task_url = "&task=$task";
		}
		
		$this->_plugins->load();
		
		$row = &$this->row;

		$row->permalink = jbGetPermalinkUrl($row->id);
		$row->comments = "";
		$mainframe		=& JFactory::getApplication();
		$date			=& JFactory::getDate( $row->created );
		//$date->setOffSet( $mainframe->getCfg( 'offset' ) );

		$row->createdFormatted	= $date->toFormat( $_JB_CONFIGURATION->get('dateFormat') );
		$row->title = jbUnhtmlspecialchars($row->title);
		$row->title = htmlspecialchars($row->title); 
		$db			=& JFactory::getDBO();
		
		$currentDate	=& JFactory::getDate();
		if ($row->state != 1 || $row->publish_up > $currentDate->toMySQL() )
		{
			echo "Cannot find the entry.The user has either change the permanent link or the content has not been published.";
			return;
		}
		else
		{
			$db->setQuery("UPDATE #__joomblog_posts SET hits=hits+1 WHERE id=$row->id");
			$db->query();
		}

		jbAddPageTitle(jbUnhtmlspecialchars($row->title));
		jbAddPathway($row->title);

		if ($mainframe->getCfg('MetaAuthor') == '1')
		{
			//$mainframe->addMetaTag( 'author' , $row->created_by );
			$doc->setMetaData( 'author' , $row->created_by );
		}

		$tags	= jbGetTags($row->id);
		
		foreach($tags as $tag){
			//$mainframe->appendMetaTag('keywords', $tag->name);
			$doc->setMetaData( 'keywords' , ($doc->getMetaData('keywords')?', ':' ').$row->metakey );
		}
		
		//$mainframe->appendMetaTag( 'description', $row->metadesc );
		
		$doc->setDescription( $row->metadesc );
		
		//$mainframe->appendMetaTag( 'keywords', $row->metakey );
		
		$doc->setMetaData( 'keywords' , ($doc->getMetaData('keywords')?', ':' ').$row->metakey );
		
		$user	=& JFactory::getUser();
		
		$tpl = new JoomblogCachedTemplate(serialize($row) . $user->usertype . $_JB_CONFIGURATION->get('template') . $task);

		if (!$tpl->is_cached())
		{
			$row->text	= '';
			
			if($row->introtext && trim($row->introtext) != '')
			{
				$row->text	.= $row->introtext;
			}

			if($_JB_CONFIGURATION->get('anchorReadmore'))
			{
				$tmpFull	= $row->text;
				$tmp		= substr($row->text, 0 , strlen($row->text) - 10);
				$row->text	= $tmp;
				$row->text	.= '<a name="readmore"></a>';
				$row->text	.= substr($tmpFull, strlen($tmpFull) - 10, strlen($tmpFull));					
			}
			
			if($row->fulltext && trim($row->fulltext) != '')
			{
				$row->text	.= $row->fulltext;
			}
			
			$row->text = str_replace(array('{jomcomment lock}','{!jomcomment}','{jomcomment}'),'',$row->text);
			
			$row->author = jbUserGetName($row->created_by, $_JB_CONFIGURATION->get('useFullName'));
			$row->authorLink = JRoute::_("index.php?option=com_joomblog$task_url&user=" . urlencode(jbGetAuthorName($row->created_by)) . "&Itemid=$Itemid");
			$row->categories = jbCategoriesURLGet($row->id, true, $task);
			$row->emailLink = JRoute::_("index2.php?option=com_content&task=emailform&id={$this->uid}");
			$row->jcategory		= '<a href="' . JRoute::_('index.php?option=com_joomblog&task=tag&category=' . $row->catid ) . '">' . jbGetJoomlaCategoryName( $row->catid ) . '</a>';

			$avatar	= 'Jb' . ucfirst($_JB_CONFIGURATION->get('avatar')) . 'Avatar';
			$avatar	= new $avatar($row->created_by);
			
			$row->avatar	= $avatar->get();
			
			if($_JB_CONFIGURATION->get('showBookmarking')){

			}
			
			$row->afterContent = '';
			$row->beforeContent = '';

			$params	= $this->_buildParams();
			$row->beforeContent		= @$this->_plugins->trigger('onBeforeDisplayContent', $row, $params, 0);
			$row->onPrepareContent	= @$this->_plugins->trigger('onPrepareContent', $row, $params, 0);
			$row->afterContent		= "<br/>". @$this->_plugins->trigger('onAfterDisplayContent', $row, $params, 0);
			
			$date					= new JDate( $row->created );
			
			//$date->setOffSet( $mainframe->getCfg('offset') );
			$row->created = $date->toFormat( "%Y-%m-%d %H:%M:%S" );

			if($_JB_CONFIGURATION->get('enableBackLink'))
				$row->afterContent .= jbGetBackLink();

			unset($row->_table);
			unset($row->_key);

			$tpl->set('userId', $user->id);
			$tpl->set('entry', $tpl->object_to_array($row));

		}


		$content = $tpl->fetch_cache(JB_TEMPLATE_PATH . "/admin/printview.html");
		return $content;
	}
}
?>
