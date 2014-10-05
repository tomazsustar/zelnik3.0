<?php

/**
 * @version     1.0.0
 * @package     com_vsebine
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Tomaž Šuštar <tomaz@zelnik.net> - http://www.zelnik.net
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');
include_once JPATH_SITE.'/components/com_vsebine/helpers/ZDate.php';

/**
 * Methods supporting a list of Vsebine records.
 */
class VsebineModelPrispevek extends JModelItem {

protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('article.id', $pk);

		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		// TODO: Tune these values based on other permissions.
		$user		= JFactory::getUser();
//		if ((!$user->authorise('core.edit.state', 'com_content')) &&  (!$user->authorise('core.edit', 'com_content'))){
//			$this->setState('filter.published', 1);
//			$this->setState('filter.archived', 2);
//		}
	}

	/**
	 * Method to get article data.
	 *
	 * @param	integer	The id of the article.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function &getItem($prispevek = false)
	{
		$prispevek = JRequest::getVar('prispevek', false);
		$app = JFactory::getApplication('site');
		$version = $app->getParams('com_vsebine')->get('version');
//		if ($this->_item === null) {
//			$this->_item = array();
//		}
//
		
		
		if ($prispevek) {
			
			try {
				
				$db = $this->getDbo();
				$query = $db->getQuery(true);
				
				if($version){
					
					$query->from('nize01_cinovicomat.vs_content AS c');
					$query->select('c.id as id, c.name as title, c.updated as edited, c.description as introtext, a.text as `fulltext`, a.publish_up, a.publish_down, a.author_name as author_alias');
					$query->join('INNER', '`nize01_cinovicomat`.vs_media_content mc ON mc.content_id = c.id AND mc.status=2' );
        			$query->join('INNER', '`nize01_cinovicomat`.vs_media as m ON mc.media_id = m.id');
        			$query->join('INNER', "`nize01_cinovicomat`.vs_contacts as co ON m.contact_id = co.id 
        			AND domain = '".$app->getParams('com_vsebine')->get('portal')."'");
					$query->join('INNER', '`nize01_cinovicomat`.vs_articles as a ON c.ref_id = a.id');
					$query->where("c.id = $prispevek");
					}
				else{
					$query->from('nize01_zelnik.vs_vsebine AS a');
					$query->select('a.*');
					$query->join('INNER', '`nize01_zelnik`.vs_portali_vsebine as pv ON pv.id_vsebine = a.id');
					$query->join('INNER', '`nize01_zelnik`.vs_portali as p ON pv.id_portala = p.id');
					$query->where("p.domena = '".$app->getParams('com_vsebine')->get('portal')."'");
					$query->where('pv.status = 2');
					if (is_numeric($prispevek)) {
						$query->where("a.id = $prispevek");
					}else{
						$query->where("a.title_url = '$prispevek'");
					}
				}
				
				
				$db->setQuery($query);
				$data = $db->loadObject();
				if (empty($data)) {
					
					return JError::raiseError(404, JText::_('COM_CONTENT_ERROR_ARTICLE_NOT_FOUND'));
				}
				

				//DATUMI
				$data->publish_up=new ZDate($data->publish_up);
				$data->publish_down=new ZDate($data->publish_down);
				//$data->checked_out_time=new ZDate($data->checked_out_time);
				$data->edited=new ZDate($data->edited);
				
				// ZNAČKE
				if($version){
					$query = $db->getQuery(true);
					$query->from('nize01_cinovicomat.vs_tags AS t');
					$query->select('t.name as tag, t.alias, tc.*');
					$query->join('INNER', "nize01_cinovicomat.vs_tags_content as tc ON tc.tag_id = t.id AND tc.content_id=$data->id");
					$db->setQuery($query);
					$tags = $db->loadObjectList();
					$server = @array_shift(explode(".",$_SERVER['HTTP_HOST']));
					//echo $server;
					//print_r($tags);
					$arr=array();
					foreach ($tags as &$tag){
						$tag->tagUrl = JRoute::_("index.php?option=com_vsebine&tags=".$tag->alias);
						$arr[]=$tag->tag;
					}
					$data->str_tags=implode(', ', $arr);
					$data->tags=$tags;
						
					// SLIKE
					
					$query = $db->getQuery(true);
					$query->from('nize01_cinovicomat.vs_multimedias AS s');
					$query->select('s.url');
					$query->join('INNER', "`nize01_cinovicomat`.vs_content AS c ON c.ref_id = s.id AND c.type =  'image'" );
					$query->join('INNER', "`nize01_cinovicomat`.vs_content_content AS cc ON c.id = cc.ref_content_id AND cc.position='head' AND cc.content_id=$data->id");
					$query->order("cc.ordering ASC");
					$db->setQuery($query);
					$slika = $db->loadResult();
					$arr=explode('/', $slika); //sparsaj ven lokacijo TODO treba popravit, da se bo lokacija ujemala z id-em
					$arr[count($arr)-1]="300x200-".$arr[count($arr)-1];
					if($server=="dev" || $server=="localhost")	$data->slika="http://dev.novicomat.si/".implode("/", $arr);
					else $data->slika="http://novicomat.si/".implode("/", $arr);
					
					$query = $db->getQuery(true);
					$query->from('nize01_cinovicomat.vs_multimedias AS s');
					$query->select('*');
					$query->join('INNER', "`nize01_cinovicomat`.vs_content AS c ON c.ref_id = s.id AND c.type =  'image'" );
					$query->join('INNER', "`nize01_cinovicomat`.vs_content_content AS cc ON c.id = cc.ref_content_id AND cc.position='right' AND cc.content_id=$data->id");
					$query->order("cc.ordering ASC");
					$db->setQuery($query);
					$slike = $db->loadObjectList();
					
					foreach ($slike as $slika){
						$arr=explode('/', $slika->url); //sparsaj ven lokacijo TODO treba popravit, da se bo lokacija ujemala z id-em
						$arr[count($arr)-1]="300x200-".$arr[count($arr)-1];
						if($server=="dev" || $server=="localhost")	$slika->url2="http://dev.novicomat.si/".implode("/", $arr);
						else $slika->url2="http://novicomat.si/".implode("/", $arr);
						
					}
					$data->slike = $slike;
					
					$query = $db->getQuery(true);
					$query->from('nize01_cinovicomat.vs_multimedias AS s');
					$query->select('*');
					$query->join('INNER', "`nize01_cinovicomat`.vs_content AS c ON c.ref_id = s.id AND c.type =  'image'" );
					$query->join('INNER', "`nize01_cinovicomat`.vs_content_content AS cc ON c.id = cc.ref_content_id AND cc.position='bottom' AND cc.content_id=$data->id");
					$query->order("cc.ordering ASC");
					$db->setQuery($query);
					$slike = $db->loadObjectList();
					$data->galerija = $slike;
					foreach ($data->galerija as $slika){
						$arr=explode('/', $slika->url); //sparsaj ven lokacijo TODO treba popravit, da se bo lokacija ujemala z id-em
						$arr[count($arr)-1]="300x200-".$arr[count($arr)-1];
						if($server=="dev" || $server=="localhost")	$slika->url2="http://dev.novicomat.si/".implode("/", $arr);
						else $slika->url2="http://novicomat.si/".implode("/", $arr);
					}
						
					// PRIPONKE
					$query = $db->getQuery(true);
					$query->from('nize01_cinovicomat.vs_multimedias AS s');
					$query->select('*');
					$query->join('INNER', "`nize01_cinovicomat`.vs_content AS c ON c.ref_id = s.id AND c.type =  'document'" );
					$query->join('INNER', "`nize01_cinovicomat`.vs_content_content AS cc ON c.id = cc.ref_content_id AND cc.content_id=$data->id");
        			$query->order("cc.ordering ASC");
					$db->setQuery($query);
					$slike = $db->loadObjectList();
					$data->priponke = $slike;
						
						
					//VIDEO
					$query = $db->getQuery(true);
					$query->from('nize01_cinovicomat.vs_multimedias AS s');
					$query->select('s.url');
					$query->join('INNER', "`nize01_cinovicomat`.vs_content AS c ON c.ref_id = s.id AND c.type =  'video'" );
					$query->join('INNER', "`nize01_cinovicomat`.vs_content_content AS cc ON c.id = cc.ref_content_id AND cc.content_id=$data->id");
					$query->where("s.format IN ('mp4')");
					$query->where("s.format='mp4'");
					$query->order("cc.ordering ASC");
					$db->setQuery($query);
					$video = $db->loadObject();
					//print_r($video);
					if(isset($video)){
						$data->video='<iframe style="display: block; margin: auto;" width="420" height="315" src="'.$video->url.'" frameborder="0" allowfullscreen></iframe>';
					}
					else $data->video="";
					$this->_item = $data;
				}
				else{
					$query = $db->getQuery(true);
					$query->from('nize01_zelnik.vs_tags AS t');
					$query->select('t.*, tv.*');
					$query->join('INNER', 'nize01_zelnik.vs_tags_vsebina as tv ON tv.id_tag = t.id');
					$query->where("tv.id_vsebine = $data->id");
					$query->where("t.tag <> ''");
					$db->setQuery($query);
					$tags = $db->loadObjectList();
	//				print_r($tags);
					$arr=array();
					foreach ($tags as &$tag){
						$tag->tagUrl = JRoute::_("index.php?option=com_vsebine&tags=".$tag->tag);
						$arr[]=$tag->tag;
					}
					$data->str_tags=implode(', ', $arr);
					$data->tags=$tags;
					
					// SLIKE
				
					$query = $db->getQuery(true);
					$query->from('nize01_zelnik.vs_slike AS s');
					$query->select('s.*, sv.*');
					$query->join('INNER', 'nize01_zelnik.vs_slike_vsebine as sv ON sv.id_slike = s.id');
					$query->where("sv.id_vsebine = $data->id");
					$query->where("sv.mesto_prikaza = 2");
					$query->order("sv.zp_st ASC");
					$db->setQuery($query);
					$slike = $db->loadObjectList();
					$data->slike = $slike;
					
					
					$query = $db->getQuery(true);
					$query->from('nize01_zelnik.vs_slike AS s');
					$query->select('s.*, sv.*');
					$query->join('INNER', 'nize01_zelnik.vs_slike_vsebine as sv ON sv.id_slike = s.id');
					$query->where("sv.id_vsebine = $data->id");
					$query->where("sv.mesto_prikaza = 3");
					$query->order("sv.zp_st ASC");
					$db->setQuery($query);
					$slike = $db->loadObjectList();
					$data->galerija = $slike;

					
					// PRIPONKE
					$query = $db->getQuery(true);
					$query->from('nize01_zelnik.vs_slike AS s');
					$query->select('s.*, sv.*');
					$query->join('INNER', 'nize01_zelnik.vs_slike_vsebine as sv ON sv.id_slike = s.id');
					$query->where("sv.id_vsebine = $data->id");
					$query->where("sv.mesto_prikaza = 4");
					$query->order("sv.zp_st ASC");
					$db->setQuery($query);
					$slike = $db->loadObjectList();
					$data->priponke = $slike;
					
					
					//VIDEO
					require_once JPATH_COMPONENT.'/helpers/ZVideoHelper.php';
					$data->video=ZVideoHelper::insertVideo($data->video);
					
					$this->_item = $data;
				}
			}
			catch (Exception $e)
			{echo $e;
				if ($e->getCode() == 404) {
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else {
					$this->setError($e);
					$this->_item = false;
				}
			}
		}

		return $this->_item;
	}

	/**
	 * Increment the hit counter for the article.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
//	public function hit($pk = 0)
//	{
//		$input    = JFactory::getApplication()->input;
//		$hitcount = $input->getInt('hitcount', 1);
//
//		if ($hitcount)
//		{
//			$pk = (!empty($pk)) ? $pk : (int) $this->getState('article.id');
//			$db = $this->getDbo();
//
//			$db->setQuery(
//					'UPDATE #__content' .
//					' SET hits = hits + 1' .
//					' WHERE id = '.(int) $pk
//			);
//
//			try
//			{
//				$db->execute();
//			}
//			catch (RuntimeException $e)
//			{
//				$this->setError($e->getMessage());
//				return false;
//			}
//		}
//		return true;
//	}

//	public function storeVote($pk = 0, $rate = 0)
//	{
//		if ( $rate >= 1 && $rate <= 5 && $pk > 0 )
//		{
//			$userIP = $_SERVER['REMOTE_ADDR'];
//			$db = $this->getDbo();
//
//			$db->setQuery(
//					'SELECT *' .
//					' FROM #__content_rating' .
//					' WHERE content_id = '.(int) $pk
//			);
//
//			$rating = $db->loadObject();
//
//			if (!$rating)
//			{
//				// There are no ratings yet, so lets insert our rating
//				$db->setQuery(
//						'INSERT INTO #__content_rating ( content_id, lastip, rating_sum, rating_count )' .
//						' VALUES ( '.(int) $pk.', '.$db->Quote($userIP).', '.(int) $rate.', 1 )'
//				);
//
//				try
//				{
//					$db->execute();
//				}
//				catch (RuntimeException $e)
//				{
//					$this->setError($e->getMessage);
//					return false;
//				}
//			} else {
//				if ($userIP != ($rating->lastip))
//				{
//					$db->setQuery(
//							'UPDATE #__content_rating' .
//							' SET rating_count = rating_count + 1, rating_sum = rating_sum + '.(int) $rate.', lastip = '.$db->Quote($userIP) .
//							' WHERE content_id = '.(int) $pk
//					);
//
//					try
//					{
//						$db->execute();
//					}
//					catch (RuntimeException $e)
//					{
//						$this->setError($e->getMessage);
//						return false;
//					}
//				} else {
//					return false;
//				}
//			}
//			return true;
//		}
//		JError::raiseWarning('SOME_ERROR_CODE', JText::sprintf('COM_CONTENT_INVALID_RATING', $rate), "JModelArticle::storeVote($rate)");
//		return false;
//	}
}
