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
$item=&$this->item?>

<div class="novica">
	<div class="znacke">
		<?php 
			$tagsArray=array();
			foreach ($item->tags as $tag){
			 $tagsArray[] = '<a href="'.$tag->tagUrl.'">'.mb_strtoupper($tag->tag).'</a>';
			}
		 	echo implode(', ',$tagsArray); ?>
	 </div>
	<h1><?php echo $item->title;?></h1>
	<div class="besedilo">
		<div class=prispevek-slike> 
			<?php foreach ($item->slike as $slika):?>
				<div><a href="<?php echo $slika->url;?>" target="_blank"><img style="width:220px;" src="<?php echo $slika->url2;?>" /></a></div>
			<?php endforeach?>
		</div>
		<?php echo $item->fulltext;?>
	</div>
	
	<div class="prispevek-video"><?php echo $item->video; ?></div>
	<?php if(count($item->galerija)):?> 
		<div class="prispevek-galerija">
			<h3>Galerija</h3>
			<?php foreach ($item->galerija as $slika):?>
				<a href="<?php echo $slika->url;?>" target="blank"><img src="<?php echo $slika->url2;?>" style="width:130px;" /></a>
			<?php endforeach?>
		</div>
	<?php endif;?>
</div>


