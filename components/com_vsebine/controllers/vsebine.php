<?php
/**
 * @version     1.0.0
 * @package     com_vsebine
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Tomaž Šuštar <tomaz@zelnik.net> - http://www.zelnik.net
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Vsebine list controller class.
 */
class VsebineControllerVsebine extends VsebineController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Vsebine', $prefix = 'VsebineModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}