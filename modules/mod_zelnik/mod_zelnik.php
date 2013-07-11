<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_weblinks
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

	require_once(JPATH_SITE."/components/com_vsebine/controller.php");
    $ctrl=new VsebineController();
    $ctrl->addModelPath(JPATH_SITE.'/components/com_vsebine/models');
  	$ctrl->addViewPath(JPATH_SITE.'/components/com_vsebine/views');
  
	$ctrl->module();
	//echo "AAAAAAAAAAAa";
//return;
