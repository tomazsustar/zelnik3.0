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
					$prispevek = JRequest::getVar('prispevek');
					$type =self::GetType($prispevek);
					JRequest::setVar('view', $type);
					//die($type);
					/*
					switch ($type){
						case 'article':
							JRequest::setVar('view', 'prispevek');
						break;
						case 'event':
							$count = self::CountArticles($prispevek, $article);
							switch ($count){
								case 0:
									echo "AAA";
									JRequest::setVar('view', 'prispevek');
									JRequest::setVar('layout', 'dogodek');
									break;
								case 1:
									echo "BBB";
									JRequest::setVar('view', 'prispevek');
									JRequest::setVar('prispevek', self::GetArticle($prispevek));
								break;
								default:
									echo "CCC";
									JRequest::setVar('view', 'prispevek');
									JRequest::setVar('prispevek', self::GetArticle($prispevek));
								break;
							}
							JRequest::setVar('view', 'vsebine');
							JRequest::setVar('dogodek', '');
						break;
					}
					*/
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
		
		public static function GetType($id){
			$db=JFactory::getDbo();
			$q="SELECT type from nize01_cinovicomat.vs_content where id=$id" ;
			$db->setQuery($q);
			return $db->loadResult();
		}
		
		public static function CountArticles($id){
			$db=JFactory::getDbo();
			$q="SELECT count(*), from  nize01_cinovicomat.vs_content c inner join 
					nize01_cinovicomat.vs_content_content cc on cc.ref_content_id = c.id
					inner join nize01_cinovicomat.vs_content ca on ca.id=cc.content_id and type='article'
					where c.id=$id" ;
			$db->setQuery($q);
			return $db->loadResult();
		}
		public static function GetArticle($id){
			$db=JFactory::getDbo();
			$q="SELECT ca.id, from  nize01_cinovicomat.vs_content c inner join
					nize01_cinovicomat.vs_content_content cc on cc.ref_content_id = c.id
					inner join nize01_cinovicomat.vs_content ca on ca.id=cc.content_id and type='article'
					inner join nize01_cinovicomat.articles a on a.id=ca.ref_id
					where c.id=$id
					order by a.publish_up desc limit 0,1" ;
			$db->setQuery($q);
			return $db->loadResult();
		}
	
}