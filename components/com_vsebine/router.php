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
 
function GetTitle($PrispevekId)
{
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	
	$query->from('nize01_zelnik.vs_vsebine AS v');
	$query->select('v.title');
	$query->where('v.id = "'.$PrispevekId.'"');
	
	$db->setQuery($query);
	$Row = $db->loadObject();
	$Title = JFilterOutput::stringURLSafe($Row->title);
	
    return $Title;
}

function VsebineBuildRoute(&$query)
{
	$segments = array();
	
	if (isset($query['prispevek'])) {
		$segments[0] = $query['prispevek'];
		$segments[1] = GetTitle($query['prispevek']);
		unset($query['prispevek']);
	}
	else if(isset($query['tags']))
 	{
		$segments[0] = $query['tags'];
		unset($query['tags']);
	}
	return $segments;
}

function VsebineParseRoute($segments)
{
	$vars = array();
	
	if(is_numeric($segments[0])) {
		$vars['view'] = 'prispevek';
		$vars['prispevek'] = (int)$segments[0];	 
	}
	else {
		$vars['view'] = 'vsebine';
		$vars['tags'] = $segments[0];	 
	}
		
	return $vars;
}

/*
function VsebineBuildRoute(&$query)
{
	$segments = array();
    
	if (isset($query['task'])) {
		$segments[] = implode('/',explode('.',$query['task']));
		unset($query['task']);
	}
	if (isset($query['id'])) {
		$segments[] = $query['id'];
		unset($query['id']);
	}

	return $segments;
}
*/
/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/vsebine/task/id/Itemid
 *
 * index.php?/vsebine/id/Itemid
 */

/*
function VsebineParseRoute($segments)
{
	$vars = array();
    
	// view is always the first element of the array
	$count = count($segments);
    
    if ($count)
	{
		$count--;
		$segment = array_pop($segments) ;
		if (is_numeric($segment)) {
			$vars['id'] = $segment;
		}
        else{
            $count--;
            $vars['task'] = array_pop($segments) . '.' . $segment;
        }
	}

	if ($count)
	{   
        $vars['task'] = implode('.',$segments);
	}
	return $vars;
}
*/
