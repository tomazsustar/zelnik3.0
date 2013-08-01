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

<table>
  
		<?php 
			foreach ($this->items as $datum => $dogodki) : 
				list($leto, $mesec, $dan) = explode('-', $datum);
				?>
				<tr>
				<td>
					<?php echo $dan; ?><br>
					<?php echo ZDate::imeMesecaKtatkoSt($mesec); ?>
				</td>
				<td>
					<?php foreach ($dogodki as $dogodek):?>	
						<div>
							<a href="<?php echo $dogodek->url?>"><?php echo $dogodek->naslov?></a><br/>
							<?php echo $dogodek->lokacija; ?><br/>
							<?php echo $dogodek->zacetek->ura(); ?>
							<?php if($dogodek->konec){
								if($datum == $dogodek->konec->datumDB()){
									echo " do ". $dogodek->konec->ura();
								}else{
									echo " do ". $dogodek->konec->datum()." ".$dogodek->konec->ura();
								}
									
							}?>
						</div>	
					<?php endforeach;?>
				</td>
				</tr>
			
			<?php endforeach; ?>
		<?php //endif; ?>
		
	
</table>
