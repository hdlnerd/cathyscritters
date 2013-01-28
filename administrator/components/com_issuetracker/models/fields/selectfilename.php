<?php
/*
 * @package Joomla.Framework
 * @copyright  Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @Version       $Id: selectfilename.php 309 2012-08-13 10:31:49Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-13 11:31:49 +0100 (Mon, 13 Aug 2012) $
 *
 */
defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');

class JFormFieldSelectFilename extends JFormField
{
   public $type = 'SelectFilename';

   protected function getInput()
   {
      // Initialize variables.
      $html = array();

      // Manager
      $managerOutput  = $this->element['manager'] ? '&amp;manager='.(string) $this->element['manager'] : '';

      $group = IssueTrackerHelper::getManagerGroup((string) $this->element['manager']);
      $textButton = 'COM_ISSUETRACKER_FORM_SELECT_'.strtoupper($group['t']);

      // Need to change this to appropriate form.
      $link = 'index.php?option=com_issuetracker&amp;view=attachments'.$group['c'].$managerOutput.'&amp;field='.$this->id;

      // Initialize some field attributes.
      $attr = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
      $attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

      // Initialize JavaScript field attributes.
      $onchange = (string) $this->element['onchange'];

      // Load the modal behavior script.
      JHtml::_('behavior.modal', 'a.modal_'.$this->id);

      // If external image, we don't need the filename will be required
      $extId      = (int) $this->form->getValue('extid');
      if ($extId > 0) {
         $readonly   = ' readonly="readonly"';
         return '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="-" '.$attr.$readonly.' />';
      }

      // Build the script.
      $script = array();
      $script[] = '  function SelectFileName_'.$this->id.'(title) {';
      $script[] = '     document.getElementById("'.$this->id.'_id").value = title;';
      $script[] = '     '.$onchange;
      $script[] = '     SqueezeBox.close();';
      $script[] = '  }';

      // Add the script to the document head.
      JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));


      $html[] = '<div class="fltlft">';
      $html[] = ' <input type="text" id="'.$this->id.'_id" name="'.$this->name.'" value="'. $this->value.'"' .
               ' '.$attr.' />';
      $html[] = '</div>';

      // Create the user select button.
      $html[] = '<div class="button2-left">';
      $html[] = '  <div class="blank">';
      $html[] = '    <a class="modal_'.$this->id.'" title="'.JText::_($textButton).'"' .
                     ' href="'.($this->element['readonly'] ? '' : $link).'"' .
                     ' rel="{handler: \'iframe\', size: {x: 780, y: 560}}">';
      $html[] = '       '.JText::_($textButton).'</a>';
      $html[] = '  </div>';
      $html[] = '</div>';


      return implode("\n", $html);
   }
}
