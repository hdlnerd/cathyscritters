<?php
$theme = $params->get('theme', 'gray');

$colors = array(
	'gray' => array('base' => '353535', 'border' => '333333', 'shadow' => '666', 'a' => 'ddd', 'active' => 'fff'),
	'blue' => array('base' => '7A8498', 'border' => '7A8498', 'shadow' => '666', 'a' => 'ddd', 'active' => 'fff'),
	'white' => array('base' => 'd5d5d5', 'border' => 'ccc', 'shadow' => 'bbb', 'a' => '666', 'active' => '333'),
	'red' => array('base' => 'A74040', 'border' => '5F2F2F', 'shadow' => '666', 'a' => 'fff', 'active' => 'fff'),
	'green' => array('base' => '81ac7b', 'border' => '3b4f38', 'shadow' => '666', 'a' => 'fff', 'active' => 'fff'),
	'orange' => array('base' => 'd9a74d', 'border' => '925813', 'shadow' => '666', 'a' => 'fff', 'active' => 'fff'),
	'brown' => array('base' => '816e56', 'border' => '3c3428', 'shadow' => '666', 'a' => 'fff', 'active' => 'fff'),
	'black' => array('base' => '252525', 'border' => '101010', 'shadow' => '333', 'a' => 'fff', 'active' => 'fff')
);

?>
.shackSlider<?php echo $params->get('container', $defaults['container']) ?> {
	<?php if ($params->get('enable_bootstrap_styles', $defaults['enable_bootstrap_styles']) == 'no') : ?>

		width: <?php echo $params->get('width', $defaults['width']) ?>px;
		width: 100% !important;
		<?php if ($params->get('description',$defaults['description']) == "yes" &&
			  $params->get('description_position',$defaults['description_position']) == "left_image") : ?>
		padding-left: <?php echo $params->get('description_width',$defaults['description_width']) ?>px;
		<?php endif; ?>
		<?php if ($params->get('description',$defaults['description']) == "yes" &&
			  $params->get('description_position',$defaults['description_position']) == "right_image") : ?>
		padding-right: <?php echo $params->get('description_width',$defaults['description_width']) ?>px;
		<?php endif; ?>
		<?php if ($params->get('description',$defaults['description']) == "yes" &&
			  $params->get('description_position',$defaults['description_position']) == "above_image") : ?>
		padding-top: <?php echo $params->get('description_height',$defaults['description_height']) ?>px;
		<?php endif; ?>
		<?php if ($params->get('description',$defaults['description']) == "yes" &&
			  $params->get('description_position',$defaults['description_position']) == "below_image") : ?>
		padding-bottom: <?php echo $params->get('description_height',$defaults['description_height']) ?>px;
		<?php endif; ?>

	<?php endif; ?>

	margin-bottom: 10px;
}

.shackSlider<?php echo $params->get('container', $defaults['container']) ?> #sliderContainer<?php echo $params->get('container', $defaults['container']) ?> {
	background: #<?php echo $colors[$theme]['base'] ?> url(<?php echo JURI::base() ?>modules/mod_jsshackslides/tmpl/images/bg.png);
	border: 2px solid #<?php echo $colors[$theme]['border'] ?>;
}

#<?php echo $params->get('container', $defaults['container']) ?>{
	width: <?php echo $params->get('width', $defaults['width']) ?>px;
	width: 100% !important;
	height: <?php echo $params->get('height', $defaults['height']) ?>px;
	margin: auto;
}

.slideTitle{
	font-family: Verdana;
	font-size: 10px;
	text-align: left;
	padding: 5px;
}

#<?php echo $params->get('container', $defaults['container']) ?>Nav {
	<?php if ($params->get('enable_bootstrap_styles', $defaults['enable_bootstrap_styles']) == 'yes') : ?>
	margin: 10px 0;
	display: block;
	<?php endif; ?>
	text-align: <?php echo $params->get('navigation_align', $defaults['navigation_align']) ?>;
}

#<?php echo $params->get('container', $defaults['container']) ?>Nav a:link, #<?php echo $params->get('container', $defaults['container']) ?>Nav a:active, #<?php echo $params->get('container', $defaults['container']) ?>Nav a:visited, #<?php echo $params->get('container', $defaults['container']) ?>Nav a:hover{
	background: url(<?php echo JURI::base() ?>modules/mod_jsshackslides/tmpl/images/<?php echo $theme ?>/item.png) no-repeat center center;
	line-height: 24px;
	font-size: 16px;
	height: 24px;
	width: 25px;
	margin: 3px auto;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	<?php if ($params->get('enable_bootstrap_styles', $defaults['enable_bootstrap_styles']) == 'no') : ?>
	color: #<?php echo $colors[$theme]['a'] ?>;
	<?php if ($params->get('navigation_label', $defaults['navigation_label']) == 'no') echo 'text-indent: -9999px;'; ?>
	text-shadow: 1px 1px 1px #<?php echo $colors[$theme]['shadow'] ?>;
	<?php endif; ?>
}

