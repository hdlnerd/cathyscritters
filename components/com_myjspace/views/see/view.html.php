<?php
/**
* @version $Id: view.html.php $
* @version		2.0.3 20/10/2012
* @package		com_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'legacy.php';

jimport('joomla.application.component.view');

class MyjspaceViewSee extends JViewLegacy
{
	function display($tpl = null)
	{		
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'user.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';
        require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util_acl.php';
		
		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();

		$nb_max_page = $pparams->get('nb_max_page', 1);
		
		// Params
		$Itemid = JRequest::getInt('Itemid', 0);
		$Itemid_pages = get_menu_itemid('index.php?option=com_myjspace&view=pages', $Itemid);
		$id = JRequest::getInt('id', 0);
		if ($id == 0) {
			$pageid_tab = JRequest::getVar('cid', array(0));
			$id = intval($pageid_tab[0]);
		}

		// User info
		$user_actual = JFactory::getuser();
		$user_page = New BSHelperUser();

		// Criteria : User info or pagename
		$pagename = '';
		if ($id > 0)
			$id = $id;
		else if ((JRequest::getVar('pagename', '')))
			$pagename = JRequest::getVar('pagename', '');
		else  {
			// Page id - check
			$list_page_tab = $user_page->GetListPageId($user_actual->id, $id);
			$nb_page = count($list_page_tab);
			if ($nb_page == 1) {
				$id = $list_page_tab[0]['id'];
			} else if ($nb_page > 1 || $nb_max_page > 1) { // Display Pages list
				$app->redirect(Jroute::_('index.php?option=com_myjspace&view=pages&Itemid='.$Itemid_pages, false)); // Default lview=see
				return;
			}
		}

		// Personnal page info
		$user_page->id = $id;
		if ($pagename != '') {
			$user_page->pagename = $pagename;
			$user_page->loadPageInfo(1);
		} else
			$user_page->loadPageInfo();

		// User (for page) info
		$table = JUser::getTable();
		if ($table->load($user_page->userid)) { // Test if user exist before to retrive info
			$user = JFactory::getUser($user_page->userid);
		} else { // User no no exist any more !
			$user = new stdClass();
			$user->id = 0;
			$user->username = ' '; // '' to do NOT display a page with no user
			$user->name = '';
		}
			
		// Increment hits, if : not empy, not the owner, no block  ... :-)
		$allow_plugin = $pparams->get('allow_plugin', 1);
		$page_increment = $pparams->get('page_increment', 1);
		$date_fmt = $pparams->get('date_fmt', 'Y-m-d H:i:s');
		if ($page_increment == 1 && $user_actual->id != $user_page->userid && $user_page->content != null && ($user_page->blockView == 1 || ($user_page->blockView == 2 && $user_actual->username != "")) )
			$user_page->updateLastAccess(hash("crc32b", addr_ip()));
		$jcomment = $pparams->get('jcomment',0);	
			
        // Content
		$uploadadmin = $pparams->get('uploadadmin', 1);
		$uploadimg = $pparams->get('uploadimg', 1);
		$chaine_files = '';
		if ($uploadadmin == 1 && $uploadimg == 1) { // May be add optional in the futur
			require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'util.php';
			$forbiden_files = array('.', '..', 'index.html', 'index.htm', 'index.php');
			$tab_list_file = list_file_dir(JPATH_ROOT.DS.$user_page->foldername.DS.$user_page->pagename, '*', $forbiden_files, 1);
			$nb = count($tab_list_file);
			for ($i = 0 ; $i < $nb ; ++$i)
				$chaine_files .= '<a href="'.JURI::base().$user_page->foldername.'/'.$user_page->pagename.'/'.$tab_list_file[$i].'">'.$tab_list_file[$i].'</a> '; 
		}

		if ($pparams->get('allow_user_content_var', 1))
			$content = $user_page->traite_prefsuf($user_page->content, $user, $page_increment, $date_fmt, $chaine_files, 0, $Itemid);
		else
			$content = $user_page->content;
			
		// [register]
		if ($pparams->get('editor_bbcode_register', 0) == 1 && strlen($content) <= 92160) { // Allow to use the dynamic tag [register]
			$uri = JFactory::getURI();
			$return = $uri->toString();
			if ($pparams->get('url_login_redirect', '')) 
				$url = $pparams->get('url_login_redirect', '');
			else {
				if (version_compare(JVERSION, '1.6.0', 'ge'))
					$url = 'index.php?option=com_users&view=login';
				else
					$url = 'index.php?option=com_user&view=login';
				$url .= '&return='.base64_encode($return); // to redirect to the originaly call page
				$url =  Jroute::_($url, false);
			}
 
			if ($user_actual->id != 0)// if registered
				$content = preg_replace('!\[register\](.+)\[/register\]!isU', '$1', $content);
			else // if not registered
				$content = preg_replace('!\[register\](.+)\[/register\]!isU', JText::sprintf('COM_MYJSPACE_REGISTER', $url), $content);		
		}

		// Force default dates
		if ($pparams->get('publish_mode', 2) == 0) { // do not take into account the dates
			$user_page->publish_up = '0000-00-00 00:00:00';
			$user_page->publish_down = '0000-00-00 00:00:00';
		}
		if ($user_page->publish_down == '0000-00-00 00:00:00')
			$user_page->publish_down = date('Y-m-d 00:00:00',strtotime("+1 day"));
			
		// Specific context
		$prefix = '';
		$suffix = '';
		$aujourdhui = time();

		// If ACL filter for display this page
		$user_mode_view_acl = $pparams->get('user_mode_view_acl', 0);
		if (version_compare(JVERSION, '1.6.0', 'ge') && $user_mode_view_acl == 1)
			$access = $user_actual->getAuthorisedViewLevels();
		else
			$access = array();
		
		if ($user_page->id == 0) {
			$content = JText::_('COM_MYJSPACE_PAGENOTFOUND');
		} else if ($user_page->blockView == 0 && $user_actual->id != $user_page->userid) {
			$content = JText::_('COM_MYJSPACE_PAGEBLOCK');
		} else if ($user_page->blockView >= 2 && ($user_mode_view_acl == 0 || ($user_mode_view_acl == 1 && !in_array($user_page->blockView, $access)))) {
			$content = JText::sprintf('COM_MYJSPACE_PAGERESERVED', get_assetgroup_label($user_page->blockView));
		} else if ($user_page->content == null) {
			$content = JText::_('COM_MYJSPACE_PAGEEMPTY');
		} else if ((strtotime($user_page->publish_up) > $aujourdhui || strtotime($user_page->publish_down) <= $aujourdhui) && ($user_actual->id != $user_page->userid || $pparams->get('publish_mode', 2) == 1)) {
			$content = JText::_('COM_MYJSPACE_PAGEUNPLUBLISHED');
		} else {
		// Top and bottom
			if ($pparams->get('page_prefix', ''))
				$prefix = '<span class="top_myjspace">'.$user_page->traite_prefsuf($pparams->get('page_prefix', ''), $user, $page_increment, $date_fmt, $chaine_files, 1, $Itemid).'</span><br />';
				
			$page_suffix = $pparams->get('page_suffix', '#bsmyjspace');
			if ($page_suffix == '#bsmyjspace' && $pparams->get('display_myjspace_ref', 1) == 0)
				$page_suffix = '';
			if ($page_suffix)
				$suffix = '<span class="bottom_myjspace">'.$user_page->traite_prefsuf($page_suffix, $user, $page_increment, $date_fmt, $chaine_files, 1, $Itemid).'</span><br />';
		}
		$content = '<div class="myjspace-prefix">'.$prefix.'</div><div class="myjspace-content"></div>'.$content.'<div class="myjspace-suffix">'.$suffix.'</div>';
		
		// Lightbox usage
		$add_lightbox = $pparams->get('add_lightbox', 1);
		$this->assignRef('add_lightbox', $add_lightbox);		
		
		// Process the prepare content for plugins
		$contenu = new stdClass();
		$contenu->text = $content;
		$contenu->toc = '';
		if ($allow_plugin >= 1) {
			JPluginHelper::importPlugin('content');
			$dispatcher	= JDispatcher::getInstance();

			$contenu->id = 0; // To have a 'false' article id (can identify all page as same J! article ...)
			$contenu->catid = $user_page->catid;
			$contenu->title = $user_page->pagename;
			$contenu->introtext = $content; // introtext = text
			$contenu->created_by = $user_page->userid; // author id
			$contenu->publish_up = $user_page->publish_up;
			$contenu->publish_down = $user_page->publish_down;
			$contenu->created = $user_page->create_date;
			$contenu->modified = $user_page->last_update_date;
			$contenu->hits = $user_page->hits;
			$contenu->category_title = BSHelperUser::GetCategory($user_page->catid);
			if ($user_page->blockView == 0)
				$contenu->state = 0;
			else
				$contenu->state = 1;
			
			$params = clone($app->getParams('com_content')); // To have all (false) 'classic' default data if a plugin call it
			$limitstart = JRequest::getString('limitstart', 0);
			
			if (version_compare(JVERSION, '1.6.0', 'lt'))
				$results = $dispatcher->trigger('onPrepareContent', array(&$contenu, &$params, &$limitstart, 0));
			else
				$results = $dispatcher->trigger('onContentPrepare', array('com_content.myjspace', &$contenu, &$params, &$limitstart, 0));
			
			if ($allow_plugin > 1) { // 1.6+
				$contenu->event = new stdClass();
				$results = $dispatcher->trigger('onContentAfterTitle', array('com_content.myjspace', &$contenu, &$params, &$limitstart, 0));
				$contenu->event->afterDisplayTitle = trim(implode("\n", $results));

				$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_content.myjspace', &$contenu, &$params, &$limitstart, 0));
				$contenu->event->beforeDisplayContent = trim(implode("\n", $results));

				$results = $dispatcher->trigger('onContentAfterDisplay', array('com_content.myjspace', &$contenu, &$params, &$limitstart, 0));
				$contenu->event->afterDisplayContent = trim(implode("\n", $results));
			}
		}
	
		// If add page number as suffix : plg_pagebreakmyjspace
		$pagename_suffix = '';
		if ($pparams->get('pagebreak_num', 1) == 1) {
			$start = JRequest::getInt( 'start' ) || JRequest::getInt( 'limitstart' ); // J1.6 or J 1.5
			if ($start) {
				$start = $start + 1;
				$pagename_suffix = ' - '.$start;
			}
			if (JRequest::getInt( 'showall' ))
				$pagename_suffix = ' - '.JText::_('COM_MYJSPACE_PAGEALL');
		}
		
		// Meta data : author
		if ($pparams->get('pageauthor', 1) == 1) {
			if ($app->getCfg('MetaAuthor',1) == '1')
				$document->setMetaData('author', $user->name);
        }

        // Meta Description
		if ($user_page->metakey != '')
			$document->setDescription($user_page->metakey.$pagename_suffix);
		else if ($pagedescription = $pparams->get('pagedescription', ''))
			$document->setDescription($user_page->pagename.' - '.$pagedescription.$pagename_suffix);
		
        // Web page title
		if ($pparams->get('pagetitle', 1) == 1) {
			$title = $user_page->pagename;
			if (empty($title)) {
				$title = $app->getCfg('sitename');
			} elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
				$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
			} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
				$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
			}
			if ($title)
				$document->setTitle($title.$pagename_suffix);
		}
		
		// Page template specific
		if ($user_page->template != '') {
			$app->setTemplate($user_page->template);
		}

		// Breadcrumbs
		$pathway = $app->getPathway();
		$pathway = $app->getPathway();
		$pathid = count($pathway->getPathwayNames());
		if ($pathid > 1)
			$pathway->setItemName($pathid-2, JText::_('COM_MYJSPACE_PAGE'));
		else if ($pathid == 1)
			$pathway->addItem(JText::_('COM_MYJSPACE_PAGE'), Jroute::_('index.php?option=com_myjspace&view=see'));
		$pathway->addItem($user_page->pagename, '');		
		
		// Background image 
		$file_background = $user_page->foldername.'/'.$user_page->pagename.'/background.jpg';
		if (@file_exists($file_background)) {
			$css_background = "background-image:url('".$file_background."');";
		} else
			$css_background = '';
		
		// Var assign
		$this->assignRef('allow_plugin', $allow_plugin);			
		$this->assignRef('contenu', $contenu);			
		$this->assignRef('pageid', $user_page->id);
		$this->assignRef('pagename', $user_page->pagename);
		$this->assignRef('css_background', $css_background);
		$this->assignRef('jcomment', $jcomment);
		
		parent::display($tpl);
	}

}
?>
