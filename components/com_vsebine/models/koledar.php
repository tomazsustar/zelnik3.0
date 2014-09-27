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
        	$query->select(
        			$this->getState(
        					'list.select', ' cc.content_id as id, c.name as title, c.name as naslov, cc.content_id as title_url, e.start_date as zacetek, e.end_date as konec, e.id as koledar_id, c.description as lokacija '
        			)
        	);
        	 
        	 
        	if ($format=="ical"){
        		$query->select('a.fulltext, a.slika, a.title, a.id as vsebina_id, a.introtext');
        	}
        	 
        	$query->from('`nize01_cinovicomat`.`vs_events` AS e');
        	 
        	$query->join('INNER', "nize01_cinovicomat.vs_content as c ON e.id = c.ref_id AND c.type = 'event' ");
        	$query->join('INNER', '`nize01_cinovicomat`.vs_media_content mc ON mc.content_id = c.id');
        	$query->join('INNER', '`nize01_cinovicomat`.vs_media as m ON mc.media_id = m.id');
        	$query->join('INNER', "`nize01_cinovicomat`.vs_contacts as co ON m.contact_id = co.id 
        			AND domain = '".$app->getParams('com_vsebine')->get('portal')."'");
        	$query->join('INNER', '`nize01_cinovicomat`.vs_content_content AS cc ON c.id = cc.ref_content_id');
        	//$query->join('inner', "`nize01_cinovicomat`.vs_content_content AS cc ON c.id = cc.content_id");
        	//$query->join('inner', "`nize01_cinovicomat`.vs_content AS c2 ON c2.id = cc.ref_content_id and c2.type='location'" );
        	//$query->join('inner', "`nize01_cinovicomat`.vs_locations AS l ON c2.ref_id = l.id " );
        	 
        	$query->order('e.start_date ASC');
        	if($prispevek){
        		
        		$query->join('INNER', "`nize01_cinovicomat`.vs_content AS c2 ON c2.id = cc.content_id AND c2.id=$prispevek" );
        	}else{
        	
        		$today = date("Y-m-d")." 00:00:00.000";
        		$query->where("e.start_date >= '".$today."'");
        	}
        	 
        	if($tags){
        		$tags = explode(',', $tags);
        		$qTags = array();
        		foreach($tags as $tag){$qTags[]=$db->quote($tag); }
        		$tags=implode(',', $qTags);
        		//echo $tags;
        		$query->join('INNER', '`nize01_cinovicomat`.vs_tags_content as tc ON tc.content_id = c.id');
        		$query->join('INNER', '`nize01_cinovicomat`.vs_tags as t ON tc.tag_id = t.id');
        		$query->where("t.name IN ($tags)");
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
		$items	= parent::getItems();
		$i=0;
		if(count($items)){
			$zac = new ZDate($items[0]->zacetek);
			$dan = $zac->datumDB();
			//echo "DAAAAN:".$dan;
			foreach ($items as $item){
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
