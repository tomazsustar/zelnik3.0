
<style type="text/css">
	.PovezaniPrispevek { border-bottom:2px solid #09F; border-radius:10px; padding:10px 5px 10px 5px; margin-bottom:10px; font-weight:normal; }
	.PovezaniPrispevek h4 { margin:0px; padding:0px; font-size:1.1em;}
	.PovezaniPrispevek p { padding:0px; margin:0px; }
	.PovezaniPrispevek img { vertical-align:top; max-width:120px; height:auto; }
</style>

<ul style="list-style:none; padding:0px; ">
<?php
        $i = 0;
		foreach($Prispevki as $Prispevek) {
			if($i == $stPrispevkov) break; ?>
            <li class='PovezaniPrispevek'>
                <a href='<?php echo JURI::base()."index.php/component/vsebine/".$Prispevek["PrispevekId"]."/".$Prispevek["Url"]; ?>'>
                	<h4 style="margin-bottom:5px;"><?php echo $Prispevek["PrispevekTitle"]; ?></h4>
                	<img src='<?php echo $Prispevek["PrispevekSlika"]; ?>' alt='<?php echo $Prispevek["PrispevekTitle"]; ?>'>
                </a>
                <p style="margin-top:5px;"><?php echo $Prispevek["PrispevekShort"]; ?></p>
            </li>
<?php
			$i++;
        }
?>
</ul>

