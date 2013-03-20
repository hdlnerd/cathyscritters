<?php
/*
 *
 * @Version       $Id: edit_user_details.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.0.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-01-04 14:39:25 +0000 (Fri, 04 Jan 2013) $
 *
 */
defined('_JEXEC') or die;

$fieldSets = $this->form->getFieldsets('user_details');
foreach ($fieldSets as $name => $fieldSet) :
    ?>
   <fieldset>
        <legend>
            <?php echo JText::_('COM_ISSUETRACKER_IDENTIFIER_DETAILS'); ?>
        </legend>
        <?php foreach ($this->form->getFieldset($name) as $field) : ?>
            <div class="formelm-area">
                <?php echo $field->label; ?>
                <?php echo $field->input; ?>
            </div>
        <?php endforeach; ?>
   </fieldset>
<?php endforeach; ?>
