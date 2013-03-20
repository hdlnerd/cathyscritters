<?php
/*
 *
 * @Version       $Id: akismetapi.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.3
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-01-04 14:39:25 +0000 (Fri, 04 Jan 2013) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.form.formrule');

if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}

if (! class_exists('Akismet')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'classes'.DS.'Akismet.php');
}

// JLoader::register('Akismet', dirname(__FILE__).'/../../classes/Akismet.php');

class JFormRuleAkismetApi extends JFormRule
{
   protected $regex = '[a-z0-9]{12}';
   protected $modifiers = 'u';
   public function test(& $element, $value, $group = null, & $input = null, & $form = null)
   {
      if (empty($value) ) return;

      if (!parent::test($element, $value, $group, $input , $form)) {
         return false;
      }

      $akismet = new Akismet($input->get('site_url'), $value);
      if (!$akismet->isKeyValid()) {
         return new JException(JText::_('COM_ISSUETRACKER_AKISMET_INVALID_API_KEY'), 500, E_ERROR);
      }
      return true;
   }
}
