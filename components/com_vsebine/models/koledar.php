<?php

/**
 * @version     1.0.0
 * @package     com_vsebine
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Tomaž Šuštar <tomaz@zelnik.net> - http://www.zelnik.net
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
include_once JPATH_SITE.'/components/com_vsebine/helpers/ZDate.php';

/**
 * Methods supporting a list of Vsebine records.
 */
class VsebineModelKoledar extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
//    protected function populateState($ordering = null, $direction = null) {
//        
//        // Initialise variables.
//        $app = JFactory::getApplication();
//
//        // List state information
//        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
//        $this->setState('list.limit', $limit);
//
//        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
//        $this->setState('list.start', $limitstart);
//        
//        
//        
//        // List state information.
//        parent::populateState($ordering, $direction);
//    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $app = JFactory::getApplication('site');
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $tags = JRequest::getVar('tags', false);
        $prispevek = JRequest::getInt('prispevek', false);
        $portal = JRequest::getVar('portal', false);
        if(!$portal) $portal = $app->getParams('com_vsebine')->get('portal');
        $format = JRequest::getVar('format', false);
        $version = $app->getParams('com_vsebine')->get('version');
        
//        $app = JFactory::getApplication();
//        echo "<pre>";
//        print_r($tags);
//        print_r(JRequest::get());
//        echo "</pre>";

        // Select the required fields from the table.
        $params = JComponentHelper::getParams('com_vsebine');
        $version = $params->get('version');
        if($version){
        	
        	if($tags){
        		$tags = $db->quote($tags);
        		
        		$query ="
select A.*, A.start_date zacetek, A.end_date konec 
from ((select ec1.*, e1.start_date, e1.end_date from `nize01_cinovicomat`.vs_tags t1
inner join `nize01_cinovicomat`.vs_tags_content tc1 on tc1.tag_id=t1.id and t1.alias=$tags
inner join `nize01_cinovicomat`.vs_content ec1 on tc1.content_id=ec1.id and type='event'
inner join `nize01_cinovicomat`.vs_events e1 on e1.id=ec1.ref_id
where e1.start_date>=current_timestamp
)
        		
union distinct
        		
(select ec2.*, e2.start_date, e2.end_date from `nize01_cinovicomat`.vs_tags t2
inner join `nize01_cinovicomat`.vs_tags_content tc2 on tc2.tag_id=t2.id and t2.alias=$tags
inner join `nize01_cinovicomat`.vs_content ac on tc2.content_id=ac.id and type='article'
inner join `nize01_cinovicomat`.vs_content_content cc2 on cc2.content_id=ac.id
inner join `nize01_cinovicomat`.vs_content ec2 on ec2.id=cc2.ref_content_id and ec2.type='event'
inner join `nize01_cinovicomat`.vs_events e2 on e2.id=ec2.ref_id
where e2.start_date>=current_timestamp and ec2.id
)) as A

INNER join `nize01_cinovicomat`.vs_media_content mc2 ON mc2.content_id = A.id
INNER join `nize01_cinovicomat`.vs_media as m2 ON mc2.media_id = m2.id
INNER join `nize01_cinovicomat`.vs_contacts as co2 ON m2.contact_id = co2.id
AND domain = ".$db->quote($portal)." order by A.start_date ASC
        				";
        				
        	}else{
        	
	        	$query->select(
	        			$this->getState(
	        					'list.select', ' cc.content_id as id, c.name as title, c.name as naslov, cc.content_id as title_url, e.start_date as zacetek, e.end_date as konec, e.id as koledar_id, cc2.name as lokacija '
	        			)
	        	);
	        	 
	        	 
	        	if ($format=="ical"){
	        		$query->select('c.id as vsebina_id, c.name as title, c.description as introtext');
	        	}
	        	 
	        	$query->from('`nize01_cinovicomat`.`vs_events` AS e');
	        	 
	        	$query->join('INNER', "nize01_cinovicomat.vs_content as c ON e.id = c.ref_id AND c.type = 'event' ");
	        	$query->join('INNER', '`nize01_cinovicomat`.vs_media_content mc ON mc.content_id = c.id');
	        	$query->join('INNER', '`nize01_cinovicomat`.vs_media as m ON mc.media_id = m.id');
	        	$query->join('INNER', "`nize01_cinovicomat`.vs_contacts as co ON m.contact_id = co.id
	        			AND domain = '".$db->quote($portal)."'");
	        	$query->join('INNER', '`nize01_cinovicomat`.vs_content_content AS cc ON c.id = cc.ref_content_id');
	        	$query->join('inner', "`nize01_cinovicomat`.vs_content_content AS ccc ON c.id = ccc.content_id");
	        	$query->join('inner', "`nize01_cinovicomat`.vs_content AS cc2 ON cc2.id = ccc.ref_content_id and cc2.type='location'" );
	        	$query->join('inner', "`nize01_cinovicomat`.vs_locations AS l ON cc2.ref_id = l.id " );
	        	 
	        	$query->order('e.start_date ASC');
	        	if($prispevek){
	        		
	        		$query->join('INNER', "`nize01_cinovicomat`.vs_content AS c2 ON c2.id = cc.content_id AND c2.id=$prispevek" );
	        	}else{
	        	
	        		$today = date("Y-m-d")." 00:00:00.000";
	        		$query->where("e.start_date >= '".$today."'");
	        	}
        	}
        	
        }else{
	        $query->select(
	                $this->getState(
	                        'list.select', 'k.lokacija, a.id, a.title, a.title_url, k.naslov, k.zacetek, k.konec, k.id as koledar_id '
	                )
	        );
	    
	        
	        if ($format=="ical"){
	        	$query->select('a.fulltext, a.slika, a.title, a.id as vsebina_id, a.introtext');
	        }
	        
	        $query->from('`nize01_zelnik`.`vs_vsebine` AS a');
	        
	        $query->join('INNER', 'nize01_zelnik.vs_koledar as k ON k.id_vsebine = a.id');
	        
	        $query->join('INNER', '`nize01_zelnik`.vs_portali_vsebine as pv ON pv.id_vsebine = a.id');
	        $query->join('INNER', '`nize01_zelnik`.vs_portali as p ON pv.id_portala = p.id');
	        $query->where("p.domena = ".$db->quote($portal));
	        $query->where('pv.status = 2');
	        
	        $query->order('k.zacetek ASC');
	    	if($prispevek){
	        	$query->where("a.id = $prispevek");
	        }else{
				$today = date("Y-m-d")." 00:00:00.000";
	        	$query->where("k.zacetek >= '".$today."'");
	        }
	        
	        if($tags){
	        	$tags = explode(',', $tags);
	        	$qTags = array();
	        	foreach($tags as $tag){$qTags[]=$db->quote($tag); }
	        	$tags=implode(',', $qTags);
	        	//echo $tags;
	        	$query->join('INNER', 'nize01_zelnik.vs_tags_vsebina as tv ON tv.id_vsebine = a.id');
	        	$query->join('INNER', 'nize01_zelnik.vs_tags as t ON tv.id_tag = t.id');
	        	$query->where("t.tag IN ($tags)");
	        	$query=str_replace("SELECT", "SELECT DISTINCT", $query);
	        }
        }
        //echo $query;
        return $query;
    }
    
    
    protected function populateState($ordering = null, $direction = null){
       $this->setState('list.limit', 25);
        $this->setState('list.offset', 0);
    }

    	/**
	 * Method to get a list of articles.
	 *
	 * Overriden to inject convert the attribs field into a JParameter object.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 * @since	1.6
	 */
	public function getItems()
	{
		$app = JFactory::getApplication('site');
		$version = $app->getParams('com_vsebine')->get('version');
		$dates=array();

		$items = parent::getItems();
		$i=0;
		if(isset($items[0]->zacetek)){
			$zac = new ZDate($items[0]->zacetek);
			$dan = $zac->datumDB();
			//echo "DAAAAN:".$dan;
			foreach ($items as $item){

				// if format = ical
				if(isset($item->vsebina_id)) {
					$db = $this->getDbo();
					$query = $db->getQuery(true);

					$query->select("c.id, a.text");
					$query->from('nize01_cinovicomat.vs_articles AS a');
					$query->join('INNER', "nize01_cinovicomat.vs_content as c ON a.id = c.ref_id AND c.type = 'article' ");
					$query->join('INNER', 'nize01_cinovicomat.vs_content_content as cc ON cc.content_id = c.id and cc.ref_content_id = '.$item->vsebina_id);
					$db->setQuery($query);
					$data = $db->loadObject();

					$item->fulltext = (isset($data->text) ? $data->text : '');
					$item->id_prispevka = (isset($data->id) ? $data->id : 0);
				}

				if(isset($item->id_prispevka) && $item->id_prispevka != 0) {
					$db = $this->getDbo();
					$query = $db->getQuery(true);

					$query->select("mu.id, CONCAT('http://dev.zelnik.net/', CONCAT(SUBSTRING_INDEX(mu.url, '/', 3),CONCAT('/300x200-',SUBSTRING_INDEX(mu.url, '/', -1)))) as url");
					$query->from('nize01_cinovicomat.vs_multimedias AS mu');
					$query->join('INNER', "nize01_cinovicomat.vs_content as c ON mu.id = c.ref_id AND c.type = 'image' ");
					$query->join('INNER', 'nize01_cinovicomat.vs_content_content as cc ON cc.ref_content_id = c.id and cc.content_id = '.$item->id_prispevka.' and cc.position = "head"');
					$db->setQuery($query);
					$data = $db->loadObject();

					$item->slika = (isset($data->url) ? $data->url : (isset($data->id) ? $data->id : ''));
				}

				//datumi
				$item->zacetek=new ZDate($item->zacetek);
				
				if($item->konec)
					$item->konec=new ZDate($item->konec);
				
				if($item->title_url=="" || $version)
					$item->url = JRoute::_("index.php?option=com_vsebine&prispevek=".$item->id."&title=".JFilterOutput::stringURLSafe($item->title));
				else
					 $item->url = JRoute::_("index.php?option=com_vsebine&prispevek=".$item->title_url);
				
					 
				if($dan != $item->zacetek->datumDB()) {
					$dan = $item->zacetek->datumDB(); 
					$i=0;
				}
				$dates[$dan][$i]=$item;
				
				$i++;
				
			}
		}
		//echo "<pre>";print_r($dates);echo "</pre>";
		return $dates;
	}

}
