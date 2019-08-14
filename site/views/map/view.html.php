<?php
/*--------------------------------------------------------------------------------------------------------|  www.vdm.io  |------/
    __      __       _     _____                 _                                  _     __  __      _   _               _
    \ \    / /      | |   |  __ \               | |                                | |   |  \/  |    | | | |             | |
     \ \  / /_ _ ___| |_  | |  | | _____   _____| | ___  _ __  _ __ ___   ___ _ __ | |_  | \  / | ___| |_| |__   ___   __| |
      \ \/ / _` / __| __| | |  | |/ _ \ \ / / _ \ |/ _ \| '_ \| '_ ` _ \ / _ \ '_ \| __| | |\/| |/ _ \ __| '_ \ / _ \ / _` |
       \  / (_| \__ \ |_  | |__| |  __/\ V /  __/ | (_) | |_) | | | | | |  __/ | | | |_  | |  | |  __/ |_| | | | (_) | (_| |
        \/ \__,_|___/\__| |_____/ \___| \_/ \___|_|\___/| .__/|_| |_| |_|\___|_| |_|\__| |_|  |_|\___|\__|_| |_|\___/ \__,_|
                                                        | |
                                                        |_|
/-------------------------------------------------------------------------------------------------------------------------------/

	@version		1.0.10
	@build			14th August, 2019
	@created		24th February, 2016
	@package		Support Groups
	@subpackage		view.html.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.module.helper');

/**
 * Supportgroups View class for the Map
 */
class SupportgroupsViewMap extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{		
		// get combined params of both component and menu
		$this->app = JFactory::getApplication();
		$this->params = $this->app->getParams();
		$this->menu = $this->app->getMenu()->getActive();
		// get the user object
		$this->user = JFactory::getUser();
		// Initialise variables.
		$this->items = $this->get('Items');
		if (isset($this->items) && SupportgroupsHelper::checkArray($this->items))
		{
			// check if clustering of the markers should be set
			$this->setClustering($this->items);
		}
		// set the default display
		$this->initScript = "setMapData(1,'totals');";
		$this->backTo = 1;
		$this->backType = 'totals';
		$this->groupsFilter = false;
		$this->backToRef = '';
		// check if it should be another
		$input = array('facility' => 'int','area' => 'int','region' => 'int','country' => 'int');
		$inputLang = array('facility' => JText::_('COM_SUPPORTGROUPS_FACILITY'),'area' => JText::_('COM_SUPPORTGROUPS_AREA'),'region' => JText::_('COM_SUPPORTGROUPS_REGION'),'country' => JText::_('COM_SUPPORTGROUPS_COUNTRY'));
		$getDetails =  array('facility' => 'facility','area' => 'area','region' => 'region','country' => 'country'); 
		$filtering = $this->app->input->getArray($input);
		foreach ($filtering as $key => $filter)
		{
			if ($filter)
			{
				// we can only add one
				$this->initScript = "setMapData(".(int) $filter.",'".$getDetails[$key]."');";
				// set main call back
				$this->backTo = $filter;
				$this->backType = $getDetails[$key];
				// groupsFilter
				$this->groupsFilter = '&'.$key.'='.(int)$filter;
				// set back to groups
				$this->backToRef = '<button class="uk-button uk-width-1-1 uk-button-small uk-margin-small-bottom uk-button-primary" onclick="history.go(-1);" value="Back"><i class="uk-icon-arrow-circle-left" ></i> '.JText::_('COM_SUPPORTGROUPS_BACK').'</button>';
			}
		}

		// Set the toolbar
		$this->addToolBar();

