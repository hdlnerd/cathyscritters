<?php
/*
 *
 * @Version       $Id: akismetapi.php 413 2012-09-04 16:32:36Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.1
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-09-04 17:32:36 +0100 (Tue, 04 Sep 2012) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.form.formrule');

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
