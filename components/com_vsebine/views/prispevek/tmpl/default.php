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
?>

<style type="text/css">
	.ArticleTag { border:thin solid #CCC; padding:3px; }
</style>

<?php 
$item=&$this->item?>

<article class="novica">
	<header class="znacke">
    	<?php 
		/* Display all article tags */
		foreach ($item->tags as $tag) {
			echo "<a href='".$tag->tagUrl."' title='Vsi prispevki označeni z značko: ".mb_strtoupper($tag->tag)."' class='ArticleTag'>".
					mb_strtoupper($tag->tag).
				 "</a>"; 
		}
		/* Article Title */
		echo "<h1>".$item->title."</h1>";
        ?>
    </header>

	<p class="besedilo">
		<?php 
			/* Article content */
			echo $item->fulltext;
		?>
        <h3>Slike</h3>
        
		<?php foreach ($item->slike as $slika):?>
            <a href="<?php echo $slika->url;?>" rel="boxplus"><img src="<?php echo $slika->url2;?>" /></a>
        <?php endforeach?>
        
        <h3>Video</h3>
        <?php echo $item->video; ?>
	</p>
 
   <footer>     
        <h3>Galerija</h3>
        <?php foreach ($item->galerija as $slika):?>
            <a href="<?php echo $slika->url;?>"><img src="<?php echo $slika->url2;?>" /></a>
        <?php endforeach?>
	</footer>
</article>


