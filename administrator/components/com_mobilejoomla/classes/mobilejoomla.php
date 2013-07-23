<?php
/**
 * Mobile Joomla!
 * http://www.mobilejoomla.com
 *
 * @version		1.2.6.2
 * @license		GNU/GPL v2 - http://www.gnu.org/licenses/gpl-2.0.html
 * @copyright	(C) 2008-2013 Kuneri Ltd.
 * @date		July 2013
 */
defined('_JEXEC') or die('Restricted access');

class MobileJoomla
{
	var $config = null;
	var $device = null;
	var $_ishomepage = false;

	static function &getConfig()
	{
		static $instance;
		if(!is_array($instance))
		{
			$config = JPATH_ADMINISTRATOR.'/components/com_mobilejoomla/config.php';
			$MobileJoomla_Settings = array ();
			include($config);
			$instance = $MobileJoomla_Settings;
		}
		return $instance;
	}

	static function &getDevice()
	{
		static $instance;
		if(!is_array($instance))
		{
			$instance = array ('markup' => false, 'real_markup' => false, 'screenwidth' => 0, 'screenheight' => 0, 'imageformats' => null, 'mimetype' => null, 'param' => array ());
		}
		return $instance;
	}

	/**
	 * @param string $markup
	 * @return MobileJoomla
	 */
	static function &getInstance($markup = '')
	{
		static $instance;
		if(!is_object($instance))
		{
			if($markup == '')
			{
				$nullvar = null;
				return $nullvar;
			}
			$class = 'MobileJoomla_'.strtoupper($markup);
			if(!class_exists($class))
			{
				$path = JPATH_ADMINISTRATOR.'/components/com_mobilejoomla/markup/'.$markup.'.php';
				require_once($path);
				if(!class_exists($class))
					JError::raiseError(500, 'Class not found: '.$class);
			}
			$instance = new $class;
			$instance->config =& MobileJoomla::getConfig();
			$instance->device =& MobileJoomla::getDevice();
		}
		return $instance;
	}

	static function &getToolbar()
	{
		static $instance = null;
		if($instance == null)
		{
			include_once JPATH_ADMINISTRATOR.'/components/com_mobilejoomla/classes/mjtoolbar.php';
			$instance = new MJToolbar;
		}
		return $instance;
	}

	function getMarkup()
	{
		return '';
	}

	function isHome()
	{
		return $this->_ishomepage;
	}

	function setHome($ishome)
	{
		$this->_ishomepage = $ishome;
	}

	function getCharset()
	{
		return 'utf-8';
	}

	function getContentType()
	{
		return 'text/html';
	}

	function getContentString()
	{
		return $this->getContentType().'; charset=utf-8';
	}

	function setHeader()
	{
		JResponse::setHeader('Content-Type', $this->getContentString(), true);
	}

	function showXMLheader()
	{
	}

	function showDocType()
	{
	}

	function getXmlnsString()
	{
		$document = JFactory::getDocument();
		$html_attrs = ' lang="' . $document->getLanguage() . '"';
		if($document->getDirection() == 'rtl')
			$html_attrs .= ' dir="rtl"';
		return $html_attrs;
	}

	function getPageTitle()
	{
		$document = JFactory::getDocument();
		return $document->getTitle();
	}

	function showHead()
	{
		echo '<jdoc:include type="head" />';
	}

	function showComponent()
	{
		if(!$this->_ishomepage || $this->getParam('componenthome'))
			echo '<jdoc:include type="component" />';
	}

	function showMessage()
	{
		echo '<jdoc:include type="message" />';
	}

	function showFooter()
	{
	}

	function processPage($text)
	{
		return $text;
	}

	function getParam($name, $default = null)
	{
		$MobileJoomla_Settings =& MobileJoomla::getConfig();
		$full_name = $this->getMarkup().'.'.$name;
		$global_name = 'global.'.$name;
		if(!isset($MobileJoomla_Settings[$full_name]))
			return isset($MobileJoomla_Settings[$name]) ? $MobileJoomla_Settings[$name] : $default;
		if($MobileJoomla_Settings[$full_name]==='' && isset($MobileJoomla_Settings[$global_name]))
			return $MobileJoomla_Settings[$global_name];
		return $MobileJoomla_Settings[$full_name];
	}

	function setParam($name, $value)
	{
		$MobileJoomla_Settings =& MobileJoomla::getConfig();
		$full_name = $this->getMarkup().'.'.$name;
		$MobileJoomla_Settings[$full_name] = $value;
	}

