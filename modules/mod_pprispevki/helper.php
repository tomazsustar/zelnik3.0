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
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_vsebine');
		$version = $params->get('version');
		
		$db = JFactory::getDBO();
		
		if($version){
				$query = "SELECT c.id, c.name, c.description
						FROM nize01_cinovicomat.vs_content AS c
						INNER JOIN `nize01_cinovicomat`.vs_content_content AS cc ON c.id = cc.content_id AND cc.position='head' AND cc.content_id=c.id
						INNER JOIN `nize01_cinovicomat`.vs_content AS s ON s.id = cc.ref_content_id AND s.type =  'multimedia'
						INNER JOIN `nize01_cinovicomat`.vs_multimedias AS m ON m.id = s.ref_id
						
						WHERE c.id = '".$PrispevekId."'
					LIMIT 1;";
		}
		else{
			$query = "SELECT v.id, v.title, v.slika, v.introtext, s.url
					FROM vs_vsebine AS v
					JOIN vs_slike_vsebine AS sv ON sv.id_vsebine = v.id
					JOIN vs_slike AS s ON sv.id_slike = s.id
					WHERE v.id = '".$PrispevekId."'
					LIMIT 1;";
		}

			
		
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
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_vsebine');
		$version = $params->get('version');
		
		if($version){
			$query = "SELECT c.id, t.name as tag FROM nize01_cinovicomat.vs_content AS c
			JOIN nize01_cinovicomat.vs_tags_content AS vt ON vt.content_id = c.id
			JOIN nize01_cinovicomat.vs_tags AS t ON vt.tag_id = t.id
			INNER JOIN `nize01_cinovicomat`.vs_media_content mc ON mc.content_id = c.id AND mc.status=2
			WHERE c.id = ".$this->id;
		}
		else{
			$query = "SELECT v.id, t.tag FROM vs_vsebine AS v
			JOIN vs_tags_vsebina AS vt ON vt.id_vsebine = v.id
			JOIN vs_tags AS t ON vt.id_tag = t.id
			WHERE v.id = '".$this->id."' AND v.state = 2";
		}
		
		
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
		$version = $params->get('version');
		
		if($version){
			$query = 
			"select common_tags, all_tags - common_tags as not_common_tags, c.id, c.name as title mm.url as slika from nize01_cinovicomat.vs_content as c
			inner join (select A.content_id, count(*) as common_tags from nize01_cinovicomat.vs_tags_content as A
			            inner join nize01_cinovicomat.vs_tags_content as B on A.tag_id = B.tag_id and B.content_id=".$Prispevek->id."
			            where A.content_id <> ".$Prispevek->id."
			            group by A.content_id having common_tags > 1) as pov on pov.content_id=c.id
			inner join (select A.content_id, count(*) as all_tags from nize01_cinovicomat.vs_tags_content as A
			            group by A.content_id) as nepov on nepov.content_id=c.id
			INNER JOIN `nize01_cinovicomat`.vs_articles as a ON c.ref_id = a.id
			INNER JOIN `nize01_cinovicomat`.vs_media_content mc ON mc.content_id = c.id AND mc.status=2
			inner join `nize01_cinovicomat`.vs_media as m ON mc.media_id = m.id
			INNER JOIN `nize01_cinovicomat`.vs_contacts as co ON m.contact_id = co.id 
			AND domain= '".$app->getParams('com_vsebine')->get('portal')."'
			JOIN `nize01_cinovicomat`.vs_content_content cc on cc.content_id=c.id and cc.position='head'
			INNER JOIN `nize01_cinovicomat`.vs_multimedias mm on cc.ref_content_id = mm.id
			order by common_tags DESC, not_common_tags asc, a.publish_up desc limit ".$Omejitev;
			
		}
		else{
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
		}
		$db->setQuery($query);
		$this->Seznam = $db->loadObjectList();
	}
}
?>
