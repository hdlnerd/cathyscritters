<?php
/*
 *
 * @Version       $Id: coloriser.php 724 2013-02-22 15:53:06Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.3
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-22 15:53:06 +0000 (Fri, 22 Feb 2013) $
 *
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class IssueTrackerChangelogColoriser
{
   public static function colorise($file, $onlyLast = false)
   {
      $ret = '';

      $lines = @file($file);
      if(empty($lines)) return $ret;

      array_shift($lines);

      foreach($lines as $line) {
         $line = trim($line);
         if(empty($line)) continue;
         $type = substr($line,0,1);
         switch($type) {
            case '=':
               continue;
               break;

            case '+':
               $ret .= "\t".'<li class="tracker-changelog-added"><span></span>'.htmlentities(trim(substr($line,2)))."</li>\n";
               break;

            case '-':
               $ret .= "\t".'<li class="tracker-changelog-removed"><span></span>'.htmlentities(trim(substr($line,2)))."</li>\n";
               break;

            case '~':
               $ret .= "\t".'<li class="tracker-changelog-changed"><span></span>'.htmlentities(trim(substr($line,2)))."</li>\n";
               break;

            case '!':
               $ret .= "\t".'<li class="tracker-changelog-important"><span></span>'.htmlentities(trim(substr($line,2)))."</li>\n";
               break;

            case '#':
               $ret .= "\t".'<li class="tracker-changelog-fixed"><span></span>'.htmlentities(trim(substr($line,2)))."</li>\n";
               break;

            default:
               if(!empty($ret)) {
                  $ret .= "</ul>";
                  if($onlyLast) return $ret;
               }
               if(!$onlyLast) $ret .= "<h3 class=\"tracker-changelog\">$line</h3>\n";
               $ret .= "<ul class=\"tracker-changelog\">\n";
               break;
         }
      }
      if (!empty($ret)) $ret .= "</ul>";

      return $ret;
   }
}