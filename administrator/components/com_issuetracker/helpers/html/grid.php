<?php
/*
 * @package    Joomla.Framework
 * @copyright  Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @Version       $Id: grid.php 724 2013-02-22 15:53:06Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-22 15:53:06 +0000 (Fri, 22 Feb 2013) $
 *
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}

// File moved location in Joomal 3.0
$filename = JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'html' . DS . 'html' . DS . 'jgrid.php';
$filenamea = JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'html' . DS . 'jgrid.php';

if (file_exists($filename)) {
  include_once($filename);
} else {
  include_once($filenamea);
}

// include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'html' . DS . 'html' . DS . 'jgrid.php');
//jimport('joomla.html.html.jgrid');

class IssuetrackerGrid extends JHtmlJGrid
{

   public static function isadmin($value, $i, $prefix = '', $enabled = true, $checkbox='cb')
   {
      if (is_array($prefix)) {
         $options    = $prefix;
         $enabled    = array_key_exists('enabled',  $options) ? $options['enabled']  : $enabled;
         $checkbox   = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
         $prefix     = array_key_exists('prefix',   $options) ? $options['prefix']   : '';
      }
      $states  = array(
         1  => array('notadministrator', 'COM_ISSUETRACKER_ADMIN',     'COM_ISSUETRACKER_NOT_ADMIN_ITEM', 'COM_ISSUETRACKER_ADMIN',     false, 'publish',   'publish'),
         0  => array('administrator',    'COM_ISSUETRACKER_NOT_ADMIN', 'COM_ISSUETRACKER_ADMIN_ITEM',     'COM_ISSUETRACKER_NOT_ADMIN', false, 'unpublish', 'unpublish')
      );
      return self::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
   }

   public static function msgnotify($value, $i, $prefix = '', $enabled = true, $checkbox='cb')
   {
      if (is_array($prefix)) {
         $options    = $prefix;
         $enabled    = array_key_exists('enabled', $options) ? $options['enabled']     : $enabled;
         $checkbox   = array_key_exists('checkbox',   $options) ? $options['checkbox'] : $checkbox;
         $prefix     = array_key_exists('prefix',  $options) ? $options['prefix']      : '';
      }
      $states  = array(
         1  => array('nonotify', 'COM_ISSUETRACKER_NOTIFY',    'COM_ISSUETRACKER_NO_NOTIFY_ITEM', 'COM_ISSUETRACKER_NOTIFY',    false, 'publish',   'publish'),
         0  => array('notify',   'COM_ISSUETRACKER_NO_NOTIFY', 'COM_ISSUETRACKER_NOTIFY_ITEM',    'COM_ISSUETRACKER_NO_NOTIFY', false, 'unpublish', 'unpublish')
      );
      return self::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
   }

   public static function staff($value, $i, $prefix = '', $enabled = true, $checkbox='cb')
   {
      if (is_array($prefix)) {
         $options    = $prefix;
         $enabled    = array_key_exists('enabled', $options) ? $options['enabled']     : $enabled;
         $checkbox   = array_key_exists('checkbox',   $options) ? $options['checkbox'] : $checkbox;
         $prefix     = array_key_exists('prefix',  $options) ? $options['prefix']      : '';
      }
      $states  = array(
         1  => array('notstaff', 'COM_ISSUETRACKER_ISSUES_STAFF',    'COM_ISSUETRACKER_NOT_STAFF_ITEM', 'COM_ISSUETRACKER_ISSUES_STAFF',    false, 'publish',   'publish'),
         0  => array('staff',   'COM_ISSUETRACKER_ISSUES_NOT_STAFF', 'COM_ISSUETRACKER_STAFF_ITEM',    'COM_ISSUETRACKER_ISSUES_NOT_STAFF', false, 'unpublish', 'unpublish')
      );
      return self::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
   }

}
