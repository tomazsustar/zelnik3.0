
<style type="text/css">
	.PovezaniPrispevek { display:table; border-bottom:1px solid #09F; padding:10px; margin-bottom:10px; font-weight:normal; }
	.PovezaniPrispevek span { display:table-cell; vertical-align:top; }
	.PovezaniPrispevek h4  { font-size:1em; color:#333; margin:5px; vertical-align:top; word-wrap:break-word; }
	.PovezaniPrispevek img { vertical-align:bottom; max-width:100px; height:auto; }
</style>

<ul style="list-style:none; padding:0px; ">
<?php
	$i = 0;
	if(count($Povezani) > 0) {
		echo "<h3>Preberite tudi:</h3>";
		foreach($Povezani->Seznam as $Prispevek) {if($i == $stPrispevkov) break; ?>
			<li class='PovezaniPrispevek'>
				<a href='<?php echo JRoute::_("index.php?option=com_vsebine&prispevek=".$Prispevek->id."&title=".$Prispevek->url); ?>'>
					<span>
					<img src='<?php echo $Prispevek->slika; ?>' alt='Uvodna slika <?php echo $Prispevek->title; ?>'>
					</span>
					<span>
					<h4 style="margin-bottom:5px;"><?php echo $Prispevek->title; $i++; ?></h4>
					</span>
				</a>
			</li>
<?php
		}
	}
?>
</ul>

