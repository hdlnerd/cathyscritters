<?php
defined('_JEXEC') or die;
?>
<?php if (JPluginHelper::isEnabled('user', 'profile')) :
	$fields = $this->item->profile->getFieldset('profile'); ?>
<div class="contact-profile" id="users-profile-custom">
	<dl>
	<?php foreach ($fields as $profile) :
		if ($profile->value) : 
			echo '<dt>'.$profile->label.'</dt>';
			$profile->text = htmlspecialchars($profile->value, ENT_COMPAT, 'UTF-8');

			switch ($profile->id) :
				case "profile_website":
					$v_http = substr ($profile->profile_value, 0, 4);

					if ($v_http == "http") :
						echo '<dd><a href="'.$profile->text.'">'.$profile->text.'</a></dd>';
					else :
						echo '<dd><a href="http://'.$profile->text.'">'.$profile->text.'</a></dd>';
					endif;
					break;

				default:
					echo '<dd>'.$profile->text.'</dd>';
					break;
			endswitch;
		endif;
	endforeach; ?>
	</dl>
</div>
<?php endif; ?>
