<?php
/**
 * @copyright Copyright (C) 2011 Joomlashack LLC. All rights reserved.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

if (version_compare(JVERSION, '3.0', 'lt')) {
	JHTML::_('behavior.mootools');
}
else {
	JHtmlBehavior::framework($extras=true);
}

// WrightTemplate class, for special settings on Wright
class WrightTemplate extends WrightTemplateBase {
	public $suffixes = true;
	// allows stacked suffixes
}

$user = JFactory::getUser();
$menutype = $this -> params -> get('menutype', 'accordion-open');

// templateTone parameter (Light = '-Light' - Dark = '-Dark')
if (!is_null(JRequest::getVar('templateTone', NULL)))
{
	$Tone = JRequest::getVar('templateTone');
	if ($Tone == '-Light' || $Tone == '-Dark') {
		$user->setParam('templateTone', JRequest::getVar('templateTone'));
		$user->save(true);
	}
}
$Tone = ($user->getParam('templateTone',''));
if ($Tone == '') {
	$Tone =  $this->params->get('Tone','' );
}
elseif ($Tone == '-Light')
{
	$Tone = '';
}
?>
<doctype>
	<html>
		<head>
			<w:head />
			<?php if (strpos($menutype,'accordion') !== FALSE): ?>
			<script language="javascript" type="text/javascript">
				var wrightAccordionHover = <?php echo ((strpos($menutype,'hover') === FALSE) ? "false" : "true"); ?>;
			</script>
			<script language="javascript" type="text/javascript" src="<?php echo $this->baseurl ?>/templates/js_formula/js/accordion.open.js"></script>
			<script language="javascript" type="text/javascript" src="<?php echo $this->baseurl ?>/templates/js_formula/js/menu.js"></script>
			<?php endif; ?>
		</head>
		<body class="<?php echo $Tone ?>">
			<div class="total" id="total">
				<div class="top">
					<div class="top-wrapper">
						<w:module type="single" name="top" chrome="xhtml" />
						<div class="clr"></div>
					</div>
				</div>
				<div class="logo">
						<div class="container_12 clearfix">
							<div class="grid_<?php echo $this->params->get('logowidth','2') ?>" id="logogrid">
								<w:logo style="none"/>
							</div>
							<div class="grid_<?php echo (12 - (int)$this->params->get('logowidth','2')) ?>" id="menu">
								<w:module type="single" name="menu" chrome="xhtml" />	
							</div>
							<div class="clear"></div>
						</div>
						
				</div>    
					
						<?php if ($this->countModules('featured')) :
						?>
						<div class="featured-wrapper">
							<div class="featured1-wrapper">
								<w:module type="single" name="featured" chrome="xhtml" />
								<div class="clr"></div>
								<div class="featured-bottom"></div>
							</div>
							<div class="clear"></div>
							<div class="featured-bottom"></div>
						</div>
						<?php    endif;?>
						
				<div class="content-wrapp">
					
					<div id="container">
						<div class="main-wrapper">
							<div class="main">
								<div class="content container_12 clearfix">
									<?php if ($this->countModules('grid-top')) :
									?>
									<div id="grid-top">
										<w:module type="grid" name="grid-top" chrome="wrightflexgridimages" />
										<div class="clearmargin"></div>
									</div>
									<?php    endif;?>
									<?php if ($this->countModules('grid-top2')) :
									?>
									<div id="grid-top2">
										<w:module type="grid" name="grid-top2" chrome="wrightflexgridimages" />
										<div class="clearmargin"></div>
									</div>
									<?php    endif;?>
									<section id="main">
										<div class="main-pad1">
											<div class="main-pad2">
												<div class="main-pad3">
													<?php if ($this->countModules('breadcrumbs')) :
													?>
													<div id="pathway" class="border">
														<div id="pathway-inner" class="inner-border">
															<w:module type="single" name="breadcrumbs" chrome="xhtml" />
															<div class="clear"></div>
														</div>
													</div>
													<div class="clear"></div>
													<?php    endif;?>

													<div class="cont-style">
														<w:content />
													</div>
													<div class="clr"></div>
													<div class="clearmargin"></div>
												</div>
											</div>
										</div>
									</section>
									<aside id="sidebar1">
										<div class="sombra"></div>
										<div class="sombra-left"></div>
										<div class="sombra-bottom"></div>
										<w:module name="sidebar1" chrome="wrightgridimages" />
										<div class="clearmargin"></div>
									</aside>
									<aside id="sidebar2">
										<div class="sombra"></div>
										<div class="sombra-left"></div>
										<div class="sombra-bottom"></div>
										<w:module name="sidebar2" chrome="wrightgridimages" />
										<div class="clearmargin"></div>
									</aside>
									<div class="clear"></div>
									<?php if ($this->countModules('grid-bottom')) :
									?>
									<div id="grid-bottom">
										<w:module type="grid" name="grid-bottom" chrome="wrightflexgridimages" />
										<div class="clearmargin"></div>
									</div>
									<?php    endif;?>
									<?php if ($this->countModules('grid-bottom2')) :
									?>
									<div id="grid-bottom2">
										<w:module type="grid" name="grid-bottom2" chrome="wrightflexgridimages" />
										<div class="clearmargin"></div>
									</div>
									<?php endif;?>
								</div>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>
					</div>
					
				</div>
				<div class="footer-wrapper">
						<div class="footer-int">
							<w:module type="single" name="footer" chrome="xhtml" />
							<w:footer />
							<div class="clear"></div>
						</div>
					</div>
			</div>
		</body>
	</html>
