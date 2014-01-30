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

jimport('joomla.application.component.view');

/**
 * View class for a list of Vsebine.
 */
class VsebineViewTemp extends JViewLegacy

{
protected $item;

	protected $params;

	protected $print;

	protected $state;

	protected $user;

	public function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		$this->user  = $user;

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app = JFactory::getApplication('site');
	}
    	
}
