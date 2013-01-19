<?php
/**
* JoomBlog component for Joomla 1.6 & 1.7
* @version $Id: tagslist.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage tagslist.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

include_once(JB_COM_PATH . '/task/base.php');

class JbblogTagslistTask extends JbblogBaseController
{	
	function JbblogTagslistTask()
	{
		$this->toolbar = JB_TOOLBAR_TAGS;
	}
	
	function display($styleid = '', $wrapTag = 'div')
	{
		$mainframe	=& JFactory::getApplication();
		
		if(empty($styleid))
		{
			jbAddPageTitle( JText::_('COM_JOOMBLOG_SHOW_TAGS_TITLE') );
		}
		
		if(JRequest::getVar('task') == 'tagslist'){
			jbAddPathway( JText::_('COM_JOOMBLOG_SHOW_TAGS_TITLE') );
		}
		
		$subWrap = 'li';
		if($wrapTag == 'ul')
		{
			$subWrap= 'li';
		}
		else
		{
			$subWrap = '';
		}

		$blogger	= JRequest::getVar( 'user' , '' , 'GET' );
		$mbItemid	= jbGetItemId();
		$content = '<'.$wrapTag.' class="blog-tags" '.$styleid.'>';
		$query = "SELECT '' AS slug, c.name, count(c.name) frequency FROM #__joomblog_tags c,#__joomblog_content_tags c2 where c.id=c2.tag GROUP BY c.name ORDER BY frequency ASC";
		$categoriesArray = jbGetTagClouds($query, 8);
		$categories = "";
		
		if ($categoriesArray)
		{
			foreach ($categoriesArray as $category)
			{
				$catclass = "tag" . $category['cloud'];
				$catname = $category['name'];
				$tagSlug	= $category['slug'];
				$tagSlug	= ($tagSlug == '') ? $category['name'] : $category['slug'];
				$tagSlug	= urlencode($tagSlug);
				// replace ampersands
				$tagSlug = str_replace('&', '-and-', $tagSlug);
				$tagSlug = str_replace('%26', '-and-', $tagSlug);
			
				if(!empty($subWrap))
				{
					$categories .= "<{$subWrap} class=\"$catclass\">";
					
					if(isset($blogger) && !empty($blogger))
					{
						$categories .= "<a href=\"" . JRoute::_("index.php?option=com_joomblog&tag=" . $tagSlug . "&user=$blogger&Itemid=$mbItemid") . "\">$catname</a> ";
					} else {
						$categories .= "<a href=\"" . JRoute::_("index.php?option=com_joomblog&task=tag&tag=" . $tagSlug . "&Itemid=$mbItemid") . "\">$catname</a> ";
					}			
					$categories .= "</$subWrap>";
				}
				else
				{
					if(isset($blogger) && !empty($blogger))
					{
						$categories .= "<a class=\"$catclass\" href=\"" . JRoute::_("index.php?option=com_joomblog&tag=" . $tagSlug . "&user=$blogger&Itemid=$mbItemid") . "\">$catname</a> ";
					}
					else
					{
						$categories .= "<a class=\"$catclass\" href=\"" . JRoute::_("index.php?option=com_joomblog&task=tag&tag=" . $tagSlug . "&Itemid=$mbItemid") . "\">$catname</a> ";
					}
					
				}
			}
		}

		$content .= trim($categories, ",");
		$content .= "</{$wrapTag}>";
		return $content;
	}
	
}
