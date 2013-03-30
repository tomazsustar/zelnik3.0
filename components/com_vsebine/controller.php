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

jimport('joomla.application.component.controller');

class VsebineController extends JControllerLegacy
{
		public function display($cachable = false, $urlparams = false)
		{
			$cachable = true;
	
			// Set the default view name and format from the Request.
			// Note we are using a_id to avoid collisions with the router and the return page.
			// Frontend is a bit messier than the backend.
//			$id    = $this->input->getInt('a_id');
//			$vName = $this->input->getCmd('view', 'categories');
//			$this->input->set('view', $vName);
	
//			$user = JFactory::getUser();
	
//			if ($user->get('id') ||
//				($this->input->getMethod() == 'POST' &&
//					(($vName == 'category' && $this->input->get('layout') != 'blog') || $vName == 'archive' ))) {
//				$cachable = false;
//			}

			if($this->input->getString('tags')){
				$this->input->set('view', 'vsebine');
			}
			if($this->input->getString('prispevek')){
				$this->input->set('view', 'prispevek');
			}
	
//			$safeurlparams = array('catid' => 'INT', 'id' => 'INT', 'cid' => 'ARRAY', 'year' => 'INT', 'month' => 'INT', 'limit' => 'UINT', 'limitstart' => 'UINT',
//				'showall' => 'INT', 'return' => 'BASE64', 'filter' => 'STRING', 'filter_order' => 'CMD', 'filter_order_Dir' => 'CMD', 'filter-search' => 'STRING', 'print' => 'BOOLEAN', 'lang' => 'CMD', 'Itemid' => 'INT');
	
			// Check for edit form.
//			if ($vName == 'form' && !$this->checkEditId('com_content.edit.article', $id)) {
//				// Somehow the person just went to the form - we don't allow that.
//				return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
//			}
	
			parent::display($cachable);
	
			return $this;
		}
		
		public function module($cachable = false, $urlparams = false){
//			//echo "<pre>";print_r($this->paths);echo "</pre>";
			$view = $this->getView('Modul', 'html');
			$mdl=$this->getModel('Koledar', 'VsebineModel');
			$view->setModel($mdl, true);
			$view->addTemplatePath(JPATH_SITE.'/components/com_vsebine/views/modul/tmpl');
			$view->setLayout('koledar');
//			//echo "<pre>bbb ";print_r($mdl->getState());echo "</pre>";
			echo $view->display();
			
		}
	
}