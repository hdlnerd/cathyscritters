<?php
/**
* JoomBlog component for Joomla
* @version $Id: install.joomblog.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage install.joomblog.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

define( "SITE_ROOT_PATH", JPATH_ROOT );

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.archive' );

class com_joomblogInstallerScript
{
        /**
         * method to install the component
         *
         * @return void
         */
        function install($parent) 
        {

        }
 
        /**
         * method to uninstall the component
         *
         * @return void
         */
        function uninstall($parent) 
        {
			  $db	=& JFactory::getDBO();
			  $db->setQuery("DELETE FROM #__menu WHERE path = 'joomblog' AND type = 'component' AND client_id = 1 AND menutype = 'main' AND link LIKE 'index.php?option=com_joomblog%' ");
			  $db->query();
			  echo '<p>Component JoomBlog successfully uninstalled</p>';
			 
			//
        }
 
        /**
         * method to update the component
         *
         * @return void
         */
        function update($parent) 
        {
			$db		=& JFactory::getDBO();
			
			$db->setQuery("SELECT `params` FROM `#__extensions` WHERE element='com_joomblog'");
			$config = $db->loadResult();
			
			if (!$config || $config=='{}' || $config=='[]')
			{
			$db->setQuery("SELECT `value` FROM #__joomblog_config ");
			$configString = $db->loadResult();
			
			$confa = $configArray = array();
			if ($configString) $configArray = explode(';',$configString);
			foreach ( $configArray as $config ) 
			{
				$name  = substr(trim($config),1,strpos($config,'="')-1);
				$name = str_replace('="','',$name);
				$name = str_replace('=','',$name);
				$value = substr(trim($config),strpos(trim($config),'="')+2,-1);
				$confa[$name] = $value;
			}
			$config = json_encode($confa);
			}
			
			$db->setQuery("UPDATE #__extensions SET params=".$db->Quote($config)." WHERE element='com_joomblog'");
			$db->query();	
			
			
			
			 //ver 1.0.3
			  
				$query = "CREATE TABLE IF NOT EXISTS `#__joomblog_multicats` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `aid` int(10) unsigned NOT NULL,
				  `cid` int(10) unsigned NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
				$db->setQuery($query);
				$db->query();
				
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
				
				$query	= "ALTER TABLE `#__joomblog_list_blogs` ADD `asset_id` INT UNSIGNED NOT NULL , ADD `approved` TINYINT UNSIGNED NOT NULL DEFAULT '1' ";
				$db->setQuery($query);
				$db->query();	

				$query	= "ALTER TABLE `#__joomblog_privacy` ADD `isblog` TINYINT UNSIGNED NOT NULL";
				$db->setQuery($query);
				$db->query();	

				$query	= "ALTER TABLE `#__joomblog_list_blogs` ADD `alias` VARCHAR( 255 ) NOT NULL AFTER `title` ";
				$db->setQuery($query);
				$db->query();
				
				
				$query = "SELECT * FROM `#__joomblog_list_blogs` WHERE id NOT IN (SELECT postid FROM `#__joomblog_privacy` WHERE `isblog`=1 )";
				$db->setQuery( $query );
				$bids = $db->loadResultArray();
				if (sizeof($bids))
				{
					foreach($bids as $bid)
					{
						$query = "INSERT INTO `#__joomblog_privacy` (`id` ,`postid` ,`posts` ,`comments` ,`isblog`) VALUES (NULL , '".$bid."', '0', '0', '1');";
						$db->setQuery( $query );
						$db->query();
					}
				}
				
								
				//**Tags fix*/
				$query = "ALTER TABLE #__joomblog_tags DROP INDEX `name`";
				$db->setQuery( $query );
				$db->query();

				//**Content table fix*/
				$query = "ALTER TABLE #__joomblog_posts ADD `sectionid` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' ";
				$db->setQuery( $query );
				$db->query();

				//**blogs table fix*/
				$query = "ALTER TABLE `#__joomblog_list_blogs` ADD `metadesc` text NOT NULL";
				$db->setQuery( $query );
				$db->query();


				
				/*** BLOG SEF FIX ***/
					$query = "SELECT `id`,`title` FROM `#__joomblog_list_blogs` WHERE `alias`=''";
					$db->setQuery( $query );
					$bids = $db->loadObjectList();
					if (sizeof($bids))
					{
						foreach($bids as $bid)
						{
							$alias = JFilterOutput::stringURLSafe($bid->title);
							$query = "UPDATE `#__joomblog_list_blogs` SET `alias` ='".$alias."'  WHERE `id` =".$bid->id." LIMIT 1 ";
							$db->setQuery($query);
							$db->query();
						}
					}
			
				/*** ***/
        }
 
        /**
         * method to run before an install/update/uninstall method
         *
         * @return void
         */
        function preflight($type, $parent) 
        {

		$pathes = $files =array();
			
			$pathes[]= SITE_ROOT_PATH . "/administrator/components/com_joomblog/controllers";
			$pathes[]= SITE_ROOT_PATH . "/administrator/components/com_joomblog/helpers";
			$pathes[]= SITE_ROOT_PATH . "/administrator/components/com_joomblog/images";
			$pathes[]= SITE_ROOT_PATH . "/administrator/components/com_joomblog/install";
			$pathes[]= SITE_ROOT_PATH . "/administrator/components/com_joomblog/models";
			$pathes[]= SITE_ROOT_PATH . "/administrator/components/com_joomblog/views";
			$pathes[]= SITE_ROOT_PATH . "/components/com_joomblog/css";
			//$pathes[]= SITE_ROOT_PATH . "/components/com_joomblog/images";
			$pathes[]= SITE_ROOT_PATH . "/components/com_joomblog/js";
			$pathes[]= SITE_ROOT_PATH . "/components/com_joomblog/libraries";
			$pathes[]= SITE_ROOT_PATH . "/components/com_joomblog/tables";
			$pathes[]= SITE_ROOT_PATH . "/components/com_joomblog/task";
			$pathes[]= SITE_ROOT_PATH . "/components/com_joomblog/templates";
			$pathes[]= SITE_ROOT_PATH . "/components/com_joomblog/views";
			
			if (sizeof($pathes))
			foreach ($pathes as $path)
			{
				if (JFolder::exists($path)) JFolder::delete($path);
			}
			

			$files[]= SITE_ROOT_PATH . "/administrator/components/com_joomblog/admin.joomblog.html.php";
			$files[]= SITE_ROOT_PATH . "/administrator/components/com_joomblog/admin.joomblog.php";
			$files[]= SITE_ROOT_PATH . "/administrator/components/com_joomblog/toolbar.joomblog.php";
			
			$files[]= SITE_ROOT_PATH . "/components/com_joomblog/defines.joomblog.php";
			$files[]= SITE_ROOT_PATH . "/components/com_joomblog/functions.joomblog.php";
			$files[]= SITE_ROOT_PATH . "/components/com_joomblog/joomblog.php";
			$files[]= SITE_ROOT_PATH . "/components/com_joomblog/template.php";
			
			if (sizeof($files))
			foreach ($files as $file)
			{
				if (is_file($file)) 	JFile::delete($file);
			}
			
        }
 
        /**
         * method to run after an install/update/uninstall method
         *
         * @return void
         */
        function postflight($type, $parent)
        {
			$mainframe	=& JFactory::getApplication();
			
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_joomblog' . DS . 'install.sql.php' );
			
			/*ver 1.0.7 */
			$db		=& JFactory::getDBO();
			$db->setQuery("ALTER TABLE `#__joomblog_privacy` ADD `jsviewgroup` INT UNSIGNED NOT NULL , ADD `jspostgroup` INT UNSIGNED NOT NULL ");
			$db->query();
			/**/
						
			$this->_createAvatarFolder();
			$this->_FixMenuLinks();
			$this->_ExtractFiles();
			$this->_FixDBTables();
			$this->_UpdateAssetCategories();
			$this->_ImportOldContent();
			//$this->_UpdateRulesComponent();
        
        ?>
			<font style="font-size:2em; color:#55AA55;" >JoomBlog component successfully installed. <font color=red>We strongly recommend upgrading all the plugins and modules to ensure the correct performance of the extension.</font></font><br/><br/>
			<table border="1" cellpadding="5" width="100%" style="background-color: #F7F8F9; border: solid 1px #d5d5d5; width: 100%; padding: 10px; border-collapse: collapse;">		
				<tr>
					<td colspan="2" style="background-color: #e7e8e9;text-align:left; font-size:16px; font-weight:400; line-height:18px "><strong><img src="<?php echo JURI::base(); ?>components/com_joomblog/assets/images/tick.png"> Getting started.</strong> Helpfull links:</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-left:20px">
						<div style="font-size:1.2em">
							<ul>
								<li><a href="<?php echo JURI::base(); ?>index.php?option=com_joomblog&view=sampledata">Install Sample Data</a></li>
								<li><a href="<?php echo JURI::base(); ?>index.php?option=com_joomblog&view=about">About JoomBlog</a></li>
								<li><a href="<?php echo JURI::base(); ?>index.php?option=com_joomblog&view=help">Component's help</a></li>
								<li><a href="http://www.joomplace.com/forum/joomla-components/joomblog.html" target="_blank">Support forum</a></li>
								<li><a href="http://www.joomplace.com/helpdesk/ticket_submit.php" target="_blank">Submit request to our technicians</a></li>
							</ul>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #e7e8e9;text-align:left; font-size:14px; font-weight:400; line-height:18px "><strong><img src="<?php echo JURI::base(); ?>components/com_joomblog/assets/images/tick.png">Latest changes: </strong></td>
				</tr>
			</table>
		<?php
        	jimport( 'joomla.filesystem.file' );
			$file		= JPATH_ROOT . DS . 'administrator' . DS .'components' . DS . 'com_joomblog' . DS . 'changelog.txt';
			if (file_exists($file))
			{
				$content	= JFile::read( $file );
				echo '<pre>'.$content.'</pre>';	
			}	
        }
        
         // Exporting old posts from content table to Joomblog_posts and update links with other tables.       
        function _ImportOldContent()
        {
        	$db	=& JFactory::getDBO();


        	//alter additional tables. add column for checking if data is new
			$db->setQuery("ALTER TABLE `#__joomblog_blogs` ADD `isnew` TINYINT UNSIGNED NOT NULL");
			$db->query();
			$db->setQuery("ALTER TABLE `#__joomblog_comment` ADD `isnew` TINYINT UNSIGNED NOT NULL");
			$db->query();
			$db->setQuery("ALTER TABLE `#__joomblog_content_tags` ADD `isnew` TINYINT UNSIGNED NOT NULL");
			$db->query();
			$db->setQuery("ALTER TABLE `#__joomblog_multicats` ADD `isnew` TINYINT UNSIGNED NOT NULL");
			$db->query();
			$db->setQuery("ALTER TABLE `#__joomblog_privacy` ADD `isnew` TINYINT UNSIGNED NOT NULL");
			$db->query();
			$db->setQuery("ALTER TABLE `#__joomblog_votes` ADD `isnew` TINYINT UNSIGNED NOT NULL");
			$db->query();

        	$query = "SELECT `oc`.`id`, `oc`.`asset_id`, `oc`.`title`, `oc`.`alias`, `oc`.`introtext`, `oc`.`fulltext`,
        	 `oc`.`state`, `oc`.`catid`, `oc`.`created`, `oc`.`created_by`, `oc`.`modified`, `oc`.`modified_by`,
        	 `oc`.`publish_up`, `oc`.`publish_down`, `oc`.`attribs`, `oc`.`version`, `oc`.`parentid`, `oc`.`ordering`,
        	 `oc`.`metakey`, `oc`.`metadesc`, `oc`.`access`, `oc`.`hits`, `oc`.`metadata`, `oc`.`language` 
        	 FROM #__content AS `oc` LEFT JOIN #__categories AS `cat` ON `oc`.`catid`=`cat`.`id` 
        	 WHERE `oc`.`state`=1 AND `cat`.`extension` LIKE 'com_joomblog';";
        	$db->setQuery($query);

        	if ($results = $db->loadObjectList())
        		{	
        			foreach( $results as $oldcontent )
        			{        	
        				$check_title = $db->getEscaped($oldcontent->title);
        				$check_alias = $db->getEscaped($oldcontent->alias);
        				$check_created = $db->getEscaped($oldcontent->created);
        				$query = "SELECT `c`.`id` FROM #__joomblog_posts AS `c` WHERE `c`.`title`='".$check_title."'
        				 AND `c`.`alias`='".$check_alias."' AND `c`.`created`='".$check_created."';";
        				$db->setQuery($query);
        				$result = $db->loadObject();
        				if (empty($result)) 
        				{
        					$id = $oldcontent->id;
        					//update posts raiting
        					$query = "SELECT * FROM #__content_raiting AS `cr` WHERE `cr`.`content_id`='".$id."' ";
        					$db->setQuery( $query );
        					if ($results = $db->loadObjectList())
        					{
        						foreach( $results as $contentraiting )
        						{
        							$query = "INSERT INTO `#__joomblog_posts_raiting` (`content_id`, `rating_sum`, `rating_count`, `lastip`) 
        							VALUES ('".$contentraiting->content_id."', '".$contentraiting->rating_sum."',
        							 '".$contentraiting->rating_count."', '".$contentraiting->lastip."');";
        							$db->setQuery($query);
			        				$db->query();	
        						}
        					}
        					
        					$asset_id = $db->getEscaped($oldcontent->asset_id);
			        		$title = $db->getEscaped($oldcontent->title);
			        		$alias = $db->getEscaped($oldcontent->alias);
			        		$introtext = $db->getEscaped($oldcontent->introtext);
			           		$fulltext = $db->getEscaped($oldcontent->fulltext);
			        		$state = $db->getEscaped($oldcontent->state);
			        		$catid = $db->getEscaped($oldcontent->catid);
			        		$created = $db->getEscaped($oldcontent->created);
			        		$created_by = $db->getEscaped($oldcontent->created_by);
			        		$modified = $db->getEscaped($oldcontent->modified);
			        		$modified_by = $db->getEscaped($oldcontent->modified_by);
			        		$publish_up = $db->getEscaped($oldcontent->publish_up);
			        		$publish_down = $db->getEscaped($oldcontent->publish_down);
			        		$attribs = $db->getEscaped($oldcontent->attribs);
			        		$version = $db->getEscaped($oldcontent->version);
			        		$parentid = $db->getEscaped($oldcontent->parentid);
							$ordering = $db->getEscaped($oldcontent->ordering);
							$metakey = $db->getEscaped($oldcontent->metakey);
							$metadesc = $db->getEscaped($oldcontent->metadesc);
							$access = $db->getEscaped($oldcontent->access);
							$hits = $db->getEscaped($oldcontent->hits);
							$metadata = $db->getEscaped($oldcontent->metadata);
							$language = $db->getEscaped($oldcontent->language);
							$query = "INSERT INTO `#__joomblog_posts` (`asset_id`, `title`, `alias`, `introtext`, `fulltext`, `state`,
							 `catid`, `created`, `created_by`, `modified`, `modified_by`, `publish_up`, `publish_down`, `attribs`, `version`,
							 `parentid`, `ordering`, `metakey`, `metadesc`, `access`, `hits`, `metadata`, `language`) 
							 VALUES ('".$asset_id."', '".$title."', '".$alias."', '".$introtext."', '".$fulltext."', '".$state."',
							 '".$catid."', '".$created."', '".$created_by."', '".$modified."', '".$modified_by."', '".$publish_up."',
							 '".$publish_down."', '".$attribs."', '".$version."', '".$parentid."', '".$ordering."', '".$metakey."',
							 '".$metadesc."', '".$access."', '".$hits."', '".$metadata."', '".$language."');";
							$db->setQuery($query);
			        		$db->query();
			        		
			        		$lastid = $db->insertid();

			        		if (!empty($lastid) AND $lastid!==$id)
			        		{
				        		
				        		$db->setQuery ("UPDATE #__joomblog_blogs AS `bl` SET `bl`.`content_id`='".$lastid."', `bl`.`isnew`='1' WHERE `bl`.`content_id`='".$id."' AND `bl`.`isnew` <> 1");
				        		$db->query();
				        		
				        		$db->setQuery ("UPDATE #__joomblog_comment AS `comm` SET `comm`.`contentid`='".$lastid."', `isnew`='1' WHERE `comm`.`contentid`='".$id."' AND `isnew` <> 1;");
				        		$db->query();

				        		$db->setQuery ("UPDATE #__joomblog_content_tags AS `contags` SET `contags`.`contentid`='".$lastid."', `isnew`='1' WHERE `contags`.`contentid`='".$id."' AND `isnew` <> 1;");
				        		$db->query();

				        		$db->setQuery ("UPDATE #__joomblog_multicats AS `multi` SET `multi`.`aid`='".$lastid."', `isnew`='1' WHERE `multi`.`aid`='".$id."' AND `isnew` <> 1;");
				        		$db->query();

				        		$db->setQuery ("UPDATE #__joomblog_privacy AS `privacy` SET `privacy`.`postid`='".$lastid."', `isnew`='1' WHERE `privacy`.`postid`='".$id."' AND `isnew` <> 1;");
				        		$db->query();
	
				        		$db->setQuery ("UPDATE #__joomblog_votes AS `vote` SET `vote`.`contentid`='".$lastid."', `isnew`='1' WHERE `vote`.`contentid`='".$id."' AND `isnew` <> 1;");
				        		$db->query();

	        					$db->setQuery ("UPDATE #__content AS `oc` SET `oc`.`state`='-2' WHERE `oc`.`id`='".$id."' ;");
				        		$db->query();

				        	}
				        	
        				}
        			}
        		}
        		
        }
        function _UpdateAssetCategories(){
			$db	=& JFactory::getDBO();
			
			$query = "SELECT * FROM #__assets  WHERE name = 'com_joomblog' ";
			$db->setQuery( $query );
			$com = $db->loadObject();
			
			$cat1 = array();
			if (is_object($com)){
				$query = "SELECT c.* FROM #__categories AS c, #__assets AS a WHERE c.asset_id = a.id AND c.extension = 'com_joomblog' AND  a.parent_id <> ".$com->id;
				$db->setQuery( $query );
				$cat1 = $db->loadObjectList();
			}
			
			if (!empty($cat1)){
				foreach($cat1 as $value){
					$db->setQuery("UPDATE #__assets SET parent_id = ".$com->id." WHERE name = 'com_joomblog.category.".$value->id."' ");
					$db->query();
				}
			}
			
			
        }
        
        function _FixDBTables()
		{
			$db	=& JFactory::getDBO();
			
			$query	= 'SHOW FIELDS FROM `#__joomblog_tags`';
			
			$db->setQuery( $query );
			$fields = $db->loadObjectList();
		
			if(empty($fields[1]->Key)){
				$db->setQuery("ALTER TABLE `#__joomblog_tags` ADD UNIQUE (`name`)");
				$db->query();
			}
		}
		
		function _ExtractFiles()
		{
			JArchive::extract(SITE_ROOT_PATH . "/administrator/components/com_joomblog/install/frontend.zip", SITE_ROOT_PATH . "/components/com_joomblog/");
			JArchive::extract(SITE_ROOT_PATH . "/administrator/components/com_joomblog/install/backend.zip", SITE_ROOT_PATH . "/administrator/components/com_joomblog/");
			
		}
		
		function _FixMenuLinks()
		{
			$db	=& JFactory::getDBO();
			
			$query = "SELECT `extension_id` FROM #__extensions WHERE `type` = 'component' AND `element`='com_joomblog'";
			$db->setQuery( $query );
			
			$comid	= $db->loadResult();
			$query = "UPDATE #__menu SET `component_id`='$comid' WHERE `link` LIKE 'index.php?option=com_joomblog%'";
			$db->setQuery($query);
			$db->query();
		}
		
		function _createAvatarFolder(){
			
			
			$joomblog_folder = JPATH_ROOT.DS.'images'.DS.'joomblog';
			if (!JFolder::exists($joomblog_folder)){
				if (!JFolder::create($joomblog_folder, 0755)) {
						 echo JString::str_ireplace("'", "\\'", JText::_('Can not create images/joomblog folder'));
						 jexit();
				}
			}
			
			$avatar_folder = $joomblog_folder.DS.'avatar';
			if (!JFolder::exists($avatar_folder)){
				if (!JFolder::create($avatar_folder, 0755)) {
						 echo JString::str_ireplace("'", "\\'", JText::_('Can not create images/joomblog folder'));
						 jexit();
				}
			}
			
		}
}

?>