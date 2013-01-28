<?php
/*
 *
 * @Version       $Id: default.php 74 2012-03-27 16:33:46Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.0.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-03-27 17:33:46 +0100 (Tue, 27 Mar 2012) $
 *
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

// TemplateUtils::addCSS('media://com_issuetracker/css/administrator.css?'.ADMINTOOLS_VERSION);
JHTML::_('behavior.mootools');
?>

<?php if(!empty($this->table)): ?>
<h1><?php echo JText::_('COM_ISSUETRACKER_OPTIMISE_INPROGRESS'); ?></h1>
<?php else: ?>
<h1><?php echo JText::_('COM_ISSUETRACKER_OPTIMISE_COMPLETE'); ?></h1>
<?php endif; ?>

<div id="progressbar-outer">
   <div id="progressbar-inner"></div>
</div>

<?php if(!empty($this->table)): ?>
<form action="index.php" name="adminForm" id="adminForm">
   <input type="hidden" name="option" value="com_issuetracker" />
   <input type="hidden" name="view" value="dbtasks" />
   <input type="hidden" name="task" value="optimize" />
   <input type="hidden" name="from" value="<?php echo $this->table ?>" />
   <input type="hidden" name="tmpl" value="component" />
</form>
<?php endif; ?>

<?php if($this->percent == 100): ?>
<div class="disclaimer">
   <h3><?php echo JText::_('COM_ISSUETRACKER_AUTOCLOSE_IN_3S'); ?></h3>
</div>
<?php endif; ?>