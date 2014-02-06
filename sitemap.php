<?php
 /* Clear URL from UTF characters (example: 'č', 'š', 'ž') */
 function HandleUTFChars($String) {
	$Url = str_replace('č','c',$String);
	$Url = str_replace('š','s',$Url);
	$Url = str_replace('ž','z',$Url);
	$Url = str_replace("%9a",'s',$Url);
	$Url = str_replace("%8a",'s',$Url);
	$Url = str_replace("%3f",'c',$Url);
	$Url = str_replace("%9e",'z',$Url);
	
	return $Url;
 }
 
 /* Clear URL from special characters (example: '.', '(', ')', ',', etc.) */
 function HandleSpecialChars($String) {	
 	$Url = str_replace("%2f",'-',$String); 
	
	for($i = 21;$i<29;$i++)
		$Url = str_replace("%".$i,'',$Url);

 	$Url = str_replace("%2c",'',$Url);
	$Url = str_replace('.','',$Url);
	$Url = str_replace("%2f",'-',$Url);
	$Url = str_replace("%3a",'-',$Url);
	$Url = str_replace('+','-',$Url);
	$Url = str_replace("--",'-',$Url);
	$Url = str_replace("---",'-',$Url);
	
	return $Url;
 }

 /* We will parse URL's from Prispevek Title (example: Umri pokončno: Dober dan za smrt -> umri-pokoncno-dober-dan-za-smrt) */
 function MakeUrl($Title) {
	$Url = urlencode($Title);
	$Url = strtolower($Url);
	
	$Url = HandleUTFChars($Url);
	$Url = HandleSpecialChars($Url);

	return $Url;
 }
 
 /* Create XML header - unified for all major browser (Google, Bing etc.) */
 function AssembleHeader() {
 	return '<?xml version="1.0" encoding="UTF-8"?>
				<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
				xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
 }
 
 /* Create XML body - all of the URL's we wish to include in sitemap.xml */
 function AssembleBody($Items) {
	$Sitemap = '';
 	while($Item = mysqli_fetch_array($Items)) {		
		$Sitemap .= '<url> 
						<loc>'."http://www.zelnik.net/component/vsebine/".$Item["id"]."/".MakeUrl($Item["title"]).'</loc> 
						<image:image>
						   <image:loc>'.$Item["image"].'</image:loc> 
						</image:image>
						<changefreq>never</changefreq>
					 </url>';
 	}
	
	return $Sitemap;
 }
 
 /* Get all items (Prispevke) from database */
 function GetItems($Domain = "zelnik.net") {
 	$Query = "SELECT v.id, v.title, s.url AS 'image'
			FROM vs_portali_vsebine AS pv
			JOIN vs_portali AS p ON pv.id_portala = p.id
			JOIN vs_vsebine AS v ON pv.id_vsebine = v.id
			JOIN vs_slike_vsebine AS sv ON sv.id_vsebine = v.id
			JOIN vs_slike AS s ON sv.id_slike = s.id
			WHERE v.state = 2 AND p.domena = '".$Domain."'
			GROUP BY v.id
			ORDER BY v.id DESC;";

	 $Connection = mysqli_connect("localhost","root","","nize01_zelnik");
	 $Items = mysqli_query($Connection,$Query);
	 mysqli_close($Connection);
	 
	 return $Items;
 }
 
 /* This is where the magic happens - we create sitemap from currect items (Prispevki) in database. */
 $Sitemap = AssembleHeader();
 $Sitemap .= AssembleBody(GetItems());
 
 /* This is the end of the sitemap.xml. */			
 $Sitemap .= '</urlset>';
 
 /* Open/create and then save 'sitemap.xml' to root folder of our project. */
 $File = @fopen('sitemap.xml','w');
 if(!$File) die('Error cannot create XML file');
 
 fwrite($File,$Sitemap);
 fclose($File);
 
 header( 'Location: index.php' ) ;
?>