<?php

/**
* JoomBlog component for Joomla 1.6
* @package JoomPortfolio
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
?>
<table class="admin" >
	<tr>
		<td valign="top" class="lefmenutd" >
			<?php echo $this->loadTemplate('menu');?>
		</td>
		<td valign="top" width="100%" >
			<div class="helptable">
				<div class="button2-left">
					<div class="blank">
						<a class="modal" rel="{handler: 'iframe'}" href="index.php?option=com_joomblog&amp;task=history&amp;tmpl=component">
							<?php  echo JText::_('COM_JOOMBLOG').' '.JoomBlogHelper::getVersion()." version history";?>
						</a>
					</div>
				</div>
				<div class="clr"><!----></div>
				<div style="text-align:left; padding:5px; font-family: verdana, arial, sans-serif; font-size: 9pt;">
	<br>
<strong>This manual outlines key features. For detailed documentation, see the <a href="http://www.joomplace.com/video-tutorials-and-documentation/joomblog/description.htm">Video Tutorials and Documentation section.</a><br><br></strong> 
<strong>1. Installation</strong>
<p style="margin-left: 40px;">
	<br>
	Select <em>Components</em> - <em>Install/Uninstall</em> from the drop-down menu of the Joomla! Administrator Panel<br>
	Browse the component's installation package file in the Upload Package File section and press Upload File&amp;Install.<br>
	Upload the extension's plugins and modules following the same steps.</p>
&nbsp;<br>
<strong>2. Configuration</strong><br>
&nbsp;<br>
<p style="margin-left: 40px;">
	Once the installation is complete, you can proceed to the configuration. The menu consists of three sections: Configuration, Plugins, and Modules.<br>
	&nbsp;<br>
	<em>Configuration</em><br>
	&nbsp;<br>
	To adjust settings,<br>
	Enter the <em>Configuration </em>section. There are four tabs in the section: General Settings, Permissions, Layout, Dashboard and Menu.<br>
	&nbsp;<br>
	<em>General Settings:</em><br>
	&nbsp;<br>
	In this tab, you can</p>
<ul style="margin-left: 40px;">
	<li>
			Enable blog RSS feed
	</li>
	<li>
			Allow inserting tags into the
	</li>
	<li>
			Enable E-mail<p></p>
	</li>
	<li>
			Enable comments and adjust relevant settings
	</li>
	<li>
			Allow showing modules on the Home page
	</li>
	<li>
			Specify the number of latest posts in the Bloggers and Categories sections
	</li>
	<li>
			Enable integration with Disqus and AddThis
	</li>
</ul>
<p style="margin-left: 40px;">
	&nbsp;<br>
	<em>Permissions:</em><br>
	&nbsp;<br>
	In this tab, you</p>
<ul style="margin-left: 40px;">
	<li>
			Allow specified users and groups to post
	</li>
	<li>
			Allow publishing/unpublishing
	</li>
	<li>
			Allow viewing introtext, entries, and comments
	</li>
	<li>
			Allow uploading images
	</li>
</ul>
<p style="margin-left: 40px;">
	&nbsp;<br>
	<em>Layout:</em><br>
	&nbsp;<br>
	In this tab, you can</p>
<ul style="margin-left: 40px;">
	<li>
			Select a template and adjust relevant settings
	</li>
	<li>
			Configure avatar settings
	</li>
	<li>
			Configure the Read More link-related settings
	</li>
	<li>
			Enable showing category, front page toolbar, the Back link, and the Print link
	</li>
</ul>
<p style="margin-left: 40px;">
	&nbsp;<br>
	<em>Dashboard</em>:<br>
	&nbsp;<br>
	In this tab, you can</p>
<ul style="margin-left: 40px;">
	<li>
			Enable using HTML editor
	</li>
	<li>
			Allow bloggers to create tags through dashboard
	</li>
	<li>
			Allow setting blog status to Published automatically
	</li>
</ul>
<p style="margin-left: 40px;">
	&nbsp;<br>
	<em>Menu</em>:<br>
	&nbsp;<br>
	In this tab, you can</p>
<ul style="margin-left: 40px;">
	<li>
			Enable/Disable the display of icons, toolbar and tabs in the menu
	</li>
	<li>
			Enable/Disable the display of menu items
	</li>
	<li>
			Specify titles for menu items
	</li>
	<li>
			Specify menu items order in the toolbar
	</li>
</ul>


<p style="margin-left: 40px;">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;<br>
	<em>Plugins</em><br>
	&nbsp;<br>
	The section contains the extension plugins. Enable the ones you would like to be used on your site.<br>
	&nbsp;<br>
	<em>Modules</em><br>
	&nbsp;<br>
	The section contains the extension modules. Enable the ones you would like to be used on your site home page.</p>
&nbsp;<br>
<strong>3 .Manage Blogs</strong><br>
&nbsp;<br>
<p style="margin-left: 40px;">
	Once the settings are configured you can start managing blogs. To do this, click the Manage Blogs tab. You will find the following sections there: Blogs, Categories, Comments, and Tags.<br>
	&nbsp;<br>
	<em>Blogs</em><br><br>
	
	This section allows managing blogs.<br><br> 
	To create a new blog,<br><br>
	Enter the Blogs section. The list of blogs will appear.<br>
	Click New in the toolbar. The Manager Blogs Items page appears.<br>
	Fill in the fields as required and press Save.<br><br>
	<strong>Note</strong>: to allow adding posts by owner only select the Private option.<br><br>
	If a blog is published all posts it contains become visible to users. If it is unpublished, posts are not available to users.<br><br>
	To delete, edit, publish or unpublish a blog, use standard Joomla! buttons.<br><br>
	
	<em>Bloggers</em><br><br>
	
	The section allows managing JoomBlog bloggers. Mind that all bloggers are Joomla! users. Be careful when deleting a blogger.<br><br>
	To add a new blogger,<br><br>
	Enter the Bloggers section. The list of blogs will appear.<br>
	Click New in the toolbar. The Manager Bloggers page with two tabs - User details and Blogger Info - appears.<br><br>
	
	Use the User Details tab to specify Name, Login Name, Password, Confirm Password, Email and select user groups a blogger will be assigned to in the Assigned User Groups field.<br>
	Use the Blogger Info tab to specify Blog Info, Description, avatars, About User, Birthday, Twitter, Site.<br><br>
	
	Note: Avatars will display only if you have specified JoomBlog in Configuration-&gt;Layout-&gt;Use Avatar.<br>
	If JomSocial is specified instead of JoomBlog JomSocial avatars will display.<br>
	Fill in the fields as required and press Save.<br><br>

	To delete, edit, publish or unpublish a blogger, use standard Joomla! buttons.<br><br>

	<em>Posts</em><br>
	&nbsp;<br>
	The section allows managing posts.<br>
	To create a new post,<br>
	Enter the <em>POsts</em> section. The list of posts appears.<br>
	Click <em>Add</em> in the toolbar. The <em>Manager Post Items</em> page appears.<br>
	Fill in the fields as required and press the <em>Save</em> button.<br>
	<strong>Note</strong>: for quicker access to the post creation form, use the <em>Add new post</em> link in the <em>Manage Posts</em> tab.<br>
	&nbsp;<br>
	To delete, edit, publish or unpublish a blog, use standard Joomla! buttons.<br>
	&nbsp;<br>
	<em>Categories</em><br>
	&nbsp;<br>
	The component allows creating categories for better blogs management.<br>
	To create a category,<br>
	Enter the <em>Categories</em> section. The list of categories will appear.<br>
	Click <em>New</em> at the top. The <em>Add a New JoomBlog Category</em> page appears.<br>
	Fill in the fields as required and press <em>Save</em>.<br>
	&nbsp;<br>
	To delete, edit, publish or unpublish a category, use standard Joomla! buttons.<br>
	&nbsp;<br>
	<em>Comments</em><br>
	&nbsp;<br>
	This section allows managing comments. Once you select the option you will see a list of comments which you can publish, unpublish or delete using standard Joomla! buttons.<br>
	&nbsp;<br>
	<em>Tags</em><br>
	&nbsp;<br>
	The section allows managing tags.<br>
	To create a new tag,<br>
	Enter the <em>Tags</em> section. The list of tags will appear.<br>
	Click <em>New</em> in the toolbar. The <em>Manager Tag Items</em> page appears.<br>
	Fill in the fields as required and press <em>Save</em>.</p>
&nbsp;<br>
<p style="margin-left: 40px;">
	To edit or delete a tag, use standard Joomla! Buttons.</p>

	&nbsp;<br>
<strong>3. Sample Data</strong><br>
&nbsp;<br>
<p style="margin-left: 40px;">
	<em>Install Sample Data</em><br><br>
	
	To check how the component works, you may make use of demo blogs by installing sample data. Once it is installed you will see the JoomBlog category and three sample blogs assigned to it.<br>

</p>
		
</div>
			</div>
		</td>
	</tr>
</table>
