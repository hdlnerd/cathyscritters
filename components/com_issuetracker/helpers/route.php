<?php
/**
 *
 * @Version       $Id: route.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.0.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-01-04 14:39:25 +0000 (Fri, 04 Jan 2013) $
 *
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Content Component Route Helper
 *
 * @static
 * @package    Joomla.Site
 * @subpackage com_issuetracker
 * @since 1.5
 */
abstract class IssueTrackerHelperRoute
{
   protected static $lookup;

   /**
    * @param   int   The route of the issue item
    */
   public static function getIssueRoute($id)
   {
      $needles = array(
         'itissue'  => array((int) $id)
      );
      //Create the link

      if (empty($id)) {
         $link = 'index.php?option=com_issuetracker&view=itissueslist';
      } else {
         $link = 'index.php?option=com_issuetracker&view=itissues&id='. $id;
      }
/*
      if ($item = self::_findItem($needles)) {
         $link .= '&Itemid='.$item;
      }
      elseif ($item = self::_findItem()) {
         $link .= '&Itemid='.$item;
      }
*/
      return $link;
   }

   public static function getFormRoute($id)
   {
      //Create the link
      if ($id) {
         $link = 'index.php?option=com_issuetracker&task=itissues.edit&a_id='. $id;
      } else {
         $link = 'index.php?option=com_issuetracker&task=itissues.edit&a_id=0';
      }

      return $link;
   }

   protected static function _findItem($needles = null)
   {
      $app     = JFactory::getApplication();
      $menus      = $app->getMenu('site');

      // Prepare the reverse lookup array.
      if (self::$lookup === null)
      {
         self::$lookup = array();

         $component  = JComponentHelper::getComponent('com_issuetracker');
         $items      = $menus->getItems('component_id', $component->id);
         foreach ($items as $item)
         {
            if (isset($item->query) && isset($item->query['view']))
            {
               $view = $item->query['view'];
               if (!isset(self::$lookup[$view])) {
                  self::$lookup[$view] = array();
               }
               if (isset($item->query['id'])) {
                  self::$lookup[$view][$item->query['id']] = $item->id;
               }
            }
         }
      }

      if ($needles)
      {
         foreach ($needles as $view => $ids)
         {
            if (isset(self::$lookup[$view]))
            {
               foreach($ids as $id)
               {
                  if (isset(self::$lookup[$view][(int)$id])) {
                     return self::$lookup[$view][(int)$id];
                  }
               }
            }
         }
      }
      else
      {
         $active = $menus->getActive();
         if ($active && $active->component == 'com_issuetracker') {
            return $active->id;
         }
      }

      return null;
   }

   public static function getIssuePermalink( $id )
   {
      JTable::addIncludePath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_issuetracker' . DS . 'tables' );
      $issue = IssueTrackerHelperSite::getTable( 'Itissues' , 'IssueTrackerTable' );
      $issue->load( $id );

      return $issue->alias;
   }

   public static function getPersonPermalink( $id )
   {
      JTable::addIncludePath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_issuetracker' . DS . 'tables' );
      $person = IssueTrackerHelperSite::getTable( 'Itpeople' , 'IssueTrackerTable' );
      $person->load( $id );

      return $person->alias;
   }

   public static function getProjectPermalink( $id )
   {
      JTable::addIncludePath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_issuetracker' . DS . 'tables' );
      $project = IssueTrackerHelperSite::getTable( 'Itprojects' , 'IssueTrackerTable' );
      $project->load( $id );

      return $project->alias;
   }


   public static function getItemId( $view='' )
   {
      static $items  = null;

      if( !isset( $items[ $view ] ) )
      {
         $db   = JFactory::getDBO();

         switch($view)
         {
            case 'itissues':
               $view='itissues';
               break;
            case 'itpeople':
               $view='itpeople';
               break;
            case 'itpeoplelist':
               $view='itpeoplelist';
               break;
            case 'itprojects':
               $view='itprojects';
               break;
            case 'itprojectslist':
               $view='itprojectslist';
               break;
            case 'itissueslist':
            default:
               $view='itissueslist';
               break;
         }

         $query   = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
               . 'WHERE ' . $db->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_issuetracker&view='.$view ) . ' '
               . 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' LIMIT 1';
         $db->setQuery( $query );
         $itemid = $db->loadResult();


         // @rule: Try to fetch based on the current view.
         if( empty( $itemid ) )
         {
            $query   = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
                  . 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_issuetracker&view=' . $view . '%' ) . ' '
                  . 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' LIMIT 1';
            $db->setQuery( $query );
            $itemid = $db->loadResult();
         }

         if(empty($itemid))
         {
            $query   = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
                  . 'WHERE ' . $db->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_issuetracker&view=itissueslist' ) . ' '
                  . 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' LIMIT 1';
            $db->setQuery( $query );
            $itemid = $db->loadResult();
         }

         //last try. get anything view that from issuetracker
         if(empty($itemid))
         {
            $query   = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
                  . 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_issuetracker&view=%' ) . ' '
                  . 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' ORDER BY `id` LIMIT 1';
            $db->setQuery( $query );
            $itemid = $db->loadResult();
         }

         // if still failed the get any item id, then get the joomla default menu item id.
         if( empty($itemid) )
         {
            $query   = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
                  . 'WHERE `home` = ' . $db->Quote( '1' ) . ' '
                  . 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' ORDER BY `id` LIMIT 1';
            $db->setQuery( $query );
            $itemid = $db->loadResult();
         }

         $items[ $view ]   = !empty($itemid)? $itemid : 1;
      }
      return $items[ $view ];
   }

}
