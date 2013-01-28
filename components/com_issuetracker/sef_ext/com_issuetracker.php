<?php
/**
 * sh404SEF support for com_issuetracker component.
 *
 * @Version       $Id: com_issuetracker.php 332 2012-08-20 19:00:51Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.0
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-08-20 20:00:51 +0100 (Mon, 20 Aug 2012) $
 *
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

require_once( JPATH_ROOT.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'helper.php' );

// ------------------  standard plugin initialize function - don't change ---------------------------
global $sh_LANG;
if( class_exists( 'shRouter' ) ) {
   $sefConfig     = shRouter::shGetConfig();
} else {
   $sefConfig     = Sh404sefFactory::getConfig();
}

$shLangName    = '';
$shLangIso     = '';
$title         = array();
$shItemidString = '';
$dosef         = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);

if ($dosef == false) return;
// ------------------  standard plugin initialize function - don't change ---------------------------

// remove common URL from GET vars list, so that they don't show up as query string in the URL
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');

// Load language file
$language = JFactory::getLanguage();
$language->load( 'com_issuetracker' , JPATH_ROOT );

// start by inserting the menu element title
$task    = isset($task) ? @$task : null;
$Itemid  = isset($Itemid) ? @$Itemid : null;

if(!empty($view) && file_exists(JPATH_ROOT.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'route.php')) {
   require_once (JPATH_ROOT.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'helper.php' );
   require_once (JPATH_ROOT.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'route.php');

   if (!empty($a_id)) {
      switch($view) {
         case 'itissues':
            $idname = 'Issue_';
            $idname .= IssueTrackerHelperRoute::getIssuePermalink($a_id);
            break;
         case 'form':
            if ($layout == 'edit' && empty($a_id)) { // submit new article
               $title[] = $sh_LANG[$shLangIso]['COM_SH404SEF_CREATE_NEW'];
            } else {
               $dosef = false;
            }
            break;
         default:
            $idname ='';
      }
   }

   if(!empty($id)) {
      // JTable::addIncludePath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_issuetracker' . DS . 'tables' );
      switch($view) {
         case 'itissues':
            $idname = 'Issue_';
            $idname .= IssueTrackerHelperRoute::getIssuePermalink($id);
            break;
         case 'form':
            $idname = 'Form_';
            $idname .= IssueTrackerHelperRoute::getIssuePermalink($id);
            break;
        case 'itpeople':
            // $idname = IssueTrackerHelperRoute::getPersonPermalink($id);
            $idname = 'Person';
            break;
         case 'itprojects':
            // $idname = IssueTrackerHelperRoute::getProjectPermalink($id);
            $idname = 'Project';
            break;
         default:
            $idname ='';
      }
   }

   if(empty($Itemid)) {
      $Itemid = IssueTrackerHelperRoute::getItemId($view);
      shAddToGETVarsList('Itemid' , $Itemid);
   }
}

$IssueTrackerName = shGetComponentPrefix($option);
$IssueTrackerName = empty($IssueTrackerName) ? getMenuTitle($option, $task, $Itemid, null, $shLangName) : $IssueTrackerName;
$IssueTrackerName = (empty($IssueTrackerName) || $IssueTrackerName == '/') ? 'IssueTracker':$IssueTrackerName;

$title[]    = $IssueTrackerName;
// Only add the text for the list views.
// $validViews = array('itissues','itissueslist','itpeople','itpeoplelist','itprojects','itprojectslist');
$validViews = array('itissueslist','itpeoplelist','itprojectslist');
$add_idname = true;

if (isset($view)) {
   if ( in_array($view, $validViews) ) {
      $title[] = JText::_( 'COM_ISSUETRACKER_SH404_VIEW_' . JString::strtoupper( $view ) );
   }
   shRemoveFromGETVarsList('view');
}

// fix when id shouldn't get removed.
if(isset($controller)) {
   //when there is a controller here, most likely we need the id for processing.
   $add_idname = false;
}

// Need the id in the url since we will get called many times for different ids selected from the list views.
// If we do not have it the Beez template open/close info truncates the id from the end of the url agter the .html text.
if(!empty($id) && $add_idname) {
   if(!empty($idname))  {
      if ($view == 'itissues' || $view == 'form' ) {
         $title[] = $idname;
      } else {
         $title[] = $idname.'_'.$id;
      }
   }
   shRemoveFromGETVarsList('id');
}

if(!empty($layout)) shRemoveFromGETVarsList('layout');   // For raise an issue screen.

if(!empty($Itemid)) shRemoveFromGETVarsList('Itemid');
if(!empty($limit)) shRemoveFromGETVarsList('limit');
if(isset($limitstart)) shRemoveFromGETVarsList('limitstart'); // limitstart can be zero
if(isset($pagestart)) {
   $pagestarttitle = 'page-' . ( $pagestart + 1 );
   $title[] = $pagestarttitle;
   shRemoveFromGETVarsList('pagestart'); // limitstart can be zero
}

// ------------------  standard plugin finalize function - don't change ---------------------------
if ($dosef){
   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString,
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
      (isset($shLangName) ? @$shLangName : null));
}
// ------------------  standard plugin finalize function - don't change ---------------------------