#<?php echo $params->get('container', $defaults['container']) ?>Nav a.active:link, #<?php echo $params->get('container', $defaults['container']) ?>Nav a.active:active, #<?php echo $params->get('container', $defaults['container']) ?>Nav a.active:visited, #<?php echo $params->get('container', $defaults['container']) ?>Nav a.active:hover{
	<?php if ($params->get('enable_bootstrap_styles', $defaults['enable_bootstrap_styles']) == 'no') : ?>
	background: url(<?php echo JURI::base() ?>modules/mod_jsshackslides/tmpl/images/<?php echo $theme ?>/item_active.png) no-repeat center center;
	color: #<?php echo $colors[$theme]['active'] ?>;
	text-shadow: 1px 1px 1px #<?php echo $colors[$theme]['shadow'] ?>;
	<?php endif; ?>
}

a.sliderPrev{
	background: url(<?php echo JURI::base() ?>modules/mod_jsshackslides/tmpl/images/<?php echo $theme ?>/next.png) no-repeat center center;
	width: 49px;
	height: 24px;
	display: inline-block;
	line-height: 24px;
	text-align: center;
	text-decoration: none;
	position: absolute;
	<?php if ($params->get('enable_bootstrap_styles', $defaults['enable_bootstrap_styles']) == 'no') : ?>
	color: #<?php echo $colors[$theme]['a'] ?> !important;
	top: <?php echo ($params->get('height', $defaults['height']) / 2) - 12; ?>px;
	<?php else : ?>
	top:
	<?php endif; ?>

}

a.sliderNext{
	background: url(<?php echo JURI::base() ?>modules/mod_jsshackslides/tmpl/images/<?php echo $theme ?>/prev.png) no-repeat center center;
	width: 49px;
	height: 24px;
	display: inline-block;
	text-decoration: none;
	line-height: 24px;
	text-align: center;
	position: absolute;
	<?php if ($params->get('enable_bootstrap_styles', $defaults['enable_bootstrap_styles']) == 'no') : ?>
	color: #<?php echo $colors[$theme]['a'] ?> !important;
	<?php endif; ?>
	top: <?php echo ($params->get('height', $defaults['height']) / 2) - 12; ?>px;
	left: <?php echo $params->get('width', $defaults['width']) - 49; ?>px;
}

#<?php echo $params->get('container', $defaults['container']) ?>Nav a.sliderPrev{
	background: url(<?php echo JURI::base() ?>modules/mod_jsshackslides/tmpl/images/<?php echo $theme ?>/prev.png) no-repeat center center;
	position: static;
	width: 49px;
}

#<?php echo $params->get('container', $defaults['container']) ?>Nav a.sliderNext{
	background: url(<?php echo JURI::base() ?>modules/mod_jsshackslides/tmpl/images/<?php echo $theme ?>/next.png) no-repeat center center;
	position: static;
	width: 49px;
}

<?php if ($params->get('enable_bootstrap_styles', $defaults['enable_bootstrap_styles']) == 'yes') : ?>
#sliderContainer {
	width: 100% !important;
	height: auto !important;
	overflow: hidden;
}

#sliderContainer div img {
	width: 100% !important;
	height: auto !important;
}

.slidermanImgCont {
	width: 100% !important;
	height: auto !important;
	position: static !important;
}

.slidermanImgCont div {
	width: 100% !important;
	height: auto !important;
	position: static !important;
}

.slidermanImgCont div img {
	width: 100% !important;
	height: auto !important;
	position: static !important;
}

#<?php echo $params->get('container', $defaults['container']) ?>{
	height: auto !important;
}

a.sliderPrev, a.sliderNext, #<?php echo $params->get('container', $defaults['container']) ?>Nav ul li a {
	background: none !important;
	width: auto !important;
}

.slidermanlinkContainer, .slidermanlinkContainer a {
	width: 100% !important;
	height: 100% !important;
}

.slidermanDescriptionBG, .slidermanDescriptionText, .slideTitle {
	-webkit-box-sizing: border-box;
	-moz-box-sizing:border-box;
	-ms-box-sizing:border-box;
	-o-box-sizing:border-box;
	box-sizing: border-box;
	<?php if (in_array($params->get('description_position', $default['description_position']), array('top', 'bottom'))) : ?>
	width: 100% !important;
	<?php else : ?>
	height: 100% !important;
	<?php endif; ?>
}

.slidermanButtonsCont {
	position: absolute;
	top: 0;
	left: 0;
	height: 100%;
	width: 100%;
}

.slidermanButtonsCont .sliderPrev, .slidermanButtonsCont .sliderNext {
	display: inline !important;
	position: static !important;
	font-size: 35px;
	margin-top: 30%;
}

.slidermanButtonsCont .sliderPrev{
	float: left;
	margin-left: 5px;
}

.slidermanButtonsCont .sliderNext {
	float: right;
	margin-right: 5px;fnav
}

<?php endif; ?>