	function hideModules($scope = '')
	{
		switch($scope)
		{
			case 'all':
				$this->setParam('jfooter', 0);
				$this->setParam('footer1', '');
				$this->setParam('footer2', '');
				$this->setParam('footer3', '');
			case '':
				$this->setParam('header1', '');
				$this->setParam('header2', '');
				$this->setParam('header3', '');
				$this->setParam('middle1', '');
				$this->setParam('middle2', '');
				$this->setParam('middle3', '');
				$this->setParam('cards', '');
		}
	}

	function getPosition($pos)
	{
		if(!isset($this->config)) return '';
		switch($pos)
		{
			case 'header':
				return $this->getParam('header1');
			case 'middle':
				return $this->getParam('middle1');
			case 'footer':
				return $this->getParam('footer1');
			case 'header2':
			case 'header3':
			case 'middle2':
			case 'middle3':
			case 'footer2':
			case 'footer3':
			case 'cards':
				return $this->getParam($pos);
		}
		return '';
	}

	function loadModules($position)
	{
		echo '<jdoc:include type="modules" name="'.$position.'" />';
	}

	function loadModulesAsCards($position)
	{
	}

	static function RescaleImages($text, $scaletype, $addstyles = false)
	{
		require_once(JPATH_ADMINISTRATOR.'/components/com_mobilejoomla/classes/imagerescaler.php');
		return ImageRescaler::RescaleImages($text, $scaletype, $addstyles);
	}

	static function isCurrentMarkup($markup)
	{
		$MobileJoomla_Device =& MobileJoomla::getDevice();
		if($markup=='auto')
			$markup = $MobileJoomla_Device['real_markup'];
		elseif($markup=='desktop' || $markup=='')
			$markup = false;
		elseif($markup=='mobile')
			$markup = $MobileJoomla_Device['real_markup']=='' ? 'xhtml' : $MobileJoomla_Device['real_markup'];
		return $markup === $MobileJoomla_Device['markup'];
	}

	static function getDeviceViewURI($device)
	{
		jimport('joomla.environment.uri');

		$MobileJoomla_Settings =& MobileJoomla::getConfig();
		$MobileJoomla_Device =& MobileJoomla::getDevice();

		$uri = clone(JURI::getInstance());
		if($uri->getVar('format')=='html')
			$uri->delVar('format');
		$uri->delVar('device');

		$desktop_uri = new JURI($MobileJoomla_Settings['desktop_url']);
		$uri->setHost($desktop_uri->getHost());

		if($device=='mobile')
			$device = $MobileJoomla_Device['real_markup']=='' ? 'xhtml' : $MobileJoomla_Device['real_markup'];

		if($device=='auto')
			$device = $MobileJoomla_Device['real_markup']=='' ? 'desktop' : $MobileJoomla_Device['real_markup'];

		switch($device)
		{
		case 'desktop':
			break;
		default:
			if($MobileJoomla_Settings[$device.'.domain'])
				$uri->setHost($MobileJoomla_Settings[$device.'.domain']);
		}

		if($device !== false)
			$uri->setVar('device', $device);

		return htmlspecialchars($uri->toString());
	}

	static function getCanonicalURI()
	{
		jimport('joomla.environment.uri');

		$MobileJoomla_Device   =& MobileJoomla::getDevice();
		$MobileJoomla_Settings =& MobileJoomla::getConfig();

		$desktop_uri = new JURI($MobileJoomla_Settings['desktop_url']);
		$uri = clone(JURI::getInstance());

		$uri_host     = preg_replace('#^www\.#', '', $uri->getHost());
		$desktop_host = preg_replace('#^www\.#', '', $desktop_uri->getHost());
		if(($uri_host == $desktop_host) && ($MobileJoomla_Device['markup'] == $MobileJoomla_Device['default_markup']))
			return false;

		$uri->delVar('device');
		$uri->delVar('format');
		$uri->setHost($desktop_uri->getHost());

		return htmlspecialchars($uri->toString());
	}

	function getAccessKey()
	{
		static $last_keynum = 0;
		if($last_keynum>=10)
			return false;
		$last_keynum++;
		return $last_keynum==10 ? '0' : $last_keynum;
	}

	static function getCacheKey()
	{
		$MobileJoomla_Device =& MobileJoomla::getDevice();

		$cachekey = array();
		$cachekey[] = $MobileJoomla_Device['markup'];
		$cachekey[] = $MobileJoomla_Device['default_markup'];
		$cachekey[] = $MobileJoomla_Device['screenwidth'];
		$cachekey[] = $MobileJoomla_Device['screenheight'];
		$cachekey[] = isset($MobileJoomla_Device['pixelratio'])
						? $MobileJoomla_Device['pixelratio']
						: '1';
		$imageformats = $MobileJoomla_Device['imageformats'];
		if(is_array($imageformats))
		{
			sort($imageformats);
			$cachekey[] = implode('', $imageformats);
		}
		return implode('_', $cachekey);
	}
}
