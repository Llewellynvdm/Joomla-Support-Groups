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
 * Supportgroups View class for the Supportgroups
 */
class SupportgroupsViewSupportgroups extends JViewLegacy
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
			// set the items to Global Arrays and Other
			$this->setGlobals($this->items);
		}
		// turn footable style off (using uk-table style for now)
		$this->fooTableStyle = 2;

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
	* Checker
	* @var         string
	*/
	public $groupBundlesKey;

	/**
	* The facilities Array
	* @var         array
	*/
	public $facilitiesArray = array();

	/**
	* set Global Arrays and other
	*
	* @params       object    $items The Support Group Values
	*
	* @return         void
	*
	*/
	protected function setGlobals(&$items)
	{
		// set buckets
		$bundels = array();
		foreach ($items as $nr => &$item)
		{
			// build the bundels
			$bundels[] = $item->id;
			// set some global arrays
			if (!isset($this->facilitiesArray[$item->facility_id]) && SupportgroupsHelper::checkString($item->facility_name))
			{
				$this->facilitiesArray[$item->facility_id] = $item->facility_name;
			}
		}
		// sort the global arrays
		if (SupportgroupsHelper::checkArray($this->facilitiesArray))
		{
			sort($this->facilitiesArray);
		}
		// json encode
		$groupBundles = json_encode($bundels);
		// set a global key
		$this->groupBundlesKey = md5($groupBundles);
		// get the session
		$session = JFactory::getSession();
		// set the data to session
		$session->set($this->groupBundlesKey,$groupBundles);
	}

	/**
	* Clean strings for Javacript
	*
	* @params    string     $string The String to Clean
	* @params    boolean  $tags Switch to set if tabs should also be cleaned
	*
	* @ return     string     The cleaned string
	*
	*/
	public function clean($string,$tags = true)
	{
		if ($tags)
		{
			$fix = array("&" => "&amp;","<" => "&lt;",">" => "&gt;",'"' => '&quot;',"'" => '&#39;',"/" => '&#x2F;');
		}
		else
		{
			$fix = array("&" => "&amp;",'"' => '&quot;',"'" => '&#39;');
		}
		// clean the string
		return str_replace(array_keys($fix), array_values($fix), $string);
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

		// Add the CSS for Footable
		$this->document->addStyleSheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
		$this->document->addStyleSheet(JURI::root() .'media/com_supportgroups/footable-v3/css/footable.standalone.min.css', (SupportgroupsHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
		// Add the JavaScript for Footable (adding all funtions)
		$this->document->addScript(JURI::root() .'media/com_supportgroups/footable-v3/js/footable.min.js', (SupportgroupsHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/javascript');
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
				// must have itin milliseconds
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
		// Set the Time To Live To JavaScript
		$this->document->addScriptDeclaration("var expire = ". (int) $expire.";");
		// Set Language To Javascript
		$this->document->addScriptDeclaration("var backToGroup = '". JText::_('COM_SUPPORTGROUPS_RETURN_TO_GROUP')."';");
		$this->document->addScriptDeclaration("var backToDetails = '". JText::_('COM_SUPPORTGROUPS_DETAILS')."';");
		// add the document default css file
		$this->document->addStyleSheet(JURI::root(true) .'/components/com_supportgroups/assets/css/supportgroups.css', (SupportgroupsHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
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
			// Get Totals from Server
			function getTotals_server(){
				var getUrl = \"index.php?option=com_supportgroups&task=ajax.getTableTotals&format=json\";
				var request = 'token='+token+'&id=1';
				return jQuery.ajax({
					type: 'GET',
					url: getUrl,
					dataType: 'jsonp',
					data: request,
					jsonp: 'callback'
				});
			}
			// set Data to display
			function getTotals(){
				// first we see if we have local storage of this data
				var data = jQuery.jStorage.get('totals');
				if (!data) {
					getTotals_server().done(function(result) {
						if(result.html){
							setTotals(result.html);
							// store the data for next time
							jQuery.jStorage.set('totals',result.html,{TTL: expire});
						}
						else
						{
							setTotals(returnError);
						}
					});
				} else {
					setTotals(data);
					// make sure to keep the Time To Live updated
					jQuery.jStorage.setTTL('totals',expire);
				}
			} 
			// set the totals
			function setTotals(totals) {
				jQuery('#totals').html(totals);
			}
			// Get Data for Item Type & Id from Server
			function getItemData_server(id, type){
				var getUrl = \"index.php?option=com_supportgroups&task=ajax.getItemData&format=json&requestView=table\";
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
			// get Data to display
			function getItemData(key){
				// hide old data and add spnner
				jQuery('#info').hide();
				jQuery('#modal-spin').show();
				// first we see if we have local storage of this data
				var data = jQuery.jStorage.get(key);
				if (!data) {
					// set id type 
					var arr = key.split('__');
					getItemData_server(arr[1], arr[0]).done(function(result) {
						if(result.html){
							setItem(result.html, key);
							// store the data for next time
							jQuery.jStorage.set(key,result.html,{TTL: expire});
						}
						else
						{
							// set an error if item date could not return
							setItem(returnError, key);
						}
					});
				} else {
					setItem(data, key);
					// make sure to keep the Time To Live updated
					jQuery.jStorage.setTTL(key,expire);
				}
			} 
			// set the item
			function setItem(data, key) {
				jQuery('#info').html(data);
				// show data and hide spnner
				jQuery('#modal-spin').hide();
				jQuery('#info').show();
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
		$help_url = SupportgroupsHelper::getHelpUrl('supportgroups');
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
