<?php
/*
 *
 * @Version       $Id: issuetracker.php 741 2013-02-27 16:33:26Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2013-02-27 16:33:26 +0000 (Wed, 27 Feb 2013) $
 *
 */

defined('JPATH_BASE') or die;

jimport('joomla.application.component.helper');

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

/**
 * Finder adapter for com_issuetracker.
 *
 * @package     Joomla.Plugin
 * @subpackage  Finder.Content
 * @since       2.5
 */
class plgFinderIssuetracker extends FinderIndexerAdapter
{
   /**
    * The plugin identifier.
    *
    * @var    string
    * @since  2.5
    */
   protected $context = 'Issues';

   /**
    * The extension name.
    *
    * @var    string
    * @since  2.5
    */
   protected $extension = 'com_issuetracker';

   /**
    * The sublayout to use when rendering the results.
    *
    * @var    string
    * @since  2.5
    *
    * Check this !
    */
   protected $layout = 'itissues';

   /**
    * The type of content that the adapter indexes.
    *
    * @var    string
    * @since  2.5
    */
   protected $type_title = 'Issue';

   /**
    * The table name.
    *
    * @var    string
    * @since  2.5
    */
   protected $table = '#__it_issues';

   /**
    * Constructor
    *
    * @param   object  &$subject  The object to observe
    * @param   array   $config    An array that holds the plugin configuration
    *
    * @since   2.5
    */
   public function __construct(&$subject, $config)
   {
      parent::__construct($subject, $config);
      $this->loadLanguage();
   }

   /**
    * Method to update the item link information when the item category is
    * changed. This is fired when the item category is published or unpublished
    * from the list view.
    *
    * @param   string   $extension  The extension whose category has been updated.
    * @param   array    $pks        A list of primary key ids of the content that has changed state.
    * @param   integer  $value      The value of the state that the content has been changed to.
    *
    * @return  void
    *
    * @since   2.5
    */
   public function onFinderCategoryChangeState($extension, $pks, $value)
   {
      // Make sure we're handling com_issuetracker projects
      if ($extension == 'com_issuetracker')
      {
         $this->categoryStateChange($pks, $value);
      }
   }

   /**
    * Method to remove the link information for items that have been deleted.
    *
    * @param   string  $context  The context of the action being performed.
    * @param   JTable  $table    A JTable object containing the record to be deleted
    *
    * @return  boolean  True on success.
    *
    * @since   2.5
    * @throws  Exception on database error.
    */
   public function onFinderAfterDelete($context, $table)
   {
      if ($context == 'com_issuetracker.itissues')
      {
         $id = $table->id;
      }
      elseif ($context == 'com_finder.index')
      {
         $id = $table->link_id;
      }
      else
      {
         return true;
      }
      // Remove the items.
      return $this->remove($id);
   }

   /**
    * Method to determine if the access level of an item changed.
    *
    * @param   string   $context  The context of the content passed to the plugin.
    * @param   JTable   $row      A JTable object
    * @param   boolean  $isNew    If the content has just been created
    *
    * @return  boolean  True on success.
    *
    * @since   2.5
    * @throws  Exception on database error.
    */
   public function onFinderAfterSave($context, $row, $isNew)
   {
      // We only want to handle articles here
      if ($context == 'com_issuetracker.itissues' || $context == 'com_issuetracker.form')
      {
         // Check if the access levels are different
         if (!$isNew && $this->old_access != $row->access)
         {
            // Process the change.
            $this->itemAccessChange($row);
         }

         // Reindex the item
         $this->reindex($row->id);
      }

      // Check for access changes in the project
      if ($context == 'com_issuetracker.itprojects')
      {
         // Check if the access levels are different
         if (!$isNew && $this->old_cataccess != $row->access)
         {
            $this->categoryAccessChange($row);
         }
      }
      return true;
   }

   /**
    * Method to reindex the link information for an item that has been saved.
    * This event is fired before the data is actually saved so we are going
    * to queue the item to be indexed later.
    *
    * @param   string   $context  The context of the content passed to the plugin.
    * @param   JTable   $row      A JTable object
    * @param   boolean  $isNew    If the content is just about to be created
    *
    * @return  boolean  True on success.
    *
    * @since   2.5
    * @throws  Exception on database error.
    */
   public function onFinderBeforeSave($context, $row, $isNew)
   {
      // We only want to handle issues here
      if ($context == 'com_issuetracker.itissues' || $context == 'com_issuetracker.form')
      {
         // Query the database for the old access level if the item isn't new
         if (!$isNew)
         {
            $this->checkItemAccess($row);
         }
      }

      // Check for access levels from the project
      if ($context == 'com_issuetracker.itprojects')
      {
         // Query the database for the old access level if the item isn't new
         if (!$isNew)
         {
            $this->checkCategoryAccess($row);
         }
      }

      return true;
   }

