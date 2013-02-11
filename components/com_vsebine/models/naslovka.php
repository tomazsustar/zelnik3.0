<?php

/**
 * @version     1.0.0
 * @package     com_vsebine
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Tomaž Šuštar <tomaz@zelnik.net> - http://www.zelnik.net
 */
defined('_JEXEC') or die;

//jimport('joomla.application.component.modellist');
require_once __DIR__ . '/vsebine.php';

/**
 * Methods supporting a list of Vsebine records.
 */
class VsebineModelNaslovka extends VsebineModelVsebine {

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


	
}
