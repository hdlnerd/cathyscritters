<?php
/*
 *
 * @Version       $Id: edit_product_details.php 710 2013-02-18 15:56:00Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-18 15:56:00 +0000 (Mon, 18 Feb 2013) $
 *
 */
defined('_JEXEC') or die;

$fieldSets = $this->form->getFieldsets('product_details');
foreach ($fieldSets as $name => $fieldSet) :
    ?>
   <fieldset>
        <legend>
            <?php echo JText::_('COM_ISSUETRACKER_PRODUCT_DETAILS_LEGEND'); ?>
        </legend>
        <?php foreach ($this->form->getFieldset($name) as $field) : ?>
            <div class="formelm">
                <?php echo $field->label; ?>
                <?php echo $field->input; ?>
            </div>
        <?php endforeach; ?>
   </fieldset>
<?php endforeach; ?>
