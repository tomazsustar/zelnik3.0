<?php
defined( '_JEXEC' ) or die;

class Prispevek {
	public $id;
	public $title;
	public $slika;
	public $url;
	public $introtext;
	public $tags;
	public $comp;
	
	function __construct($PrispevekId) {
		$db = JFactory::getDBO();
		//$Tag = JRequest::getVar('tags', false);

		$query = "SELECT v.id, v.title, v.slika, v.introtext, s.url
					FROM vs_vsebine AS v
					JOIN vs_slike_vsebine AS sv ON sv.id_vsebine = v.id
					JOIN vs_slike AS s ON sv.id_slike = s.id
					WHERE v.id = '".$PrispevekId."'
					LIMIT 1;";		
		
		$db->setQuery($query);
		$Row = $db->loadObject();
		
		$this->id = $PrispevekId;
		$this->title = (isset($Row->title) ? $Row->title : "");
		$this->introtext = (isset($Row->introtext) ? $Row->introtext : "");
		$this->slika = (isset($Row->slika) ? $Row->slika : "");
		$this->url = $this->MakeUrl($this->title);
		$this->tags = $this->GetAllTags();
	}
	
	public function SetComp($Comp) {
		$this->comp = $Comp;
	}
	
	private function MakeUrl() {
		$Url = trim($this->title);
		$Url = str_replace("%C4%8D","c",$Url);
		$Url = str_replace("%C5%A1","s",$Url);
		$Url = str_replace("%C5%BE","z",$Url);
		$Url = str_replace("%20","-",$Url);
		$Url = str_replace(" ","-",$Url);
		
		return strtolower($Url);
	}
	
	private function GetAllTags() {
		$query = "SELECT v.id, t.tag FROM vs_vsebine AS v
		JOIN vs_tags_vsebina AS vt ON vt.id_vsebine = v.id
		JOIN vs_tags AS t ON vt.id_tag = t.id
		WHERE v.id = '".$this->id."' AND v.state = 2";
		
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$results = $db->loadObjectList();
		$Tags = array();
		
		foreach($results as $Tag) {
			array_push($Tags,$Tag->tag);
		}
		
		return $Tags;
	}
	
	public function Check() {
		if($this->introtext != "" && $this->slika != "" && $this->title != "")
			return true;
		else return false;
	}
}

class PovezaniPrispevki {
	public $Seznam;
	
	function __construct($Prispevek) {
		$db = JFactory::getDBO();
		$QueryTags = array();
		
		foreach($Prispevek->tags as $Tag)
			array_push($QueryTags,"t.tag = '".$Tag."'");
			
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_vsebine');

		$query = "SELECT v.id FROM vs_vsebine AS v
		JOIN vs_tags_vsebina AS vt ON vt.id_vsebine = v.id
		JOIN vs_tags AS t ON vt.id_tag = t.id
		JOIN vs_portali_vsebine AS pv ON pv.id_vsebine = v.id
		JOIN vs_portali AS p ON pv.id_portala = p.id
		WHERE  v.id != ".$Prispevek->id." AND v.state = 2 AND (".implode(" OR ",$QueryTags).") AND p.domena = '".$params->get('portal')."'
		ORDER BY v.id DESC;";
		$db->setQuery($query);

		$this->Seznam = $this->PridobiPovezane($Prispevek->tags, $db->loadObjectList());
	}

	private function CompareTags($Tags1,$Tags2) {
		$Count1 = count($Tags1);
		$Count2 = count($Tags2);
		
		if($Count2<$Count1) {
			$Less = $Count1-$Count2;
			for($i=0;$i<$Less;$i++)
				$Tags2[] = -1;
		}
		
		$Tags = array_merge($Tags1,$Tags2);
		$UniqueArray = count(array_unique($Tags));
		
		return $UniqueArray - $Count1;
	}

	private function PridobiPovezane($Tags, $Prispeveki) {
		$Seznam = array();
		
		foreach($Prispeveki as $Item) {
			$Prispevek = new Prispevek($Item->id);
			if($Prispevek->Check()) {
				$Prispevek->SetComp($this->CompareTags($Tags,$Prispevek->tags));
				array_push($Seznam,$Prispevek);
			}
		}
		
		
		usort($Seznam,array("PovezaniPrispevki","RazvrstiSeznam"));
		return array_unique($Seznam,SORT_REGULAR);
	}
	
	private function RazvrstiSeznam($a, $b) {
		if ($a->comp == $b->comp) {
			return 0;
		}
		return ($a->comp < $b->comp) ? -1 : 1;
	}

}
?>
