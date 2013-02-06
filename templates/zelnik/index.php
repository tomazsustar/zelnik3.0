<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.zelnik
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->getCfg('sitename');

if($task == "edit" || $layout == "form" )
{
	$fullWidth = 1;
}
else
{
	$fullWidth = 0;
}

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');

// Add Stylesheets
$doc->addStyleSheet('templates/'.$this->template.'/css/template.css');

// Load optional rtl Bootstrap css and Bootstrap bugfixes
JHtmlBootstrap::loadCss($includeMaincss = false, $this->direction);

// Add current user information
$user = JFactory::getUser();

// Adjusting content width
if ($this->countModules('position-7') && $this->countModules('position-8'))
{
	$span = "span6";
}
elseif ($this->countModules('position-7') && !$this->countModules('position-8'))
{
	$span = "span9";
}
elseif (!$this->countModules('position-7') && $this->countModules('position-8'))
{
	$span = "span9";
}
else
{
	$span = "span12";
}

// Logo file or site title param
if ($this->params->get('logoFile'))
{
	$logo = '<img src="'. JURI::root() . $this->params->get('logoFile') .'" alt="'. $sitename .'" />';
}
elseif ($this->params->get('sitetitle'))
{
	$logo = '<span class="site-title" title="'. $sitename .'">'. htmlspecialchars($this->params->get('sitetitle')) .'</span>';
}
else
{
	$logo = '<span class="site-title" title="'. $sitename .'">'. $sitename .'</span>';
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<jdoc:include type="head" />
	<?php
	// Use of Google Font
	if ($this->params->get('googleFont'))
	{
	?>
		<link href='http://fonts.googleapis.com/css?family=<?php echo $this->params->get('googleFontName');?>' rel='stylesheet' type='text/css' />
		<style type="text/css">
			h1,h2,h3,h4,h5,h6,.site-title{
				font-family: '<?php echo str_replace('+', ' ', $this->params->get('googleFontName'));?>', sans-serif;
			}
		</style>
	<?php
	}
	?>
	<?php
	// Template color
	if ($this->params->get('templateColor'))
	{
	?>
	<style type="text/css">
		body.site
		{
			border-top: 0px solid <?php echo $this->params->get('templateColor');?>;
			background-color: <?php echo $this->params->get('templateBackgroundColor');?>
		}
		a
		{
			color: <?php echo $this->params->get('templateColor');?>;
		}
		.navbar-inner, .nav-list > .active > a, .nav-list > .active > a:hover, .dropdown-menu li > a:hover, .dropdown-menu .active > a, .dropdown-menu .active > a:hover, .nav-pills > .active > a, .nav-pills > .active > a:hover,
		.btn-primary, 
		{
			background: <?php echo $this->params->get('templateColor');?>;
		}
		.navbar-inner
		{
			-moz-box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
			-webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
			box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
		}
	</style>
		<script src="templates/zelnik/js/js-scrollbars-v2/js/aplweb.scrollbars.js" type="text/javascript"></script>
	<script src="templates/zelnik/js/jquery-1.7.1.min.js" type="text/javascript"></script>
	<!-- styles needed by jScrollPane -->
<link type="text/css" href="templates/zelnik/css/jquery.jscrollpane.css" rel="stylesheet" media="all" />
 
<!-- latest jQuery direct from google's CDN -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js">
</script>
 
<!-- the mousewheel plugin - optional to provide mousewheel support -->
<script type="text/javascript" src="templates/zelnik/js/jquery.mousewheel.js"></script>
 
<!-- the jScrollPane script -->
<script type="text/javascript" src="templates/zelnik/js/jquery.jscrollpane.min.js"></script>
<script type="text/javascript">
		
		//fixiranje
		$(function(){ // document ready

		  if (!!$('.sticky').offset()) { // make sure ".sticky" element exists

		    var stickyTop = $('.sticky').offset().top; // returns number 

		    $(window).scroll(function(){ // scroll event

		      var windowTop = $(window).scrollTop(); // returns number 

		      if (stickyTop < windowTop){
		        $('.sticky').css({ position: 'fixed',top: '10px'});
				$('.body-background').css({ position: 'fixed',bottom: '0px'});
		      }
		      else {
		        $('.sticky').css({ position: 'absolute',top: '200px'});
				$('.body-background').css({ position: 'absolute',bottom: '-200px'});
		      }
		      if (window.innerHeight > 900){
		        $('.sticky').css({ position: 'fixed',top: '200px'});
				$('.body-background').css({ position: 'fixed',bottom: '-200px'});
				$('.logo').css({ position: 'fixed', top: 0 });
				$('.body').css({ margin: '260px auto auto auto' });
		      }
			  else {
			  $('.logo').css({ position: 'relative'});
			  $('.body').css({ margin: '60px auto auto auto' });
			  }
		    });
		  }
		  });
		  
		  
		 // višina okna 
		 $(window.onresize = function() {
		$(".body-background").css("height", (window.innerHeight) + "px");
		$(".container").css("height", (window.innerHeight) + "px");
		
		var element = document.getElementById('body'),
			style = window.getComputedStyle(element),
			sirinaBody = parseInt(style.getPropertyValue('max-width'));
		
		if (window.innerWidth < sirinaBody) {
		$("#leva-reklama").css("display", "none");
		}
		else {
		$("#leva-reklama").css("display", "block");
		}
		//skrolanje
		$(function() {
    $('.content-area').jScrollPane();
});

$('.jspDrag').hide();
$('.jspScrollable').mouseenter(function(){
    $(this).find('.jspDrag').stop(true, true).fadeIn('slow');
});
$('.jspScrollable').mouseleave(function(){
    $(this).find('.jspDrag').stop(true, true).fadeOut('slow');
});	
	});
	</script>
	

	<?php
	}
	?>
	<!--[if lt IE 9]>
		<script src="<?php echo $this->baseurl ?>/media/jui/js/html5.js"></script>
	<![endif]-->
</head>
	
<body class="site">
	<!-- Header -->
	<div class="header">
		<div class="logo">
			<?php echo '<a href="'.$this->baseurl .'">'. $logo .'</a>'; ?>
			<?php if ($this->params->get('sitedescription')) { echo '<div class="site-description">'. htmlspecialchars($this->params->get('sitedescription')) .'</div>'; } ?>
		</div>		
		<div class="header sticky">				
			<jdoc:include type="modules" name="position-1" />
			<div class="home">
				<?php echo '<a href="'.$this->baseurl .'">'.'<img src="'.$this->baseurl .'/templates/zelnik/images/home.png" alt="Home"> </a>'; ?> 
			</div>
			<div class="social">
				<div> 
					<div style="float: left; z-index:1000;">
						<a rel="nofollow" href="https://www.facebook.com/zelnik.net" target="_blank"><img style="width: 8px; height: 16px; margin:5px;" <?php echo 'src="'.$this->baseurl .'/templates/zelnik/images/fb_button.png"'; ?> alt="Facebook" title="Sledite nam na Facebooku"></a>
					</div>
					<div style="float: left; z-index:1000;">
						<a rel="nofollow" href="https://twitter.com/zelniknet" target="_blank"><img style=" width: 12px; height: 16px; margin:5px;" <?php echo 'src="'.$this->baseurl .'/templates/zelnik/images/twitter_button.png"'; ?> alt="Twitter" title="Sledite nam na Twitterju"></a>
					</div>
					<div style="float: left; z-index:1000;">
						<a rel="nofollow" <?php echo 'href="'.$this->baseurl .'/index.php?format=feed&type=rss"'; ?> target="_blank"><img style=" width: 16px; height: 16px; margin:5px;" <?php echo 'src="'.$this->baseurl .'/templates/zelnik/images/rss_button.png"'; ?> alt="Rss" title="Pregled RSS spletne strani"></a>
					</div>
				</div>
			<jdoc:include type="modules" name="position-0" />	
			</div>
			<div class="vrh-levi">
			<div class="vogal-levi"></div>
			<div class="sredina"></div>
			<div class="vogal-desni"></div>
			</div>
			<div class="vrh-desni">
			<div class="vogal-levi"></div>
			<div class="sredina"></div>
			<div class="vogal-desni"></div>
			</div>
		</div>
	</div>	
	<!-- Body -->
	<div class="body-background"></div>	
	<div id="body" class="body">		
		<div id="leva-reklama"></div>
		<div class="container">
			<div id="glavno-okno" class="sticky">
				<div id="rob-levi"></div>				
				<div class="sredina"></div>
				<div id="rob-desni"></div>
			</div>
			<div id="desno-okno" class="sticky">
				<div id="rob-levi-right"></div>
				<div class="sredina"></div>
				<div id="rob-desni-right"></div>
			</div>	
			<div id="content">
				<!-- Begin Content -->
				<jdoc:include type="modules" name="position-3" style="xhtml" />
				<jdoc:include type="message" />
				<jdoc:include type="component" />
				<jdoc:include type="modules" name="position-2" style="none" />
				<!-- End Content -->
			</div> 
			<div id="desna-reklama"></div>
		</div>
		<div id="element"></div>
	</div>
	<!-- Footer -->
	<div class="footer"></div>
	<div id="front-footer">
		<div id="footer-text">
			&copy; <?php echo $sitename; ?> <?php echo date('Y');?>
		</div>
	</div>
</body>
</html>