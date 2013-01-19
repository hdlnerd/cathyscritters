<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: rss.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage rss.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once( JB_COM_PATH.DS.'task'.DS.'base.php' );
require_once( JB_COM_PATH.DS.'libraries'.DS.'feedcreator.php');
require_once( JB_COM_PATH.DS.'libraries'.DS.'datamanager.php');

class JbblogRssTask extends JbblogBaseController
{
 	
	function display()
	{
		header('Content-type:application/xml');
		$author = "";
		$category = "";
		$search = "";
		$archive = "";
		$jcategory = "";
		
		$blogger		= JRequest::getVar( 'user' , '' , 'REQUEST' );
		$category		= JRequest::getVar( 'category' , '' , 'REQUEST' );
		$keyword		= JRequest::getVar( 'keyword' , '' , 'REQUEST' );
		$archive		= JRequest::getVar( 'archive' , '' , 'REQUEST' );
		
		if( !empty( $blogger ) )
		{
			if (is_string( $blogger ) )
			{
				$author = jbGetAuthorId( urldecode( $blogger ) );
			}
			else
			{
				$author = intval( urldecode( $blogger ) );
			}
		}
		
		if( !empty($category) && is_numeric($category))
		{
			$jcategory	= $category;
			$category	= '';
		}
		else if( !empty( $category ) && !is_numeric( $category ) )
		{
			$category = urldecode(htmlspecialchars( $category ));
		}
		
		if( !empty( $keyword ) )
		{
			$search = urldecode(htmlspecialchars( $keyword ));
		}
		
		if( !empty( $archive ) )
		{
			$archive = urldecode(htmlspecialchars( $archive ));
		}
		
		$this->_rss($author, $category, $jcategory , $search, $archive);
		exit;
	}
	
