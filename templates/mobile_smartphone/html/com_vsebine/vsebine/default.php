<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

JHtml::_('behavior.caption');

// If the page class is defined, add to class as suffix.
// It will be a separate class if the user starts it with a space
/*?>
<div class="blog-featured<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->get('show_page_heading') != 0) : ?>
<div class="page-header">
	<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
</div>
<?php endif; ?>

<?php */ //if (!empty($this->intro_items)) : ?>
<?php 
$count = count($this->items);
$item=&$this->items[0];
//list($width, $height) = @getimagesize($item->slika);
//if($height!=0){
//$razmerje = $width / $height;
//if($razmerje>1.5) {
//$visek = (1-$width/($height*1.5))*50;
//$izbira="height: 100%; margin-left: ".$visek."%";
//}
//else  {
//$visek = (1-$height/($width/1.5))*25;
//$izbira="width: 100%; margin-top: ".$visek."%";
//}
//}
$izbira="width: 100%;";
?>
<div class="naslovna-novica">
	<div class="znacka"><a href="<?php echo $item->tagUrl?>"><?php echo mb_strtoupper($item->tag); ?></a></div>
	<h1><a href="<?php echo $item->url?>">
	<div class="crop-veliki">
	<img alt="" src="<?php echo $item->slika;?> "  style="<?php echo $izbira?>"/>
	</div>
	<?php echo $item->title;?></a></h1>
	<div class="uvodni-text"><?php echo $item->introtext;?></div>
</div>

<?php

for($i = 1; $i <= 4; $i += 1) {
if($i&1) { ?>
	<div class="stolpec"> 
<?php 
	}
if($count>$i){ $item=&$this->items[$i];
//list($width, $height) = @getimagesize($item->slika);
//if($height!=0){
//$razmerje = $width / $height;
//if($razmerje>1.5) {
//$visek = (1-$width/($height*1.5))*50;
//$izbira="height: 100%; margin-left: ".$visek."%";
//}
//else  {
//$visek = (1-$height/($width/1.5))*25;
//$izbira="width: 100%; margin-top: ".$visek."%";
//}
//}
$izbira="width: 100%;";
?>
<div class="naslovna-novica-pomozna">
	<div class="znacka"><a href="<?php echo $item->tagUrl?>"><?php echo mb_strtoupper($item->tag); ?></a></div>
	<a href="<?php echo $item->url?>">
		<div class="crop-mali">
			<img alt="" src="<?php echo $item->slika;?> " style="<?php echo $izbira?>"/>
		</div>
	<?php echo $item->title;?>
	</a>
</div>
<?php }
if(($i+1)&1) { 
	?>
	</div> 
<?php 
	} 
}
?>
<div id="reklama">
<?php
$modules = JModuleHelper::getModules('position-6');
foreach($modules as $module)
{
echo JModuleHelper::renderModule($module);
}
if (!isset($module)) {echo "<hr />";}
?>
</div>

<?php
//for($j = 1; $j <= 24; $j += 3) { 
for($j = 1; $j <= 24; $j += 1) { 
	if($j&1) { ?>
	<div class="stolpec-precno"> 
<?php 
	}?>
<div class="stolpec">
<?php
if($count>(4+$j)){ $item=&$this->items[4+$j]; 
//list($width, $height) = @getimagesize($item->slika);
//if($height!=0){
//$razmerje = $width / $height;
//if($razmerje>1.5) {
//$visek = (1-$width/($height*1.5))*50;
//$izbira="height: 100%; margin-left: ".$visek."%";
//}
//else  {
//$visek = (1-$height/($width/1.5))*25;
//$izbira="width: 100%; margin-top: ".$visek."%";
//}
//}
$izbira="width: 100%;";
?>
<div class="naslovna-novica">
	<div class="znacka"><a href="<?php echo $item->tagUrl?>"><?php echo mb_strtoupper($item->tag); ?></a></div>
	<h1><a href="<?php echo $item->url?>">
	<div class="crop-veliki">
	<img alt="" src="<?php echo $item->slika;?> " style="<?php echo $izbira?>"/>
	</div>
	<?php echo $item->title;?></a></h1>
	<div class="uvodni-text"><?php echo $item->introtext;?></div>
</div>

<?php  } 

//for($i = (5+$j); $i <= (6+$j); $i += 1) {
for($i = (1+$j); $i <= (2+$j); $i += 1) {
	if(($i+$j)&1) { ?>
	<div class="stolpec"> 
<?php 
	}
if($count>$i){ $item=&$this->items[$i];
//list($width, $height) = @getimagesize($item->slika);
//if($height!=0){
//$razmerje = $width / $height;
//if($razmerje>1.5) {
//$visek = (1-$width/($height*1.5))*50;
//$izbira="height: 100%; margin-left: ".$visek."%";
//}
//else  {
//$visek = (1-$height/($width/1.5))*25;
//$izbira="width: 100%; margin-top: ".$visek."%";
//}
//}
$izbira="width: 100%;";
?>
<div class="naslovna-novica-pomozna">
	<a href="<?php echo $item->url?>">
		<div class="crop-mali">
			<img alt="" src="<?php echo $item->slika;?> " style="<?php echo $izbira?>"/>
		</div>
	<?php echo $item->title;?>
	</a>
</div>
<?php } 
if(($i+$j-1)&1) {
	?>
	</div> 
<?php 
	} 
}
?>
</div>
<?php
if(($j+1)&1) { 
	?>
	</div> 
<?php 
	} 
}


/*foreach ($this->items as $key => $item) : ?>

					<?php //$this->item = &$item;
					//echo $this->loadTemplate('item');?>
					
				<div>
					<?php //echo $item->title."-".$item->publish_up; ?>
				</div>
			

		<?php endforeach; */?>
<?php //endif; ?>

