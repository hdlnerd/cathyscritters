<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<?php
	echo "<!-- Social Share Buttons | Powered by <a href=\"http://www.e-max.it\" title=\"Web Agency\" target=\"_blank\">Web Agency</a> -->";
	
?>
<div class="social-share-button-mod<?php echo $params->get('moduleclass_sfx');?>">
    <?php
    echo SocialShareButtonsHelper::getFacebookLike($params, $url, $title);
    echo SocialShareButtonsHelper::getFacebookShareMe($params, $url, $title);
    echo SocialShareButtonsHelper::getTwitter($params, $url, $title);
    echo SocialShareButtonsHelper::getReTweetMeMe($params, $url, $title);
    echo SocialShareButtonsHelper::getDigg($params, $url, $title);
    echo SocialShareButtonsHelper::getStumbpleUpon($params, $url, $title);
    echo SocialShareButtonsHelper::getLinkedIn($params, $url, $title);
    echo SocialShareButtonsHelper::getBuzz($params, $url, $title);
    echo SocialShareButtonsHelper::getGooglePlusOne($params, $url, $title);
    ?>
</div>
<div style="clear:both;"></div>
<?php
	
	if (($params->get( 'credits'))) {
		echo "<div class=\"social_share_buttons_credits\">Powered by <a href=\"http://www.e-max.it\" title=\"Web Marketing\" target=\"_blank\">Web Marketing</a></div>";
	}
	else {
		echo "<div class=\"social_share_buttons_credits\" style=\"display:none;\">Powered by <a href=\"http://www.e-max.it\" title=\"Web Marketing\" target=\"_blank\">Web Marketing</a></div>";
	}

	echo "<!-- Social Share Buttons | Powered by <a href=\"http://www.e-max.it\" title=\"Web Agency\" target=\"_blank\">Web Agency</a> -->";
?>
<div style="clear:both;"></div>