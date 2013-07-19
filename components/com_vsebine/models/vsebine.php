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

/**
 * Methods supporting a list of Vsebine records.
 */
class VsebineModelVsebine extends JModelList {

	var $sotredTags=array();
    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
    	
        parent::__construct($config);
        $this->setSortedTags();
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
//        $app = JFactory::getApplication();
//        echo "<pre>";
//        print_r($tags);
//        print_r(JRequest::get());
//        echo "</pre>";

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*'
                )
        );
        
        $query->from('`nize01_zelnik`.`vs_vsebine` AS a');
        
        
        $query->where('a.publish_up < current_timestamp');
        
        
        $query->order('a.publish_up DESC');
        
        $query->join('INNER', '`nize01_zelnik`.vs_portali_vsebine as pv ON pv.id_vsebine = a.id');
        $query->join('INNER', '`nize01_zelnik`.vs_portali as p ON pv.id_portala = p.id');
        $query->where("p.domena = '".$app->getParams('com_vsebine')->get('portal')."'");
        $query->where('pv.status = 2');
        
        if($tags){
        	$tags = explode(',', $tags);
        	$qTags = array();
        	foreach($tags as $tag){$qTags[]=$db->quote($tag); }
        	$tags=implode(',', $qTags);
        	//echo $tags;
        	$query->join('INNER', '`nize01_zelnik`.vs_tags_vsebina as tv ON tv.id_vsebine = a.id');
        	$query->join('INNER', '`nize01_zelnik`.vs_tags as t ON tv.id_tag = t.id');
        	$query->where("t.tag IN ($tags)");
        }else{
        	$query->where('(a.publish_down > current_timestamp or a.publish_down is null)');
        	$query->where('(a.frontpage = 1)');
        }
        
        $query=str_replace("SELECT", "SELECT DISTINCT", $query);
        return $query;
    }
    
    
    protected function populateState($ordering = null, $direction = null){
       $this->setState('list.limit', 100);
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
		$podstr = JRequest::getVar('tags', false);
		$zdruzi = JRequest::getVar('zdruzi', 0);

		$items	= parent::getItems();
		$i=0;
		$blocks=array();
		//echo count($items);
		foreach ($items as $item){
			//značke
			$item->tags = explode(',',$item->tags);
			$item->tags=$this->sortItemTags($item->tags);
			$item->tag = $item->tags[0];
			$item->tagUrl = JRoute::_("index.php?option=com_vsebine&tags=".$item->tag);
			if($item->title_url=="")
				$item->url = JRoute::_("index.php?option=com_vsebine&prispevek=".$item->id);
			else
				 $item->url = JRoute::_("index.php?option=com_vsebine&prispevek=".$item->title_url);
			if(!$podstr && $zdruzi){
			 	if($i<5){
			 		$blocks[0][]=$item;
			 		
			 	}else{
			 		foreach ($this->sotredTags as $st){
			 			if(in_array($st->tag, $item->tags)){
			 				if(!isset($blocks[$st->tag]) || 
			 					count($blocks[$st->tag])<3){
			 					$blocks[$st->tag][]=$item;
			 				}
			 			}
			 		}		 		
			 	}
			}
		 	$i++;
		}
		$return=array();
		if(!$podstr && $zdruzi){
			foreach ($blocks as $key => $block){
				$count = count($block);
				if($count==5 || $count==3)
					$return=array_merge($return, $block);
			}
			//echo "<pre>".print_r($blocks)."</pre>";
			return $return;
		}
		else return $items;
	}
	
	private function setSortedTags(){
		$q="SELECT count(t.id) as cnt, t.tag, t.id FROM `nize01_zelnik`.`vs_tags_vsebina` ts
			inner join `nize01_zelnik`.vs_tags t on t.id=ts.id_tag group by t.id order by cnt desc limit 1, 20"; //preštej značke
		$db = $this->getDbo();
		$db->setQuery($q);
		$this->sotredTags=$db->loadObjectList();
	}
	
	public function sortItemTags($tags){
		$return=array();
		foreach ($this->sotredTags as $ct){
			if(in_array($ct->tag, $tags)){
					$return[]=$ct->tag;
			}	
		}		
		//pripni še ostale
		$diff=array_diff($tags, $return); 
		$return=array_merge($return, $diff);
		return $return;
	}

}
