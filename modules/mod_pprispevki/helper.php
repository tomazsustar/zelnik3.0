<?php
defined( '_JEXEC' ) or die;

function GetAllTags($PrispevekId) {
	$query = "SELECT v.id, t.tag FROM vs_vsebine AS v
	JOIN vs_tags_vsebina AS vt ON vt.id_vsebine = v.id
	JOIN vs_tags AS t ON vt.id_tag = t.id
	WHERE v.id = '".$PrispevekId."' AND v.state = 2";
	
	$db = JFactory::getDBO();
	$db->setQuery($query);
	$results = $db->loadObjectList();
	$Tags = array();
	
	foreach($results as $Tag) {
		array_push($Tags,$Tag->tag);
	}
	
	return $Tags;
}

function CompareTags($Tags1,$Tags2) {
	$Count1 = count($Tags1);
	$Count2 = count($Tags2);
	$Tags = array_merge($Tags1,$Tags2);
	$Count = count($Tags)-$Count1;
	
	return count(array_unique($Tags));
}

function GetPovezani($PrispevekId) {
	$Tags = GetAllTags($PrispevekId);
	$QueryTags = array();
	
	foreach($Tags as $Tag) {
		array_push($QueryTags,"t.tag = '".$Tag."'");
	}
	
	$query = "SELECT v.id, v.title, t.tag FROM vs_vsebine AS v
	JOIN vs_tags_vsebina AS vt ON vt.id_vsebine = v.id
	JOIN vs_tags AS t ON vt.id_tag = t.id
	WHERE  v.id != ".$PrispevekId." AND v.state = 2 AND (".implode(" OR ",$QueryTags).")
	ORDER BY v.id DESC;";
	
	$db = JFactory::getDBO();
	$db->setQuery($query);
	$results = $db->loadObjectList();
	
	return $results;
}

function GetPovezaniCompatibility($PrispevekId, $Prispeveki) {
	$Tags = GetAllTags($PrispevekId);
	$Counts = array();
	
	foreach($Prispeveki as $Prispevek) {
		$PrispevekTags = GetAllTags($Prispevek->id);
		$Kompatibilnost = CompareTags($Tags,$PrispevekTags);
		array_push($Counts,array("PrispevekId" => $Prispevek->id, "Kompatibilnost" => $Kompatibilnost, "PrispevekTitle" => $Prispevek->title));
	}
	
	return $Counts;
}

function SortConnected($array, $on, $order=SORT_ASC)
{
 $new_array = array();
 $sortable_array = array();

 if (count($array) > 0) {
	 foreach ($array as $k => $v) {
		 if (is_array($v)) {
			 foreach ($v as $k2 => $v2) {
				 if ($k2 == $on) {
					 $sortable_array[$k] = $v2;
				 }
			 }
		 } else {
			 $sortable_array[$k] = $v;
		 }
	 }

	 switch ($order) {
		 case SORT_ASC:
			 asort($sortable_array);
		 break;
		 case SORT_DESC:
			 arsort($sortable_array);
		 break;
	 }

	 foreach ($sortable_array as $k => $v) {
		 $new_array[$k] = $array[$k];
	 }
 }
 return $new_array;
}

function MakeUrl($Title) {
	$Url = trim($Title);
	$Url = str_replace("%C4%8D","c",$Url);
	$Url = str_replace("%C5%A1","s",$Url);
	$Url = str_replace("%C5%BE","z",$Url);
	$Url = str_replace("%20","-",$Url);
	$Url = str_replace(" ","-",$Url);
	
	return strtolower($Url);
}

function AssemlePrispevki($Prispevki) {
	$NewArray = array();
	$i = 0;
	
	foreach($Prispevki as $Prispevek) {
		if($i == 5) break;
		
		$query = "SELECT v.id, v.title, v.introtext, s.url
					FROM vs_vsebine AS v
					JOIN vs_slike_vsebine AS sv ON sv.id_vsebine = v.id
					JOIN vs_slike AS s ON sv.id_slike = s.id
					WHERE v.id = '".$Prispevek["PrispevekId"]."'
					LIMIT 1;";
		
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$Row = $db->loadObject();
		$Url = makeUrl($Row->title);
		
		if(isset($Row->id)) {
			$Vsebina = array(
				"PrispevekId" => $Row->id,
				"PrispevekTitle" => $Row->title,
				"PrispevekShort" => $Row->introtext,
				"PrispevekSlika" => $Row->url,
				"Url" => $Url
			);
			
			array_push($NewArray,$Vsebina);
			$i++;
		}
	}
	
	return $NewArray;
}

// THIS IS WHERE THE MAGIC HAPPENS :)

$db = JFactory::getDBO();
$Tag = JRequest::getVar('tags', false);

if(JRequest::getVar('prispevek')) {
	$PrispevekId = JRequest::getVar('prispevek');

	$Povezani = GetPovezani($PrispevekId);
	$PovezaniCompatibility = GetPovezaniCompatibility($PrispevekId,$Povezani);
	$Prispevki = SortConnected($PovezaniCompatibility, 'Kompatibilnost', SORT_ASC);
	$Prispevki = array_unique($Prispevki,SORT_REGULAR);
	
	$Prispevki = AssemlePrispevki($Prispevki);
}
else {
	$Prispevki = array();
}
?>