   /**
    * Method to update the link information for items that have been changed
    * from outside the edit screen. This is fired when the item is published,
    * unpublished, archived, or unarchived from the list view.
    *
    * @param   string   $context  The context for the content passed to the plugin.
    * @param   array    $pks      A list of primary key ids of the content that has changed state.
    * @param   integer  $value    The value of the state that the content has been changed to.
    *
    * @return  void
    *
    * @since   2.5
    */
   public function onFinderChangeState($context, $pks, $value)
   {
      // We only want to handle issues here
      if ($context == 'com_issuetracker.itissues' || $context == 'com_issuetracker.form')
      {
         $this->itemStateChange($pks, $value);
      }

      // We only want to handle project here
      if ($context == 'com_issuetracker.itprojects')
      {
         $this->itemStateChange($pks, $value);
      }
      // Handle when the plugin is disabled
      if ($context == 'com_plugins.plugin' && $value === 0)
      {
         $this->pluginDisable($pks);
      }
   }

   /**
    * Method to index an item. The item must be a FinderIndexerResult object.
    *
    * @param   FinderIndexerResult  $item    The item to index as an FinderIndexerResult object.
    * @param   string               $format  The item format
    *
    * @return  void
    *
    * @since   2.5
    * @throws  Exception on database error.
    */
   protected function index(FinderIndexerResult $item, $format = 'html')
   {
      // Check if the extension is enabled
      if (JComponentHelper::isEnabled($this->extension) == false)
      {
         return;
      }

      // Initialize the item parameters.
      $registry = new JRegistry;
      $registry->loadString($item->params);
      $item->params = JComponentHelper::getParams('com_issuetracker', true);
      $item->params->merge($registry);

//      $registry = new JRegistry;
//      $registry->loadString($item->metadata);
//      $item->metadata = $registry;

      // Trigger the onContentPrepare event.
      $item->summary = FinderIndexerHelper::prepareContent($item->summary, $item->params);
      $item->body = FinderIndexerHelper::prepareContent($item->body, $item->params);
      // Could enable progress & resolution summary - Options?

      // Build the necessary route and path information.
      $item->url = $this->getURL($item->id, $this->extension, $this->layout);
      $item->route = IssueTrackerHelperRoute::getIssueRoute($item->slug);  // , $item->catslug);
      // $item->route = ContentHelperRoute::getArticleRoute($item->slug, $item->catslug);
      $item->path = FinderIndexerHelper::getContentPath($item->route);

      // Get the menu title if it exists.
      $title = $this->getItemMenuTitle($item->url);

      // Adjust the title if necessary.
      if (!empty($title) && $this->params->get('use_menu_title', true))
      {
         $item->title = $title;
      }

      // Add the meta-author.
//      $item->metaauthor = $item->metadata->get('author');

      // Add the meta-data processing instructions.
//      $item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
//      $item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
//      $item->addInstruction(FinderIndexer::META_CONTEXT, 'metaauthor');
//      $item->addInstruction(FinderIndexer::META_CONTEXT, 'author');
//      $item->addInstruction(FinderIndexer::META_CONTEXT, 'created_by_alias');
/*
      // Handle the issue progress data.    This cimmented out doesnot make much difference.
      if ($item->params->get('show_progress', true))
      {
         $item->addInstruction(FinderIndexer::META_CONTEXT, 'progress');
      }

      // Handle the issue resolution summary.
      if ($item->params->get('show_resolution', true))
      {
         $item->addInstruction(FinderIndexer::META_CONTEXT, 'resolution');
      }
*/
      // Translate the state. Articles should only be published if the category is published.
      $item->state = $this->translateState($item->state, $item->cat_state);

      // Add the type taxonomy data.
      $item->addTaxonomy('Type', 'Issue');

      // Add the author taxonomy data.   What about the identified by field?
      if (!empty($item->author) || !empty($item->created_by_alias))
      {
         $item->addTaxonomy('Author', !empty($item->created_by_alias) ? $item->created_by_alias : $item->author);
      }

      // Add the category taxonomy data.
      $item->addTaxonomy('Project', $item->category, $item->cat_state, $item->cat_access);

      // Add the language taxonomy data.   Ready for v2.0
      $item->addTaxonomy('Language', $item->language);

      // Get content extras.
      FinderIndexerHelper::getContentExtras($item);

      // Index the item.
      FinderIndexer::index($item);
   }

