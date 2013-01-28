<?php
/*
 *
 * @Version       $Id: icon.php 440 2012-09-10 10:33:15Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.1
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-09-10 11:33:15 +0100 (Mon, 10 Sep 2012) $
 *
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Content Component HTML Helper
 *
 * @static
 * @package    Joomla.Site
 * @subpackage com_issuetracker
 * @since 1.5
 *
 * Note only the email and print pop up are currently being used.  Other code is not exercised. 19/02/2012 GSC
 * Edit code now exercised 10/08/2012 GSC
 */
class JHtmlIcon
{
   static function create($article, $params)
   {
      $uri = JFactory::getURI();

      $url = 'index.php?option=com_issuetracker&task=itissues.add&return='.base64_encode($uri).'&a_id=0';

      if ($params->get('show_icons')) {
         $text = JHtml::_('image','system/new.png', JText::_('JNEW'), NULL, true);
      } else {
         $text = JText::_('JNEW').'&#160;';
      }

      $button =  JHtml::_('link',JRoute::_($url), $text);

      $output = '<span class="hasTip" title="'.JText::_('COM_ISSUETRACKER_CREATE_ISSUE').'">'.$button.'</span>';
      return $output;
   }

   static function email($article, $params, $attribs = array())
   {
      require_once(JPATH_SITE . '/components/com_mailto/helpers/mailto.php');
      // Added to resolve helper call.
      require_once(JPATH_SITE . '/components/com_issuetracker/helpers/route.php');

      $uri        = JURI::getInstance();
      $base       = $uri->toString(array('scheme', 'host', 'port'));
      $template   = JFactory::getApplication()->getTemplate();
      if (isset($article->id)) {
         $link = $base.JRoute::_(IssueTrackerHelperRoute::getIssueRoute($article->id) , false);
      } else {
         $link = $base.JRoute::_(IssueTrackerHelperRoute::getIssueRoute("") , false);
      }
      $url     = 'index.php?option=com_mailto&tmpl=component&template='.$template.'&link='.MailToHelper::addLink($link);

      $status  = 'width=400,height=350,menubar=yes,resizable=yes';

      if ($params->get('show_icons')) {
         $text = JHtml::_('image','system/emailButton.png', JText::_('JGLOBAL_EMAIL'), NULL, true);
      } else {
         $text = '&#160;'.JText::_('JGLOBAL_EMAIL');
      }

      $attribs['title'] = JText::_('JGLOBAL_EMAIL');
      $attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";

      $output = JHtml::_('link',JRoute::_($url), $text, $attribs);
      return $output;
   }

   /**
    * Display an edit icon for the item.
    *
    * This icon will not display in a popup window, nor if the article is trashed.
    * Edit access checks must be performed in the calling code.
    *
    * @param   object   $article    The item in question.
    * @param   object   $params     The item parameters
    * @param   array    $attribs    Not used??
    *
    * @return  string   The HTML for the article edit icon.
    * @since   1.6
    */
   static function edit($article, $params, $attribs = array())
   {
      // Initialise variables.
      $user    = JFactory::getUser();
      $userId  = $user->get('id');
      $uri     = JFactory::getURI();

      // Ignore if in a popup window.
      if ($params && $params->get('popup')) {
         return;
      }

      // Ignore if the state is negative (trashed).
      if ($article->state < 0) {
         return;
      }

      // Ignore if we are not permitting front end editing
      if ($params->get('allow_fe_edit') == 0 ) {
         return;
      }

      JHtml::_('behavior.tooltip');

      // Show checked_out icon if the article is checked out by a different user
      if (property_exists($article, 'checked_out') && property_exists($article, 'checked_out_time') && $article->checked_out > 0 && $article->checked_out != $user->get('id')) {
         $checkoutUser  = JFactory::getUser($article->checked_out);
         $button        = JHtml::_('image','system/checked_out.png', NULL, NULL, true);
         $date          = JHtml::_('date',$article->checked_out_time);
         $tooltip       = JText::_('JLIB_HTML_CHECKED_OUT').' :: '.JText::sprintf('COM_ISSUETRACKER_CHECKED_OUT_BY', $checkoutUser->name).' <br /> '.$date;
         return '<span class="hasTip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
      }

      // $url     = 'index.php?option=com_issuetracker&task=itissues.edit&a_id='.$article->id.'&return='.base64_encode($uri);
      $url     = 'index.php?option=com_issuetracker&view=form&layout=edit&a_id='.$article->id.'&return='.base64_encode($uri);
      $icon    = $article->state ? 'edit.png' : 'edit_unpublished.png';
      $text    = JHtml::_('image','system/'.$icon, JText::_('JGLOBAL_EDIT'), NULL, true);

      if ($article->state == 0) {
         $overlib = JText::_('JUNPUBLISHED');
      }
      else {
         $overlib = JText::_('JPUBLISHED');
      }

      $date    = JHtml::_('date',$article->created_on);
      // $author = $article->created_by ? $article->created_by : $article->author;
      $author  = $article->created_by;

      $overlib .= '&lt;br /&gt;';
      $overlib .= $date;
      $overlib .= '&lt;br /&gt;';
      $overlib .= JText::sprintf('COM_ISSUETRACKER_CREATED_BY',htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));

      $button  = JHtml::_('link',JRoute::_($url), $text);

      $output  = '<span class="hasTip" title="'.JText::_('COM_ISSUETRACKER_EDIT_ISSUE').' :: '.$overlib.'">'.$button.'</span>';

      return $output;
   }


   static function print_popup($id, $params, $attribs = array())
   {

      $url     = '&tmpl=component&print=1&layout=default&page='.@ $request->limitstart;
      $status  = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

      // checks template image directory for image, if non found default are loaded
      if ($params->get('show_icons')) {
         $text = JHtml::_('image','system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);
      } else {
         $text = JText::_('JGLOBAL_ICON_SEP') .'&#160;'. JText::_('JGLOBAL_PRINT') .'&#160;'. JText::_('JGLOBAL_ICON_SEP');
      }

      $attribs['title'] = JText::_('JGLOBAL_PRINT');
      $attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
      $attribs['rel']      = 'nofollow';

      return JHtml::_('link',JRoute::_($url), $text, $attribs);
   }

   static function print_screen($article, $params, $attribs = array())
   {
      // checks template image directory for image, if non found default are loaded
      if ($params->get('show_icons')) {
         $text = JHtml::_('image','system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);
      } else {
         $text = JText::_('JGLOBAL_ICON_SEP') .'&#160;'. JText::_('JGLOBAL_PRINT') .'&#160;'. JText::_('JGLOBAL_ICON_SEP');
      }
      return '<a href="#" onclick="window.print();return false;">'.$text.'</a>';
   }

}
