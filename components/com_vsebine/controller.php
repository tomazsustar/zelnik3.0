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

			if(JRequest::getVar('view')!='koledar'){ //če koledar view ni določen
				if(JRequest::getVar('tags')){
					JRequest::setVar('view', 'vsebine');
				}
				if(JRequest::getVar('prispevek')){
					JRequest::setVar('view', 'prispevek');
				}
			}
	
			parent::display($cachable);
	
			return $this;
		}
		
		public function module($cachable = false, $urlparams = false){
//			//echo "<pre>";print_r($this->paths);echo "</pre>";
			$view = $this->getView('Koledar', 'html');
			$mdl=$this->getModel('Koledar', 'VsebineModel');
			$view->setModel($mdl, true);
			//$view->addTemplatePath(JPATH_SITE.'/components/com_vsebine/views/modul/tmpl');
			$app = JFactory::getApplication();
			$template = $app->getTemplate();
			$template_path=JPATH_SITE.'/templates/'.$template.'/html/com_vsebine/koledar';
			if(!file_exists($template_path.'/default.php')){
				$view->addTemplatePath(JPATH_SITE.'/components/com_vsebine/views/koledar/tmpl');
			}else{
				$view->addTemplatePath($template_path);
			}
			//echo "<pre>".print_r($view)."</pre>";
//			//echo "<pre>bbb ";print_r($mdl->getState());echo "</pre>";
			echo $view->display();
			
		}
	
}