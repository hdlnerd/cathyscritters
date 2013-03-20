<?php
/*
 *
 * @Version       $Id: router.php 669 2013-01-04 14:39:25Z geoffc $
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

/**
 * IssueTRackerBuildRoute
 *
 * @param   array A named array
 * @return  array
 */
function IssueTrackerBuildRoute($query)
{
   $segments = array();

   if (isset($query['task'])) {
      $segments[] = $query['task'];
      unset($query['task']);
   }
   if (isset($query['id'])) {
      $segments[] = $query['id'];
      unset($query['id']);
   }

   return $segments;
}

/**
 * IssueTrackerParseRoute
 *
 * @param   array A named array
 * @param   array
 */
function IssueTrackerParseRoute($segments)
{
   $vars = array();

   // view is always the first element of the array
   $count = count($segments);

   if ($count)
   {
      $count--;
      $segment = array_shift($segments);
      if (is_numeric($segment)) {
         $vars['id'] = $segment;
      } else {
         $vars['task'] = $segment;
      }
   }

   if ($count)
   {
      $count--;
      $segment = array_shift($segments) ;
      if (is_numeric($segment)) {
         $vars['id'] = $segment;
      }
   }

   return $vars;
}
