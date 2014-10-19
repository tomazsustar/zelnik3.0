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
 
function GetTag($Tag) {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	$params = JComponentHelper::getParams('com_vsebine');
	$version = $params->get('version');	
	if($version){
		$query->from('nize01_cinovicomat.vs_tags AS t');
		$query->select('t.alias, t.name as tag');
		$query->where("t.alias = '$Tag' OR t.name='$Tag'");
	}else{
		$query->from('nize01_zelnik.vs_tags AS t');
		$query->select('t.*');
		$query->where('t.tag = "'.$Tag.'"');
	}
	$db->setQuery($query);
	$Row = $db->loadObject();
	
    return $Row;
}

function GetPrispevek($Alias) {
	$params = JComponentHelper::getParams('com_vsebine');
	$version = $params->get('version');
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	
	if($version){
		$query->from('nize01_cinovicomat.vs_content AS c');
		$query->select('c.id');
		$query->where('c.name = "'.str_replace(':','-',$Alias).'"');
	}
	else{
		$query->from('nize01_zelnik.vs_vsebine AS v');
		$query->select('v.id');
		$query->where('v.title_url = "'.str_replace(':','-',$Alias).'"');
	}
	
	
	
	$db->setQuery($query);
	$Row = $db->loadObject();
	
    return $Row->id;
}

function ParseOutTag($TagAlias) {
	$params = JComponentHelper::getParams('com_vsebine');
	$version = $params->get('version');
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	if($version){
		$query->from('nize01_cinovicomat.vs_tags AS t');
		$query->select('t.name as tag');
		$query->where("t.alias LIKE '$TagAlias'");
	}else{
		$query->from('nize01_zelnik.vs_tags AS t');
		$query->select('t.tag');
		$query->where('t.alias = "'.$TagAlias.'"');
	}
	$db->setQuery($query);
	$Row = $db->loadObject();
	
    return $Row->tag;
}

function ParseOutPrispevek($PrispevekId) {
	$params = JComponentHelper::getParams('com_vsebine');
	$version = $params->get('version');
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	
	if($version){
		return "";
	}else{
		$query->from('nize01_zelnik.vs_vsebine AS v');
		$query->select('v.title_url');
		$query->where('v.id = '.$PrispevekId.'');
		$db->setQuery($query);
		$Row = $db->loadObject();
	}
	
	

    return $Row->title_url;
}

function VsebineBuildRoute(&$query) {
	$segments = array();
	$app  = JApplication::getInstance('site');
	$menu = $app->getMenu();
	
	if(isset($query['prispevek'])) {
		$segments[] = 'Prispevek';
		$PrispevekUrl = ParseOutPrispevek($query['prispevek']);
		
		if($PrispevekUrl == "") {
			$segments[] = $query['prispevek'];
			$segments[] = $query['title'];
		}
		else
			$segments[]= $PrispevekUrl;
		
		unset($query['title']);
		unset($query['prispevek']);
	}
	else if(isset($query['tag']) || isset($query['tags'])) {
		$Tag = (isset($query['tag']) ? GetTag($query['tag']) : GetTag($query['tags']));
		
		$segments[] = $Tag->alias;
		
		if(isset($query['tag'])) unset($query['tag']);
		else unset($query['tags']);
	}

	return $segments;
}

function VsebineParseRoute($segments)
{
	$vars = array();
	
	if($segments[0] == 'Prispevek') {
		if(is_numeric($segments[1])) {
			$vars['prispevek'] = $segments[1];
			$vars['title'] = $segments[2];
		}
		else {
			$vars['prispevek'] = GetPrispevek($segments[1]);
			$vars['title'] = $segments[1];
		}
		
		$vars['view'] = 'prispevek';
	}
	else {
		$vars['tag'] = ParseOutTag($segments[0]);
		$vars['tags'] = ParseOutTag($segments[0]);
		
		$vars['view'] = 'vsebine';
	}
	

	return $vars;
}