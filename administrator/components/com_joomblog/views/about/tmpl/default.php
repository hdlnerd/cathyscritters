<?php

/**
* JoomBlog component for Joomla 1.6 & 1.7
* @package JoomBlog
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted Access');

$imgpath = JURI::root().'/administrator/components/com_joomblog/assets/images/';
JHtml::_('behavior.tooltip');
?>
<table class="admin">
	<tbody>
		<tr>
			<td valign="top" class="lefmenutd" >
				<?php echo $this->loadTemplate('menu');?>
			</td>
			<td valign="top" width="100%" >
				<table border="1" width="100%" class="about_table" >
					<tr>
						<th colspan="2" class="a_comptitle">
							<strong>
								<?php echo JText::_('COM_JOOMBLOG'); ?>
							</strong>
							component for Joomla! 1.6 and 1.7 and 2.5 Developed by  
							<a href="http://www.JoomPlace.com">
								JoomPlace
							</a>.
						</th>
					</tr>
					<tr>
						<td width="13%"  align="left">Installed version:</td>
						<td align="left">
							&nbsp;<b><?php echo JoomBlogHelper::getVersion();?></b>
						</td>
					</tr>
					<tr>
						<td align="left">Latest version:</td>
						<td>
							<div id="joomport_LatestVersion">
								<a href="check_now" onclick="return joomport_CheckVersion();" class="update_link">
									Check now
								</a>
							</div>
						</td>
					 </tr>
					 <tr>
						<td valign="top" align="left">About:</td>
						<td align="left">
							JoomBlog component includes significant and necessary functionality to make your Joomla! blog extension management easy and user-friendly. This blog component allows configuring template, avatar, blog text settings; moderating comments; adjusting permissions on posting and publishing, and many more. 
						</td>
					</tr>						
					<tr>
						<td align="left">Support forum:</td>
						<td align="left">
							<a target="_blank" href="http://www.JoomPlace.com/support">http://www.JoomPlace.com/support</a>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<br />
							<div class="clr"></div>
							<table border="1" cellpadding="5" width="100%" class="thank_tabl">
								<tr>
									<th colspan="2" class="thank_ftd">
										<strong>
											<img src="<?php echo $imgpath;?>tick.png">Say your "Thank you" to Joomla community for WonderFull Joomla CMS
										</strong>
									</th>
								</tr>					
								<tr>
									<td style="padding-left:20px">
										<div class="thank_fdiv">
											<p style="font-size:12px;">
												<span style="font-size:14pt;">Say your "Thank you" to Joomla community</span> for WonderFull Joomla CMS and <span style="font-size:14pt;">help it</span> by sharing your experience with this component. It will only take 1 min for registration on <a href="http://extensions.joomla.org/extensions/news-production/blog/16108" target="_blank">http://extensions.joomla.org/</a> and 3 minutes to write useful review! A lot of people will thank you!
											</p>
										</div>
										<div style="float:left;margin:5px">
											<a href="http://extensions.joomla.org/extensions/news-production/blog/16108" target="_blank">
											<img src="http://www.joomplace.com/components/com_jparea/assets/images/rate-2.png" />
											</a>
										</div>
										<div style="clear:both;margin:5px;padding-top:5px;">
											<hr color="#CCCCCC"/>
										</div>
										<div style="clear:both"><!--x--></div>
									</td>
								</tr>
								<tr>
									<th colspan="2" class="about_news"><strong><img src="<?php echo JURI::root();?>/administrator/components/com_joomblog/assets/images/tick.png" />Joomplace news/campaigns</strong></th>
								</tr>
								<tr>
									<td colspan="2" style="padding-left:20px" align="justify">
										<div id="joomport_LatestNews" style="width:496px;">
											<script type="text/javascript" language="javascript">
												<!--//--><![CDATA[//><!-- 
												joomport_CheckNews(); 
												//--><!]]>
											</script>
										</div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>