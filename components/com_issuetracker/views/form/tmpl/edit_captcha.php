<?php
/*
 *
 * @Version       $Id: edit_captcha.php 67 2012-03-13 18:48:41Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.0.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-03-13 18:48:41 +0000 (Tue, 13 Mar 2012) $
 *
 */
defined('_JEXEC') or die;

$fieldSets = $this->form->getFieldsets('captcha');
foreach ($fieldSets as $name => $fieldSet) :
?>
   <fieldset>
        <legend>
            <?php echo JText::_('COM_ISSUETRACKER_CAPTCHA_DETAILS'); ?>
        </legend>
        <?php foreach ($this->form->getFieldset($name) as $field) : ?>
            <div class="formelm-area">
                <?php echo $field->label; ?>
                <?php echo $field->input; ?>
            </div>
        <?php endforeach; ?>
   </fieldset>
<?php endforeach; ?>
