<?php
/*
 *
 * @Version       $Id: coloriser.php 105 2012-04-12 10:30:48Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.0.1
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-04-12 11:30:48 +0100 (Thu, 12 Apr 2012) $
 *
 */
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

      return $ret;
   }
}