		// set the document
		$this->_prepareDocument();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		parent::display($tpl);
	}

	/**
	* Get the Google API Key and set to URL if not reached limit
	* 
	* @return    string    The url to the Google API
	*/
	public function getGoogleAPI()
	{
		$api_key = $this->params->get('api_key');
		if ($api_key)
		{
			return 'https://maps.googleapis.com/maps/api/js?key='.$api_key.'&callback=getMapScript';
		}
		return 'https://maps.googleapis.com/maps/api/js?sensor=false&callback=getMapScript';
	}

	/**
	* Cluster Switch
	* @var         boolean
	*/
	public $cluster = false;

	/**
	* Clustering control method
	*
	* @params       object    $items The Support Group Values
	*
	* @return       void
	*
	*/
	private function setClustering(&$items)
	{
		$cluster = $this->params->get('cluster', null);
		if ($cluster)
		{
			$cluster_at = $this->params->get('cluster_at', 300);
			$total = count($items);
			if ($cluster_at <= $total)
			{
				$this->cluster = true;
			}
		}
	}


	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{

		// always make sure jquery is loaded.
		JHtml::_('jquery.framework');
		// Load the header checker class.
		require_once( JPATH_COMPONENT_SITE.'/helpers/headercheck.php' );
		// Initialize the header checker.
		$HeaderCheck = new supportgroupsHeaderCheck;

		// Load uikit options.
		$uikit = $this->params->get('uikit_load');
		// Set script size.
		$size = $this->params->get('uikit_min');
		// Set css style.
		$style = $this->params->get('uikit_style');

		// The uikit css.
		if ((!$HeaderCheck->css_loaded('uikit.min') || $uikit == 1) && $uikit != 2 && $uikit != 3)
		{
			$this->document->addStyleSheet(JURI::root(true) .'/media/com_supportgroups/uikit-v2/css/uikit'.$style.$size.'.css', (SupportgroupsHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
		}
		// The uikit js.
		if ((!$HeaderCheck->js_loaded('uikit.min') || $uikit == 1) && $uikit != 2 && $uikit != 3)
		{
			$this->document->addScript(JURI::root(true) .'/media/com_supportgroups/uikit-v2/js/uikit'.$size.'.js', (SupportgroupsHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/javascript');
		}

		// Load the script to find all uikit components needed.
		if ($uikit != 2)
		{
			// Set the default uikit components in this view.
			$uikitComp = array();
			$uikitComp[] = 'data-uk-grid';
			$uikitComp[] = 'uk-placeholder';
		}

		// Load the needed uikit components in this view.
		if ($uikit != 2 && isset($uikitComp) && SupportgroupsHelper::checkArray($uikitComp))
		{
			// load just in case.
			jimport('joomla.filesystem.file');
			// loading...
			foreach ($uikitComp as $class)
			{
				foreach (SupportgroupsHelper::$uk_components[$class] as $name)
				{
					// check if the CSS file exists.
					if (JFile::exists(JPATH_ROOT.'/media/com_supportgroups/uikit-v2/css/components/'.$name.$style.$size.'.css'))
					{
						// load the css.
						$this->document->addStyleSheet(JURI::root(true) .'/media/com_supportgroups/uikit-v2/css/components/'.$name.$style.$size.'.css', (SupportgroupsHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
					}
					// check if the JavaScript file exists.
					if (JFile::exists(JPATH_ROOT.'/media/com_supportgroups/uikit-v2/js/components/'.$name.$size.'.js'))
					{
						// load the js.
						$this->document->addScript(JURI::root(true) .'/media/com_supportgroups/uikit-v2/js/components/'.$name.$size.'.js', (SupportgroupsHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/javascript', (SupportgroupsHelper::jVersion()->isCompatible('3.8.0')) ? array('type' => 'text/javascript', 'async' => 'async') : true);
					}
				}
			}
		}
		// load the meta description
		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		// load the key words if set
		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		// check the robot params
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		// Add the JavaScript for JStore
		$this->document->addScript(JURI::root() .'media/com_supportgroups/js/jquery.json.min.js');
		$this->document->addScript(JURI::root() .'media/com_supportgroups/js/jstorage.min.js');
		// check if we should use browser storage
		$setBrowserStorage = $this->params->get('set_browser_storage', null);
		if ($setBrowserStorage)
		{
			// check what (Time To Live) show we use
			$storageTimeToLive = $this->params->get('storage_time_to_live', 'global');
			if ('global' == $storageTimeToLive)
			{
				// use the global session time
				$session = JFactory::getSession();
				// must have it in milliseconds
				$expire = ($session->getExpire()*60)* 1000;
			}
			else
			{
				// use the Supportgroups Global setting
				if (0 !=  $storageTimeToLive)
				{
					// this will convert the time into milliseconds
					$storageTimeToLive =  $storageTimeToLive * 1000;
				}
				$expire = $storageTimeToLive;
			}
		}
		else
		{
			// set to use no storage
			$expire = 10;
		}
		// set an error message if needed
		$this->document->addScriptDeclaration("var returnError = '<div class=\"uk-alert uk-alert-warning\"><h1>".JText::_('COM_SUPPORTGROUPS_AN_ERROR_HAS_OCCURRED')."!</h1><p>".JText::_('COM_SUPPORTGROUPS_PLEASE_TRY_AGAIN_LATER').".</p></div>';");
		// add group filtering to query
		if ($this->groupsFilter)
		{
			$this->document->addScriptDeclaration("var groupFilter = '".$this->groupsFilter."';");
		}
		else
		{
			$this->document->addScriptDeclaration("var groupFilter = '';");
		}
		// Set the Time To Live To JavaScript
		$this->document->addScriptDeclaration("var expire = ". (int) $expire.";");
		// Set the the back to global values
		$this->document->addScriptDeclaration("var mainBackTo = '".$this->backTo."';");
		$this->document->addScriptDeclaration("var mainBackType = '".$this->backType."';");
		$this->document->addScriptDeclaration("var backTo = '".$this->backTo."';");
		$this->document->addScriptDeclaration("var backType = '".$this->backType."';");
		// check if clustering should be loaded
		if ($this->cluster)
		{
			$this->document->addScript(JURI::root() .'media/com_supportgroups/js-marker-cluster/src/markerclusterer.js');
		}
		// add the document default css file
		$this->document->addStyleSheet(JURI::root(true) .'/components/com_supportgroups/assets/css/map.css', (SupportgroupsHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
		// Set the Custom CSS script to view
		$this->document->addStyleDeclaration("
			#map_wrapper {
			    height: 600px;
			}
			
			#map_canvas {
			    width: 100%;
			    height: 100%;
			}
			
			.mapping  {
			    -webkit-box-shadow: 0 0 13px 2px #CADFAA;
			    box-shadow: 0 0 13px 2px #CADFAA;
			}
			
			#global {
			    height: 600px;
			}
		");
		// Set the Custom JS script to view
		$this->document->addScriptDeclaration("
			// set ajax
			jQuery.ajaxSetup({
			    async: true
			});
			// Get Data for Map from Server
			function getItemData_server(id, type){
				var getUrl = \"index.php?option=com_supportgroups&task=ajax.getItemData&format=json&requestView=map\";
				if(token.length > 0 && id > 0 && type.length > 0){
					var request = 'token='+token+'&id='+id+'&type='+type;
				}
				return jQuery.ajax({
					type: 'GET',
					url: getUrl,
					dataType: 'jsonp',
					data: request,
					jsonp: 'callback'
				});
			}
			
			// set Data to display
			function setMapData(id,type){
				// show getting spinner
				jQuery('#getting').show();
				// hide the old data
				jQuery('#map_info').hide();
				// first we see if we have local storage of this data
				var data = jQuery.jStorage.get(id+'_'+type);
				if (!data) {
					getItemData_server(id,type).done(function(result) {
						if(result.html){
							loadMapData(result.html);
							// store the data for next time
							jQuery.jStorage.set(id+'_'+type,result.html,{TTL: expire});
							// set global back switch
							backSwitch(id,type);
						}
						else
						{
							loadMapData(returnError);
						}
					});
				} else {
					loadMapData(data);
					// make sure to keep the Time To Live updated
					jQuery.jStorage.setTTL(id+'_'+type,expire);
					// set global back switch
					backSwitch(id,type);
				}
			} 
			
			// function to set back globals
			function backSwitch(id,type) {
				// insure we have a global back
				if ('group' == type)
				{
					backTo = id;
					backType = 'group';
				}
			}
			
			// function to load the data
			function loadMapData(data){
				// hide the getting spinner
				jQuery('#getting').hide();
				// set the data
				jQuery('#map_info').html(data);
				// show the data
				jQuery('#map_info').show();
			}
			
			// Get Map Script
			function getMapScript_server(id, type){
				var getUrl = \"index.php?option=com_supportgroups&task=ajax.getItemData&format=json&requestView=map\";
				if(token.length > 0 ){
					var request = 'token='+token+'&id=1&type=groups'+groupFilter;
				}
				return jQuery.ajax({
					type: 'GET',
					url: getUrl,
					dataType: 'jsonp',
					data: request,
					jsonp: 'callback'
				});
			}
			// get Map script
			function getMapScript(){
				getMapScript_server().done(function(result) {
					if(result){
						loadMapScript(result);
					}
				});
			}
			
			// function to load the data
			function loadMapScript(data){
				// set the script
				jQuery('#script').html(data);
				// then initiate the map
				initialize();
			}
			
			// the isSet function
			function isSet(val)
			{
				if ((val != undefined) && (val != null) && 0 !== val.length){
					return true;
				}
				return false;
			}
		");
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		// adding the joomla toolbar to the front
		JLoader::register('JToolbarHelper', JPATH_ADMINISTRATOR.'/includes/toolbar.php');
		
		// set help url for this view if found
		$help_url = SupportgroupsHelper::getHelpUrl('map');
		if (SupportgroupsHelper::checkString($help_url))
		{
			JToolbarHelper::help('COM_SUPPORTGROUPS_HELP_MANAGER', false, $help_url);
		}
		// now initiate the toolbar
		$this->toolbar = JToolbar::getInstance();
	}

	/**
	 * Get the modules published in a position
	 */
	public function getModules($position, $seperator = '', $class = '')
	{
		// set default
		$found = false;
		// check if we aleady have these modules loaded
		if (isset($this->setModules[$position]))
		{
			$found = true;
		}
		else
		{
			// this is where you want to load your module position
			$modules = JModuleHelper::getModules($position);
			if ($modules)
			{
				// set the place holder
				$this->setModules[$position] = array();
				foreach($modules as $module)
				{
					$this->setModules[$position][] = JModuleHelper::renderModule($module);
				}
				$found = true;
			}
		}
		// check if modules were found
		if ($found && isset($this->setModules[$position]) && SupportgroupsHelper::checkArray($this->setModules[$position]))
		{
			// set class
			if (SupportgroupsHelper::checkString($class))
			{
				$class = ' class="'.$class.'" ';
			}
			// set seperating return values
			switch($seperator)
			{
				case 'none':
					return implode('', $this->setModules[$position]);
					break;
				case 'div':
					return '<div'.$class.'>'.implode('</div><div'.$class.'>', $this->setModules[$position]).'</div>';
					break;
				case 'list':
					return '<ul'.$class.'><li>'.implode('</li><li>', $this->setModules[$position]).'</li></ul>';
					break;
				case 'array':
				case 'Array':
					return $this->setModules[$position];
					break;
				default:
					return implode('<br />', $this->setModules[$position]);
					break;
				
			}
		}
		return false;
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var, $sorten = false, $length = 40)
	{
		// use the helper htmlEscape method instead.
		return SupportgroupsHelper::htmlEscape($var, $this->_charset, $sorten, $length);
	}
}
