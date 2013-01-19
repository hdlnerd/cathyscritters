<?php
/**
* JoomBlog component for Joomla
* @version $Id: install.sql.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage install.sql.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

$db	=& JFactory::getDBO();

$query ="CREATE TABLE IF NOT EXISTS `#__joomblog_privacy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `postid` int(11) NOT NULL,
  `posts` int(10) unsigned NOT NULL,
  `comments` int(10) unsigned NOT NULL,
  `isblog`  TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)";
$db->setQuery($query);
$db->query();

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_tags` (
      `id` int(10) unsigned NOT NULL auto_increment,
      `name` varchar(50) NOT NULL default '',
      `default` int(3) unsigned NOT NULL default '0',
      `slug` varchar(255) NOT NULL,
      PRIMARY KEY  (`id`)
      ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 "; 
$db->setQuery($query);
$db->query();

$query	= 'ALTER TABLE `#__joomblog_tags` ADD INDEX ( `default` )';
$db->setQuery($query);
$db->query();	

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_admin` (
    `sid` varchar(128) NOT NULL,
    `cid` int(10) NOT NULL,
    `date` datetime NOT NULL,
    `type` int(3) unsigned NOT NULL default '0',
    PRIMARY KEY  (`sid`)
  ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 "; 
$db->setQuery($query);
$db->query();

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_config` (
  `name` varchar(64) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`name`)
  ) TYPE=MyISAM; "; 
$db->setQuery($query);
$db->query();

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_content_tags` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `contentid` int(10) unsigned NOT NULL default '0',
  `tag` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
  ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 "; 
$db->setQuery($query);
$db->query();

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_user` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `description` text NOT NULL,
  `title` text NOT NULL,
  `feedburner` text NOT NULL,
  `style` text NOT NULL,
  `params` text NOT NULL,
  `about` text NOT NULL,
  `site` varchar(255) NOT NULL default '',
  `twitter` varchar(255) NOT NULL default '',
  `birthday` date default '0000-00-00',
  `avatar` text NOT NULL,
  PRIMARY KEY  (`user_id`)
  ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 "; 
$db->setQuery($query);
$db->query();

$db->setQuery("ALTER TABLE `#__joomblog_user` ADD `avatar` text NOT NULL");
$db->query();

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_list_blogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `published` int(3) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(10) unsigned NOT NULL,
  `private` int(3) NOT NULL,
  `title` varchar(250) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `metadata` text NOT NULL,
  `metadesc` text NOT NULL,
  `metakey` text NOT NULL,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the jos_assets table.',
  `approved` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
$db->setQuery($query);
$db->query();

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_multicats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
$db->setQuery($query);
$db->query();

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_blogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(10) unsigned NOT NULL,
  `blog_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ";
$db->setQuery($query);
$db->query();

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_modules` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` text NOT NULL,
  `published` int(1) unsigned NOT NULL default '0',
  `ordering` int(10) unsigned NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`)
  ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 "; 
$db->setQuery($query);
$db->query();

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_votes` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) default NULL,
  `contentid` int(11) default NULL,
  `vote` int(3) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 "; 
$db->setQuery($query);
$db->query();

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_comment_votes` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) default NULL,
  `commentid` int(11) default NULL,
  `vote` int(3) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 "; 
$db->setQuery($query);
$db->query();

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_plugins` (
  `published` INT( 1 ) UNSIGNED NOT NULL DEFAULT '0',
  `id` INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY ( `id` )
  ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 "; 
$db->setQuery($query);
$db->query();

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_comment` (
  `id` int(10) NOT NULL auto_increment,
  `parentid` int(11) NOT NULL default '0',
  `user_id` int(11) unsigned NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  `contentid` int(11) NOT NULL default '0',
  `ip` varchar(15) NOT NULL default '',
  `name` varchar(200) default NULL,
  `title` varchar(200) NOT NULL default '',
  `comment` text NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime default '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `email` varchar(100) NOT NULL default '',
  `voted` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `contentid` (`contentid`),
  KEY `published` (`published`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ";
$db->setQuery($query);
$db->query();

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `catid` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `attribs` varchar(5120) NOT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `metadata` text NOT NULL,
  `language` char(7) NOT NULL COMMENT 'The language code for the article.',
  `sectionid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `access` (`access`),
  KEY `state` (`state`),
  KEY `catid` (`catid`),
  KEY `createdby` (`created_by`),
  KEY `language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ";
$db->setQuery($query);
$db->query();

$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_posts_rating` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `rating_sum` int(10) unsigned NOT NULL DEFAULT '0',
  `rating_count` int(10) unsigned NOT NULL DEFAULT '0',
  `lastip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ";
$db->setQuery($query);
$db->query();

//////////////////////////////////////////////////////////////////////////////
///////				Create Uncategorised for JoomBlog					//////
//////////////////////////////////////////////////////////////////////////////

$db->setQuery("SELECT id FROM #__categories WHERE `extension` = 'com_joomblog' AND `title` = 'Uncategorised'");
$exists = $db->loadResult();
if(!$exists)
{
	$date = JFactory::getDate();
	$rules_array = array();
	$rules_array['core.edit.state'] = array();
	$rules_array['core.edit'] = array();
	$rules_array['core.delete'] = array();
		
	$db->setQuery("SELECT MAX(id) from #__categories WHERE extension = 'com_joomblog' AND level = 1 ");
	$max_cat_id = $db->loadResult();
		
	if($max_cat_id){
		$cat_alias = "uncategorised".($max_cat_id+1);
	}else{
		$cat_alias = "uncategorised";
	}
		
	$data = array( 
		'extension'=> "com_joomblog",
		'title'=> "Uncategorised",
		'published'=> "1",
		'access'=> "1",
		'parent_id'=> "0",
		'level'=> "0",
		'path'=> "joomblog",
		'alias'=> $cat_alias,
		'params'=> "{\"category_layout\":\"\",\"image\":\"\"}",
		'metadata'=> "{\"author\":\"\",\"robots\":\"\"}",
		'created_user_id'=> "42",
		'created_time'=> ''.$date->toFormat().'',
		'modified_user_id'=> "0",
		'modified_time'=> "0000-00-00 00:00:00",
		'language'=> "*",
		'rules'=>$rules_array 
	);
		
	$row = JTable::getInstance("Category");
		
	if ($row->parent_id != $data['parent_id'] || $data['id'] == 0) {
		$row->setLocation($data['parent_id'], 'last-child');
	}
		
	$row->bind($data);
	$row->store(true);
		
	$category_id = $row->id;
}

//////////////////////////////////////////////////////////////////////////////
///////				Create JoomBlog Users From Joomla Users				//////
//////////////////////////////////////////////////////////////////////////////


$db->setQuery("SELECT id FROM #__users");
$users = $db->loadObjectList();

$jbuser = array();
$db->setQuery("SELECT user_id FROM #__joomblog_user");
$jbuser = $db->loadResultArray();

$insert = array();
if (!empty($users)){	
	foreach($users as $user)
	{
		if (!in_array($user->id, $jbuser)){
			$insert[] = "(".$user->id.", '', '', '', '', '', '', '', '', '0000-00-00', '')";
		}
	}
	
	$insert_val = implode(',', $insert);
	
	$db->setQuery("INSERT INTO #__joomblog_user VALUES ".$insert_val);
	$db->query();
}

$query	= "ALTER TABLE `#__joomblog_list_blogs` ADD `asset_id` INT UNSIGNED NOT NULL , ADD `approved` TINYINT UNSIGNED NOT NULL DEFAULT '1' ";
$db->setQuery($query);
$db->query();	

$query	= "ALTER TABLE `#__joomblog_privacy` ADD `isblog` TINYINT UNSIGNED NOT NULL";
$db->setQuery($query);
$db->query();	

$query	= "ALTER TABLE `#__joomblog_list_blogs` ADD `alias` VARCHAR( 255 ) NOT NULL AFTER `title` ";
$db->setQuery($query);
$db->query();	
