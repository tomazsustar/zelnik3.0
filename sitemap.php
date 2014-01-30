<?php
 function MakeUrl($Title) {
	$Url = urlencode($Title);
	$Url = str_replace("+","-",$Url);

	return strtolower($Url);
 }

 $xml_output = '<?xml version="1.0" encoding="UTF-8"?>
	<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
  	xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
 
 $query = "SELECT v.id, v.title, s.url AS 'image'
			FROM vs_portali_vsebine AS pv
			JOIN vs_vsebine AS v ON pv.id_vsebine = v.id
			JOIN vs_slike_vsebine AS sv ON sv.id_vsebine = v.id
			JOIN vs_slike AS s ON sv.id_slike = s.id
			WHERE v.state = 2 AND pv.id_portala = 1";

 $con=mysqli_connect("localhost","root","","nize01_zelnik");
 $result = mysqli_query($con,$query);

 while($row = mysqli_fetch_array($result)) {		
		$xml_output .= '<url> 
							<loc>'."http://www.zelnik.net/component/vsebine/".$row["id"]."/".MakeUrl($row["title"]).'</loc> 
							<image:image>
							   <image:loc>'.$row["image"].'</image:loc> 
							</image:image>
							<changefreq>never</changefreq>
						</url>';
 }
				
 $xml_output .= '</urlset>';
 
 mysqli_close($con);
 $fp = @fopen('sitemap.xml','w');
 if(!$fp) {
     die('Error cannot create XML file');
 }
 fwrite($fp,$xml_output);
 fclose($fp);
 
  header( 'Location: index.php' ) ;
?>