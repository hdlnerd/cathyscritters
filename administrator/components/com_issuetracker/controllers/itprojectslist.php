<?php
/*
 *
 * @Version       $Id: itprojectslist.php 669 2013-01-04 14:39:25Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-01-04 14:39:25 +0000 (Fri, 04 Jan 2013) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Issue Tracker Controller
 *
 * @package       Joomla.Components
 * @subpackage    com_issuetracker
 */

jimport('joomla.application.component.controlleradmin');

class IssuetrackerControllerItprojectslist extends JControllerAdmin
{
   /**
    * Proxy for getModel.
    * @since   1.6
    */
   public function getModel($name = 'itprojects', $prefix = 'IssuetrackerModel')
   {
      $model = parent::getModel($name, $prefix, array('ignore_request' => true));
      return $model;
   }

  /**
   * Save the manual order inputs from the categories list page.
   *
   * @return  void
   * @since   1.6
   */
  public function saveorder()
  {
     JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

     // Get the arrays from the Request
     $order   = JRequest::getVar('order',   null, 'post', 'array');
     $originalOrder = explode(',', JRequest::getString('original_order_values'));

     // Make sure something has changed
     if (!($order === $originalOrder)) {
        parent::saveorder();
     } else {
        // Nothing to reorder
        $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
        return true;
     }
  }

  /**
    * Rebuild the nested set tree.
    *
    * @return  bool  False on failure or error, true on success.
    * @since   1.6
    */
   public function rebuild()
   {
      JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

      $extension = 'com_issuetracker';
      $this->setRedirect(JRoute::_('index.php?option=com_issuetracker&view=itprojectslist&extension='.$extension, false));

      // Initialise variables.
      $model = $this->getModel();

      if ($model->rebuild()) {
         // Rebuild succeeded.
         $this->setMessage(JText::_('COM_ISSUETRACKER_PROJECTS_REBUILD_SUCCESS'));
         return true;
      } else {
         // Rebuild failed.
         $this->setMessage(JText::_('COM_ISUETRACKER_PROJECTS_REBUILD_FAILURE'));
         return false;
      }
   }
}