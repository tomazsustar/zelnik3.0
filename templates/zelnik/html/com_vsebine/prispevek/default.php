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

$document = JFactory::getDocument();
$document->addScript(JUri::base().'/plugins/content/boxplus/js/boxplus.min.js');
$document->addScript(JUri::base().'/plugins/content/boxplus/js/boxplus.lang.min.js?lang=en-GB');
$document->addStyleSheet(JUri::base().'/plugins/content/boxplus/css/boxplus.min.css');
$document->addStyleSheet(JUri::base().'/plugins/content/boxplus/css/boxplus.darksquare.css');

$document->addScriptDeclaration('window.addEvent("load", function() {
												new JCaption("img.caption");
											});
			boxplus.autodiscover(
			false,
			{"theme":"darksquare",
			"autocenter":true,
			"autofit":true,
			"slideshow":0,
			"loop":false,
			"captions":"bottom",
			"thumbs":"inside",
			"width":800,
			"height":600,
			"duration":250,
			"transition":"linear",
			"contextmenu":true});'
);


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
$item=&$this->item?>
<!--publish_up
created
author_alias-->
<div class="novica">
	
	<div class="znacke">
		<?php 
			$tagsArray=array();
			foreach ($item->tags as $tag){
			 $tagsArray[] = '<a href="'.$tag->tagUrl.'">'.mb_strtoupper($tag->tag).'</a>';
			}
		 	echo implode(', ',$tagsArray); ?>
	 </div>
	 <p><strong><?php if (!empty($item->author_alias)) {echo $item->author_alias.',';} elseif (!empty($item->author)){echo $item->author.',';}?></strong><?php if ($item->publish_up <= new Datetime()) {?> <?php echo $item->publish_up->format('j. n. Y');}?></p>
	<h1><?php echo $item->title;?></h1>
	<div class="besedilo">
		<div class=prispevek-slike> 
			<?php foreach ($item->slike as $slika):?>
				<div><a href="<?php echo $slika->url;?>" rel="boxplus-slike" target="_blank"><img src="<?php echo $slika->url2;?>" /></a></div>
			<?php endforeach?>
		</div>
		<?php echo $item->fulltext;?>
	</div>
	
	<div class="prispevek-video"><?php echo $item->video; ?></div>
	
	<?php if(count($item->galerija)):?> <hr />
		<div class="prispevek-galerija">
			<?php foreach ($item->galerija as $slika):?>
				<a rel="boxplus-slike" href="<?php echo $slika->url;?>" target="blank"><img src="<?php echo $slika->url2;?>" /></a>
			<?php endforeach?>
		</div>
	<?php endif;?>
	<?php if(count($item->priponke)):?> 
		<div class="prispevek-galerija">
			<h3>Priponke:</h3>
			<?php foreach ($item->priponke as $priponka):?>
				<a href="<?php echo $priponka->url;?>" target="blank"><?php echo $priponka->ime_slike?></a><br>
			<?php endforeach?>
		</div>
	<?php endif;?>
</div>


