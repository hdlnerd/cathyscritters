<?php
/*
 *
 * @Version       $Id: script.php 461 2012-09-12 09:15:26Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.2
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-09-12 10:15:26 +0100 (Wed, 12 Sep 2012) $
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//Import filesystem libraries. Perhaps not necessary, but does not hurt
jimport( 'joomla.filesystem.file' );

class com_issuetrackerInstallerScript
{
   /*
    * The release value would ideally be extracted from <version> in the manifest file,
    * but at preflight, the manifest file exists only in the uploaded temp folder.
    */
   private $release  = '1.2.2';     // Main release version
   private $prelease = '1.2.2';     // Plugin release version
   private $pname = 'com_issuetracker';

   /*
    * $parent is the class calling this method.
    * $type is the type of change (install, update or discover_install, not uninstall).
    * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
    * If preflight returns false, Joomla will abort the update and undo everything already done.
    */
   function preflight( $type, $parent ) {
      // this component does not work with Joomla releases prior to 2.5
      // abort if the current Joomla release is older
      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '2.5', 'lt' ) ) {
         Jerror::raiseWarning(null, 'Cannot install com_issuetracker in a Joomla release prior to 2.5');
         return false;
      }

      // abort if the release being installed is not newer than the currently installed version
      if ( $type == 'update' ) {
         $oldRelease = $this->getParam('version');
         $rel = $oldRelease . ' to ' . $this->release;
         if ( version_compare( $this->release, $oldRelease, 'le' ) ) {
            Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade ' . $rel);
            return false;
         }
      }
      else { $rel = $this->release; }

      echo '<p>' . JText::_('COM_ISSUETRACKER_PREFLIGHT_' . strtoupper($type) . '_TEXT') . ' to version ' . $rel . '</p>';
   }

   /*
    * $parent is the class calling this method.
    * install runs after the database scripts are executed.
    * If the extension is new, the install method is run.
    * If install returns false, Joomla will abort the install and undo everything already done.
    */
   function install( $parent ) {
      echo '<p>' . JText::_('COM_ISSUETRACKER_INSTALL_TEXT') . ' to version: ' . $this->release . '</p>';
      // Install plugin
      $this->installPlugin();
      $this->createDBobjects();

      // Since this is a fresh install create the default project and person.
      $this->createDefEntries();

      // Since this is a fresh install set the Super user to be an issues_admin and a staff member so that
      // they avoid any db messages if they try and save an issue before setting real staff members.
      $this->set_admin_staff();

      // You can have the backend jump directly to the newly installed component configuration page
      // $parent->getParent()->setRedirectURL('index.php?option=com_issuetracker');
   }

   /*
    * $parent is the class calling this method.
    * update runs after the database scripts are executed.
    * If the extension exists, then the update method is run.
    * If this returns false, Joomla will abort the update and undo everything already done.
    */
   function update( $parent ) {
      $this->installPlugin();
      $this->check_itissues_constraints();

//      if ( $this->release == '1.2.0') $this->update_people();
      if ( version_compare( $this->release, '1.2.0', 'ge' ) ) $this->update_people();

      // Rerun the creation of create the default project and person.
      $this->createDefEntries();

      $this->createDBobjects();

      echo '<p>' . JText::_('COM_ISSUETRACKER_UPDATE_TEXT') . ' version: ' . $this->release . '</p>';
   }

   /*
    * $parent is the class calling this method.
    * $type is the type of change (install, update or discover_install, not uninstall).
    * postflight is run after the extension is registered in the database.
    */
   function postflight( $type, $parent ) {
      // set initial values for component parameters
      $params['my_param0'] = 'Component version ' . $this->release;
      $this->setParams( $params );

      echo '<p>' . JText::_('COM_ISSUETRACKER_POSTFLIGHT_' . strtoupper($type) . '_TEXT') . ' version: ' . $this->release . '</p>';
   }

   /*
    * $parent is the class calling this method
    * uninstall runs before any other action is taken (file removal or database processing).
    */
   function uninstall( $parent ) {
      $this->deinstallPlugin();
      echo '<p>' . JText::_('COM_ISSUETRACKER_UNINSTALL_TEXT') . ' version: ' . $this->release . '</p>';
   }

   /*
    * get a variable from the manifest file (actually, from the manifest cache).
    */
   function getParam( $name ) {
      $db = JFactory::getDbo();
      $db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_issuetracker"');
      $manifest = json_decode( $db->loadResult(), true );
      return $manifest[ $name ];
   }

   /*
    * sets parameter values in the component's row of the extension table
    */
   function setParams($param_array) {
      if ( count($param_array) > 0 ) {
         // read the existing component value(s)
         $db = JFactory::getDbo();
         $db->setQuery('SELECT params FROM #__extensions WHERE name = "com_issuetracker"');
         $params = json_decode( $db->loadResult(), true );
         // add the new variable(s) to the existing one(s)
         foreach ( $param_array as $name => $value ) {
            $params[ (string) $name ] = (string) $value;
         }
         // store the combined new and existing values back as a JSON string
         $paramsString = json_encode( $params );
         $db->setQuery('UPDATE #__extensions SET params = ' .
            $db->quote( $paramsString ) . ' WHERE name = "com_issuetracker"' );
            $db->query();
      }
   }

   function installPlugin() {
      //install system plugin
      $database = JFactory::getDBO();

      // Required for Joomla 3.0
      if(!defined('DS')){
         define('DS',DIRECTORY_SEPARATOR);
      }

      $plgSrc = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'plugins'.DS.'system'.DS;
      $plgDst = JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'issuetracker'.DS;
      if(!file_exists($plgDst)){
         mkdir($plgDst);
      }
      $system_plugin_success = 0;
      $system_plugin_success = JFile::copy($plgSrc.'issuetracker.php', $plgDst.'issuetracker.php');
      JFile::copy($plgSrc.'issuetracker.xml', $plgDst.'issuetracker.xml');
      JFile::copy($plgSrc.'index.html', $plgDst.'index.html');


      if( $system_plugin_success ) {
         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_SYSPLUGIN_INSTALL_TEXT') . '</p>';
         //enable plugin
         $database->setQuery("SELECT extension_id, enabled FROM #__extensions WHERE type='plugin' AND element='issuetracker' AND folder='system' ");
         $rows = $database->loadObjectList();
         $system_plugin_id = 0;
         $system_plugin_enabled = 0;
         foreach( $rows as $row ) {
            $system_plugin_id = $row->extension_id;
            $system_plugin_enabled = $row->enabled;
         }

         if ( $system_plugin_id ) {
            //plugin is already installed
            if ( !$system_plugin_enabled ){
               //publish plugin
               $database->setQuery( "UPDATE #__extensions SET enabled='1', access='1' WHERE extension_id='$system_plugin_id' ");
               $database->query();
            }
         } else {
            //insert plugin and enable it
            $manifest_cache = '{"legacy":false,"name":"System - Issue Tracker","type":"plugin","creationDate":"September 2012","author":"G S Chapman","copyright":"Copyright (C) 2012 Macrotone Consulting Ltd","authorEmail":"-","authorUrl":"www.macrotoneconsulting.co.uk","version":"1.2.2","description":"Updates Issue tracker people table when Joomla users are added, modified or deleted.","group":""}';
            $manifest_cache = addslashes($manifest_cache);
            $database->setQuery( "INSERT INTO #__extensions SET name='System - Issue Tracker', type='plugin', element='issuetracker', folder='system', enabled='1', access='1', manifest_cache='$manifest_cache' ");
            $database->query();
         }
         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_SYSPLUGIN_ENABLED_TEXT') . '</p>';
      } else {
         echo '<p style="color: red;">' . JText::_('COM_ISSUETRACKER_SYSPLUGIN_NOTINSTALLED_TEXT') . '</p><p><a href="http://www.macrotoneconsulting.co.uk/extensions/issuetracker" target="_blank">download the system plugin</a> and install with the Joomla installer.</p>';
      }

      // Now install search plugin
      $plgSrc = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'plugins'.DS.'search'.DS;
      $plgDst = JPATH_ROOT.DS.'plugins'.DS.'search'.DS.'issuetracker'.DS;
      if(!file_exists($plgDst)){
         mkdir($plgDst);
      }

      $search_plugin_success = 0;
      $search_plugin_success = JFile::copy($plgSrc.'issuetracker.php', $plgDst.'issuetracker.php');
      JFile::copy($plgSrc.'issuetracker.xml', $plgDst.'issuetracker.xml');
      JFile::copy($plgSrc.'index.html', $plgDst.'index.html');

      if( $search_plugin_success ) {
         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_SEARCHPLUGIN_INSTALL_TEXT') . '</p>';
         //enable plugin
         $database->setQuery("SELECT extension_id, enabled FROM #__extensions WHERE type='plugin' AND element='issuetracker' AND folder='search' ");
         $rows = $database->loadObjectList();
         $search_plugin_id = 0;
         $search_plugin_enabled = 0;
         foreach( $rows as $row ) {
            $search_plugin_id = $row->extension_id;
            $search_plugin_enabled = $row->enabled;
         }

         if ( $search_plugin_id ) {
            //plugin is already installed
            if ( !$search_plugin_enabled ){
                //publish plugin
                $database->setQuery( "UPDATE #__extensions SET enabled='1', access='1' WHERE extension_id='$search_plugin_id' ");
                $database->query();
            }
         } else {
             //insert plugin and enable it
             $manifest_cache = '{"legacy":false,"name":"Search - Issue Tracker","type":"plugin","creationDate":"September 2012","author":"G S Chapman","copyright":"Copyright (C) 2012 Macrotone Consulting Ltd","authorEmail":"-","authorUrl":"www.macrotoneconsulting.co.uk","version":"1.2.2","description":"Provides search ability of Issues.","group":""}';
             $manifest_cache = addslashes($manifest_cache);
             $database->setQuery( "INSERT INTO #__extensions SET name='Search - Issue Tracker', type='plugin', element='issuetracker', folder='search', enabled='1', access='1', manifest_cache='$manifest_cache' ");
             $database->query();
         }
           echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_SEARCHPLUGIN_ENABLED_TEXT') . '</p>';
      } else {
         echo '<p style="color: red;">' . JText::_('COM_ISSUETRACKER_SEARCHPLUGIN_NOTINSTALLED_TEXT') . '</p><p><a href="http://www.macrotoneconsulting.co.uk/extensions/issuetracker" target="_blank">download the search plugin</a> and install with the Joomla installer.</p>';
      }

      // Now install finder smart search plugin
      $plgSrc = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'plugins'.DS.'finder'.DS;
      $plgDst = JPATH_ROOT.DS.'plugins'.DS.'finder'.DS.'issuetracker'.DS;
      if(!file_exists($plgDst)){
         mkdir($plgDst);
      }

      $finder_plugin_success = 0;
      $finder_plugin_success = JFile::copy($plgSrc.'issuetracker.php', $plgDst.'issuetracker.php');
      JFile::copy($plgSrc.'issuetracker.xml', $plgDst.'issuetracker.xml');
      JFile::copy($plgSrc.'index.html', $plgDst.'index.html');

      if( $finder_plugin_success ) {
         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_FINDERPLUGIN_INSTALL_TEXT') . '</p>';
         //enable plugin
         $database->setQuery("SELECT extension_id, enabled FROM #__extensions WHERE type='plugin' AND element='issuetracker' AND folder='finder' ");
         $rows = $database->loadObjectList();
         $finder_plugin_id = 0;
         $finder_plugin_enabled = 0;
         foreach( $rows as $row ) {
            $finder_plugin_id = $row->extension_id;
            $finder_plugin_enabled = $row->enabled;
         }

         if ( $finder_plugin_id ) {
            //plugin is already installed
            if ( !$finder_plugin_enabled ){
                //publish plugin
                $database->setQuery( "UPDATE #__extensions SET enabled='1', access='1' WHERE extension_id='$finder_plugin_id' ");
                $database->query();
            }
         } else {
             //insert plugin but do not enable it
             $manifest_cache = '{"legacy":false,"name":"Smart Search - Issue Tracker","type":"plugin","creationDate":"September 2012","author":"G S Chapman","copyright":"Copyright (C) 2012 Macrotone Consulting Ltd","authorEmail":"-","authorUrl":"www.macrotoneconsulting.co.uk","version":"1.2.2","description":"Provides smart search (finder) ability of Issues.","group":""}';
             $manifest_cache = addslashes($manifest_cache);
             $database->setQuery( "INSERT INTO #__extensions SET name='Smart Search - Issue Tracker', type='plugin', element='issuetracker', folder='finder', enabled='0', access='1', manifest_cache='$manifest_cache' ");
             $database->query();
         }
           echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_FINDERPLUGIN_NOT_ENABLED_TEXT') . '</p>';
      } else {
         echo '<p style="color: red;">' . JText::_('COM_ISSUETRACKER_FINDERPLUGIN_NOTINSTALLED_TEXT') . '</p><p><a href="http://www.macrotoneconsulting.co.uk/extensions/issuetracker" target="_blank">download the search plugin</a> and install with the Joomla installer.</p>';
      }

   }

    function deinstallPlugin() {
      // Deinstall system plugin
      // Check if plugin installed.
      $database = JFactory::getDBO();
      $database->setQuery("SELECT extension_id, enabled FROM #__extensions WHERE type='plugin' AND element='issuetracker' AND folder='system' ");
      $rows = $database->loadObjectList();

      foreach ( $rows as $row ) {
         $system_plugin_id = $row->extension_id;
         if ( $system_plugin_id ) {
            //plugin is present so remove it
            $database->setQuery( "DELETE FROM #__extensions WHERE extension_id='$system_plugin_id' ");
            $database->query();
            echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_SYSPLUGIN_DEINSTALL_TEXT') . '</p>';
           } else {
            echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_SYSPLUGIN_NOTINSTALLED_TEXT') . '</p>';
         }
      }

      // Now remove files
      $plgDst = JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'issuetracker'.DS;
      if ( JFolder::exists($plgDst) ) {
         if ( JFile::exists($plgDst.'issuetracker.php') ) { JFile::delete($plgDst.'issuetracker.php'); }
         if ( JFile::exists($plgDst.'issuetracker.xml') ) { JFile::delete($plgDst.'issuetracker.xml'); }
         if ( JFile::exists($plgDst.'index.html') )          { JFile::delete($plgDst.'index.html'); }
         rmdir($plgDst);
         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_SYSPLUGIN_DIR_REMOVED_TEXT') . '</p>';
       } else {
          echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_SYSPLUGIN_DIR_NOTPRESENT_TEXT') . '</p>';
       }

      // Deinstall search plugin
      // Check if plugin installed.
      $database->setQuery("SELECT extension_id, enabled FROM #__extensions WHERE type='plugin' AND element='issuetracker' AND folder='search' ");
      $rows = $database->loadObjectList();

      foreach ( $rows as $row ) {
         $search_plugin_id = $row->extension_id;
         if ( $search_plugin_id ) {
            //plugin is present so remove it
            $database->setQuery( "DELETE FROM #__extensions WHERE extension_id='$search_plugin_id' ");
            $database->query();
            echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_SEARCHPLUGIN_DEINSTALL_TEXT') . '</p>';
           } else {
            echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_SEARCHPLUGIN_NOTINSTALLED_TEXT') . '</p>';
         }
      }

      // Now remove files
      $plgDst = JPATH_ROOT.DS.'plugins'.DS.'search'.DS.'issuetracker'.DS;
      if ( JFolder::exists($plgDst) ) {
         if ( JFile::exists($plgDst.'issuetracker.php') ) { JFile::delete($plgDst.'issuetracker.php'); }
         if ( JFile::exists($plgDst.'issuetracker.xml') ) { JFile::delete($plgDst.'issuetracker.xml'); }
         if ( JFile::exists($plgDst.'index.html') )          { JFile::delete($plgDst.'index.html'); }
         rmdir($plgDst);
         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_SEARCHPLUGIN_DIR_REMOVED_TEXT') . '</p>';
       } else {
          echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_SEARCHPLUGIN_DIR_NOTPRESENT_TEXT') . '</p>';
       }

      // Deinstall finder plugin
      // Check if plugin installed.
      $database->setQuery("SELECT extension_id, enabled FROM #__extensions WHERE type='plugin' AND element='issuetracker' AND folder='finder' ");
      $rows = $database->loadObjectList();

      foreach ( $rows as $row ) {
         $finder_plugin_id = $row->extension_id;
         if ( $finder_plugin_id ) {
            //plugin is present so remove it
            $database->setQuery( "DELETE FROM #__extensions WHERE extension_id='$finder_plugin_id' ");
            $database->query();
            echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_FINDERPLUGIN_DEINSTALL_TEXT') . '</p>';
           } else {
            echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_FINDERPLUGIN_NOTINSTALLED_TEXT') . '</p>';
         }
      }

      // Now remove files
      $plgDst = JPATH_ROOT.DS.'plugins'.DS.'finder'.DS.'issuetracker'.DS;
      if ( JFolder::exists($plgDst) ) {
         if ( JFile::exists($plgDst.'issuetracker.php') ) { JFile::delete($plgDst.'issuetracker.php'); }
         if ( JFile::exists($plgDst.'issuetracker.xml') ) { JFile::delete($plgDst.'issuetracker.xml'); }
         if ( JFile::exists($plgDst.'index.html') )       { JFile::delete($plgDst.'index.html'); }
         rmdir($plgDst);
         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_FINDERPLUGIN_DIR_REMOVED_TEXT') . '</p>';
       } else {
          echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_FINDERPLUGIN_DIR_NOTPRESENT_TEXT') . '</p>';
       }
   }

   function set_admin_staff()
   {
      // Update people table.  Only used for a new install.
      $db      = JFactory::getDbo();
      $query   = "UPDATE #__it_people SET staff=1, issues_admin=1 WHERE person_name='Super User'";
      $db->setQuery($query);
      $db->query();
   }

   function update_people()
   {
      // Only applied to release 1.2.0 and above
      // Update people table if we need to.
      $db = JFactory::getDbo();
      $query  = "select count(*) from #__it_people where registered = '1'";
      $db->setQuery($query);
      $cnt = $db->loadResult();

      // Check if we have any registered entries in our people table.  If we do then this must be an existing install
      // so we do not have to do anything.
      if ( $cnt == '0' ) {
         $db->setQuery('UPDATE #__it_people SET registered = "1" WHERE id > 20 ');
         $db->query();

         $db->setQuery('UPDATE #__it_people SET user_id = id WHERE registered = "1" ');
         $db->query();

         // Add check to see if any staff already
         $query  = "select count(*) from #__it_people where staff = '1'";
         $db->setQuery($query);
         $cnts = $db->loadResult();

         if ( $cnts == '0') {
            $db->setQuery('UPDATE #__it_people SET staff = 1 WHERE user_id IN (SELECT distinct assigned_to_person_id FROM #__it_issues)');
            $db->query();
         }

         $app = JFactory::getApplication();
         $prefix = $app->getCfg('dbprefix');
         $table = $prefix . 'it_people';

         // Add check to see if UQ exists
         $query   = "select count(*) from information_schema.TABLE_CONSTRAINTS ";
         $query  .= "where table_name = '".$table."' ";
         $query  .= "and constraint_name = '".$table."_userid_uk"."'";
         $db->setQuery($query);
         $cntu    = $db->loadResult();

         if ( $cntu == '0') {
            $db->setQuery("ALTER TABLE #__it_people ADD UNIQUE KEY `#__it_people_userid_uk` (`user_id`)");
            $db->query();
         }
      }
   }

   function check_itissues_constraints()
   {
      // Needed if we ran a 1.0.0 or 1.0.1 version of the application.
      // If so we need to rename two it_issues constraints.
      $app = JFactory::getApplication();
      // Get database prefix.
      $prefix = $app->getCfg('dbprefix');
      $table = $prefix . 'it_issues';

      $db = JFactory::getDbo();
      $query  = "select count(*) from information_schema.REFERENTIAL_CONSTRAINTS ";
      $query .= "where table_name = '".$table."' ";
      $query .= "and SUBSTRING(constraint_name, 1, 15) != '".$table."'";
      $db->setQuery($query);
      $cnt = $db->loadResult();

      if ($cnt > 0 ) {
         // Get old prefix.  Needed in case used Akeeba was used and the old constraint prefix was different.
         // Assumes a 6 letter prefix including the underscore.
         $query  = 'select distinct substring(constraint_name,1,6) ';
         $query .= 'from information_schema.REFERENTIAL_CONSTRAINTS ';
         $query .= "where table_name = '".$table."' ";
         $query .= "and SUBSTRING(constraint_name, 1, 15) != '".$table."'";
         $db->setQuery($query);
         $oprefix = $db->loadResult();

         // Remove misnamed constraints.
         $query  = 'ALTER TABLE `#__it_issues` ';
         $query .= "DROP FOREIGN KEY ".$oprefix."it_people_priority_fk,";
         $query .= "DROP FOREIGN KEY ".$oprefix."it_people_status_fk";
         $db->setQuery($query);
         $db->query();

         // Add the constraints back in
         $query  = 'ALTER TABLE `#__it_issues` ';
         $query .= ' ADD CONSTRAINT `#__it_issues_priority_fk` FOREIGN KEY (priority) REFERENCES `#__it_priority` (id) ON UPDATE RESTRICT ON DELETE RESTRICT,';
         $query .= ' ADD CONSTRAINT `#__it_issues_status_fk` FOREIGN KEY (status) REFERENCES `#__it_status` (id) ON UPDATE RESTRICT ON DELETE RESTRICT';
         $db->setQuery($query);
         $db->query();

         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_CONSTRAINTS_RENAMED') . '</p>';

      }
   }

   function createDBobjects()
   {
      $db = JFactory::getDbo();
      $db->setQuery('UPDATE #__it_meta SET version = "'. $this->release . '", type ="component" ');
      $db->query();

      /*
       * Create database triggers.
       */

      $query= "DROP TRIGGER IF EXISTS `#__it_projects_bi`;";
      $db->setQuery($query);
      $db->query();

      $query="create trigger `#__it_projects_bi`";
      $query.= "\nbefore insert on `#__it_projects`";
      $query.= "\nfor each row";
      $query.= "\nBEGIN ";
      $query.= "\n   IF (NEW.ACTUAL_END_DATE = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.ACTUAL_END_DATE := NULL;";
      $query.= "\n   END IF;";
      $query.= "\n   IF (NEW.CREATED_ON IS NULL OR NEW.CREATED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.CREATED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.CREATED_BY IS NULL OR NEW.CREATED_BY = '') THEN";
      $query.= "\n      SET NEW.CREATED_BY := USER();";
      $query.= "\n   END IF; ";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      $query= "DROP TRIGGER IF EXISTS `#__it_projects_bu`;";
      $db->setQuery($query);
      $db->query();

      $query= "create trigger `#__it_projects_bu`";
      $query.= "\nbefore update on `#__it_projects`";
      $query.= "\nfor each row ";
      $query.= "\nBEGIN ";
      $query.= "\n   IF (NEW.ACTUAL_END_DATE = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.ACTUAL_END_DATE := NULL;";
      $query.= "\n   END IF;";
      $query.= "\n   IF (NEW.MODIFIED_ON IS NULL OR NEW.MODIFIED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.MODIFIED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.MODIFIED_BY IS NULL OR NEW.MODIFIED_BY = '') THEN";
      $query.= "\n      SET NEW.MODIFIED_BY := USER();";
      $query.= "\n   END IF; ";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      $query= "DROP TRIGGER IF EXISTS `#__it_people_bi`;";
      $db->setQuery($query);
      $db->query();

      $query= "create trigger `#__it_people_bi`";
      $query.= "\nbefore insert on `#__it_people`";
      $query.= "\nfor each row";
      $query.= "\nBEGIN";
      $query.= "\n   IF (NEW.CREATED_ON IS NULL OR NEW.CREATED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.CREATED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.CREATED_BY IS NULL OR NEW.CREATED_BY = '') THEN";
      $query.= "\n      SET NEW.CREATED_BY := USER();";
      $query.= "\n   END IF; ";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      $query= "DROP TRIGGER IF EXISTS `#__it_people_bu`;";
      $db->setQuery($query);
      $db->query();

      $query= "create trigger `#__it_people_bu`";
      $query.= "\nbefore update on `#__it_people`";
      $query.= "\nfor each row";
      $query.= "\nBEGIN";
      $query.= "\n   IF (NEW.MODIFIED_ON IS NULL OR NEW.MODIFIED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.MODIFIED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.MODIFIED_BY IS NULL OR NEW.MODIFIED_BY = '') THEN";
      $query.= "\n      SET NEW.MODIFIED_BY := USER();";
      $query.= "\n   END IF; ";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      $query="DROP TRIGGER IF EXISTS `#__it_issues_bi`;";
      $db->setQuery($query);
      $db->query();

      $query= "create trigger `#__it_issues_bi`";
      $query.= "\nbefore insert on `#__it_issues`";
      $query.= "\nfor each row";
      $query.= "\nbegin";
      $query.= "\n   IF (NEW.ID IS NULL) THEN";
      $query.= "\n      SET NEW.ID := 0;";
      $query.= "\n   END IF;";
      $query.= "\n   IF (NEW.CREATED_ON IS NULL OR NEW.CREATED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.CREATED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.CREATED_BY IS NULL  OR NEW.CREATED_BY = '') THEN";
      $query.= "\n      SET NEW.CREATED_BY := USER();";
      $query.= "\n   END IF; ";
      $query.= "\nend;";
      $db->setQuery($query);
      $db->query();

      $query= "DROP TRIGGER IF EXISTS `#__it_issues_bu`;";
      $db->setQuery($query);
      $db->query();

      $query= "CREATE TRIGGER `#__it_issues_bu`";
      $query.= "\nBEFORE UPDATE ON `#__it_issues`";
      $query.= "\nFOR EACH ROW";
      $query.= "\nBEGIN";
      $query.= "\n   IF (NEW.MODIFIED_ON IS NULL OR NEW.MODIFIED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.MODIFIED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.MODIFIED_BY IS NULL OR NEW.MODIFIED_BY = '') THEN";
      $query.= "\n      SET NEW.MODIFIED_BY := USER();";
      $query.= "\n   END IF; ";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      $query="DROP TRIGGER IF EXISTS `#__it_priority_bi`;";
      $db->setQuery($query);
      $db->query();

      $query= "CREATE TRIGGER `#__it_priority_bi` BEFORE INSERT ON `#__it_priority` FOR EACH ROW";
      $query.= "\nBEGIN";
      $query.= "\n   IF (NEW.ID IS NULL) THEN";
      $query.= "\n      SET NEW.ID := 0;";
      $query.= "\n   END IF;";
      $query.= "\n   IF (NEW.CREATED_ON IS NULL OR NEW.CREATED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.CREATED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.CREATED_BY IS NULL  OR NEW.CREATED_BY = '') THEN";
      $query.= "\n      SET NEW.CREATED_BY := USER();";
      $query.= "\n   END IF; ";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      $query="DROP TRIGGER IF EXISTS `#__it_priority_bu`;";
      $db->setQuery($query);
      $db->query();

      $query= "CREATE TRIGGER `#__it_priority_bu` BEFORE UPDATE ON `#__it_priority` FOR EACH ROW";
      $query.= "\nBEGIN";
      $query.= "\n   IF (NEW.MODIFIED_ON IS NULL OR NEW.MODIFIED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.MODIFIED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.MODIFIED_BY IS NULL OR NEW.MODIFIED_BY = '') THEN";
      $query.= "\n      SET NEW.MODIFIED_BY := USER();";
      $query.= "\n   END IF;";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      $query="DROP TRIGGER IF EXISTS `#__it_status_bi`;";
      $db->setQuery($query);
      $db->query();

      $query= "CREATE TRIGGER `#__it_status_bi` BEFORE INSERT ON `#__it_status` FOR EACH ROW";
      $query.= "\nBEGIN";
      $query.= "\n   IF (NEW.ID IS NULL) THEN";
      $query.= "\n      SET NEW.ID := 0;";
      $query.= "\n   END IF;";
      $query.= "\n   IF (NEW.CREATED_ON IS NULL OR NEW.CREATED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.CREATED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.CREATED_BY IS NULL  OR NEW.CREATED_BY = '') THEN";
      $query.= "\n      SET NEW.CREATED_BY := USER();";
      $query.= "\n   END IF;";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      $query="DROP TRIGGER IF EXISTS `#__it_status_bu`;";
      $db->setQuery($query);
      $db->query();

      $query= "CREATE TRIGGER `#__it_status_bu` BEFORE UPDATE ON `#__it_status` FOR EACH ROW";
      $query.= "\nBEGIN";
      $query.= "\n   IF (NEW.MODIFIED_ON IS NULL OR NEW.MODIFIED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.MODIFIED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.MODIFIED_BY IS NULL OR NEW.MODIFIED_BY = '') THEN";
      $query.= "\n      SET NEW.MODIFIED_BY := USER();";
      $query.= "\n   END IF;";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      $query="DROP TRIGGER IF EXISTS `#__it_roles_bi`;";
      $db->setQuery($query);
      $db->query();

      $query= "CREATE TRIGGER `#__it_roles_bi` BEFORE INSERT ON `#__it_roles` FOR EACH ROW";
      $query.= "\nBEGIN";
      $query.= "\n   IF (NEW.ID IS NULL) THEN";
      $query.= "\n      SET NEW.ID := 0;";
      $query.= "\n   END IF;";
      $query.= "\n   IF (NEW.CREATED_ON IS NULL OR NEW.CREATED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.CREATED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.CREATED_BY IS NULL  OR NEW.CREATED_BY = '') THEN";
      $query.= "\n      SET NEW.CREATED_BY := USER();";
      $query.= "\n   END IF;";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      $query="DROP TRIGGER IF EXISTS `#__it_roles_bu`;";
      $db->setQuery($query);
      $db->query();

      $query= "CREATE TRIGGER `#__it_roles_bu` BEFORE UPDATE ON `#__it_roles` FOR EACH ROW";
      $query.= "\nBEGIN";
      $query.= "\n   IF (NEW.MODIFIED_ON IS NULL OR NEW.MODIFIED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.MODIFIED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.MODIFIED_BY IS NULL OR NEW.MODIFIED_BY = '') THEN";
      $query.= "\n      SET NEW.MODIFIED_BY := USER();";
      $query.= "\n   END IF;";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      $query="DROP TRIGGER IF EXISTS `#__it_types_bi`;";
      $db->setQuery($query);
      $db->query();

      $query= "CREATE TRIGGER `#__it_types_bi` BEFORE INSERT ON `#__it_types` FOR EACH ROW";
      $query.= "\nBEGIN";
      $query.= "\n   IF (NEW.ID IS NULL) THEN";
      $query.= "\n      SET NEW.ID := 0;";
      $query.= "\n   END IF;";
      $query.= "\n   IF (NEW.CREATED_ON IS NULL OR NEW.CREATED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.CREATED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.CREATED_BY IS NULL  OR NEW.CREATED_BY = '') THEN";
      $query.= "\n      SET NEW.CREATED_BY := USER();";
      $query.= "\n   END IF;";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      $query="DROP TRIGGER IF EXISTS `#__it_types_bu`;";
      $db->setQuery($query);
      $db->query();

      $query= "CREATE TRIGGER `#__it_types_bu` BEFORE UPDATE ON `#__it_types` FOR EACH ROW";
      $query.= "\nBEGIN";
      $query.= "\n   IF (NEW.MODIFIED_ON IS NULL OR NEW.MODIFIED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.MODIFIED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.MODIFIED_BY IS NULL OR NEW.MODIFIED_BY = '') THEN";
      $query.= "\n      SET NEW.MODIFIED_BY := USER();";
      $query.= "\n   END IF;";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      $query="DROP TRIGGER IF EXISTS `#__it_emails_bi`;";
      $db->setQuery($query);
      $db->query();

      $query= "CREATE TRIGGER `#__it_emails_bi` BEFORE INSERT ON `#__it_emails` FOR EACH ROW";
      $query.= "\nBEGIN";
      $query.= "\n   IF (NEW.ID IS NULL) THEN";
      $query.= "\n      SET NEW.ID := 0;";
      $query.= "\n   END IF;";
      $query.= "\n   IF (NEW.CREATED_ON IS NULL OR NEW.CREATED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.CREATED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.CREATED_BY IS NULL  OR NEW.CREATED_BY = '') THEN";
      $query.= "\n      SET NEW.CREATED_BY := USER();";
      $query.= "\n   END IF;";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      $query="DROP TRIGGER IF EXISTS `#__it_emails_bu`;";
      $db->setQuery($query);
      $db->query();

      $query= "CREATE TRIGGER `#__it_email_bu` BEFORE UPDATE ON `#__it_email` FOR EACH ROW";
      $query.= "\nBEGIN";
      $query.= "\n   IF (NEW.MODIFIED_ON IS NULL OR NEW.MODIFIED_ON = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.MODIFIED_ON := sysdate();";
      $query.= "\n   END IF; ";
      $query.= "\n   IF (NEW.MODIFIED_BY IS NULL OR NEW.MODIFIED_BY = '') THEN";
      $query.= "\n      SET NEW.MODIFIED_BY := USER();";
      $query.= "\n   END IF;";
      $query.= "\nEND;";
      $db->setQuery($query);
      $db->query();

      /*
       * Create procedures to handle sample data.
       */

      $query="DROP PROCEDURE IF EXISTS `#__create_sample_projects`;";
      $db->setQuery($query);
      $db->query();

      $query="create procedure `#__create_sample_projects`()";
      $query.= "\nbegin";
      $query.= "\ninsert into `#__it_projects` (id, project_name, start_date, target_end_date)";
      $query.= "\nvalues (2, 'Internal Infrastructure', date_sub(now(), interval 150 day), date_sub(now(), interval 30 day)),";
      $query.= "\n (3, 'New Payroll Rollout', date_sub(now(), interval 150 day), date_add(now(), interval 15 day)),";
      $query.= "\n (4, 'Email Integration', date_sub(now(), interval 120 day), date_sub(now(), interval 60 day)),";
      $query.= "\n (5, 'Public Website Operational', date_sub(now(), interval 60 day), date_add(now(), interval 30 day)),";
      $query.= "\n (6, 'Employee Satisfaction Survey', date_sub(now(), interval 30 day), date_add(now(), interval 60 day));";
      $query.= "\ncommit;";
      $query.= "\nend;";
      $db->setQuery($query);
      $db->query();

      $query="DROP PROCEDURE IF EXISTS `#__create_sample_people`;";
      $db->setQuery($query);
      $db->query();

      $query="create procedure `#__create_sample_people`()";
      $query.= "\nbegin";
      $query.= "\ninsert into `#__it_people` (id, person_name, person_email, registered, person_role, username, assigned_project)";
      $query.= "\nvalues (2, 'Thomas Cobley', 'tom.cobley@bademail.com', '0', '1', 'tcobley', null), ";
      $query.= "\n (3, 'Harry Hawke', 'harry.hawke@bademail.com', '0', '4', 'hhawke', null), ";
      $query.= "\n (4, 'Tom Pearce', 'tom.pearce@bademail.com', '0', '4', 'tpearce', null), ";
      $query.= "\n (5, 'Bill Brewer', 'bill.brewer@bademail.com', '0', '3', 'bbrewer', 2), ";
      $query.= "\n (6, 'Jan Stewer', 'jan.stewer@bademail.com', '0', '3', 'jstewer', 3), ";
      $query.= "\n (7, 'Peter Gurney', 'peter.gurney@bademail.com', '0', '3', 'pgurney', 4), ";
      $query.= "\n (8, 'Peter Davy', 'peter.davy@bademail.com', '0', '3', 'pdavy', 5), ";
      $query.= "\n (9, 'Daniel Whiddon', 'daniel.whiddon@bademail.com', '0', '3', 'dwhiddon', 6), ";
      $query.= "\n (10, 'Jack London', 'jack.london@bademail.com', '0', '5', 'jlondon', 2), ";
      $query.= "\n (11, 'Mark Tyne', 'mark.tyne@bademail.com', '0', '5', 'mtyne', 2), ";
      $query.= "\n (12, 'Jane Kerry', 'jane.kerry@bademail.com', '0', '5', 'jkerry', 6), ";
      $query.= "\n (13, 'Olive Pope', 'olive.pope@bademail.com', '0', '5','opope', 3), ";
      $query.= "\n (14, 'Russ Sanders', 'russ.sanders@bademail.com', '0', '5', 'rsanders', 4), ";
      $query.= "\n (15, 'Tucker Uberton', 'tucker.uberton@bademail.com', '0', '5', 'ruberton', 4), ";
      $query.= "\n (16, 'Vicky Mitchell', 'vicky.mitchell@bademail.com', '0', '5', 'vmitchell', 5), ";
      $query.= "\n (17, 'Scott Tiger', 'scott.tiger@bademail.com', '0', '5', 'stiger', 5),";
      $query.= "\n (18, 'John Gilpin', 'john.gilpin@bademail.com', '0', '5', 'jgilpin', 5);";
      $query.= "\ncommit;";
      $query.= "\nend;";
      $db->setQuery($query);
      $db->query();

      $query= "DROP PROCEDURE IF EXISTS `#__create_sample_issues`;";
      $db->setQuery($query);
      $db->query();

      // The issues sampels changed in release 1.2.0 since the assigned_to field has to now be to a registered user.
      $query= "create procedure `#__create_sample_issues`()";
      $query.= "\nbegin";
      $query.= "\ninsert into `#__it_issues`";
      $query.= "\n(id, issue_summary, issue_description,alias,issue_type,";
      $query.= "\nidentified_by_person_id, identified_date,";
      $query.= "\nrelated_project_id, assigned_to_person_id, status, priority,";
      $query.= "\ntarget_resolution_date, progress, actual_resolution_date, resolution_summary)";
      $query.= "\nvalues";
      $query.= "\n(1, 'Midwest call center servers have no failover due to Conn Creek plant fire','','DAAAAAAAA1','1',";
      $query.= "\n6, date_sub(now(), interval 80 day),";
      $query.= "\n4, null, '1', '3', date_sub(now(), interval 73 day), 'Making steady progress.', date_sub(now(), interval 73 day), ''),";
      $query.= "\n(2, 'Timezone ambiguity in some EMEA regions is delaying bulk forwarding to mirror sites','','DAAAAAAAA2','1',";
      $query.= "\n6, date_sub(now(), interval 100 day),";
      $query.= "\n4, null, '4', '2', date_sub(now(), interval 80 day),'','',''),";
      $query.= "\n(3, 'Some vendor proposals lack selective archiving and region-keyed retrieval sections','','DAAAAAAAA3','1',";
      $query.= "\n6, date_sub(now(), interval 110 day),";
      $query.= "\n4, null, '1', '3', date_sub(now(), interval 90 day), '', date_sub(now(), interval 95 day), ''),";
      $query.= "\n(4, 'Client software licenses expire for Bangalore call center before cutover','','DAAAAAAAA4','1',";
      $query.= "\n1, date_sub(now(), interval 70 day),";
      $query.= "\n4, null, '1', '1', date_sub(now(), interval 60 day), '',date_sub(now(), interval 66 day),'Worked with HW, applied patch set.'),";
      $query.= "\n(5, 'Holiday coverage for DC1 and DC3 not allowed under union contract, per acting steward at branch 745','','DAAAAAAAA5','1',";
      $query.= "\n1, date_sub(now(), interval 100 day),";
      $query.= "\n4, null, '1', '1', date_sub(now(), interval 90 day), '',date_sub(now(), interval 95 day), 'Worked with HW, applied patch set.'),";
      $query.= "\n(6, 'Review rollout schedule with HR VPs/Directors','','DAAAAAAAA6','1',";
      $query.= "\n8, date_sub(now(), interval 30 day),";
      $query.= "\n6, null, '1', '3', date_sub(now(), interval 15 day), '',date_sub(now(), interval 20 day),''),";
      $query.= "\n(7, 'Distribute translated categories and questions for non-English regions to regional team leads','','DAAAAAAAA7','1',";
      $query.= "\n8, date_sub(now(), interval 2 day),";
      $query.= "\n6, null, '4', '3', date_add(now(), interval 10 day), 'currently beta testing new look and feel','',''),";
      $query.= "\n(8, 'Provide survey FAQs to online newsletter group','','DAAAAAAAA8','1',";
      $query.= "\n1, date_sub(now(), interval 10 day),";
      $query.= "\n6, null, '4', '3', date_add(now(), interval 20 day), '','',''),";
      $query.= "\n(9, 'Need better definition of terms like work group, department, and organization for categories F, H, and M-W','','DAAAAAAAA9','1',";
      $query.= "\n1, date_sub(now(), interval 8 day),";
      $query.= "\n6, null, '4', '2', date_add(now(), interval 15 day), '','',''),";
      $query.= "\n(10, 'Legal has asked for better definitions on healthcare categories for Canadian provincial regs compliance','','DAAAAAAA10','1',";
      $query.= "\n1, date_sub(now(), interval 10 day),";
      $query.= "\n6, null, '1', '3', date_add(now(), interval 20 day), '',date_sub(now(), interval 1 day),''),";
      $query.= "\n(11, 'Action plan review dates conflict with effectivity of organizational consolidations for Great Lakes region','','DAAAAAAA11','1',";
      $query.= "\n1, date_sub(now(), interval 9 day),";
      $query.= "\n6, null, '4', '3', date_add(now(), interval 45 day), '','',''),";
      $query.= "\n(12, 'Survey administration consulting firm requires indemnification release letter from HR SVP','','DAAAAAAA12','1',";
      $query.= "\n1, date_sub(now(), interval 30 day),";
      $query.= "\n6, null, '1', '2', date_sub(now(), interval 15 day), '', date_sub(now(), interval 17 day), ''),";
      $query.= "\n(13, 'Facilities, Safety health-check reports must be signed off before capital asset justification can be approved','','DAAAAAAA13','1',";
      $query.= "\n4, date_sub(now(), interval 145 day),";
      $query.= "\n2, null, '1', '3', date_sub(now(), interval 100 day), '',date_sub(now(), interval 110 day),''),";
      $query.= "\n(14, 'Cooling and Power requirements exceed 90% headroom limit -- variance from Corporate requested','','DAAAAAAA14','1',";
      $query.= "\n4, date_sub(now(), interval 45 day),";
      $query.= "\n2, null, '1', '1', date_sub(now(), interval 30 day), '',date_sub(now(), interval 35 day),''),";
      $query.= "\n(15, 'Local regulations prevent Federal contracts compliance on section 3567.106B','','DAAAAAAA15','1',";
      $query.= "\n4, date_sub(now(), interval 90 day),";
      $query.= "\n2, null, '1', '1', date_sub(now(), interval 82 day), '',date_sub(now(), interval 85 day),''),";
      $query.= "\n(16, 'Emergency Response plan failed county inspector''s review at buildings 2 and 5','','DAAAAAAA16','1',";
      $query.= "\n4, date_sub(now(), interval 35 day),";
      $query.= "\n2, null, '4', '1', date_sub(now(), interval 5 day), '','',''),";
      $query.= "\n(17, 'Training for call center 1st and 2nd lines must be staggered across shifts','','DAAAAAAA17','1',";
      $query.= "\n5, date_sub(now(), interval 8 day),";
      $query.= "\n3, null, '1', '3', date_add(now(), interval 10 day), '',date_sub(now(), interval 1 day),''),";
      $query.= "\n(18, 'Semi-monthly ISIS feed exceeds bandwidth of Mississauga backup site','','DAAAAAAA18','1',";
      $query.= "\n5, date_sub(now(), interval 100 day),";
      $query.= "\n3, null, '3', '3', date_sub(now(), interval 30 day), 'pending info from supplier','',''),";
      $query.= "\n(19, 'Expat exception reports must be hand-reconciled until auto-post phaseout complete','','DAAAAAAA19','1',";
      $query.= "\n5, date_sub(now(), interval 17 day),";
      $query.= "\n3, null, '1', '1', date_add(now(), interval 4 day), '',date_sub(now(), interval 4 day),''),";
      $query.= "\n(20, 'Multi-region batch trial run schedule and staffing plan due to directors by end of phase review','','DAAAAAAA20','1',";
      $query.= "\n5, now(),";
      $query.= "\n3, null, '4', '1', date_add(now(), interval 15 day), '','',''),";
      $query.= "\n(21, 'Auditors'' signoff requires full CSB compliance report','','DAAAAAAA21','1',";
      $query.= "\n5, date_sub(now(), interval 21 day),";
      $query.= "\n3, null, '4', '1', date_sub(now(), interval 7 day), '','',''),";
      $query.= "\n(22, 'Review security architecture plan with consultant','','DAAAAAAA22','1',";
      $query.= "\n1, date_sub(now(), interval 60 day),";
      $query.= "\n5, null, '1', '1', date_sub(now(), interval 45 day), '',date_sub(now(), interval 40 day),''),";
      $query.= "\n(23, 'Evaluate vendor load balancing proposals against capital budget','','DAAAAAAA23','1',";
      $query.= "\n7, date_sub(now(), interval 50 day),";
      $query.= "\n5, null, '1', '1', date_sub(now(), interval 45 day), '',date_sub(now(), interval 43 day),''),";
      $query.= "\n(24, 'Some preferred domain names are unavailable in registry','','DAAAAAAA24','1',";
      $query.= "\n7, date_sub(now(), interval 55 day),";
      $query.= "\n5, null, '1', '3', date_sub(now(), interval 45 day), '',date_sub(now(), interval 50 day),''),";
      $query.= "\n(25, 'Establish grid management capacity-expansion policies with ASP','','DAAAAAAA25','1',";
      $query.= "\n7, date_sub(now(), interval 20 day),";
      $query.= "\n5, null, '4', '3', date_sub(now(), interval 5 day), '','',''),";
      $query.= "\n(26, 'Access through proxy servers blocks some usage tracking tools','','DAAAAAAA26','1',";
      $query.= "\n7, date_sub(now(), interval 10 day),";
      $query.= "\n5, null, '1', '1', date_sub(now(), interval 5 day), '',date_sub(now(), interval 1 day),''),";
      $query.= "\n(27, 'Phase I stress testing cannot use production network','','DAAAAAAA27','1',";
      $query.= "\n7, date_sub(now(), interval 11 day),";
      $query.= "\n5, null, '4', '1', sysdate(), '','',''),";
      $query.= "\n(28, 'DoD clients must have secure port and must be blocked from others','','DAAAAAAA28','1',";
      $query.= "\n7, date_sub(now(), interval 20 day),";
      $query.= "\n5, null, '3', '1', sysdate(),";
      $query.= "\n'Waiting on Security Consultant, this may drag on.','','');";
      $query.= "\ncommit;";
      $query.= "\nend;";
      $db->setQuery($query);
      $db->query();

      $query= "DROP PROCEDURE IF EXISTS `#__add_it_sample_data`;";
      $db->setQuery($query);
      $db->query();

      $query= "create procedure `#__add_it_sample_data`()";
      $query.= "\nBEGIN";
      $query.= "\n   CALL `#__create_sample_projects`();";
      $query.= "\n   CALL `#__create_sample_people`();";
      $query.= "\n   CALL `#__create_sample_issues`();";
      $query.= "\nend;";
      $db->setQuery($query);
      $db->query();

      $query= "DROP PROCEDURE IF EXISTS `#__remove_it_sample_data`;";
      $db->setQuery($query);
      $db->query();

      $query= "create procedure `#__remove_it_sample_data`()";
      $query.= "\nBEGIN";
      $query.= "\n   delete from `#__it_issues` where id < 29;";
      $query.= "\n   delete from `#__it_people` where id >1 AND id < 19;";
      $query.= "\n   delete from `#__it_projects` where id > 1 AND id < 7;";
      $query.= "\n   commit;";
      $query.= "\nend;";
      $db->setQuery($query);
      $db->query();

   }

   /*
    * Procedure to create a default person and a default project both with an id of zero.
    * Also synchronise with the Joomla users table.
    */

   function createDefEntries()
   {
      $user = JFactory::getUser();

      $db = JFactory::getDbo();
      $query = "INSERT IGNORE INTO `#__it_projects` (id, project_name, project_description, start_date, state, created_by, created_on)";
      $query.= "\nvalues (1, 'Unspecified Project', 'Unspecified Project', now(), 1, '".$user->username."', now());";
      $db->setQuery($query);
      $db->query();

      // Check to see if the Super user is using the id of 1
      $query = "SELECT id from `#__it_people` WHERE person_name ='Super User'";
      $db->setQuery($query);
      $super_id = $db->loadResult();

      if ( $super_id == 1 ) {
         $query=  "INSERT IGNORE INTO `#__it_people` (id, person_name, username, person_email, registered, person_role, created_by, created_on, assigned_project)";
         $query.= "\nvalues (2, 'Anonymous', 'anon', 'anonymous@bademail.com', '0', '6', '".$user->username."', now(), 1);";
         $db->setQuery($query);
         $db->query();
      } else {
         $query=  "INSERT IGNORE INTO `#__it_people` (id, person_name, username, person_email, registered, person_role, created_by, created_on, assigned_project)";
         $query.= "\nvalues (1, 'Anonymous', 'anon', 'anonymous@bademail.com', '0', '6', '".$user->username."', now(), 1);";
         $db->setQuery($query);
         $db->query();
      }

      // $query = "CALL `#__update_it_people`()";
      $query = "INSERT IGNORE INTO `#__it_people` (user_id, person_name, username, person_email, registered, person_role, assigned_project, created_by, created_on)";
      $query.= "\n   SELECT id, name, username, email, '1', '6', 1, '".$user->username."', registerDate FROM `#__users`";
      $db->setQuery($query);
      $db->query();
   }

}
