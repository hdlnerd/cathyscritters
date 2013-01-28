<?php
/*
 *
 * @Version       $Id: view.html.php 414 2012-09-04 16:33:46Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.1
 * @Copyright     Copyright (C) 2011 - 2012 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2012-09-04 17:33:46 +0100 (Tue, 04 Sep 2012) $
 *
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

if (! class_exists('IssueTrackerHelperSite')) {
    require_once( JPATH_ROOT.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'helper.php');
}

/**
 * HTML Article View class for the Issue Tracker component
 *
 * @package    Joomla.Site
 * @subpackage com_issuetracker
 * @since      1.5
 */
class IssueTrackerViewForm extends JView
{
   protected $form;
   protected $item;
   protected $return_page;
   protected $state;
   protected $viewonly;

   public function display($tpl = null)
   {
      // Initialise variables.
      $app     = JFactory::getApplication();
      $user    = JFactory::getUser();

      // Get model data.
      $this->state      = $this->get('State');
      $this->item       = $this->get('Item');
      $this->form       = $this->get('Form');
      $this->print      = JRequest::getBool('print');

      $viewonly         = 0;

      $this->return_page   = $this->get('ReturnPage');
      $isadmin             = IssueTrackerHelperSite::isIssueAdmin($user->id);
      $this->state->params->set('issues_admin',$isadmin);

      if (empty($this->item->id)) {
         $authorised = $user->authorise('core.create', 'com_issuetracker') || (count($user->getAuthorisedCategories('com_issuetracker', 'core.create')));
      } else {
         $userauth   = $this->item->params->get('access-edit');
         $authorised = $userauth || $isadmin;
      }

      $wysiwyg   = $this->item->params->get('wysiwyg_editor');

      $this->state->params->set('new_record','0');
      $this->state->params->set('admin_edit','0');

      // Field defaults are set up for a new issue creation so we have to change this if we are editing.
      if ( $authorised === true ) {
         if (! empty($this->item->id)) {
            // Change display for editable fields
            $this->state->params->set('show_product_req','0');
            $this->form->setFieldAttribute('additional_info',     'type',     'editor');
            $this->form->setFieldAttribute('issue_summary',       'readonly', 'true');
            $this->form->setFieldAttribute('issue_summary',       'required', 'false');
            $this->form->setFieldAttribute('issue_description',   'readonly', 'true');
            $this->form->setFieldAttribute('issue_description',   'required', 'false');
            $this->form->setFieldAttribute('identified_date',     'readonly', 'true');
            $this->form->setFieldAttribute('identified_date',     'disabled', 'true');
 //           $this->form->setFieldAttribute('alias',             'type',     'text');
 //           $this->form->setFieldAttribute('alias',             'readonly', 'true');
            $this->form->setFieldAttribute('status',              'type',     'text');
            $this->form->setFieldAttribute('status',              'readonly', 'true');
            $this->form->setFieldAttribute('priority',            'type',     'text');
            $this->form->setFieldAttribute('priority',            'readonly', 'true');

            if ( $userauth ) {
               // Specific user only changeable fields
               $this->state->params->set('admin_edit','0');
               // additional details
               // $this->state->params->set('show_product_req','0');
               // $this->form->setFieldAttribute('issue_summary',             'type',     'editor');
               // $this->form->setFieldAttribute('issue_summary',             'filter',   'safehtml');
               // $this->form->setFieldAttribute('issue_summary',             'hide',     'articlesanywhere,image,pagebreak,readmore,article');
               // $this->form->setFieldAttribute('issue_description',         'readonly', 'true');
               // $this->form->setFieldAttribute('issue_description',         'type',     'editor');
               // $this->form->setFieldAttribute('issue_description',         'filter',   'safehtml');
               // $this->form->setFieldAttribute('issue_description',         'hide',     'articlesanywhere,image,pagebreak,readmore,article');
               $this->form->setFieldAttribute('additional_info',           'required', 'true');
               // $this->form->setFieldAttribute('additional_info',           'readonly', 'false');
               if ( $wysiwyg == 1 ) {
                  $this->form->setFieldAttribute('additional_info',           'type',     'editor');
               } else {
                  $this->form->setFieldAttribute('additional_info',           'type',     'textarea');
               }
               // $this->form->setFieldAttribute('additional_info',           'hide',     'articlesanywhere,image,pagebreak,readmore,article');
               $this->form->setFieldAttribute('additional_info',           'filter',   'safehtml');
               // $this->form->setFieldAttribute('resolution_summary',        'type',     'editor');
               // $this->form->setFieldAttribute('resolution_summary',        'filter',   'safehtml');
               // $this->form->setFieldAttribute('resolution_summary',        'readonly', 'true');
               // $this->form->setFieldAttribute('resolution_summary',        'hide',     'articlesanywhere,image,pagebreak,readmore,article');
               // Restrictive status to open or closed for the user editing only.
               // $this->form->setFieldAttribute('status',                 'type',     'issuetracker_r_status');
               $this->form->setFieldAttribute('identified_by_person_id',   'readonly', 'true');
               $this->form->setFieldAttribute('identified_by_person_id',   'required', 'false');
               $this->form->setFieldAttribute('identified_by_person_id',   'disabled', 'true');
               $this->form->setFieldAttribute('identified_by_person_id',   'type',     'personname');
               $this->form->setFieldAttribute('status',                    'type',     'statusname');
               $this->form->setFieldAttribute('status',                    'readonly', 'true');
               $this->form->setFieldAttribute('priority',                  'type',     'priorityname');
               $this->form->setFieldAttribute('priority',                  'readonly', 'true');
               $this->form->setFieldAttribute('assigned_to_person_id',     'readonly', 'true');
               $this->form->setFieldAttribute('assigned_to_person_id',     'disabled', 'false');
               $this->form->setFieldAttribute('assigned_to_person_id',     'type',     'personname');
               $this->form->setFieldAttribute('target_resolution_date',    'required', 'false');
               $this->form->setFieldAttribute('target_resolution_date',    'readonly', 'true');
               $this->form->setFieldAttribute('target_resolution_date',    'disabled', 'true');
               $this->form->setFieldAttribute('actual_resolution_date',    'required', 'false');
               $this->form->setFieldAttribute('actual_resolution_date',    'readonly', 'true');
               $this->form->setFieldAttribute('actual_resolution_date',    'disabled', 'true');
               $this->form->setFieldAttribute('progress',                  'readonly', 'true');
               // $this->form->setFieldAttribute('progress',                  'type',     'editor');
               // $this->form->setFieldAttribute('progress',                  'filter',   'safehtml');
               // $this->form->setFieldAttribute('progress',                  'buttons',  'false');
               // $this->form->setFieldAttribute('progress',                  'hide',     'articlesanywhere,pagebreak,readmore,image,article,toggle editor');
            }

            if ( $isadmin ) {
               // Specific administrator only changable fields
               $this->state->params->set('admin_edit','1');
               $this->state->params->set('show_target_date_field','1');
               $this->form->setFieldAttribute('issue_summary',             'type',     'editor');
               $this->form->setFieldAttribute('issue_summary',             'filter',   'safehtml');
               $this->form->setFieldAttribute('issue_summary',             'hide',     'articlesanywhere,pagebreak,image,readmore,article');
               $this->form->setFieldAttribute('additional_info',           'type',     'hidden');
               $this->form->setFieldAttribute('additional_info',           'required', 'false');
               $this->form->setFieldAttribute('notify',                    'type',     'hidden');
               $this->form->setFieldAttribute('issue_description',         'type',     'editor');
               $this->form->setFieldAttribute('issue_description',         'filter',   'safehtml');
               $this->form->setFieldAttribute('issue_description',         'hide',     'articlesanywhere,pagebreak,readmore,article');
               $this->form->setFieldAttribute('identified_by_person_id',   'type',     'issuetrackerperson');
               $this->form->setFieldAttribute('identified_date',           'type',     'calendar');
               $this->form->setFieldAttribute('identified_date',           'readonly', 'false');
               $this->form->setFieldAttribute('identified_date',           'disabled', 'false');
               $this->state->params->set('show_issue_state','1');

               $this->form->setFieldAttribute('status',                    'type',     'issuetrackerstatus');
               $this->form->setFieldAttribute('priority',                  'type',     'issuetrackerpriority');
               $this->form->setFieldAttribute('resolution_summary',        'type',     'editor');
               $this->form->setFieldAttribute('resolution_summary',        'filter',   'safehtml');
               $this->form->setFieldAttribute('resolution_summary',        'hide',     'articlesanywhere,pagebreak,readmore,article');
               $this->state->params->set('show_target_date_field','1');
               $this->form->setFieldAttribute('target_resolution_date',   'type',     'calendar');
               $this->state->params->set('show_actual_res_date','1');
               $this->form->setFieldAttribute('actual_resolution_date',    'type',     'calendar');
               $this->form->setFieldAttribute('target_resolution_date',    'required', 'false');
               $this->form->setFieldAttribute('target_resolution_date',    'readonly', 'false');
               $this->form->setFieldAttribute('target_resolution_date',    'disabled', 'false');
               $this->form->setFieldAttribute('actual_resolution_date',    'required', 'false');
               $this->form->setFieldAttribute('actual_resolution_date',    'readonly', 'false');
               $this->form->setFieldAttribute('actual_resolution_date',    'disabled', 'false');
               $this->state->params->set('show_staff_details','1');
               $this->form->setFieldAttribute('assigned_to_person_id',     'type',     'issuetrackerperson');

               // Since we are an issue admin we can update the progress field.
               $this->state->params->set('show_progress_field','1');
               $this->form->setFieldAttribute('progress', 'type',    'editor');
               $this->form->setFieldAttribute('progress', 'filter',  'safehtml');
               $this->form->setFieldAttribute('progress', 'hide',    'articlesanywhere,pagebreak,readmore,article');

               // Required to prevent saving error
               $this->form->setFieldAttribute('product_version', 'required', 'false');
               $this->form->setFieldAttribute('pdetails', 'required', 'false');

            }
         } else {
            $this->form->setFieldAttribute('additional_info', 'required', 'false');
            // New record creation
            $this->state->params->set('new_record','1');
            if ( $user->guest ) {
               // Nothing special at the moment, defaults are fine.
            } else {
               // A logged in user  Give them a proper editor if configured.
               if ( $wysiwyg == 1 || $isadmin ) {
                  $this->form->setFieldAttribute('issue_summary',       'type', 'editor');
                  $this->form->setFieldAttribute('issue_description',   'type', 'editor');
               } else {
                  $this->form->setFieldAttribute('issue_summary',       'type', 'textarea');
                  $this->form->setFieldAttribute('issue_description',   'type', 'textarea');
               }
               $this->form->setFieldAttribute('issue_summary',       'filter',   'safehtml');
               $this->form->setFieldAttribute('issue_summary',       'hide',     'articlesanywhere,pagebreak,image,readmore,article');
               $this->form->setFieldAttribute('additional_info',     'type',     'hidden');
               $this->form->setFieldAttribute('issue_description',   'hide',     'articlesanywhere,pagebreak,readmore,article');
               $this->form->setFieldAttribute('issue_description',   'readonly', 'false');
               $this->form->setFieldAttribute('issue_description',   'disabled', 'false');
               $this->form->setFieldAttribute('issue_description',   'required', 'true');
               $this->form->setFieldAttribute('issue_description',   'filter',   'safehtml');
               // Allow them to set a priority as well.  We do not have to stick to it!
               $this->form->setFieldAttribute('priority',            'type',     'issuetrackerpriority');
            }

            if ( $isadmin ) {
               // Allow admin to open and closed with all progress and resolution fields available
               $this->state->params->set('admin_edit','1');
               $this->state->params->set('show_product_req','0');
               $this->form->setFieldAttribute('notify',                    'type',     'hidden');
               $this->state->params->set('show_resolution_field','1');
               $this->form->setFieldAttribute('resolution_summary',        'type',     'editor');
               $this->form->setFieldAttribute('resolution_summary',        'filter',   'safehtml');
               $this->form->setFieldAttribute('resolution_summary',        'hide',     'articlesanywhere,pagebreak,readmore,article');
               $this->state->params->set('show_progress_field','1');
               $this->form->setFieldAttribute('progress',                  'type',     'editor');
               $this->form->setFieldAttribute('progress',                  'filter',   'safehtml');
               $this->form->setFieldAttribute('progress',                  'hide',     'articlesanywhere,pagebreak,readmore,article');
               $this->state->params->set('show_target_date_field','1');
               $this->form->setFieldAttribute('target_resolution_date',    'type',     'calendar');
               $this->state->params->set('show_actual_res_date','1');
               $this->form->setFieldAttribute('actual_resolution_date',    'type',     'calendar');
               $this->form->setFieldAttribute('identified_by_person_id',   'type',     'issuetrackerperson');
               $this->form->setFieldAttribute('assigned_to_person_id',     'type',     'issuetrackerperson');
               $this->form->setFieldAttribute('priority',                  'type',     'issuetrackerpriority');
               $this->state->params->set('show_issue_status','1');
               $this->state->params->set('show_issue_state','1');
               $this->form->setFieldAttribute('status',                    'type',     'issuetrackerstatus');
               $this->state->params->set('show_identified_by','1');
               $this->state->params->set('show_staff_details','1');
               $this->form->setFieldAttribute('assigned_to_person_id',     'type',     'issuetrackerstaff');
               $this->form->setFieldAttribute('additional_info',           'type',     'hidden');
               // $this->form->setFieldAttribute('issue_description', 'readonly', 'false');
               // $this->form->setFieldAttribute('issue_description', 'disabled', 'false');
               // $this->form->setFieldAttribute('issue_description', 'type', 'editor');
               // $this->form->setFieldAttribute('issue_description', 'filter', 'safehtml');
               // $this->form->setFieldAttribute('issue_description', 'hide', 'articlesanywhere,pagebreak,readmore,article');
               $this->form->setFieldAttribute('additional_info',           'required', 'false');
               $this->form->setFieldAttribute('product_version',           'required', 'false');
               $this->form->setFieldAttribute('pdetails',                  'required', 'false');
            }
         }
      } else {
         // View only now instead of redirect back.   Not authorised fall through.
         // Should not ever get here but just in case!
/*
         $this->state->params->set('allow_fe_edit','0');
         $this->form->setFieldAttribute('issue_summary', 'readonly', 'true');
         $this->form->setFieldAttribute('issue_summary', 'required', 'false');
         $this->form->setFieldAttribute('issue_summary', 'disabled', 'true');
         $this->form->setFieldAttribute('issue_description', 'readonly', 'true');
         $this->form->setFieldAttribute('issue_description', 'required', 'false');
         $this->form->setFieldAttribute('issue_description', 'disabled', 'true');
         $this->form->setFieldAttribute('issue_description', 'type', 'editor');
         $this->form->setFieldAttribute('issue_description', 'filter', 'safehtml');
         $this->form->setFieldAttribute('issue_description', 'buttons', 'false');
         $this->form->setFieldAttribute('issue_description', 'hide', 'articlesanywhere,pagebreak,readmore,image,article,toggle editor');
         $this->form->setFieldAttribute('identified_date', 'readonly', 'true');
         $this->form->setFieldAttribute('identified_date', 'disabled', 'true');
         $this->form->setFieldAttribute('alias', 'type', 'hidden');
//         $this->form->setFieldAttribute('alias', 'readonly', 'true');
//         $this->form->setFieldAttribute('alias', 'disabled', 'true');
         $this->form->setFieldAttribute('status', 'type', 'statusname');
         $this->form->setFieldAttribute('status', 'readonly', 'true');
         $this->form->setFieldAttribute('priority', 'type', 'priorityname');
         $this->form->setFieldAttribute('priority', 'readonly', 'true');
         $this->form->setFieldAttribute('issue_type', 'readonly', 'true');
         $this->form->setFieldAttribute('issue_type', 'required', 'false');
         $this->form->setFieldAttribute('issue_type', 'disabled', 'true');
         $this->form->setFieldAttribute('issue_type', 'type', 'issuetypename');
         $this->form->setFieldAttribute('identified_by_person_id', 'readonly', 'true');
         $this->form->setFieldAttribute('identified_by_person_id', 'required', 'false');
         $this->form->setFieldAttribute('identified_by_person_id', 'disabled', 'true');
         $this->form->setFieldAttribute('identified_by_person_id', 'type', 'personname');
         $this->form->setFieldAttribute('related_project_id', 'readonly', 'true');
         $this->form->setFieldAttribute('related_project_id', 'required', 'false');
         $this->form->setFieldAttribute('related_project_id', 'disabled', 'true');
         $this->form->setFieldAttribute('related_project_id', 'type', 'projectname');
         $this->form->setFieldAttribute('notify', 'readonly', 'true');
         $this->form->setFieldAttribute('notify', 'disabled', 'true');
         $this->form->setFieldAttribute('notify', 'type', 'hidden');
         $this->form->setFieldAttribute('resolution_summary', 'disabled', 'true');
         $this->form->setFieldAttribute('resolution_summary', 'readonly', 'true');
         $this->form->setFieldAttribute('resolution_summary', 'required', 'false');
         $this->form->setFieldAttribute('resolution_summary', 'type', 'editor');
         $this->form->setFieldAttribute('resolution_summary', 'filter', 'safehtml');
         $this->form->setFieldAttribute('resolution_summary', 'buttons', 'false');
         $this->form->setFieldAttribute('resolution_summary', 'hide', 'articlesanywhere,pagebreak,readmore,image,article,toggle editor');
         $this->form->setFieldAttribute('progress', 'type', 'editor');
         $this->form->setFieldAttribute('progress', 'filter', 'safehtml');
         $this->form->setFieldAttribute('progress', 'buttons', 'false');
         $this->form->setFieldAttribute('progress', 'hide', 'articlesanywhere,pagebreak,readmore,image,article,toggle editor');

         $this->form->setFieldAttribute('target_resolution_date', 'required', 'false');
         $this->form->setFieldAttribute('target_resolution_date', 'readonly', 'true');
         $this->form->setFieldAttribute('target_resolution_date', 'disabled', 'true');
         $this->form->setFieldAttribute('actual_resolution_date', 'required', 'false');
         $this->form->setFieldAttribute('actual_resolution_date', 'readonly', 'true');
         $this->form->setFieldAttribute('actual_resolution_date', 'disabled', 'true');
         $this->form->setFieldAttribute('assigned_to_person_id', 'readonly', 'true');
         $this->form->setFieldAttribute('assigned_to_person_id', 'disabled', 'false');
         $this->form->setFieldAttribute('assigned_to_person_id', 'type', 'personname');

*/
         $previousurl = $_SERVER['HTTP_REFERER'];
         $msg = JText::_('COM_ISSUETRACKER_LOGON_OR_REG_MSG');
         $app->redirect($previousurl, $msg);

      }

      if (empty($this->item)) {
        } else {
         $this->form->bind($this->item);
      }

      // Check for errors.
      if (count($errors = $this->get('Errors'))) {
         JError::raiseWarning(500, implode("\n", $errors));
         return false;
      }

      // Create a shortcut to the parameters.
      $parameters = &$this->state->params;

      //Escape strings for HTML output
      $this->pageclass_sfx = htmlspecialchars($parameters->get('pageclass_sfx'));

      $this->parameters = $parameters;
      $this->user = $user;

      // Set the default project and type in the display - May need to move this.
      // If a guest set general defaults otherwise set user defaults.
      if ( $user->guest ) {
         $def_proj = $this->parameters->get('def_project', 1);
       } else {
         // Get users default project
         $def_proj = IssueTrackerHelperSite::getUserdefproj($user->id);
      }

      $def_type = $this->parameters->get('def_type', 2);
      if ( empty( $this->item->id ) ) {
         $this->form->setFieldAttribute('related_project_id', 'default', $def_proj);
         $this->form->setFieldAttribute('issue_type', 'default', $def_type);
      }

      $this->_prepareDocument($this->item);
      parent::display($tpl);
   }

   /**
    * Prepares the document
    */
   protected function _prepareDocument($data)
   {
      $app        = JFactory::getApplication();
      $menus      = $app->getMenu();
      $title      = null;

      // Because the application sets a default page title,
      // we need to get it from the menu item itself
      $menu = $menus->getActive();
      if ($menu) {
         $this->parameters->def('page_heading', $this->parameters->get('page_title', $menu->title));
      } else {
         $this->parameters->def('page_heading', JText::_('COM_ISSUETRACKER_FORM_EDIT_ISSUE'));
      }

      $title = $this->parameters->def('page_title', JText::_('COM_ISSUETRACKER_FORM_EDIT_ISSUE'));
      if ($app->getCfg('sitename_pagetitles', 0) == 1) {
         $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);

      } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
         $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
      }
      $this->document->setTitle($title);

      if (!empty($data->id)) {
         $pathway = $app->getPathWay();
         $pathway->addItem('Issue '.$data->alias, '');
      }
   }
}