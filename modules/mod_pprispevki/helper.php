<?php
defined( '_JEXEC' ) or die;

class Prispevek {
	public $id;
	public $title;
	public $slika;
	public $url;
	public $introtext;
	public $tags;
	
	function __construct($PrispevekId) {
		$db = JFactory::getDBO();

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
	
	private function MakeUrl() {
		return JFilterOutput::stringUrlSafe($this->title);
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
}

class PovezaniPrispevki {
	public $Seznam;
	
	function __construct($Prispevek, $Omejitev) {
		$db = JFactory::getDBO();	
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_vsebine');

		$query = "select common_tags, all_tags - common_tags as not_common_tags, v.id, v.title, v.slika from vs_vsebine as v
					inner join (select A.id_vsebine, count(*) as common_tags from vs_tags_vsebina as A
						inner join vs_tags_vsebina as B on A.id_tag = B.id_tag and B.id_vsebine=".$Prispevek->id."
						where A.id_vsebine <> ".$Prispevek->id."
						group by A.id_vsebine having common_tags > 1) as pov on pov.id_vsebine=v.id
					inner join (select A.id_vsebine, count(*) as all_tags from vs_tags_vsebina as A
						group by A.id_vsebine) as nepov on nepov.id_vsebine=v.id
					inner JOIN vs_portali_vsebine AS pv ON pv.id_vsebine = v.id AND pv.status = 2
					inner JOIN vs_portali AS p ON pv.id_portala = p.id AND p.domena = '".$params->get('portal')."'	  
					order by common_tags DESC, not_common_tags asc, publish_up desc limit ".$Omejitev;
		
		$db->setQuery($query);
		$this->Seznam = $db->loadObjectList();
	}
}
?>
