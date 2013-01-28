<?php
/*
 * @package    Joomla.Framework
 * @copyright  Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 *
 *
 * @Version       $Id: grid.php 317 2012-08-14 17:34:17Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-14 18:34:17 +0100 (Tue, 14 Aug 2012) $
 *
 */


include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'html' . DS . 'html' . DS . 'jgrid.php');
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
