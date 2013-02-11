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
$item=&$this->items[0]?>
<div class="naslovna-novica">
	<div class="znacka"><a href="<?php echo $item->tagUrl?>"><?php echo mb_strtoupper($item->tag); ?></a></div>
	<img alt="" src="<?php echo $item->slika;?> ">
	<h1><a href="<?php echo $item->url?>"><?php echo $item->title;?></a></h1>
	<div class="uvodni-text"><?php echo $item->introtext;?></div>
</div>

<?php if($count>1){ $item=&$this->items[1];?>
<div class="naslovna-novica">
	<div class="znacka"><a href="<?php echo $item->tagUrl?>"><?php echo mb_strtoupper($item->tag); ?></a></div>
	<img alt="" src="<?php echo $item->slika;?> ">
	<h1><a href="<?php echo $item->url?>"><?php echo $item->title;?></a></h1>
	<div class="uvodni-text"><?php echo $item->introtext;?></div>
</div>
<?php } 

if($count>2){ $item=&$this->items[2];?>
<div class="naslovna-novica">
	<div class="znacka"><a href="<?php echo $item->tagUrl?>"><?php echo mb_strtoupper($item->tag); ?></a></div>
	<img alt="" src="<?php echo $item->slika;?> ">
	<h1><a href="<?php echo $item->url?>"><?php echo $item->title;?></a></h1>
	<div class="uvodni-text"><?php echo $item->introtext;?></div>
</div>

<?php  } 

if($count>3){ $item=&$this->items[3];?>
<div class="naslovna-novica">
	<div class="znacka"><a href="<?php echo $item->tagUrl?>"><?php echo mb_strtoupper($item->tag); ?></a></div>
	<img alt="" src="<?php echo $item->slika;?> ">
	<h1><a href="<?php echo $item->url?>"><?php echo $item->title;?></a></h1>
	<div class="uvodni-text"><?php echo $item->introtext;?></div>
</div>

<?php }

foreach ($this->items as $key => $item) : ?>

					<?php //$this->item = &$item;
					//echo $this->loadTemplate('item');?>
					
				<div>
					<?php echo $item->title."-".$item->publish_up; ?>
				</div>
			

		<?php endforeach; ?>
<?php //endif; ?>

