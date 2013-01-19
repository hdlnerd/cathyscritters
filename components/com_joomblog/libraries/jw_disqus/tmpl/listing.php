<?php 
/**
* JoomBlog component for Joomla
* @version $Id: listing.php 2011-03-16 17:30:15
* @package JoomBlog
* @subpackage listing.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$fix_url = JURI::base().$content_check->alias.".html" ;
?>


<!-- Disqus comments counter and anchor link -->
<a class="jwDisqusListingCounterLink" href="<?php echo $fix_url; ?>#disqus_thread" title="<?php echo JText::_('COM_JOOMBLOG_ADDACOMMENT'); ?>">
	<?php echo JText::_('COM_JOOMBLOG_ADDACOMMENT'); ?>
</a>
