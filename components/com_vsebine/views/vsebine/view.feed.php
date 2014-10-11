<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML View class for the Content component
 *
 * @package     Joomla.Site
 * @subpackage  com_content
 * @since       1.5
 */
class VsebineViewVsebine extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app       = JFactory::getApplication();
		$doc       = JFactory::getDocument();
		$params    = $app->getParams();
		$feedEmail = $app->getCfg('feed_email', 'author');
		$siteEmail = $app->getCfg('mailfrom');

		// Get some data from the model
		$app->input->set('limit', $app->getCfg('feed_limit'));
		$category = $this->get('Category');
		$rows     = $this->get('Items');

		$doc->link = JRoute::_("");

		foreach ($rows as $row)
		{
			// Strip html from feed item title
			$title = $this->escape($row->title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			// Compute the article slug
			$row->slug = $row->title ? ($row->id . ':' . $row->title) : $row->id;

			// Url link to article
			$link = JRoute::_("index.php?option=com_vsebine&prispevek=".$row->id."&title=".JFilterOutput::stringURLSafe($row->title));

			// Get row fulltext
			$db = JFactory::getDbo();
			$query = 'SELECT' .$db->quoteName('fulltext'). 'FROM #__content WHERE id ='.$row->id;
			$db->setQuery($query);
			$row->fulltext = $db->loadResult();

			// Get description, author and date
			$description = ($params->get('feed_summary', 0) ? $row->fulltext : "<img src='".$row->slika."' style='vertical-align:bottom; max-height:100px; margin-right:20px; width:auto;' alt='slika vsebine'>".$row->introtext);
			$author = $row->created_by_alias ? $row->created_by_alias : $row->author;
			@$date = ($row->publish_up ? date('r', strtotime($row->publish_up)) : '');
			
			// Load individual item creator class
			$item           = new JFeedItem;
			$item->title    = $title;
			$item->link     = $link;
			$item->image = $row->slika;
			$item->date     = $date;
			$item->category = $row->category_title;
			$item->author   = $author;

			if ($feedEmail == 'site')
			{
				$item->authorEmail = $siteEmail;
			}
			elseif ($feedEmail === 'author')
			{
				$item->authorEmail = $row->author_email;
			}

			// Add readmore link to description if introtext is shown, show_readmore is true and fulltext exists
			if (!$params->get('feed_summary', 0) && $params->get('feed_show_readmore', 0) && $row->fulltext)
			{
				$description .= '<p class="feed-readmore"><a target="_blank" href ="' . $item->link . '">' . JText::_('COM_CONTENT_FEED_READMORE') . '</a></p>';
			}

			// Load item description and add div
			$item->description	= '<div class="feed-description">'.$description.'</div>';

			// Loads item info into rss array
			$doc->addItem($item);
		}
	}
}
