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

//		if ($this->_item === null) {
//			$this->_item = array();
//		}
//
		if ($prispevek) {

			try {
				$db = $this->getDbo();
				$query = $db->getQuery(true);


				$query->from('vs_vsebine AS a');

				// Join on category table.
				$query->select('a.*');

				// Join on user table.
//				$query->select('u.name AS author');
//				$query->join('LEFT', '#__users AS u on u.id = a.created_by');

				// Join on contact table
				

				// Filter by start and end dates.
//				$nullDate = $db->Quote($db->getNullDate());
//				$date = JFactory::getDate();
//
//				$nowDate = $db->Quote($date->toSql());

//				$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
//				$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

				// Join to check for category published state in parent categories up the tree
				// If all categories are published, badcats.id will be null, and we just use the article state
//				$subquery = ' (SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ';
//				$subquery .= 'ON cat.lft BETWEEN parent.lft AND parent.rgt ';
//				$subquery .= 'WHERE parent.extension = ' . $db->quote('com_content');
//				$subquery .= ' AND parent.published <= 0 GROUP BY cat.id)';
//				$query->join('LEFT OUTER', $subquery . ' AS badcats ON badcats.id = c.id');

				// Filter by published state.
//				$published = $this->getState('filter.published');
//				$archived = $this->getState('filter.archived');
//
				if (is_numeric($prispevek)) {
					$query->where("a.id = $prispevek");
				}else{
					$query->where("a.title_url = '$prispevek'");
				}
				
				
				$db->setQuery($query);

				$data = $db->loadObject();

				if (empty($data)) {
					return JError::raiseError(404, JText::_('COM_CONTENT_ERROR_ARTICLE_NOT_FOUND'));
				}

				// Check for published state if filter set.
//				if (((is_numeric($published)) || (is_numeric($archived))) && (($data->state != $published) && ($data->state != $archived))) {
//					return JError::raiseError(404, JText::_('COM_CONTENT_ERROR_ARTICLE_NOT_FOUND'));
//				}

				// Convert parameter fields to objects.
//				$registry = new JRegistry;
//				$registry->loadString($data->attribs);
//
//				$data->params = clone $this->getState('params');
//				$data->params->merge($registry);
//
//				$registry = new JRegistry;
//				$registry->loadString($data->metadata);
//				$data->metadata = $registry;

				// Compute selected asset permissions.
//				$user	= JFactory::getUser();

				// Technically guest could edit an article, but lets not check that to improve performance a little.
//				if (!$user->get('guest')) {
//					$userId	= $user->get('id');
//					$asset	= 'com_content.article.'.$data->id;
//
//					// Check general edit permission first.
//					if ($user->authorise('core.edit', $asset)) {
//						$data->params->set('access-edit', true);
//					}
//					// Now check if edit.own is available.
//					elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
//						// Check for a valid user and that they are the owner.
//						if ($userId == $data->created_by) {
//							$data->params->set('access-edit', true);
//						}
//					}
//				}

				// Compute view access permissions.
//				if ($access = $this->getState('filter.access')) {
//					// If the access filter has been set, we already know this user can view.
//					$data->params->set('access-view', true);
//				}
//				else {
//					// If no access filter is set, the layout takes some responsibility for display of limited information.
//					$user = JFactory::getUser();
//					$groups = $user->getAuthorisedViewLevels();
//
//					if ($data->catid == 0 || $data->category_access === null) {
//						$data->params->set('access-view', in_array($data->access, $groups));
//					}
//					else {
//						$data->params->set('access-view', in_array($data->access, $groups) && in_array($data->category_access, $groups));
//					}
//				}

				$this->_item = $data;
			}
			catch (Exception $e)
			{
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
