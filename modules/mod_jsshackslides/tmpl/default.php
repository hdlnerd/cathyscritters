<?php
/**
 * @version   1.x
 * @package   ShackSlides
 * @copyright (C) 2010 Joomlashack / Meritage Assets Corp
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Direct access to files is not permitted');

// This can be used in an override to change default settings. User can override
// settings in the module settings page still.
$defaults = array(
	'width' => '500', // width of container
	'height' => '250', // height of container
	'autoplay' => '5', // number of seconds per slide on autoplay, 0 to disable
	'pause' => 'yes', // pauses the autoplay on hover over slider
	'description' => 'yes', // displays image discription box
	'description_background' => 'ffffff', // description background color hex code
	'description_transparent_background' => 'no', // description background is not transparent by default
	'description_opacity' => '0.5', // description background opacity
	'description_height' => '50', // description height if position is top/bottom
	'description_width' => '50', // description width if position is right/left
	'description_position' => 'bottom', // top,button,right,left are options
	'description_overflow' => 'hidden', // default description overflow (from image) is hidden
	'buttons' => 'no', // displays the next/prev buttons
	'buttons_opacity' => '1', // buttons opacity
	'buttons_prev_label' => '<', // previous button label
	'buttons_next_label' => '>', // next button label
	'navigation' => 'yes', // displays the navigation
	'navigation_buttons' => 'yes', // displays next/prev buttons in navigation bar
	'navigation_label' => 'yes', // shows the numbers for navigation
	'navigation_align' => 'center', // shows the numbers for navigation
	'mousewheel' => 'no', // can use mousewheel for navigation
	'container' => 'slider', // id for the slider container,
	'enable_bootstrap_styles' => 'no',
	'include_bootstrap' => 'no',
	'main_container_class' => '',
	'extra_container' => 'no',
	'extra_container_class' => ''
);

// Adding the javascript and css files to the document
$doc = JFactory::getDocument();
$doc->addScript(JURI::base() . 'modules/mod_jsshackslides/assets/sliderman.js');

ob_start();
include(JPATH_ROOT.'/modules/mod_jsshackslides/tmpl/css/sliderman.css.php');
$styles = ob_get_contents();
ob_end_clean();

$doc->addStyleDeclaration($styles);


// Check if we have to include Twitter Bootstrap styles
$include_bootstrap = ($params->get('include_bootstrap', $defaults['include_bootstrap']) == "yes");
$enable_bootstrap_styles = ($params->get('enable_bootstrap_styles', $defaults['enable_bootstrap_styles']) == "yes");

if ($include_bootstrap) {

	$doc->addStylesheet(JURI::base() . 'modules/mod_jsshackslides/assets/wrappedbootstrap.css');

}

//determine which span class we need to use
$span_class = '';
if ($enable_bootstrap_styles) {
	$span_class = 'span12';
	if ($params->get('description', $defaults['description']) == 'yes') {

		if ( in_array($params->get('description_position', $defaults['description_position']), array('left_image', 'right_image')) ) {
				$span_class = 'span8';
		}
	}
}

$extra_container = ($params->get('extra_container', $defaults['extra_container']) == "yes");


?>

<?php if ($include_bootstrap) : ?><div class='jsbootstrap'><?php endif; ?>

<?php if ($extra_container) : ?><div class='<?php echo $params->get('extra_container_class', $defaults['extra_container_class']); ?>'><?php endif; ?>

<?php if ($enable_bootstrap_styles) : ?><div id='shackslides-row' class='row-fluid'><?php endif ;?>

	<div class="shackSlider<?php echo $params->get('container', $defaults['container']) ?> <?php echo $params->get('main_container_class', $defaults['main_container_class']); ?> <?php echo $span_class; ?>">

		<div id="<?php echo $params->get('container', $defaults['container']) ?>">


			<?php for ($i = 0; $i < count($images); $i++) : ?>
				<?php if ($images[$i] === false) continue; ?>
				<?php if ($links[$i]) : ?>
					<a href="<?php echo $links[$i]; ?>"<?php if ($params->get('anchor_target', 'self') == 'blank') echo ' target="_blank" ' ?>>
				<?php endif; ?>
						<img src="<?php echo $base.$images[$i] ?>" title="<?php echo strip_tags($titles[$i]) ?>" alt="<?php echo strip_tags($titles[$i]) ?>" />
				<?php if ($links[$i]) : ?>
					</a>
				<?php endif; ?>

				<?php if ($titles[$i] && $params->get('description', $defaults['description']) == 'yes') : ?>
					<div class="slideTitle">
						<?php echo $titles[$i]; ?>
					</div>
				<?php endif; ?>

			<?php endfor; ?>

		</div>

		<div id="<?php echo $params->get('container', $defaults['container']) ?>Nav"></div>

	</div>

<?php if ($enable_bootstrap_styles) : ?></div><?php endif; ?>

<?php if ($extra_container) : ?></div><?php endif; ?>

<?php if ($include_bootstrap) : ?></div><?php endif; ?>

<?php include(JPATH_BASE.'/'.'modules'.'/'.'mod_jsshackslides'.'/'.'assets'.'/'.'script.js.php'); ?>
