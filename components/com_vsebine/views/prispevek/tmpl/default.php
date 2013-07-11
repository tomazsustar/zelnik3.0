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

$document->addScript(JPATH_BASE.'/plugins/content/boxplus/js/boxplus.min.js');
$document->addScript(JPATH_BASE.'/plugins/content/boxplus/js/boxplus.lang.min.js?lang=en-GB');
$document->addStyleSheet(JPATH_BASE.'/plugins/content/boxplus/css/boxplus.min.css');
$document->addStyleSheet(JPATH_BASE.'/plugins/content/boxplus/css/boxplus.darksquare.css');
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
		<?php foreach ($item->tags as $tag):?>
			 <a href="<?php echo $tag->tagUrl?>"><?php echo mb_strtoupper($tag->tag); ?></a>, 
		 <?php endforeach ?>
	 </div>
	<h1><?php echo $item->title;?></h1>
	<div class="besedilo">
		<?php echo $item->fulltext;?>
		
	</div>
	
	<h3>Slike</h3>
	<?php foreach ($item->slike as $slika):?>
		<a href="<?php echo $slika->url;?>" rel="boxplus"><img src="<?php echo $slika->url2;?>" /></a>
	<?php endforeach?>
	
	<h3>Video</h3>
	<?php echo $item->video; ?>
	<h3>Galerija</h3>
	<?php foreach ($item->galerija as $slika):?>
		<a href="<?php echo $slika->url;?>"><img src="<?php echo $slika->url2;?>" /></a>
	<?php endforeach?>
	
</div>