	function _rss($bloggerID = "", $tags = "", $category = '',  $keywords = "", $archive="")
	{

		global $JBBLOG_LANG, $_JB_CONFIGURATION, $Itemid;
		
		$mainframe	=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		
		if (!$_JB_CONFIGURATION->get('useRSSFeed') or $_JB_CONFIGURATION->get('useRSSFeed') == "0")
		{
			echo '<error>';
			echo JText::_('COM_JOOMBLOG_RSS_FEED_NOT_ENABLED');
			echo '</error>';
			return;
		}
		
		$blogger_username = "";
		
		if ($bloggerID != "")
		{
			$query	= "SELECT * from #__users WHERE id='$bloggerID'";
			$db->setQuery( $query );

			$blogger	= $db->loadObjectList();
			
			if ($blogger)
			{
				$blogger = $blogger[0];
				$blogger_username = ($_JB_CONFIGURATION->get('useFullName')=="1" ? $blogger->name :$blogger->username);
			}
			else
			{
				$blogger_username = "";
			}
		}
		
		if( !empty( $archive ) )
		{
			$archive = urldecode($archive);
			$archive = date("Y-m-d 00:00:00", strtotime($archive."-01"));
		}
		
		$rss = new RSSCreator20();

		$rssLimit	= ( $_JB_CONFIGURATION->get('rssFeedLimit') != 0 ) ? (int) $_JB_CONFIGURATION->get('rssFeedLimit') : 20;

		$searchby = array('limit' => $rssLimit,
				'limitstart' => 0,
				'authorid' => $bloggerID,
				'category' => $tags,
				'jcategory'	=> $category,
				'search' => $keywords,
				'archive' => $archive,
				);
	
		$entries = mb_get_entries($searchby);
		$total = $searchby['total'];
		
		if(!class_exists('JoomblogTemplate'))
		{
			require_once( JB_COM_PATH.DS.'template.php' );
		}
		    
		$tpl = new JoomblogCachedTemplate(serialize($entries) . "_rss" . strval($bloggerID) . strval($tags) . strval($keywords) , strval($archive));
		
		if (!$tpl->is_cached()) 
		{
			$title	= JText::_('COM_JOOMBLOG_RSS_FEED_PAGE_TITLE');
			
			if( isset( $blogger_username ) && !empty( $blogger_username ) )
			{
				$title	.= ' ' . JText::sprintf( 'COM_JOOMBLOG_RSS_FEED_PAGE_TITLE_FROM' , $blogger_username );
			}

			if( isset( $tags ) && !empty( $tags ) )
			{
				$title	.= ' ' . JText::sprintf( 'COM_JOOMBLOG_RSS_FEED_PAGE_TITLE_TAGGED' , $tags );
			}

			if( isset( $category ) && !empty( $category ) )
			{
				$title	.= ' ' . JText::sprintf('COM_JOOMBLOG_RSS_FEED_PAGE_TITLE_CATEGORY', jbGetJoomlaCategoryName( $category ) );
			}
			
			if( isset( $keywords ) && !empty( $keywords ) )
			{
				$title	.= ', ' . JText::sprintf( 'COM_JOOMBLOG_RSS_FEED_PAGE_TITLE_BLOGGER_KEYWORDS' , $keywords );
			}

			if ($archive and $archive != "")
			{
				$archive_display	= date("F Y", strtotime($archive));
				$title 				.= " - $archive_display";
			}
			
			$db->setQuery("SELECT description from #__joomblog_user WHERE user_id='$bloggerID'");
			$description = $db->loadResult();
			
			if (!$description or $description == "")
				$description = "$title";
			
			// remove readmore tag
			$description = str_replace('{readmore}', '', $description);	
				
			$rss->title 		= $title;
			$rss->description	= $description;
			$rss->encoding		= 'UTF-8';
			$rss->link			= rtrim( JURI::base() , '/' );
			$rss->cssStyleSheet = NULL;
			
			if ($entries)
			{
				$count = 0;
				
				foreach($entries as $row)
				{
					$count++;
					
					if ($count > $rssLimit)
					{
						break;
					}

					$item = new FeedItem();
					$item->title = $row->title != "" ? $row->title : "...";
					$item->title = jbUnhtmlspecialchars($item->title);
					
					if( empty( $row->fulltext ) )
					{
						$itemDesc	= ( !empty( $row->introtext ) ) ? $row->introtext : JText::_('COM_JOOMBLOG_NO_BLOG_DESCRIPTION');
					}
					else
					{
						$itemDesc	= $row->introtext . $row->fulltext;
					}
					$desc_length_max = 500;
					
					$itemDesc 			= strip_tags($itemDesc, '<p> <br /> <br/> <br> <u> <i> <b> <img>');
					$actualDescLength	= JString::strlen($itemDesc);
					$itemDesc			= JString::substr($itemDesc, 0, $desc_length_max);
					$itemDesc			= preg_replace("/\r\n|\n|\r/", "<br/>", $itemDesc);
					$itemDesc = str_replace(array('{jomcomment lock}','{!jomcomment}','{jomcomment}'),'',$itemDesc);
					
					if ($actualDescLength > $desc_length_max)
					{
						$itemDesc .= JText::_('COM_JOOMBLOG_READMORE');
					}

					$itemDesc			= str_replace('{readmore}', '', $itemDesc);
					$itemDesc			= str_replace('alt=&quot;Listenin', '', $itemDesc);
					$item->description	= $itemDesc;

					$item->link			= html_entity_decode(  $row->permalink  );
					$date				= new JDate( $row->created );
					
					//$date->setOffset( $mainframe->getCfg( 'offset' ) );

					$item->date			= $date->toRFC822( true );
					$item->author		= null;// jbGetAuthorName($row->created_by , '1');
					
					$categoriesList 	= jbGetTags($row->id);
					
					$extraElements 		= array ();
					
					if ($categoriesList)
					{
						$categories = "";
						$indentString = " ";
						
						foreach ($categoriesList as $category)
						{
							$categoryName = $category->name ;
							
							if ($categories != "")
							{
								$categories .= "</category>\n$indentString<category>";
							}
								
							$categories .= $categoryName;
						}
						
						$extraElements['category'] = $categories;
						//$item->author = '<dc:creator>'.$item->author.'</dc:creator>';
						$item->additionalElements = $extraElements;
					}
					
					$rss->addItem($item);
				}
			}
			
			$tpl->set('rss', $rss->createFeed());
		}
		
		$rsscontent = $tpl->fetch_cache(JB_TEMPLATE_PATH . "/admin/rss.tmpl.html");
		while (@ob_end_clean());
		
		echo $rsscontent;
		exit;
	}
}
	
