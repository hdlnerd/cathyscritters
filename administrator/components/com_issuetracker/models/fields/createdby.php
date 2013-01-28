<?php
/*
 *
 * @Version       $Id: createdby.php 295 2012-07-16 12:14:05Z geoffc $
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
class JFormFieldCreatedby extends JFormField
{
   /**
    * The form field type.
    *
    * @var     string
    * @since   1.6
    */
   protected $type = 'createdby';

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


      //Load user
      $user_id = $this->value;
      if ($user_id) {
         $user = JFactory::getUser($user_id);
      } else {
         $user = JFactory::getUser();
         $html[] = '<input type="hidden" name="'.$this->name.'" value="'.$user->id.'" />';
      }
      $html[] = "<div>".$user->name." (".$user->username.")</div>";

      return implode($html);
   }
}