<?php
/*
 *
 * @Version       $Id: default.php 746 2013-02-27 17:09:28Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-27 17:09:28 +0000 (Wed, 27 Feb 2013) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();

$db   = JFactory::getDBO();
$sql  = "SELECT version FROM ".$db->quoteName('#__it_meta')." WHERE type='component'";
$db->setQuery( $sql);
$version = $db->loadResult();

// website root directory
$_root = JURI::root();

//Add css


$script = <<<ENDSCRIPT
window.addEvent( 'domready' ,  function() {
   $('btnchangelog').addEvent('click', showChangelog);
});

function showChangelog()
{
    SqueezeBox.fromElement(
        $('tracker-changelog'), {
            handler: 'adopt',
            size: {
                x: 550,
                y: 500
            }
        }
    );
}
ENDSCRIPT;

$document = JFactory::getDocument();
$document->addScriptDeclaration($script,'text/javascript');

?>

<div id="cpanel" style="float:left;width:40%;">

    <h2><?php echo JText::_('COM_ISSUETRACKER_TOOLS') ?></h2>

    <div style="float:left;">
       <div class="icon">
          <a href="index.php?option=com_issuetracker&amp;view=itissueslist">
             <img alt="<?php echo JText::_('COM_ISSUETRACKER_ISSUES'); ?>"
             src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_issuetracker/images/48/issues.png" />
             <span><?php echo JText::_('COM_ISSUETRACKER_ISSUES'); ?></span>
          </a>
       </div>
    </div>

    <div style="float:left;">
       <div class="icon">
          <a href="index.php?option=com_issuetracker&amp;view=itpeoplelist">
             <img alt="<?php echo JText::_('COM_ISSUETRACKER_PEOPLE'); ?>"
             src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_issuetracker/images/48/users.png" />
             <span><?php echo JText::_('COM_ISSUETRACKER_PEOPLE'); ?></span>
          </a>
       </div>
    </div>

    <div style="float:left;">
       <div class="icon">
          <a href="index.php?option=com_issuetracker&amp;view=itprojectslist">
             <img alt="<?php echo JText::_('COM_ISSUETRACKER_PROJECTS'); ?>"
              src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_issuetracker/images/48/projects.png" />
             <span><?php echo JText::_('COM_ISSUETRACKER_PROJECTS'); ?></span>
          </a>
       </div>
    </div>

    <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
       <div class="icon">
          <a href="index.php?option=com_issuetracker&view=dbtasks&task=syncusers" id="optimize">
             <img src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_issuetracker/images/48/sync.png"
             border="0" alt="<?php echo JText::_('COM_ISSUETRACKER_SYNC_USERS') ?>" />
             <span>
                <?php echo JText::_('COM_ISSUETRACKER_SYNC_USERS') ?><br/>
             </span>
          </a>
       </div>
    </div>

    <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
       <div class="icon">
          <a href="index.php?option=com_issuetracker&view=itloglist">
             <img src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_issuetracker/images/48/log.png"
             border="0" alt="<?php echo JText::_('COM_ISSUETRACKER_DISPLAY_LOG') ?>" />
             <span>
                <?php echo JText::_('COM_ISSUETRACKER_DISPLAY_LOG') ?><br/>
             </span>
          </a>
       </div>
    </div>

    <div style="clear: both;"></div>

    <h2><?php echo JText::_('COM_ISSUETRACKER_UPDATES') ?></h2>

   <?php echo LiveUpdate::getIcon(); ?>

   <div style="clear: both;"></div>

   <h2><?php echo JText::_('COM_ISSUETRACKER_SDATA') ?></h2>

   <!-- ?php if($this->isMySQL): ? -->
      <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
         <div class="icon">
            <a href="index.php?option=com_issuetracker&view=dbtasks&task=addsampledata" id="optimize">
               <img src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_issuetracker/images/48/add_sample_data.png"
               border="0" alt="<?php echo JText::_('COM_ISSUETRACKER_ADD_SDATA') ?>" />
               <span>
                  <?php echo JText::_('COM_ISSUETRACKER_ADD_SDATA') ?><br/>
               </span>
            </a>
         </div>
      </div>

      <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
         <div class="icon">
            <a href="index.php?option=com_issuetracker&view=dbtasks&task=remsampledata" id="optimize">
               <img src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_issuetracker/images/48/delete_sample_data.png"
                border="0" alt="<?php echo JText::_('COM_ISSUETRACKER_DEL_SDATA') ?>" />
                <span>
                  <?php echo JText::_('COM_ISSUETRACKER_DEL_SDATA') ?><br/>
               </span>
            </a>
         </div>
      </div>

   <!-- ?php endif; ? -->

   <div class="clr"></div>
</div>

<div id="tabs" style="float:right; width:60%;">
   <?php
   echo "<p>";
   echo JHtml::_('tabs.start', 'IssueTracker', array('useCookie'=>1, 'startOffset'=> 4));

      if ($this->params->get('show_summary_rep', 0)) {
         echo JHtml::_('tabs.panel', JText::_('COM_ISSUETRACKER_SUMMARY_ISSUES'), 'summaryissuestab' );
         echo "<div>";
         $rows = &$this->summaryIssues;
         echo "<table width='100%' cellspacing='1' cellpadding='2' >";
            echo "<thead>";
               echo "<tr>";
                  echo "<td>".JText::_('COM_ISSUETRACKER_PROJECT_NAME')."</td>";
                  echo "<td>".JText::_('COM_ISSUETRACKER_FIRST_OPENED_DATE')."</td>";
                  echo "<td>".JText::_('COM_ISSUETRACKER_LAST_CLOSED_DATE')."</td>";
                  echo "<td>".JText::_('COM_ISSUETRACKER_TOTAL_ISSUES')."</td>";
                  echo "<td>".JText::_('COM_ISSUETRACKER_OPEN_ISSUES')."</td>";
                  echo "<td>".JText::_('COM_ISSUETRACKER_ONHOLD_ISSUES')."</td>";
                  echo "<td>".JText::_('COM_ISSUETRACKER_INPROGRESS_ISSUES')."</td>";
                  echo "<td>".JText::_('COM_ISSUETRACKER_CLOSED_ISSUES')."</td>";
                  echo "<td>".JText::_('COM_ISSUETRACKER_OPEN_NOPRIOR')."</td>";
                  echo "<td>".JText::_('COM_ISSUETRACKER_OPEN_HIGH')."</td>";
                  echo "<td>".JText::_('COM_ISSUETRACKER_OPEN_MEDIUM')."</td>";
                  echo "<td>".JText::_('COM_ISSUETRACKER_OPEN_LOW')."</td>";
               echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
               foreach ( $rows as $row) { ?>
               <tr class="<?php echo "row$k"; ?>">
                  <td><?php echo $row->project_name; ?></td>
                  <td><?php echo $row->first_identified; ?></td>
                  <td><?php echo $row->last_closed; ?></td>
                  <td><?php echo $row->total_issues; ?></td>
                  <td><?php echo $row->open_issues; ?></td>
                  <td><?php echo $row->onhold_issues; ?></td>
                  <td><?php echo $row->inprogress_issues; ?></td>
                  <td><?php echo $row->closed_issues; ?></td>
                  <td><?php echo $row->open_no_prior; ?></td>
                  <td><?php echo $row->open_high_prior; ?></td>
                  <td><?php echo $row->open_medium_prior; ?></td>
                  <td><?php echo $row->open_low_prior; ?></td>
              </tr>
              <?php } echo "</tbody>";
            echo "</table>";
            echo "</div>";
      }

      echo JHtml::_('tabs.panel', JText::_('COM_ISSUETRACKER_LATEST_ISSUES'), 'latestissuestab' );
      echo "<div>";
      $rows = &$this->latestIssues;
      echo "<table width='100%' cellspacing='1' cellpadding='2' >";
         echo "<thead>";
            echo "<tr>";
               echo "<td>".JText::_('COM_ISSUETRACKER_IDENTIFIED_DATE')."</td>";
               echo "<td>".JText::_('COM_ISSUETRACKER_ISSUE_SUMMARY')."</td>";
               echo "<td>".JText::_('COM_ISSUETRACKER_PROJECT_NAME')."</td>";
               echo "<td>".JText::_('JPUBLISHED')."</td>";
            echo "</tr>";
         echo "</thead>";
         echo "<tbody>";
            foreach ( $rows as $row) {
               $link    = JRoute::_( 'index.php?option=com_issuetracker&task=itissues.edit&id='. $row->id );
            ?>
            <tr class="<?php echo "row$k"; ?>">
               <td><?php echo $row->issuedate; ?></td>
               <td><?php echo "<a href='" . $link . "'>"; echo $row->issue_summary; echo "</a>"; ?></td>
               <td><?php echo $row->project_name; ?></td>
               <!-- td><?php if ( $row->state) {
                            echo "<img src='" . $_root . "administrator/templates/bluestork/images/admin/tick.png' width='16px' height='16px' />";
                        } else {
                            echo "<img src='" . $_root . "administrator/templates/bluestork/images/admin/publish_r.png' width='16px' height='16px' />";
                        }
                  ?>
               </td -->
               <td class="center">
                  <?php echo JHtml::_('jgrid.published', $row->state, $row, 'itissueslist.', 0, 'cb'); ?>
               </td>
           </tr>
           <?php  } echo "</tbody>";
         echo "</table>";
         echo "</div>";

      echo JHtml::_('tabs.panel', JText::_('COM_ISSUETRACKER_OVERDUE_ISSUES'), 'overdueissuestab' );
      echo "<div>";
      $rows = &$this->overdueIssues;
      echo "<table width='100%' cellspacing='1' cellpadding='2' >";
         echo "<thead>";
            echo "<tr>";
               echo "<td>".JText::_('COM_ISSUETRACKER_ASSIGNEE')."</td>";
               echo "<td>".JText::_('COM_ISSUETRACKER_TARGET_DATE')."</td>";
               echo "<td>".JText::_('COM_ISSUETRACKER_PROJECT_NAME')."</td>";
               echo "<td>".JText::_('COM_ISSUETRACKER_PRIORITY')."</td>";
               echo "<td>".JText::_('COM_ISSUETRACKER_ISSUE_SUMMARY')."</td>";
            echo "</tr>";
         echo "</thead>";
         echo "<tbody>";
            foreach ( $rows as $row ) {
               $link    = JRoute::_( 'index.php?option=com_issuetracker&task=itissues.edit&id='. $row->id );
            ?>
            <tr class="<?php echo "row$k"; ?>">
               <td><?php echo $row->assignee; ?></td>
               <td><?php echo $row->target_resolution_date; ?></td>
               <td><?php echo $row->project_name; ?></td>
               <td><?php echo $row->priority; ?></td>
               <td><?php echo "<a href='" . $link . "'>"; echo $row->issue_summary; echo "</a>"; ?></td>
           </tr>
           <?php  } echo "</tbody>";
         echo "</table>";
         echo "</div>";

      if ($this->params->get('show_unassigned_rep', 0)) {
      echo JHtml::_('tabs.panel', JText::_('COM_ISSUETRACKER_UNASSIGNED_ISSUES'), 'unassignedissuestab' );
      echo "<div>";
      $rows = &$this->unassignedIssues;
      echo "<table width='100%' cellspacing='1' cellpadding='2' >";
         echo "<thead>";
            echo "<tr>";
               echo "<td>".JText::_('COM_ISSUETRACKER_ISSUE_SUMMARY')."</td>";
               echo "<td>".JText::_('COM_ISSUETRACKER_PROJECT_NAME')."</td>";
               echo "<td>".JText::_('COM_ISSUETRACKER_IDENTIFIEE')."</td>";
               echo "<td>".JText::_('COM_ISSUETRACKER_TARGET_DATE')."</td>";
               echo "<td>".JText::_('COM_ISSUETRACKER_PRIORITY')."</td>";
            echo "</tr>";
         echo "</thead>";
         echo "<tbody>";
            foreach ( $rows as $row) {
               $link    = JRoute::_( 'index.php?option=com_issuetracker&task=itissues.edit&id='. $row->id );
            ?>
            <tr class="<?php echo "row$k"; ?>">
               <td><?php echo "<a href='" . $link . "'>"; echo $row->issue_summary; echo "</a>"; ?></td>
               <td><?php echo $row->project_name; ?></td>
               <td><?php echo $row->identifiee; ?></td>
               <td><?php echo $row->target_resolution_date; ?></td>
               <td><?php echo $row->priority; ?></td>
           </tr>
           <?php  } echo "</tbody>";
         echo "</table>";
         echo "</div>";
      // echo $pane->endPanel();
      }

      echo JHtml::_('tabs.panel', JText::_('COM_ISSUETRACKER_ABOUT'), 'abouttab' );
         ?>
         <div style="text-align:center">
            <div style="margin: 10px 0px 0px 0px">
               <h1>Issue Tracker</h1>
            </div>

            <div>
               <h2>
               <?php echo JText::_('COM_ISSUETRACKER_VERSION') . " " . $version; ?>
               </h2>
            </div>

            <div>
               <h3>
               <?php echo JText::_('COM_ISSUETRACKER_BY'); ?>
               </h3>
            </div>

            <div style="margin: 10px 0px 10px 0px">
               <a href="http://www.macrotoneconsulting.co.uk" title="Macrotone" target="_blank"><img alt="Macrotone Consulting Ltd." src="../media/com_issuetracker/images/system/macrotone.png" /></a>
            </div>

            <div>
               <br />
               G S Chapman
               <br />
               <br />
                  <a href="#" id="btnchangelog">CHANGELOG</a>
               <br />
            </div>
            <div style="display:none;">
               <div id="tracker-changelog">
                  <?php
                     require_once dirname(__FILE__).'/coloriser.php';
                     echo IssueTrackerChangelogColoriser::colorise(JPATH_COMPONENT_ADMINISTRATOR.'/CHANGELOG.php');
                  ?>
               </div>
            </div>

            <div style="text-align: center;" >
               <br />
               <a target="_blank" title="Donate online to Macrotone" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=macrotone%40macrotone%2eco%2euk&lc=GB&item_name=Macrotone%2eco%2euk&item_number=Issue%20Tracker&no_note=0&currency_code=GBP&bn=PP%2dDonationsBF%3apaypal%2epng%3aNonHostedGuest">
               <img src="https://www.paypal.com/en_US/i/btn/x-click-butcc-donate.gif" alt="PayPal - The safer, easier way to donate online!" type="image" /></a>
            </div>

            <div>
               <br />
               <h3>
               <?php echo JText::_('COM_ISSUETRACKER_CREDITS'); ?>
               </h3>
            </div>

            <div>
               <br />
               <?php echo JText::_('COM_ISSUETRACKER_CREDIT_TEXT1A'); ?>
               <?php echo JText::_('COM_ISSUETRACKER_CREDIT_TEXT1B'); ?>
               <?php echo JText::_('COM_ISSUETRACKER_CREDIT_TEXT1C'); ?>
               <?php echo JText::_('COM_ISSUETRACKER_CREDIT_TEXT1D'); ?>
               <?php echo JText::_('COM_ISSUETRACKER_CREDIT_TEXT1E'); ?>
               <?php echo JText::_('COM_ISSUETRACKER_CREDIT_TEXT1F'); ?>
              <br /><br />
               <?php echo JText::_('COM_ISSUETRACKER_CREDIT_TEXT2'); ?>
               <br /><br />
               <?php echo JText::_('COM_ISSUETRACKER_CREDIT_TEXT3'); ?>
               <br /><br />
               <?php echo JText::_('COM_ISSUETRACKER_CREDIT_TEXT4'); ?>
            </div>

         </div>
         <?php
      echo JHtml::_('tabs.end');
   ?>
</div>

<div class="clr"></div>

<form method="post" name="adminForm" id="adminForm">
    <input type="hidden" name="c" value="default" />
    <input type="hidden" name="view" value="default" />
    <input type="hidden" name="option" value="com_issuetracker" />
    <input type="hidden" name="task" value="" />
</form>