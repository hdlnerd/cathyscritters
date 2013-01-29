<?php

/**
 * @version   1.x
 * @package   ShackSlides
 * @copyright (C) 2010 Joomlashack / Meritage Assets Corp
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Direct access to files is not permitted');

$effects = array(
	'default' => "shackeffects.push('random'); // Random",
	'stairs' => "shackeffects.push({name: 'shackeffect0', cols: 10, rows: 5, delay: 10, fade: true, order: 'straight_stairs'}); // default",
	'slide_bottom' => "shackeffects.push({name: 'shackeffect1', move: true, bottom: true, duration: 400}); // Slide in from bottom",
	'slide_left' => "shackeffects.push({name: 'shackeffect2', move: true, left: true, duration: 400}); // Slide in from left",
	'slide_right' => "shackeffects.push({name: 'shackeffect3', move: true, right: true, duration: 400}); // Slide in from right",
	'slide_top' => "shackeffects.push({name: 'shackeffect4', move: true, top: true, duration: 400}); // Slide in from top",
	'fade' => "shackeffects.push({name: 'shackeffect5', fade: true, duration: 400}); // Fade",
	'swirl_tl' => "shackeffects.push({name: 'shackeffect6', cols: 10, rows: 5, delay: 20, fade: true, order: 'swirl', road: 'TL', duration: 400}); // Swirl from top left",
	'swirl_br' => "shackeffects.push({name: 'shackeffect7', cols: 10, rows: 5, delay: 20, fade: true, reverse: true, order: 'swirl', road: 'BR', duration: 400}); // Swirl from bottom right",
	'random' => "shackeffects.push({name: 'shackeffect8', rows: 5, cols: 10, delay: 20, duration: 400, order: 'random', fade: true, chess: true}); // Random boxes",
	'chess' => "shackeffects.push({name: 'shackeffect9', rows: 5, cols: 10, delay: 20, duration: 400, order: 'straight', fade: true, chess: true}); // Straight chess",
	'snake' => "shackeffects.push({name: 'shackeffect10', rows: 5, cols: 10, delay: 20, duration: 400, order: 'snake', fade: true}); // Snake chase",
	'rain' => "shackeffects.push('rain'); // Rain in from top left",
		);
$selected = $params->get('effect', 'default');
if (is_array($selected)) {
	foreach ($params->get('effect') as $e) {
		$effect .= $effects[$e].PHP_EOL;
	}
}
else {
	$effect = $effects[$selected];
}

$code = "<script type=\"text/javascript\">	var shackeffects = new Array(); var loaded = false;
	";
$code .= $effect;
$code .="
	var ".$params->get('container', $defaults['container'])." = Sliderman.slider({
	contentmode: false,
	container: '".$params->get('container', 'slider')."',
	width: ".$params->get('width', $defaults['width']).",
	height: ".$params->get('height', $defaults['height']).",
	effects: shackeffects,
	display: {
		bootstrap: " . (($params->get('enable_bootstrap_styles', $defaults['enable_bootstrap_styles']) == 'yes') ? '1' : '0') . ",
		pause: ".(($params->get('pause', $defaults['pause']) == 'yes') ? '1' : '0').",
		autoplay: ".(1000*$params->get('autoplay', $defaults['autoplay'])).",
		always_show_loading: false,
		description: {
			".(($params->get('description', $defaults['description']) == 'no') ? "hide: true, " : "")."
			transparent_background: ".($params->get('description_transparent_background', $defaults['description_transparent_background']) == "yes" ? 'true' : 'false').",
			background: '#".$params->get('description_background', $defaults['description_background'])."',
			opacity: ".$params->get('description_opacity', $defaults['description_opacity']).",
			height: ".$params->get('description_height', $defaults['description_height']).",
			width: ".$params->get('description_width', $defaults['description_width']).",
			position: '".$params->get('description_position', $defaults['description_position'])."',
			overflow: '".$params->get('description_overflow', $defaults['description_overflow'])."'
		},
		";
		if ($params->get('buttons', $defaults['buttons']) == 'yes') {
			$code .= "buttons: {
				opacity: ".$params->get('buttons_opacity', $defaults['buttons_opacity']).",
				prev: {
					className: 'sliderPrev',
					label: '".$params->get('buttons_prev_label', $defaults['buttons_prev_label'])."'
				},
				next: {
					className: 'sliderNext',
					label: '".$params->get('buttons_next_label', $defaults['buttons_next_label'])."'
				}
			},
			";
		}
		if ($params->get('navigation', $defaults['navigation']) == 'yes') {
			$code .= "navigation: {
				container: '".$params->get('container', $defaults['container'])."Nav',
				label: 1".(($params->get('navigation_buttons', $defaults['navigation_buttons']) == 'yes') ?
					",
                    prev: {
						className: 'sliderPrev',
						label: '".$params->get('buttons_prev_label', $defaults['buttons_prev_label'])."'
					},
					next: {
						className: 'sliderNext',
						label: '".$params->get('buttons_next_label', $defaults['buttons_next_label'])."'
					}" : "")."
			},
			";
		}
		$code .= "mousewheel: ".(($params->get('mousewheel', $defaults['mousewheel']) == 'yes') ? "true" : "false")."
	}";
	if ($params->get('enable_bootstrap_styles', $defaults['enable_bootstrap_styles']) == 'yes') {
		$code .= ",events : {
			before: function(slider) {
				var o = document.getElement('.slidermanImgCont img');
				if (o == null) return;
				var h = o.getSize().y;
				document.getElement('.slidermanImgCont').setStyles({'max-height': h});
			},
			after: function(slider) {  document.getElement('.slidermanImgCont').setStyles({'max-height' : 'none'}); }
		}";
	}
	$code .="
});
</script>";

echo $code;
