<?php
/*
 *
 * @Version       $Id: timecreated.php 295 2012-07-16 12:14:05Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-07-16 13:14:05 +0100 (Mon, 16 Jul 2012) $
 *
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldTimecreated extends JFormField
{
   /**
    * The form field type.
    *
    * @var     string
    * @since   1.6
    */
   protected $type = 'timecreated';

   /**
    * Method to get the field input markup.
    *
    * @return  string   The field input markup.
    * @since   1.6
    */
   protected function getInput()
   {
      // Initialize variables.
      $html = array();

      $date  = JFactory::getDate();

      $time_created = $this->value;
      if (!$time_created) {
         $time_created = $date->toMySQL();
         // $time_created = date("Y-m-d H:i:s");
         $html[] = '<input type="hidden" name="'.$this->name.'" value="'.$time_created.'" />';
      }
      $jdate = new JDate($time_created);
      $pretty_date = $jdate->format(JText::_('DATE_FORMAT_LC2'));
      $html[] = "<div>".$pretty_date."</div>";

      return implode($html);
   }
}