   /**
    * Method to setup the indexer to be run.
    *
    * @return  boolean  True on success.
    *
    * @since   2.5
    */
   protected function setup()
   {
      // Load dependent classes.
      include_once JPATH_SITE . '/components/com_issuetracker/helpers/route.php';

      // Add parameters checks here

      return true;
   }

   /**
    * Method to get the SQL query used to retrieve the list of content items.
    *
    * @param   mixed  $sql  A JDatabaseQuery object or null.
    *
    * @return  JDatabaseQuery  A database object.
    *
    * @since   2.5
    */
   protected function getListQuery($sql = null)
   {
      $db = JFactory::getDbo();

      // Check if we can use the supplied SQL query.
      $sql = is_a($sql, 'JDatabaseQuery') ? $sql : $db->getQuery(true);
/*
      $sql->select('a.id, a.title, a.alias, a.introtext AS summary, a.fulltext AS body');
      $sql->select('a.state, a.catid, a.created AS start_date, a.created_by');
      $sql->select('a.created_by_alias, a.modified, a.modified_by, a.attribs AS params');
      $sql->select('a.metakey, a.metadesc, a.metadata, a.language, a.access, a.version, a.ordering');
      $sql->select('a.publish_up AS publish_start_date, a.publish_down AS publish_end_date');
      $sql->select('c.title AS category, c.published AS cat_state, c.access AS cat_access');
*/
      $sql->select('a.id AS id, a.alias, a.issue_summary AS title, a.issue_description AS body');
      $sql->select('a.issue_description AS summary');
      $sql->select('a.progress AS progress');
      $sql->select('a.resolution_summary AS resolution');
      $sql->select('a.related_project_id AS catid, a.created_on AS start_date, a.created_by');
      $sql->select('a.ordering, a.modified_on AS modified, a.modified_by');
      $sql->select('a.identified_date AS publish_start_date, 1 AS access, a.state AS state');
      $sql->select('c.project_name AS category, c.state AS cat_state, 1 AS cat_access');

      // Above only returns the sub-category of the project- would need to expand out whole name using sql

      // Handle the alias CASE WHEN portion of the query
      // This also puts the alias and catid and itemId on the back of the route in the finder_links table.

      $case_when_item_alias = ' CASE WHEN ';
      $case_when_item_alias .= $sql->charLength('a.alias');
      $case_when_item_alias .= ' THEN ';
      $a_id = $sql->castAsChar('a.id');
      $case_when_item_alias .= $sql->concatenate(array($a_id, 'a.alias'), ':');
      $case_when_item_alias .= ' ELSE ';
      $case_when_item_alias .= $a_id.' END as slug';
      $sql->select($case_when_item_alias);

/*
      $case_when_item_alias = ' CASE WHEN ';
      $case_when_item_alias .= $sql->charLength('a.issue_summary');
      $case_when_item_alias .= ' THEN ';
      $a_id = $sql->castAsChar('a.id');
      $case_when_item_alias .= $sql->concatenate(array($a_id, 'a.issue_summary'), ':');
      $case_when_item_alias .= ' ELSE ';
      $case_when_item_alias .= $a_id.' END AS slug';
      $sql->select($case_when_item_alias);
*/
//      $sql->select('a.id AS slug');

      $sql->where('a.state = 1');          // Prevents unpublished issues being put in Finder index.

      $case_when_category_alias = ' CASE WHEN ';
      $case_when_category_alias .= $sql->charLength('c.project_name');
      $case_when_category_alias .= ' THEN ';
      $c_id = $sql->castAsChar('c.id');
      $case_when_category_alias .= $sql->concatenate(array($c_id, 'c.project_name'), ':');
      $case_when_category_alias .= ' ELSE ';
      $case_when_category_alias .= $c_id.' END as catslug';
      $sql->select($case_when_category_alias);

      $sql->select('u.username AS author');

      $sql->from('#__it_issues AS a');
      $sql->join('LEFT', '#__it_projects AS c ON c.id = a.related_project_id');
      $sql->join('LEFT', '#__it_people AS u ON u.id = a.identified_by_person_id');

      return $sql;
   }
}
