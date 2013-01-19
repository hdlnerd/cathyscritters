<?php
/**
* @version $Id: uninstall.myjspace.php $
* @version		2.0.3 20/10/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

?>
<h1>MyJspace Uninstall</h1>
<?php
	// Config
	$pparams = JComponentHelper::getParams('com_myjspace');
	$foldername = $pparams->get('foldername', 'myjsp');
	$db	= JFactory::getDBO();		
	
	// Get old content, save the content into param and drop old table #__myjspace_cfg
	echo "<p>Recreating the #__myjspace table and it's content in case of downgrade to older version than BS MyJspace 1.8.0 ...<br /></p>";

	$query = "CREATE TABLE IF NOT EXISTS `#__myjspace_cfg` ( `foldername` varchar(100) NOT NULL, PRIMARY KEY (`foldername`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$db->setQuery($query);
	$db->query();

	$query = "DELETE FROM `#__myjspace_cfg`";
	$db->setQuery($query);
	$db->query();	

	$query = "INSERT INTO `#__myjspace_cfg` (`foldername`) VALUES (".$db->Quote($foldername).");";
	$db->setQuery($query);
	$db->query();
	
	// Update id for downgrade from >= 2.0.0 to < 2.0.0
	$query = "SELECT `userid` FROM `#__myjspace` GROUP BY `userid` HAVING COUNT(*) > 1";
	$db->setQuery($query);
	$db->query();
	$num_rows = $db->getNumRows();
	
	if ($num_rows <= 0) {
		echo "<p>Updating pages ID to be compatible for downgrade :-( to older version than BS MyJspace 2.0.0<br /></p>";
		$query = "UPDATE `#__myjspace` SET `id` = `userid` WHERE `id` != `userid`";
		$db->setQuery($query);
		$db->query();
	} else
		echo "<p>To downgrade: you need to have only one page per user. Reinstall the component, delete the extra page(s) for ".$num_rows." user(s) and uninstall the component.<br /></p>";

?>
<p>
<b><u>bye bye :-(</u></b><br /><br />
BS MyJspace tables (with user's data) and files into the folder '<?php echo $foldername; ?>' are not deleted during the uninstall process<br />
So you can upgrade MyJspace keeping user's data installing the new version<br />
If you don't want to keep them: delete manually folder 'myjsp' and tables #__myjspace and #__myjspace_cfg<br />
</p>
