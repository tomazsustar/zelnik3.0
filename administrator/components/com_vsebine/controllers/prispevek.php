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

jimport('joomla.application.component.controllerform');

/**
 * Prispevek controller class.
 */
class VsebineControllerPrispevek extends JControllerForm
{

    function __construct() {
        $this->view_list = 'vsebine';
        parent::__construct();
    }

}