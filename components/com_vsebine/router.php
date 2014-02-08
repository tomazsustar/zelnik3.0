<?php
/**
 * @version     1.0.0
 * @package     com_vsebine
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Tomaž Šuštar <tomaz@zelnik.net> - http://www.zelnik.net
 */

// No direct access
defined('_JEXEC') or die;

/**
 * @param	array	A named array
 * @return	array
 */
 
function GetTagId($Tag) {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	
	$query->from('nize01_zelnik.vs_tags AS t');
	$query->select('t.id');
	$query->where('t.tag = "'.$Tag.'"');
	
	$db->setQuery($query);
	$Row = $db->loadObject();
	
    return $Row->id;
}

function ParseOutTag($TagId) {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	
	$query->from('nize01_zelnik.vs_tags AS t');
	$query->select('t.tag');
	$query->where('t.id = "'.$TagId.'"');
	
	$db->setQuery($query);
	$Row = $db->loadObject();

    return $Row->tag;
}

function VsebineBuildRoute(&$query)
{
	$segments = array();
	
	if(isset($query['prispevek'])) {
		$segments[] = 'Prispevek';
		$segments[] = $query['prispevek'];
		
		if(isset($query['title'])) {
			$segments[] = $query['title'];
			unset($query['title']);
		}
		unset($query['prispevek']);
	}
	else if(isset($query['tag']) || isset($query['tags'])) {
		$segments[] = 'Tag';
		$segments[] = (isset($query['tag']) ? GetTagId($query['tag']) : GetTagId($query['tags']));
		$segments[] = (isset($query['tag']) ? JFilterOutput::stringURLSafe($query['tag']) : JFilterOutput::stringURLSafe($query['tags']));
		
		if(isset($query['tag'])) unset($query['tag']);
		else unset($query['tags']);
	}

	return $segments;
}

function VsebineParseRoute($segments)
{
	$vars = array();
	
	if($segments[0] == 'Prispevek') {
		$vars['prispevek'] = $segments[1];
		if(isset($segments[2]))
			$vars['title'] = $segments[2];
		$vars['view'] = 'prispevek';
	}
	else if($segments[0] == 'Tag'){
		$var['tag'] = ParseOutTag($segments[1]);
		$vars['tags'] = ParseOutTag($segments[1]);
		$vars['view'] = 'vsebine';
	}
	

	return $vars;
}