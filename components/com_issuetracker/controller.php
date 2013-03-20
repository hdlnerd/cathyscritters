<?php
/**
 * com_issuetracker default controller
 *
 * @Version       $Id: controller.php 724 2013-02-22 15:53:06Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-22 15:53:06 +0000 (Fri, 22 Feb 2013) $
 *
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Issue Tracker Component Controller
 *
 * @package Issue Tracker
 */
class IssueTrackerController extends JController
{
   /**
    * __construct
    *
    * @param array $config
    */
   function __construct($config = array())
   {
       parent::__construct($config);
   }

   /**
    * Method to display the view
    *
    * @param  boolean         If true, the view output will be cached
    * @param  array           An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
    *
    * @return JController     This object to support chaining.
    *
    * @access  public
    */
   function display($cachable = false, $urlparams = false)
   {

      $cachable = true;
      $view    = JRequest::getCmd('view', 'itissueslist');
      JRequest::setVar('view', $view);
      $layout  = JRequest::getCmd('layout', 'default');
      // Note we are using a_id to avoid collisions with the router and the return page.
      // Frontend is a bit messier than the backend.
      $id      = JRequest::getInt('a_id');

      $user    = JFactory::getUser();

      if ($user->get('id') || $_SERVER['REQUEST_METHOD'] == 'POST') {
         $cachable = false;
      }

      $safeurlparams = array('catid'=>'INT','id'=>'INT','cid'=>'ARRAY','year'=>'INT','month'=>'INT','limit'=>'INT','limitstart'=>'INT',
         'showall'=>'INT','return'=>'BASE64','filter'=>'STRING','filter_order'=>'CMD','filter_order_Dir'=>'CMD','filter-search'=>'STRING','print'=>'BOOLEAN','lang'=>'CMD');

      // Check for edit form.
      if ($view == 'itissues' && $layout == 'edit' && !$this->checkEditId('com_issuetracker.edit.itissues', $id)) {
         // Somehow the person just went direct to the form - we don't allow that.
         $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
         $this->setMessage($this->getError(), 'error');
         $this->setRedirect(JRoute::_('index.php?option=com_issuetracker&view=itissueslist', false));
         return false;
      }

       parent::display($cachable, $safeurlparams);
   }
}