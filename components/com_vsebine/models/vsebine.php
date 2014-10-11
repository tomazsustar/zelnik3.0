<?php

/**
 * @version     1.0.0
 * @package     com_vsebine
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Tomaž Šuštar <tomaz@zelnik.net> - http://www.zel
        	
        nik.net
 */
defined('_JEXEC') or die;


jimport('joomla.application.component.modellist');
include_once JPATH_SITE.'/components/com_vsebine/helpers/ZDate.php';
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
        $version = $app->getParams('com_vsebine')->get('version');
//        $app = JFactory::getApplication();
//        echo "<pre>";
//        print_r($tags);
//        print_r(JRequest::get());
//        echo "</pre>";

        // Select the required fields from the table.
        if($version){
        	$query->select(
        			$this->getState(
        					'list.select', "c.description as introtext, 
        					mm.url as slika, 
        					c.name as title,
        					a.publish_up,
        					c.id as id
        					"
        			)
        	);
        	 
        	$query->from('`nize01_cinovicomat`.`vs_content` AS c');
        	 
        	$query->where("c.type = 'article' ");
        	
        	$query->join('INNER', '`nize01_cinovicomat`.vs_media_content mc ON mc.content_id = c.id AND mc.status=2');
        	$query->join('INNER', '`nize01_cinovicomat`.vs_media as m ON mc.media_id = m.id ');
        	$query->join('INNER', "`nize01_cinovicomat`.vs_contacts as co ON m.contact_id = co.id 
        			AND domain = '".$app->getParams('com_vsebine')->get('portal')."'");
        	
        	$query->join('INNER', '`nize01_cinovicomat`.vs_articles as a ON c.ref_id = a.id');			
        	$query->order('a.publish_up DESC');
        	
        	$query->join('INNER', "`nize01_cinovicomat`.vs_content_content AS cc ON c.id = cc.content_id AND cc.correlation='header-image'");
        	$query->join('INNER', "`nize01_cinovicomat`.vs_content AS c2 ON c2.id = cc.ref_content_id " );
        	$query->join('INNER', "`nize01_cinovicomat`.vs_multimedias AS mm ON c2.ref_id = mm.id " );
        	
        	if($tags){
        		$tags = explode(',', $tags);
        		$qTags = array();
        		foreach($tags as $tag){$qTags[]=$db->quote($tag); }
        		$tags=implode(',', $qTags);
        		//echo $tags;
        		$query->join('INNER', '`nize01_cinovicomat`.vs_tags_content as tc ON tc.content_id = c.id');
        		$query->join('INNER', '`nize01_cinovicomat`.vs_tags as t ON tc.tag_id = t.id');
        		$query->where("t.name IN ($tags)");
        	}else{
        		$query->where("(a.publish_down > current_timestamp or a.publish_down is null or a.publish_down='0000-00-00') and a.publish_up <= current_timestamp");
        		$query->join('LEFT', '`nize01_cinovicomat`.vs_tags_content as tc ON tc.content_id = c.id');
        		$query->where('(a.frontpage = 1 OR tc.tag_id=815)');
        	}
        	 
        	$query=str_replace("SELECT", "SELECT DISTINCT", $query);
        }else{
	        $query->select(
	                $this->getState(
	                        'list.select', 'a.*'
	                )
	        );
	        
	        $query->from('`nize01_zelnik`.`vs_vsebine` AS a');
	        
	        
	        $query->where('a.publish_up <= current_timestamp');
	        
	        
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
        }
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
		//echo " zdruzi: ".$zdruzi;
		$items	= parent::getItems();
		$i=0;
		$blocks=array();
		$app = JFactory::getApplication('site');
		$menu   = $app->getMenu();
		$activeId = $menu->getActive()->id;
		$params = JComponentHelper::getParams('com_vsebine');
		$version = $params->get('version');
		$server = @array_shift(explode(".",$_SERVER['HTTP_HOST']));
		//echo $server;
		//echo count($items);
		if($version){
			foreach ($items as $item){
				//značke
				$item->publish_up=new ZDate($item->publish_up);
				$item->tags = $this->getTagsForContentID($item->id);
				$item->tags=$this->sortItemTags($item->tags);
				$tagsLower=array_map('mb_strtolower', $item->tags);
				$item->tag = $item->tags[0];
				$item->tagUrl = JRoute::_("index.php?option=com_vsebine&tag=".$item->tag."&Itemid=".$activeId);
				//if($item->title_url=="")
				$item->url = JRoute::_("index.php?option=com_vsebine&prispevek=".$item->id.
						"&title=".JFilterOutput::stringURLSafe($item->title).
						"&Itemid=".$activeId);
				
				$arr=explode('/', $item->slika);
				$arr[count($arr)-1]="300x200-".$arr[count($arr)-1];
				if($server=="dev" || $server=="localhost")	$item->slika="http://dev.novicomat.si/".implode("/", $arr);
				else $item->slika="http://novicomat.si/".implode("/", $arr);
				
				//echo '"'.$item->slika.'"<br>';
				
				//print_r($arr);
				//else
				//	 $item->url = JRoute::_("component/vsebine/prispevek/".JFilterOutput::stringURLSafe($item->title));
				if(!$podstr && $zdruzi){
					if($i<5){
						$blocks[0][]=$item;
			
					}else{
						//if ($item->id == 2576){echo "AAA".in_array("Kultura", $item->tags);}//echo "AAAAAAAA";}
						foreach ($this->sotredTags as $st){
			
							if(in_array(mb_strtolower($st->tag), $tagsLower)){
								//if ($item->id == 2591){echo $st->tag;}//echo "AAAAAAAA";}
								if(!isset($blocks[$st->tag]) ||
										count($blocks[$st->tag])<3){
									$blocks[$st->tag][]=$item;
									break;
								}
							}
						}
					}
				}
				$i++;
			}
		}else {
			foreach ($items as $item){
				//značke
				$item->publish_up=new ZDate($item->publish_up);
				$item->tags = array_map('trim',(explode(',',$item->tags)));
				$item->tags=$this->sortItemTags($item->tags);
				$tagsLower=array_map('mb_strtolower', $item->tags);
				$item->tag = $item->tags[0];
				$item->tagUrl = JRoute::_("index.php?option=com_vsebine&tag=".$item->tag."&Itemid=".$activeId);
				//if($item->title_url=="")
				$item->url = JRoute::_("index.php?option=com_vsebine&prispevek=".$item->id.
						"&title=".JFilterOutput::stringURLSafe($item->title).
						"&Itemid=".$activeId);
				//else
				//	 $item->url = JRoute::_("component/vsebine/prispevek/".JFilterOutput::stringURLSafe($item->title));
				if(!$podstr && $zdruzi){
					if($i<5){
						$blocks[0][]=$item;
			
					}else{
						//if ($item->id == 2576){echo "AAA".in_array("Kultura", $item->tags);}//echo "AAAAAAAA";}
						foreach ($this->sotredTags as $st){
			
							if(in_array(mb_strtolower($st->tag), $tagsLower)){
								//if ($item->id == 2591){echo $st->tag;}//echo "AAAAAAAA";}
								if(!isset($blocks[$st->tag]) ||
										count($blocks[$st->tag])<3){
									$blocks[$st->tag][]=$item;
									break;
								}
							}
						}
					}
				}
				$i++;
			}
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
		$params = JComponentHelper::getParams('com_vsebine');
		$version = $params->get('version');
		
		if($version){
			$q="SELECT count(t.id) as cnt, t.alias as tag, t.id FROM `nize01_cinovicomat`.`vs_tags_content` tc
				inner join `nize01_cinovicomat`.vs_tags t on t.id=tc.tag_id group by t.id order by cnt desc limit 1, 20"; //preštej značke
			$db = $this->getDbo();
			$db->setQuery($q);
			$this->sotredTags=$db->loadObjectList();
		}else{
			$q="SELECT count(t.id) as cnt, t.tag, t.id FROM `nize01_zelnik`.`vs_tags_vsebina` ts
				inner join `nize01_zelnik`.vs_tags t on t.id=ts.id_tag group by t.id order by cnt desc limit 1, 20"; //preštej značke
			$db = $this->getDbo();
			$db->setQuery($q);
			$this->sotredTags=$db->loadObjectList();
		}
	}
	
	private function getTagsForContentID($content_id){
		$params = JComponentHelper::getParams('com_vsebine');
		$version = $params->get('version');
	
		if($version){
			$q="SELECT t.name from nize01_cinovicomat.vs_tags t
				inner join `nize01_cinovicomat`.vs_tags_content tc on t.id=tc.tag_id and content_id=$content_id" ; 
			$db = $this->getDbo();
			$db->setQuery($q);
			return $db->loadColumn();
		}else{
			return array();
		}
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
