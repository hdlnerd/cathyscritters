<?php
/**
* @version $Id: default.php $
* @version		2.0.0 30/06/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_myjspace/assets/myjspace.css');
?>
<h2>BS MyJspace</h2>
<div class="myjspace">
<br />
<p><?php echo $this->version; ?></p>
</div>
