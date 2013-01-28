<?php
/*
 *
 * @Version       $Id: issuetypename.php 309 2012-08-13 10:31:49Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-13 11:31:49 +0100 (Mon, 13 Aug 2012) $
 *
 */
defined('_JEXEC') or die('Restricted access');

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldIssueTypename extends JFormField
{
   /**
    * The form field type.
    *
    * @var     string
    * @since   1.6
    */
   protected $type = 'issuetypename';

   /**
    * Method to get the field input markup.
    *
    * @return  string   The field input markup.
    * @since   1.6
    */
   protected function getInput()
   {
    // Initialize variables.
      $text = '';

      $db      = JFactory::getDbo();
      $query   = $db->getQuery(true);

      $query->select('type_name As text');
      $query->from('#__it_types a');
      $query->where('id = '.$this->value);

      // Get the options.
      $db->setQuery($query);

      $text = $db->loadResult();

      return $text;

   }
}