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
        
        $query->from('`vs_vsebine` AS a');
        
        $query->where('a.state = 2');
        
        
        $query->order('publish_up DESC');
        
        if($tags){
        	$tags = explode(',', $tags);
        	$qTags = array();
        	foreach($tags as $tag){$qTags[]=$db->quote($tag); }
        	$tags=implode(',', $qTags);
        	//echo $tags;
        	$query->join('INNER', 'vs_tags_vsebina as tv ON tv.id_vsebine = a.id');
        	$query->join('INNER', 'vs_tags as t ON tv.id_tag = t.id');
        	$query->where("t.tag IN ($tags)");
        	
        }
        
        $query=str_replace("SELECT", "SELECT DISTINCT", $query);
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
		$items	= parent::getItems();
		foreach ($items as $item){
			//značke
			$item->tags = explode(',',$item->tags);
			$item->tag = $item->tags[0];
			$item->tagUrl = JRoute::_("index.php?option=com_vsebine&tags=".$item->tag);
			if($item->title_url=="")
				$item->url = JRoute::_("index.php?option=com_vsebine&prispevek=".$item->id);
			else
				 $item->url = JRoute::_("index.php?option=com_vsebine&prispevek=".$item->title_url);
		
			
		}
		
		return $items;
	}

}
