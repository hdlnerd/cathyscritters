<?php 
/**
* JoomBlog component for Joomla 1.6
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

function showJbField($form, $name='')
{
	echo '<td class="jbadmintitle">';
	echo $form->getLabel($name);
	echo '</td><td>';
	echo $form->getInput($name);
	echo '</td>';
}
?>
<style type="text/css">
.hide {
	display: none;
}
</style>
<form action="<?php echo JRoute::_('index.php?option=com_joomblog&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="settings-form" class="form-validate">
	<table class="admin jbsettings" width="100%">
	<tr>
		<td valign="top" class="lefmenutd" >
				<?php echo $this->loadTemplate('menu');?>
		</td>
		<td valign="top" width="100%">
			<?php echo JHtml::_('tabs.start','topics-tabs', array('useCookie'=>1));?>
			<?php echo JHtml::_('tabs.panel',JText::_('COM_JOOMBLOG_SETTINGS_GLOBAL'), 'global-details');?>
				<br/>
				<div class="width-50 fltlft">
					<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_TITLE_AND_DESCRIPTION'); ?></legend>
					<table cellspacing="1" class="adminlist">
							<tr class="row0">
								<?php showJbField($this->form, 'mainBlogTitle'); ?>
							</tr>
							<tr class="row1">
								<?php showJbField($this->form, 'mainBlogDesc'); ?>
							</tr>
					</table>						
				</fieldset>
				</div>
				<div class="width-50 fltlft">
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_RECENT_POSTS'); ?></legend>
					<table cellspacing="1" class="adminlist">
							<tr class="row0">
								<?php showJbField($this->form,'BloggerRecentPosts'); ?>
							</tr>
							<tr class="row1">
								<?php showJbField($this->form,'CategoriesRecentPosts'); ?>
							</tr>
					</table>						
				</fieldset>
				</div>
				
				<div class="width-50 fltrt">
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_TAGS'); ?></legend>
					<table cellspacing="1" class="adminlist">
							<tr class="row0"><?php showJbField($this->form,'allowDefaultTags'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'enableUserCreateTags'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'ShowUntaggedWithoutTags'); ?></tr>
					</table>						
				</fieldset>
				</div>
								
				<div class="width-50 fltlft">
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_COMMENTS'); ?></legend>
					<table cellspacing="1" class="adminlist">
							<tr class="row0"><?php showJbField($this->form,'useComment'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'allowModerateComment'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'useCommentreCaptcha'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'recaptcha_publickey'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'recaptcha_privatekey'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'useCommentCaptcha'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'notifyCommentAdmin'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'notifyCommentAuthor'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'useCommentCaptchaRegisteredUsers'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'useCommentOnlyRegisteredUsers'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'limitComment'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'useDisqus'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'disqusSubDomain'); ?></tr>
							
					</table>						
				</fieldset>
				</div>
				
				<div class="width-50 fltlft">
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_RSS_FEEDS'); ?></legend>
					<table cellspacing="1" class="adminlist">
							<tr class="row0"><?php showJbField($this->form,'useRSSFeed'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'rssFeedLimit'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'titleFeed'); ?></tr>
                            <tr class="row1"><?php showJbField($this->form, 'useFeedBurnerIntegration'); ?></tr>
                            <tr class="row0"><?php showJbField($this->form, 'rssFeedBurnerLabel'); ?></tr>
                            <tr class="row1"><?php showJbField($this->form, 'rssFeedBurner'); ?></tr>
                            <tr class="row0"><?php showJbField($this->form); ?></tr>
                            <script>
                                if (document.getElementById('jform_useRSSFeed1').getAttribute("checked") == "checked" || document.getElementById('jform_useRSSFeed0').getAttribute("checked") == "checked") {
                                    document.getElementById('jform_rssFeedBurnerLabel').setAttribute('value', 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php?option=com_joomblog&task=rss');
                                }
                            </script>
					</table>				
				</fieldset>
				</div>
				
				<div class="width-50 fltlft">
					<fieldset class="settingfieldset">
						<legend><?php echo JText::_('COM_JOOMBLOG_NOTIFICATION'); ?></legend>
						<table cellspacing="1" class="adminlist">
								<tr class="row0"><?php showJbField($this->form,'allowNotification'); ?></tr>
								<tr class="row1"><?php showJbField($this->form,'adminEmail'); ?></tr>
								<tr class="row0">
								<td class="jbadmintitle">
								<label><?php echo JText::_('COM_JOOMBLOG_EDIT_MAIL_TEMPLATES'); ?></label>
								</td>
								<td>
								<?php 
									$file_path = JPATH_SITE.'/components/com_joomblog/templates/default/newblog.notify.tmpl.html';
       									if (file_exists($file_path) && is_writable($file_path))
       									{
       							?>
				       					<div class="button2-left">
											<div class="blank">
												<a href="index.php?option=com_joomblog&amp;task=settings.editempl&amp;tmpl=component&amp;t=newblog.notify" rel="{handler: 'iframe', size: {x: 600, y: 450}, onClose: function() {}}" class="modal">
													<?php echo JText::_('COM_JOOMBLOG_EDIT_NEWBLOG_TEMPLATE'); ?>
												</a>
											</div>
										</div>
       							<?php	}?>
								<?php 
									$file_path = JPATH_SITE.'/components/com_joomblog/templates/default/new.notify.tmpl.html';
       									if (file_exists($file_path) && is_writable($file_path))
       									{
       							?>
				       					<div class="button2-left">
											<div class="blank">
												<a href="index.php?option=com_joomblog&amp;task=settings.editempl&amp;tmpl=component&amp;t=new.notify" rel="{handler: 'iframe', size: {x: 600, y: 450}, onClose: function() {}}" class="modal">
													<?php echo JText::_('COM_JOOMBLOG_EDIT_NEWPOST_TEMPLATE'); ?>
												</a>
											</div>
										</div>
       							<?php	}?>
       							<?php 
									$file_path = JPATH_SITE.'/components/com_joomblog/templates/default/update.notify.tmpl.html';
       									if (file_exists($file_path) && is_writable($file_path))
       									{
       							?>
				       					<div class="button2-left">
											<div class="blank">
												<a href="index.php?option=com_joomblog&amp;task=settings.editempl&amp;tmpl=component&amp;t=update.notify" rel="{handler: 'iframe', size: {x: 600, y: 450}, onClose: function() {}}" class="modal">
													<?php echo JText::_('COM_JOOMBLOG_EDIT_UPDPOST_TEMPLATE'); ?>
												</a>
											</div>
										</div>
       							<?php	}?>
       							
       							<?php 
       							/*in future
									$file_path = JPATH_SITE.'/components/com_joomblog/templates/default/updateblog.notify.tmpl.html';
       									if (file_exists($file_path) && is_writable($file_path))
       									{
       							?>
				       					<div class="button2-left">
											<div class="blank">
												<a href="index.php?option=com_joomblog&amp;task=settings.editempl&amp;tmpl=component&amp;t=updateblog.notify" rel="{handler: 'iframe', size: {x: 600, y: 450}, onClose: function() {}}" class="modal">
													<?php echo JText::_('COM_JOOMBLOG_EDIT_UPDBLOG_TEMPLATE'); ?>
												</a>
											</div>
										</div>
       							<?php	}
       							*/?>
       							</td>
								</tr>
						</table>						
					</fieldset>
				</div>
								
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_INTEGRATION'); ?></legend>
					<table cellspacing="1" class="adminlist">
							<tr class="row0"><?php showJbField($this->form,'integrJoomSoc'); ?></tr>
					</table>						
				</fieldset>
				
       		 <div class="clr"></div>
       		 
			<?php echo JHtml::_('tabs.panel',JText::_('COM_JOOMBLOG_SETTINGS_LAYOUT'), 'layout-details');?>
				<br/>
				<div class="width-50 fltlft">
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_MAIN_PAGE'); ?></legend>
					<table cellspacing="1" class="adminlist">
							<tr class="row0"><?php showJbField($this->form,'numEntry'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'categoryDisplay'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'frontpageToolbar'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'showPrimaryTitles'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'dateFormat'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'enableBackLink'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'enablePrintLink'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'modulesDisplay'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'mambotFrontpage'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'showBlogTitle'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'showRandomPost'); ?></tr>
							
					</table>
				</fieldset>
				</div>
				<div class="width-50 fltrt">
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_AUTHOR_SETTINGS'); ?></legend>
					<table cellspacing="1" class="adminlist">
							<tr class="row0"><?php showJbField($this->form,'avatar'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'showUserLink'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'useFullName'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'avatarWidth'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'avatarHeight'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'maxFileSize'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'linkAvatar'); ?></tr>
					</table>
				</fieldset>
				</div>
				<div class="width-50 fltlft">
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_RMS'); ?></legend>
					<table cellspacing="1" class="adminlist">
							<tr class="row0"><?php showJbField($this->form,'useIntrotext'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'autoReadmorePCount'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'readMoreLink'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'disableReadMoreTag'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'necessaryReadmore'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'anchorReadmore'); ?></tr>				
					</table>
				</fieldset>
				</div>
			<div class="clr"></div>
			<?php echo JHtml::_('tabs.panel',JText::_('COM_JOOMBLOG_NEW_YEAR_STYLE'), 'layout-details');?>
				<br/>
				<div class="width-65 fltlft">
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_NEW_YEAR_STYLE'); ?></legend>
					<?php $path = Juri::base().'media/com_joomblog/images/';
					$path = str_replace ('/administrator','',$path); ?>
					<table cellspacing="1" class="adminlist">
							<tr class="row1"><?php showJbField($this->form,'useNewYearStyleheader'); ?><td style="width:10% !important;"><image src="<?php echo $path.'xmas_jb_santa_top.png';?>"></td></tr>
							<tr class="row0"><?php showJbField($this->form,'useNewYearStylefooter'); ?><td style="width:10% !important;"><image src="<?php echo $path.'xmas_jb_childrens_btm.png';?>"></td></tr>
							<tr class="row1"><?php showJbField($this->form,'useNewYearStylesocial'); ?><td style="width:10% !important;"><image src="<?php echo $path.'xmas_jb_scbtns_bg.png';?>"></td></tr>
							<tr class="row0"><?php showJbField($this->form,'useNewYearStyleh3'); ?><td style="width:10% !important;"><image src="<?php echo $path.'xmas_ball.png';?>"></td></tr>

					</table>
				</fieldset>
				</div>
			<div class="clr"></div>			
			<?php echo JHtml::_('tabs.panel',JText::_('COM_JOOMBLOG_SETTINGS_DASHBOARD'), 'dashboard-details');?>
				<br/>
				<div class="width-50 fltlft">
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_CONFIGURATIONS'); ?></legend>
					<table cellspacing="1" class="adminlist">
							<tr class="row0"><?php showJbField($this->form,'autoapproveblogs'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'defaultPublishStatus'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'useMCEeditor'); ?></tr>		
					</table>
				</fieldset>
				</div>
			<div class="clr"></div>
			<?php echo JHtml::_('tabs.panel',JText::_('COM_JOOMBLOG_SETTINGS_MENU'), 'menu-details');?>
				<br />
				<div class="width-50 fltlft">
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_BASIC_CONFIGURATION'); ?></legend>
						<table cellspacing="1" class="adminlist">
							<tr class="row0"><?php showJbField($this->form,'showIcons'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'showTabs'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'showPanel'); ?></tr>		
						</table>
				</fieldset>
				</div>
				<div class="width-50 fltrt">
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_SHOW_MENU_ITEMS'); ?></legend>
						<table cellspacing="1" class="adminlist">
							<tr class="row0"><?php showJbField($this->form,'showPosts'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'showBlogs'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'showSearch'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'showBloggers'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'showAddBlog'); ?></tr>	
							<tr class="row0"><?php showJbField($this->form,'showProfile'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'showAddPost'); ?></tr>	
							<tr class="row0"><?php showJbField($this->form,'showCategories'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'showArchive'); ?></tr>	
							<tr class="row0"><?php showJbField($this->form,'showTags'); ?></tr>
						</table>
				</fieldset>
				</div>
				<div class="width-50 fltlft">
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_TITLES_MENU_ITEMS'); ?></legend>
						<table cellspacing="1" class="adminlist">
							<tr class="row0"><?php showJbField($this->form,'titlePosts'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'titleBlogs'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'titleSearch'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'titleAddBlog'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'titleProfile'); ?></tr>	
							<tr class="row0"><?php showJbField($this->form,'titleAddPost'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'titleBloggers'); ?></tr>	
							<tr class="row0"><?php showJbField($this->form,'titleCategories'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'titleArchive'); ?></tr>	
							<tr class="row0"><?php showJbField($this->form,'titleTags'); ?></tr>
						</table>
				</fieldset>
				</div>
				<div class="clr"></div>
				<div class="clr"></div>
				<div class="width-50 fltlft">
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_ORDER_MENU_ITEMS'); ?></legend>
						<table cellspacing="1" class="adminlist">
							<tr class="row0"><?php showJbField($this->form,'firstPosition'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'secondPosition'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'thirdPosition'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'fourthPosition'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'fifthPosition'); ?></tr>	
							<tr class="row0"><?php showJbField($this->form,'sixthPosition'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'seventhPosition'); ?></tr>	
							<tr class="row0"><?php showJbField($this->form,'eighthPosition'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'ninthPosition'); ?></tr>	
							<tr class="row0"><?php showJbField($this->form,'tenthPosition'); ?></tr>
						</table>
				</fieldset>
				</div>
				
			<div class="clr"></div>
			
			<?php echo JHtml::_('tabs.panel',JText::_('COM_JOOMBLOG_SOCIAL_INTEGRATIONS'), 'social-details');?>
			<br />
			<?php echo JHtml::_('tabs.start','social-tabs', array('useCookie'=>1));?>
			<?php echo JHtml::_('tabs.panel', 'Twitter', 'twitter-details');?>		
				<br/>
					<div class="width-50 fltlft">
							<fieldset class="settingfieldset">
							<legend><?php echo JText::_('Twitter settings'); ?></legend>
								<table cellspacing="1" class="adminlist">
									<tr class="row0"><?php showJbField($this->form,'usetwitter'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'twitterlang'); ?></tr>
									<tr class="row0"><?php showJbField($this->form,'twitterName'); ?></tr>
								</table>
							</fieldset>
					</div>
					<div class="width-50 fltlft">		
							<fieldset class="settingfieldset">
							<legend><?php echo JText::_('Twitter buttons in list of posts'); ?></legend>
								<table cellspacing="1" class="adminlist">					
									<tr class="row0"><?php showJbField($this->form,'showtwitterInList'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'twitterliststyle'); ?></tr>
									<tr class="row0"><?php showJbField($this->form,'twitterfollowliststyle'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'positiontwitterInList'); ?></tr>
								</table>
							</fieldset>	
					</div>
					<div class="width-50 fltrt">
							<fieldset class="settingfieldset">
							<legend><?php echo JText::_('Twitter buttons in post'); ?></legend>
								<table cellspacing="1" class="adminlist">					
									<tr class="row0"><?php showJbField($this->form,'showtwitterInPost'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'twitterpoststyle'); ?></tr>
									<tr class="row0"><?php showJbField($this->form,'twitterfollowpoststyle'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'positiontwitterInPost'); ?></tr>							
								</table>
						</fieldset>
				</div>
			<div class="clr"></div>	
			<?php echo JHtml::_('tabs.panel', 'Facebook', 'facebook-details');?>		
				<br/>
				<div class="width-50 fltlft">
							<fieldset class="settingfieldset">
							<legend><?php echo JText::_('Facebook settings'); ?></legend>
								<table cellspacing="1" class="adminlist">
									<tr class="row0"><?php showJbField($this->form,'usefacebook'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'fb_sendbutton'); ?></tr>
									<tr class="row0"><?php showJbField($this->form,'fbwidth'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'fbadmin'); ?></tr>
									<tr class="row0"><?php showJbField($this->form,'fbappid'); ?></tr>
									<?php echo '<tr class="row1"><td style="font-size:1.091em;" class="jbadmintitle">'.$this->form->getlabel("og_defimage").'</td><td class="jbadmintitle">' 
									. JHTML::_('list.images', 'jform[page_image]', $this->item->page_image, '',$imagesDir, $extensions) . '</td></tr>';?>
								</table>
							</fieldset>
				</div>	
				<div class="width-50 fltlft">		
							<fieldset class="settingfieldset">
							<legend><?php echo JText::_('Facebook buttons in list of posts'); ?></legend>
								<table cellspacing="1" class="adminlist">					
									<tr class="row0"><?php showJbField($this->form,'showfbInList'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'fb_style_list'); ?></tr>
									<tr class="row0"><?php showJbField($this->form,'positionfbInList'); ?></tr>
								</table>
							</fieldset>	
					</div>
					<div class="width-50 fltrt">
							<fieldset class="settingfieldset">
							<legend><?php echo JText::_('Facebook buttons in post'); ?></legend>
								<table cellspacing="1" class="adminlist">					
									<tr class="row0"><?php showJbField($this->form,'showfbInPost'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'fb_style_post'); ?></tr>
									<tr class="row0"><?php showJbField($this->form,'positionfbInPost'); ?></tr>							
								</table>
						</fieldset>
				</div>
			<div class="clr"></div>		
			<?php echo JHtml::_('tabs.panel', 'Google +', 'gp-details');?>		
				<br/>
				<div class="width-50 fltlft">
							<fieldset class="settingfieldset">
							<legend><?php echo JText::_('Google+ settings'); ?></legend>
								<table cellspacing="1" class="adminlist">
									<tr class="row0"><?php showJbField($this->form,'usegp'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'gp_language'); ?></tr>
								</table>
							</fieldset>
				</div>	
				<div class="width-50 fltlft">		
							<fieldset class="settingfieldset">
							<legend><?php echo JText::_('Google+ button in list of posts'); ?></legend>
								<table cellspacing="1" class="adminlist">					
									<tr class="row0"><?php showJbField($this->form,'showgpInList'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'gp_style_list'); ?></tr>
									<tr class="row0"><?php showJbField($this->form,'positiongpInList'); ?></tr>
								</table>
							</fieldset>	
					</div>
					<div class="width-50 fltrt">
							<fieldset class="settingfieldset">
							<legend><?php echo JText::_('Google+ button in post'); ?></legend>
								<table cellspacing="1" class="adminlist">					
									<tr class="row0"><?php showJbField($this->form,'showgpInPost'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'gp_style_post'); ?></tr>
									<tr class="row0"><?php showJbField($this->form,'positiongpInPost'); ?></tr>							
								</table>
						</fieldset>
				</div>	
				<div class="clr"></div>
            <?php echo JHtml::_('tabs.panel', 'Pinterest', 'pi-dateils') ?>
                <br/>
                <div class="width-50 fltlft">
                    <fieldset class="settingsfieldset">
                        <legend><?php echo JText::_('Pinterest settings'); ?></legend>
                        <table cellspacing="1" class="adminlist">
                            <tr class="row0"><?php showJbField($this->form,'usepi'); ?></tr>
                            <tr class="row1"><?php showJbField($this->form,'pi_language'); ?></tr>
                        </table>
                    </fieldset>
                </div>
                <div class="width-50 fltlft">
                    <fieldset class="settingfieldset">
                        <legend><?php echo JText::_('Pinterest button in list of posts'); ?></legend>
                        <table cellspacing="1" class="adminlist">
                            <tr class="row0"><?php showJbField($this->form,'showpiInList'); ?></tr>
                            <tr class="row1"><?php showJbField($this->form,'pi_style_list'); ?></tr>
                            <tr class="row0"><?php showJbField($this->form,'positionpiInList'); ?></tr>
                        </table>
                    </fieldset>
                </div>
                <div class="width-50 fltrt">
                    <fieldset class="settingfieldset">
                        <legend><?php echo JText::_('Pinterest button in post'); ?></legend>
                        <table cellspacing="1" class="adminlist">
                            <tr class="row0"><?php showJbField($this->form,'showpiInPost'); ?></tr>
                            <tr class="row1"><?php showJbField($this->form,'pi_style_post'); ?></tr>
                            <tr class="row0"><?php showJbField($this->form,'positionpiInPost'); ?></tr>
                        </table>
                    </fieldset>
                </div>
            <div class="clr"></div>
			<?php echo JHtml::_('tabs.panel', 'LinkedIn', 'ln-details');?>		
				<br/>
				<div class="width-50 fltlft">
							<fieldset class="settingfieldset">
							<legend><?php echo JText::_('LinkedIn settings'); ?></legend>
								<table cellspacing="1" class="adminlist">
									<tr class="row0"><?php showJbField($this->form,'useln'); ?></tr>
									
								</table>
							</fieldset>
				</div>	
				<div class="width-50 fltlft">		
							<fieldset class="settingfieldset">
							<legend><?php echo JText::_('LinkedIn button in list of posts'); ?></legend>
								<table cellspacing="1" class="adminlist">					
									<tr class="row1"><?php showJbField($this->form,'showlnInList'); ?></tr>
									<tr class="row0"><?php showJbField($this->form,'ln_style_list'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'positionlnInList'); ?></tr>
								</table>
							</fieldset>	
					</div>
					<div class="width-50 fltrt">
							<fieldset class="settingfieldset">
							<legend><?php echo JText::_('LinkedIn button in post'); ?></legend>
								<table cellspacing="1" class="adminlist">					
									<tr class="row1"><?php showJbField($this->form,'showlnInPost'); ?></tr>
									<tr class="row0"><?php showJbField($this->form,'ln_style_post'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'positionlnInPost'); ?></tr>							
								</table>
						</fieldset>
				</div>	
				<div class="clr"></div>	
			<?php echo JHtml::_('tabs.panel', 'AddThis', 'adth-details');?>		
				<br/>
				<div class="width-50 fltlft">
							<fieldset class="settingfieldset">
							<legend><?php echo JText::_('AddThis settings'); ?></legend>
								<table cellspacing="1" class="adminlist">
									<tr class="row0"><?php showJbField($this->form,'useAddThis'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'addThisName'); ?></tr>
									<tr class="row0"><?php showJbField($this->form,'addthis_language'); ?></tr>
								</table>
							</fieldset>
				</div>
				<div class="width-50 fltrt">
					<fieldset class="settingfieldset">
							<legend><?php echo JText::_('AddThis in list of posts'); ?></legend>
								<table cellspacing="1" class="adminlist">
									<tr class="row0"><?php showJbField($this->form,'showAddThisInList'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'addthis_list_button_style'); ?></tr>
									<tr class="row0"><?php showJbField($this->form,'addThisListPosition'); ?></tr>									
								</table>
					</fieldset>
				</div>
				<div class="width-50 fltlft">
					<fieldset class="settingfieldset">
							<legend><?php echo JText::_('AddThis in post'); ?></legend>
								<table cellspacing="1" class="adminlist">
									<tr class="row0"><?php showJbField($this->form,'showAddThisInPost'); ?></tr>
									<tr class="row1"><?php showJbField($this->form,'addthis_post_button_style'); ?></tr>
									<tr class="row0"><?php showJbField($this->form,'addThisPostPosition'); ?></tr>
								</table>
					</fieldset>
				</div>
			<div class="clr"></div>	
			<?php echo JHtml::_('tabs.end'); ?>					
			<div class="clr"></div>
			<?php echo JHtml::_('tabs.panel',JText::_('JCONFIG_PERMISSIONS_LABEL'), 'permission-details');?>
				<br/>
				<div class="width-50 fltlft">
				<fieldset class="settingfieldset">
					<legend><?php echo JText::_('COM_JOOMBLOG_GENERAL_PERMISSIONS'); ?></legend>
						<table cellspacing="1" class="adminlist">
							<tr class="row0"><?php showJbField($this->form,'disallowedPosters'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'allowedPosters'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'allowedPublishers'); ?></tr>
							<tr class="row1"><?php showJbField($this->form,'viewIntro'); ?></tr>
							<tr class="row0"><?php showJbField($this->form,'viewEntry'); ?></tr>	
							<tr class="row1"><?php showJbField($this->form,'viewComments'); ?></tr>
						</table>
				</fieldset>
				</div>
				<div class="width-50 fltlft">
				<fieldset class="settingfieldset">
				<legend><?php echo JText::_('JCONFIG_PERMISSIONS_LABEL'); ?></legend>
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
				</div>
			<div class="clr"></div>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="id" value="<?php echo (isset($this->item->id)?$this->item->id:0)?>" />				
				<?php echo JHtml::_('form.token'); ?>
			</div>
			<div class="clr"></div>			
			<?php echo JHtml::_('tabs.end'); ?>
		</td>
  </tr>
</table>
</form>
