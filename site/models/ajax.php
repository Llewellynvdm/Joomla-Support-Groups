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
	@subpackage		ajax.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.helper');

/**
 * Supportgroups Ajax Model
 */
class SupportgroupsModelAjax extends JModelList
{
	protected $app_params;

	public function __construct()
	{
		parent::__construct();
		// get params
		$this->app_params = JComponentHelper::getParams('com_supportgroups');

	}

	// Used in map
	/**
	* The Language Strings for Types
	* @var         array
	*/
	protected $langType;

	/**
	* The Type
	* @var         string
	*/
	protected $type;
	
	/**
	* The markers for the map
	* @var         boolean
	*/
	public $markers = false;

	/**
	* The Map Marker Image URLs
	* @var         string
	*/
	public $markerImages;

	/**
	* The Info Window Content in Map
	* @var         string
	*/
	public $windowContent;

	/**
	* The Map Details Blocks
	* @var         array
	*/
	protected $mapDetailsBlock = array();

	/**
	* Cluster Switch
	* @var         boolean
	*/
	public $cluster = false;

	/**
	* Get the Item Data For Type
	* 
	* @return    string    Formatted html string
	*/
	public function getItemData(&$id,&$type,&$requestView)
	{
		switch ($type)
		{
			case 'country':
				$this->country = $id;
			break;
			case 'region':
				$this->region = $id;
			break;
			case 'facility':
				$this->facility = $id;
			break;
			case 'area':
				$this->area = $id;
			break;
			case 'group':
				$this->group = $id;
			break;
			case 'groups':
				$input = array('facility' => 'int','area' => 'int','region' => 'int','country' => 'int');
				$where = array('facility' => 'facility','area' => 'area','region' => 'region','country' => 'country');
				$filtering = JFactory::getApplication()->input->getArray($input);
				foreach ($filtering as $key => $filter)
				{
					if ($filter)
					{
						$this->$where[$key] = (int) $filter;
					}
				}
			break;
		}
		// set the type
		$this->type = (string) $type;
		// insure the set of language tags are loaded
		$this->langType = array(
			'totals' => JText::_('COM_SUPPORTGROUPS_TOTALS'),
			'country' => JText::_('COM_SUPPORTGROUPS_COUNTRY'),
			'region' => JText::_('COM_SUPPORTGROUPS_REGION'),
			'facility' => JText::_('COM_SUPPORTGROUPS_FACILITY'),
			'area' => JText::_('COM_SUPPORTGROUPS_AREA'),
			'group' => JText::_('COM_SUPPORTGROUPS_GROUP'));
		// set the request view 
		$this->requestView = $requestView;
		// return the data as HTML
		return $this->getData($id);
	}
		
	/**
	* Get Data
	* 
	* @return    string    Formatted html string
	*/
	protected function getData(&$id)
	{
		// get group
		$groups = $this->getItems($id);
		if (isset($groups) && SupportgroupsHelper::checkArray($groups))
		{
			return $this->getDisplay($groups,$id);
		}
		return false;
	}
	
	/**
	* Get display of data
	* 
	* @return    string    Formatted html string
	*/
	protected function getDisplay(&$groups,&$id)
	{
		// init the Display builder
		$display = array();
		$displaying = false;
		// set some totals language
		$langKeys = array(
			'members' => '<b>'.JText::_('COM_SUPPORTGROUPS_TOTAL_MEMBERS').'</b>',
			'children' => '<b>'.JText::_('COM_SUPPORTGROUPS_TOTAL_CHILDREN').'</b>',
			'on_art' => '<b>'.JText::_('COM_SUPPORTGROUPS_TOTAL_ON_ART').'</b>',
			'male' => JText::_('COM_SUPPORTGROUPS_MALE'),
			'female' => JText::_('COM_SUPPORTGROUPS_FEMALE'),
			'male_children' => JText::_('COM_SUPPORTGROUPS_MALE_CHILDREN'),
			'female_children' => JText::_('COM_SUPPORTGROUPS_FEMALE_CHILDREN'),
			'male_art' => JText::_('COM_SUPPORTGROUPS_MALE_ON_ART'),
			'female_art' => JText::_('COM_SUPPORTGROUPS_FEMALE_ON_ART')
			);
		// sum the total groups
		$totalGroups = count($groups);
		// house cleaning per type
		switch ($this->type)
		{
			case 'totals':
			case 'country':
			case 'region':
			case 'facility':
			case 'area':
				// change layout if table
				if ($this->requestView == 'table' && 'totals' == $this->type)
				{
					// set the group total
					$display['totals'][JText::_('COM_SUPPORTGROUPS_TOTAL_GROUPS')] = $totalGroups;
				}
				else
				{
					// set the group total
					$display['totals'][] = '<b>'.JText::_('COM_SUPPORTGROUPS_TOTAL_GROUPS').'</b> <i class="uk-icon-angle-double-right uk-text-muted"></i> '.$totalGroups;
				}
				// set the totals
				$totals = $this->getTotals($id);
				if ($totals)
				{
					foreach ($langKeys as $key => $lang)
					{
						if (isset($totals->$key))
						{
							// change layout if table
							if ($this->requestView == 'table' && 'totals' == $this->type)
							{
								$display['totals'][$lang] = $totals->$key;
							}
							else
							{
								$display['totals'][] = $lang.' <i class="uk-icon-angle-double-right uk-text-muted"></i> '.$totals->$key;
							}
						}
					}
					if ('totals' == $this->type)
					{
						// check if a map page message has been set
						$totalMessage = $this->app_params->get('total_message', '<h2>'.JText::_('COM_SUPPORTGROUPS_GLOBAL_TOTALS_OF_ALL_GROUPS').'</h2>');
						$display['details'] = $totalMessage;
					}
				}
				// set groups & some details
				foreach ($groups as $nr => &$item)
				{
					// only add distance to facility
					$distance = '';
					if ('facility' == $this->type)
					{
						$from = explode(',',str_replace(array('(',')'),'',$item->marker));
						$to = explode(',',str_replace(array('(',')'),'',$item->facility_marker));
						if (isset($from[0]) && isset($from[1]) && isset($to[0]) && isset($to[1]))
						{
							$actualDistance = $this->getDistance($from[0],$from[1],$to[0],$to[1]);
							$facilityType = (isset($item->facility_type)) ? $item->facility_type : JText::_('COM_SUPPORTGROUPS_FACILITY');
							$distance = '<br /><i style="color: '.$item->area_color.';" class="uk-icon-taxi"></i> '
								.JText::_('COM_SUPPORTGROUPS_THIS_GROUP_IS').' <b>'.$actualDistance
								.'km</b> '.JText::sprintf('COM_SUPPORTGROUPS_FROM_S',strtolower($facilityType)).'.';
						}
					}
					if ('country' != $this->type && 'totals' != $this->type)
					{
						// we want to sort the groups by distance if it is a facility group
						if ('facility' == $this->type)
						{
								$display['groups'][$nr] = array();
								// build the groups array
								$display['groups'][$nr]['string'] = '<i style="color: '.$item->area_color
									.';" class="uk-icon-map-marker"></i> '.$item->name.' <i style="color: '.$item->area_color
									.';" class="uk-icon-phone"></i> '.$item->phone.' <i style="color: '.$item->area_color
									.';" class="uk-icon-users"></i> '.$item->members.$distance;
								// add the sort key
								if (isset($actualDistance))
								{
									// add the sort key
									$display['groups'][$nr]['order'] = $actualDistance;
								}
								else
								{
									$display['groups'][$nr]['order'] = (int) $item->members;
								}
						}
						else
						{
								// build the groups array
								$display['groups'][] = '<i style="color: '.$item->area_color
									.';" class="uk-icon-map-marker"></i> '.$item->name.' <i style="color: '.$item->area_color
									.';" class="uk-icon-phone"></i> '.$item->phone.' <i style="color: '.$item->area_color
									.';" class="uk-icon-users"></i> '.$item->members;
						}
					}
					// some details that we need to only set once (never for totals)
					if ('totals' != $this->type && !isset($display['details']))
					{
						$display['details'] = $this->setDetailDisplay($item);
					}
				}
			break;
			case 'group':
				$display['details'] = $this->setDetailDisplay($groups[0]);
			break;
			case 'groups':
				return $this->getMapData($groups);
			break;
		}
		// only set details if details display was build
		if (isset($display['details']))
		{
			$displaying .= $display['details'] ;
		}
		// only set totals Un ordered list if total display was build
		if (isset($display['totals']) && SupportgroupsHelper::checkArray($display['totals']))
		{
			// change layout if table
			if ($this->requestView == 'table' && 'totals' == $this->type)
			{
				$tableStart = '<table class="uk-table uk-table-hover uk-table-condensed"><caption>'.$display['details'].'</caption>';
				$tableHead = '<thead><tr>';
				$tableBody = '<tbody><tr>';
				// set the totals layout for table view
				foreach ($display['totals'] as $lang => $vTotal)
				{
					$tableHead .= '<th>'.$lang.'</th>';
					$tableBody .= '<td>'.$vTotal.'</td>';
				}
				$tableHead .= '</tr></thead>';
				$tableBody .= '</tr></tbody>';
				$tableEnd = '</table>';
				// set to display
				$displaying = $tableStart.$tableHead.$tableBody.$tableEnd;
			}
			else
			{
				$displaying .= '<ul class="uk-list uk-list-striped"><li>'.implode('</li><li>',$display['totals']).'</li></ul>';
			}
		}
		// only set groups Un ordered list if group display was build
		if (isset($display['groups']) && SupportgroupsHelper::checkArray($display['groups']))
		{
			$displaying .= $this->setGroupsDisplay($display['groups'],$groups[0],$totalGroups);
		}
		// return display if it has been set
		if ($displaying && SupportgroupsHelper::checkString($displaying))
		{
			if ($this->requestView != 'table')
			{
				// set nav menu
				switch ($this->type)
				{
					case 'facility':
						$displaying = '<div class="'.$this->type.'-menu"><button type="button" class="uk-button uk-button-success uk-button-small uk-width-1-1" onclick="goTo(backType+\'_\'+backTo+\'_nav\');"><i class="uk-icon-arrow-left"></i> '.JText::_('COM_SUPPORTGROUPS_BACK_TO_GROUP').'</button></div>'.$displaying;
						break;
					case 'country':
					case 'region':
					case 'area':
						$displaying = '<div class="'.$this->type.'-menu"><button type="button" class="uk-button uk-button-success uk-button-small uk-width-1-1" onclick="setMapData(backTo,backType);"><i class="uk-icon-arrow-left"></i> '.JText::_('COM_SUPPORTGROUPS_BACK_TO_GROUP').'</button></div>'.$displaying;
						break;
					case 'group':					
							$displaying = '<button type="button" class="uk-button uk-button-success uk-button-small uk-width-1-1" onclick="setMapData(mainBackTo,mainBackType);resetMap();"><i class="uk-icon-arrow-left"></i> '.JText::_('COM_SUPPORTGROUPS_BACK_TO_START').'</button>'.$displaying;
						break;
				}
			}
			return array('html' => $displaying);
		}
		return false;
	}

	/**
	* set Details Display
	*
	* @params      object   $item The group item
	*
	* @ return     string    The details in html
	*
	*/
	protected function setDetailDisplay(&$item)
	{
		// set the facility marker if needed
		$facility_marker = false;
		// check if the facility has an marker set
		if (isset($item->facility_marker) && (strpos($item->facility_marker,')') !== false))
		{
			$facility_marker = (string) str_replace(array('(',')'),'',$item->facility_marker);
		}
		if ('group' == $this->type)
		{
			// reset display builder
			$display = array();
			// details settings
			$setDetails = array(
				'phone' => array('icon' => 'phone', 'lang' =>  JText::_('COM_SUPPORTGROUPS_PHONE'), 'onclick' => false ),
				'area_name' => array('icon' => 'map-marker', 'lang' =>  JText::_('COM_SUPPORTGROUPS_AREA'), 'onclick' => 'area' ),
				'region_name' => array('icon' => 'map-signs', 'lang' =>  JText::_('COM_SUPPORTGROUPS_REGION'), 'onclick' => 'region' ),
				'facility_name' => array('icon' => 'medkit', 'lang' =>  JText::_('COM_SUPPORTGROUPS_FACILITY'), 'onclick' => 'facility' ),
				'country_name' => array('icon' => 'flag', 'lang' =>  JText::_('COM_SUPPORTGROUPS_COUNTRY'), 'onclick' => 'country' )
			);
			// check for details
			$details = '';
			if (isset($item->details) && SupportgroupsHelper::checkString($item->details) && $this->requestView != 'table')
			{
				$randomKey = SupportgroupsHelper::randomkey(8);
				$details =  '<button class="uk-button uk-button-primary uk-width-1-1 uk-margin-small-bottom uk-button-mini" data-uk-modal="{target:\'#'.$randomKey.'\'}">'
				.JText::_('COM_SUPPORTGROUPS_MORE_DETAILS').'</button>'
				. '<div id="'.$randomKey.'" class="uk-modal">'
				. '<div class="uk-modal-dialog">'
				. '<a class="uk-modal-close uk-close"></a>'
				. '<button type="button" onclick="printMe(\''.JFactory::getConfig()->get( 'sitename' ).'\', \''.$randomKey.'_print\')" class="uk-button uk-button-primary uk-button-mini uk-align-center"><i class="uk-icon-print"></i> '.JText::_('COM_SUPPORTGROUPS_PRINT_PREVIEW').'</button>'
				. '<div id="'.$randomKey.'_print"><div class="uk-modal-header"><h1>'.$item->name.' - '.JText::_('COM_SUPPORTGROUPS_DETAILS').'</h1></div>'
				. '<div class="uk-overflow-container">'
				. $item->details
				. '</div></div></div></div>';
			}
			// set Additional Info
			$details .= $this->setAdditionalInfo($item);
			foreach ($setDetails as $key => $set)
			{
				// add goTo function
				if ('facility_name' == $key && $facility_marker && $this->requestView != 'table')
				{				
					$theOnclick = 'goTo(\'facility_'.$item->facility_id.'_nav\');';
				}
				elseif ($set['onclick'])
				{
					// set Map Data Action
					$theOnclick = 'setMapData('.$item->{$set['onclick'].'_id'}.',\''.$set['onclick'].'\');';
				}
				else
				{
					// no goTo Action
					$theOnclick = '';
				}
				// update lang for facility if needed
				if ('facility_name' == $key && isset($item->facility_type))
				{
					$set['lang'] = $item->facility_type;
				}
				// update lang for area if needed
				if ('area_name' == $key && isset($item->area_type))
				{
					$set['lang'] = $item->area_type;
				}
				// don't add these onlclick if table view
				if ($this->requestView == 'table')
				{
					$onclick = ''; $onclicka = '';
				}
				else
				{
					// only set the onclick event if needed
					$onclick = ($set['onclick']) ? '<a href="javascript:void(0)" onclick="'.$theOnclick.'">' : '';
					$onclicka = ($set['onclick']) ? '</a>' : '';
				}
				// now load the display array
				$display[] = '<i style="color: '.$item->area_color.';" class="uk-icon-'.$set['icon'].'"></i> '
					.$set['lang'].' <i class="uk-icon-angle-double-right uk-text-muted"></i> <b class="uk-text-bold">'.$onclick.$item->$key.$onclicka.'</b>';
			}
			if ($this->requestView == 'table')
			{
				return '<h2 style="color: '.$item->area_color
					.';">'.$item->name
					.'</h2><ul class="uk-list uk-list-striped"><li>'.implode('</li><li>',$display).'</li></ul>';
			}
			return '<h2><a  style="color: '.$item->area_color
				.';" href="javascript:void(0)"  onclick="goTo('
				.'\'group_'.$item->id.'_nav\')" data-uk-tooltip  title="'.JText::_('COM_SUPPORTGROUPS_GO_TO_GROUP_ON_MAP').'">'.$item->name
				.'</a></h2>'.$details.'<ul class="uk-list uk-list-striped"><li>'.implode('</li><li>',$display).'</li></ul>';
		}
		// add facility type to name
		if ($this->requestView != 'group' && isset($item->facility_name) && isset($item->facility_type))
		{
			$item->facility_name = $item->facility_name .' - '. $item->facility_type;
		}
		// add are type to name
		if ($this->requestView != 'group' && isset($item->area_name) && isset($item->area_type))
		{
			$item->area_name = $item->area_name .' - '. $item->area_type;
		}
		// reset display builder
		$display = '';
		// icon settings
		$setIcons = array(
			'area' => 'map-marker',
			'region' => 'map-signs',
			'facility' => 'medkit',
			'country' => 'flag');
			// setup som lan globals
		$this->facilityType = (isset($item->facility_type)) ? $item->facility_type : JText::_('COM_SUPPORTGROUPS_FACILITY');
		$this->areaType = (isset($item->area_type)) ? $item->area_type : JText::_('COM_SUPPORTGROUPS_AREA');
		// setup facility Notice
		$facilityNotice = ('facility' == $this->type) ? '<p>'.JText::sprintf('COM_SUPPORTGROUPS_SUPPORT_GROUPS_ACCESSING_THIS_S_REPORT_THE_FOLLOWING',strtolower($this->facilityType)).'.</p>':'';
		$goToFacility = JText::sprintf('COM_SUPPORTGROUPS_GO_TO_S_ON_MAP', strtolower($this->facilityType));
		// add goTo function for facility
		if ('facility' == $this->type && $facility_marker && $this->requestView != 'table')
		{
			$display = '<h2><a href="javascript:void(0)" style="color: '.$item->area_color.';"  onclick="goTo('
				.'\'facility_'.$item->facility_id.'_nav\')" data-uk-tooltip  title="'.$goToFacility
				.'"><i class="uk-icon-'.$setIcons[$this->type].'"></i> '.$item->{$this->type.'_name'}.'</a></h2>'; 
		}
		// add facility type to name
		elseif ('facility' == $this->type && isset($item->facility_type))
		{
			$display = '<h2><a href="javascript:void(0)" style="color: '.$item->area_color.';" '
				.' data-uk-tooltip  title="'.$goToFacility
				.'"><i class="uk-icon-'.$setIcons[$this->type].'"></i> '.$item->{$this->type.'_name'}.'</a></h2>'; 
		}
		// add area type to name
		elseif ('area' == $this->type)
		{
			// no goTo Action
			$display = '<h2 style="color: '.$item->area_color.';"  data-uk-tooltip title="'.$this->areaType
				.'"><i class="uk-icon-'.$setIcons[$this->type].'"></i> '.$item->{$this->type.'_name'}.'</h2>';  
		}
		else
		{
			// no goTo Action
			$display = '<h2 style="color: '.$item->area_color.';"  data-uk-tooltip title="'.$this->langType[$this->type]
				.'"><i class="uk-icon-'.$setIcons[$this->type].'"></i> '.$item->{$this->type.'_name'}.'</h2>'; 
		}
		// check for phone
		if (isset($item->{$this->type.'_phone'}) && SupportgroupsHelper::checkString($item->{$this->type.'_phone'}))
		{
			$display .= '<i style="color: '.$item->area_color.';" class="uk-icon-phone"></i> '
					.JText::_('COM_SUPPORTGROUPS_PHONE').' <i class="uk-icon-angle-double-right uk-text-muted"></i> <b class="uk-text-bold">'.$item->{$this->type.'_phone'}.'</b>';
		}
		// check for details
		if (isset($item->{$this->type.'_details'}) && SupportgroupsHelper::checkString($item->{$this->type.'_details'}) && $this->requestView != 'table')
		{
			$randomKey = SupportgroupsHelper::randomkey(8);
			$display .=  '<button class="uk-button uk-button-primary uk-width-1-1 uk-margin-small-bottom uk-button-mini" data-uk-modal="{target:\'#'.$randomKey.'\'}">'
			.JText::_('COM_SUPPORTGROUPS_MORE_DETAILS').'</button>'
			. '<div id="'.$randomKey.'" class="uk-modal">'
			. '<div class="uk-modal-dialog">'
			. '<a class="uk-modal-close uk-close"></a>'
			. '<button type="button" onclick="printMe(\''.JFactory::getConfig()->get( 'sitename' ).'\', \''.$randomKey.'_print\')" class="uk-button uk-button-primary uk-button-mini uk-align-center"><i class="uk-icon-print"></i> '.JText::_('COM_SUPPORTGROUPS_PRINT_PREVIEW').'</button>'
			. '<div id="'.$randomKey.'_print"><div class="uk-modal-header"><h1>'.$item->{$this->type.'_name'}.' - '.JText::_('COM_SUPPORTGROUPS_DETAILS').'</h1></div>'
			. '<div class="uk-overflow-container">'
			. $item->{$this->type.'_details'}
			. '</div></div></div></div>';
		}
		return $display.$facilityNotice;
	}

	/**
	* Build the Additional Info display block
	*
	* @params    object    $object The Support Group Values
	*
	* @ return     string     The Value String
	*
	*/
	public function setAdditionalInfo(&$object)
	{
		if (isset($object->additionalInfo) && SupportgroupsHelper::checkArray($object->additionalInfo))
		{
			$strings = array();
			$div = '';
			foreach ($object->additionalInfo as $additionalInfo)
			{
				$randomKey = SupportgroupsHelper::randomkey(8);
				$strings[$additionalInfo->type.'__'.$additionalInfo->name] = array();
				$strings[$additionalInfo->type.'__'.$additionalInfo->name]['string'] = '<i style="color: '.$object->area_color.';" class="uk-icon-certificate"></i> <a href="#'.$randomKey.'" data-uk-offcanvas>'.$additionalInfo->name.'</a>';
				$strings[$additionalInfo->type.'__'.$additionalInfo->name]['type'] = '<b>'. SupportgroupsHelper::safeString($additionalInfo->type,'W').'</b>';
				if (!isset($additionalInfo->details) || !SupportgroupsHelper::checkString($additionalInfo->details))
				{
					$additionalInfo->details = JText::_('COM_SUPPORTGROUPS_NO_DETAILS_HAVE_BEEN_SET_PLEASE_CHECK_AGAIN_LATTER');
				}
				$div .= '<div id="'.$randomKey.'" class="uk-offcanvas"><div class="uk-offcanvas-bar"><div class="uk-panel"><div class="uk-panel-badge uk-badge">'.$additionalInfo->type.'</div><h3 class="uk-panel-title">'.$additionalInfo->name.'</h3>'.$additionalInfo->details.'</div></div></div>';
			}
			// now sort the capacities
			ksort($strings);
			// now build display
			$display = array();
			foreach ($strings as $string)
			{
				if (!isset($display[$string['type']]))
				{
					$display[$string['type']] = '<dt>'.$string['type'].'</dt><dd><ul class="uk-list">';
				}
				$display[$string['type']] .= '<li>'.$string['string'].'</li>';
			}
			return '<dl class="uk-description-list-line">'.implode('</ul></dd>',$display).'</ul></dd></dl>'.$div;
		}
		return '';
	}

	/**
	* set Groups Display
	*
	* @params      array   $groups The groups array
	*
	* @ return     string    The groups html
	*
	*/
	protected function setGroupsDisplay(&$groups,&$item,$totalGroups)
	{
		$randomKey = SupportgroupsHelper::randomkey(8);
		// setup the group total in title
		$totalGroups = ($totalGroups) ? '<span data-uk-tooltip title="'.JText::_('COM_SUPPORTGROUPS_TOTAL_RELATED_GROUPS').'">('.$totalGroups.')</span> ' : '';
		// setup facility Notice
		$this->facilityType = (isset($this->facilityType)) ? $this->facilityType : JText::_('COM_SUPPORTGROUPS_FACILITY');
		$facilityNotice = ('facility' == $this->type) ? '<p>'.JText::sprintf('COM_SUPPORTGROUPS_SUPPORT_GROUPS_ACCESSING_THIS_S',strtolower($this->facilityType)).'.</p>':'';
		// insure we sort the groups by distance if type is facility
		if ('facility' == $this->type)
		{
			// sort the groups
			function sortByOrder($a, $b)
			{
				return $a['order'] - $b['order'];
			}
			usort($groups, "sortByOrder");
			// make sure we are back with single array
			$buketGroups = array();
			foreach ($groups as $group)
			{
				$buketGroups[] = $group['string'];
			}
			$groups = $buketGroups;
		}
		// start the construction of the group display
		if ($this->requestView == 'table')
		{
			// if table view return Unorderd List
			return '<h2>'.$totalGroups.JText::_('COM_SUPPORTGROUPS_RELATED_GROUPS').'</h2>'.$facilityNotice
			. '<ul class="uk-list uk-list-striped"><li>'.implode('</li><li>',$groups).'</li></ul>';	
		}
		// update the lang if needed
		if ('facility' == $this->type)
		{
			$this->langType[$this->type] = $this->facilityType;
		}
		$this->areaType = (isset($this->areaType)) ? $this->areaType : JText::_('COM_SUPPORTGROUPS_AREA');
		if ('area' == $this->type)
		{
			$this->langType[$this->type] = $this->areaType;
		}
		// if not table view return modal
		return '<button class="uk-button uk-width-1-1 uk-margin-small-bottom" data-uk-modal="{target:\'#'.$randomKey.'\'}">'.$totalGroups.JText::_('COM_SUPPORTGROUPS_RELATED_GROUPS').'</button>'
			. '<div id="'.$randomKey.'" class="uk-modal">'
			. '<div class="uk-modal-dialog">'
			. '<a class="uk-modal-close uk-close"></a>'
			. '<button type="button" onclick="printMe(\''.JFactory::getConfig()->get( 'sitename' ).'\', \''.$randomKey.'_print\')" class="uk-button uk-button-primary uk-button-mini uk-align-center"><i class="uk-icon-print"></i> '.JText::_('COM_SUPPORTGROUPS_PRINT_PREVIEW').'</button>'
			. '<div id="'.$randomKey.'_print"><div class="uk-modal-header"><h1>'.$item->{$this->type.'_name'}.' - '.JText::_('COM_SUPPORTGROUPS_GROUPS').'</h1></div>'
			. '<div class="uk-overflow-container">'.$facilityNotice
			. '<ul class="uk-list uk-list-striped"><li>'.implode('</li><li>',$groups).'</li></ul>'
			. '</div></div></div></div>';		
	}

	/**
	* Clustering control method
	*
	* @params       object    $items The Support Group Values
	*
	* @return       void
	*
	*/
	protected function setClustering(&$items)
	{
		$cluster = $this->app_params->get('cluster', null);
		if ($cluster)
		{
			$cluster_at = $this->app_params->get('cluster_at', 300);
			$total = count($items);
			if ($cluster_at <= $total)
			{
				$this->cluster = true;
			}
		}
	}
	
	/**
	* get Map Data (javascript)
	*
	* @params       object    $items The Support Group Values
	*
	* @return       void
	*
	*/
	protected function getMapData(&$items)
	{
		// check the clustering option
		$this->setClustering($items);
		// now set the map Data
		$this->setMapData($items);
		// do some hous cleaning
		$clusterOptions = '';
		if ($this->cluster)
		{
			$clusterOptions = '// Options for the clusterer
				var clusterOptions = {gridSize: '.$this->app_params->get('clustergridsize', 100).', maxZoom: '.$this->app_params->get('clustermaxzoom', 7).', imagePath: "'.JURI::root() .'media/com_supportgroups/js-marker-cluster/images/m'.'"};
				// Cluster the markers
				var markerClusterer = new MarkerClusterer(map, markersArray, clusterOptions);';
		}
		// start building the javascript
		return '<script type="text/javascript">	
			// make map global
			var map;

			// the marker array
			var markersArray = [];
			var markersIndex = [];
			var markers = [];
			
			// start-up the Map
			function initialize() {

				// make sure we reset these
				markersArray = [];
				markersIndex = [];
				markers = [];

				var bounds = new google.maps.LatLngBounds();
				var mapOptions = {
					mapTypeId: \''.$this->app_params->get('maptype', 'roadmap').'\'
				};

				// Display a map on the page
				map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
				map.setTilt(45);

				// Multiple Marker Images
			       '.$this->markerImages.'

			       // Multiple Markers
			       '.$this->markers.'

				// Info Window Content
				'.$this->windowContent.'

				// Display multiple markers on a map
				var infoWindow = new google.maps.InfoWindow(), marker, i;

				// Loop through our array of markers & place each one on the map  
				for( i = 0; i < markers.length; i++ ) {
					var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
					bounds.extend(position);
					marker = new google.maps.Marker({
						position: position,
						map: map,
						icon: markers[i][3]
					});

					// Allow each marker to have an info window    
					google.maps.event.addListener(marker, \'click\', (function(marker, i) {
						return function() {
							infoWindow.setContent(infoWindowContent[i][0]);
							infoWindow.open(map, marker);
							// get Group data
							setMapData(infoWindowContent[i][1],infoWindowContent[i][2]);
						}
					})(marker, i));

					// add to array
					markersArray.push(marker);
					markersIndex.push(infoWindowContent[i][2]+\'_\'+infoWindowContent[i][1]+\'_nav\');

					// Automatically center the map fitting all markers on the screen
					map.fitBounds(bounds);
				}

			       '.$clusterOptions.'

				// Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
				var boundsListener = google.maps.event.addListener((map), \'bounds_changed\', function(event) {
					this.setZoom('.$this->app_params->get('maxzoom', 6).');
					google.maps.event.removeListener(boundsListener);
				});
				// hide the main menu if needed
				jQuery(\'.\'+mainBackType+\'-menu\').hide();
    
			}
			// set go to marker function
			function getIndex(value){
				indexes = jQuery.map(markersIndex, function(obj, index) {
				    if(obj == value) {
					return index;
				    }
				})
				return indexes[0];
			}
			// trigger the marker info window
			function clickMarker(index){
				google.maps.event.trigger(markersArray[index], \'click\');
			}
			// set go to marker function
			function resetMap(){
				initialize();
			}
			// set go to marker function
			function goTo(id){
				if (id)
				{
					// open info window
					var index = getIndex(id);
					if (index)
					{
						// set new location
						var newLatLng = new google.maps.LatLng(markers[index][1], markers[index][2]);
						map.setCenter(newLatLng);
						// get nice and close
						map.setZoom('.$this->app_params->get('gotozoom', 8).');
						setTimeout(function(){
								clickMarker(index);
						}, 500);
					}
				}
			}
			</script>';
	}

	/**
	* set Map Data
	*
	* @params       object    $items The Support Group Values
	*
	* @return       void
	*
	*/
	protected function setMapData(&$items)
	{
		// reset all buckets
		$markersBucket = array();
		$markerImagesBucket = array();
		$contentBucket = array();
		$facilityMarkers = array();
		foreach ($items as $nr => &$item)
		{
			// build the facility markers array
			if (isset($item->facility_alias) && !isset($facilityMarkers[$item->facility_id]) && isset($item->facility_marker) && (strpos($item->facility_marker,')') !== false))
			{
				$facilityMarkers[$item->facility_id] = new stdClass;
				// fix the marker string and load the array
				$facilityMarkers[$item->facility_id]->id = $item->facility_id;
				$facilityMarkers[$item->facility_id]->marker = str_replace(array('(',')'),'',$item->facility_marker);
				$facilityMarkers[$item->facility_id]->name = $this->clean($item->facility_name).' - '.$this->clean($item->facility_type);
				$facilityMarkers[$item->facility_id]->alias = $item->facility_alias;
				$facilityMarkers[$item->facility_id]->phone = $item->facility_phone;
			}
			// check if group has marker
			if (isset($item->marker) && (strpos($item->marker,')') !== false))
			{
				if (strpos($item->area_color,'#') === false)
				{
					$item->area_color = '53B5DE';
				}
				else
				{
					// fix the color string
					$item->area_color = trim($item->area_color, '#');
				}
				// set the code name
				$codeName = SupportgroupsHelper::safeString($item->alias);
				// fix the marker string
				$item->marker = str_replace(array('(',')'),'',$item->marker);
				// marker key
				$A = strtoupper($codeName[0]);
				$markerKey = $A.$item->area_color;
				// load the marker images
				$markerImagesBucket[$markerKey] = "var ".$markerKey."Icon = ".
				"'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=".$A."|".$item->area_color."|000000';";
				// load the markers
				$markersBucket[] = "['".$this->clean($item->name)."', $item->marker, ".$markerKey."Icon]";
				$contentBucket[] = "['<div class=\"info_content\"><h3 style=\"color: #".$item->area_color.";\">".$this->clean($item->name)."</h3>".$this->getMapDetailsBlock($item)."</div>', ".$item->id.", 'group']";
			}
		}
		// now build the facility markers
		if (SupportgroupsHelper::checkArray($facilityMarkers))
		{
			// load the marker images
			$markerImagesBucket['facilityIcon'] = "var facilityIcon = '".JURI::root().
				"media/com_supportgroups/images/facility.png';";
			foreach ($facilityMarkers as $item)
			{
				// set the code name
				$codeName = SupportgroupsHelper::safeString($item->alias);
				// load the markers
				$markersBucket[] = "['".$item->name."', $item->marker, facilityIcon]";
				// check for details 
				$details = '';
				if (SupportgroupsHelper::checkString($item->phone))
				{
					$details = '<b>'.JText::_('COM_SUPPORTGROUPS_PHONE').'</b>: '.$item->phone;
				}
				$contentBucket[] = "['<div class=\"info_content\"><h3>&#10010 ".$item->name."</h3>".$details."</div>', ".$item->id.", 'facility']";
			}
		}
		// now set the markers to map
		if (SupportgroupsHelper::checkArray($markersBucket))
		{
			$this->markerImages = implode("\n    ",$markerImagesBucket);
		    	$this->markers = 'markers = ['.implode(',',$markersBucket).'];';
			$this->windowContent = 'var infoWindowContent = ['.implode(',',$contentBucket).'];';
		}
	}


	/**
	* set Map Details Block
	*
	* @params    object    $object The Support Group Values
	*
	* @return       string     The Value String
	*
	*/
	protected function setMapDetailsBlock(&$object)
	{
		if (!isset($this->mapDetailsBlock[$object->id]))
		{
			// the display
			$diplay = array();
			// the array for the details block
			$detailsBlock = array(
				'male' => JText::_('COM_SUPPORTGROUPS_MALES'),
				'female' => JText::_('COM_SUPPORTGROUPS_FEMALES'),
				'male_children' => JText::_('COM_SUPPORTGROUPS_MALE_CHILDREN'),
				'female_children' => JText::_('COM_SUPPORTGROUPS_FEMALE_CHILDREN'),
				'male_art' => JText::_('COM_SUPPORTGROUPS_MALES_ON_ART'),
				'female_art' => JText::_('COM_SUPPORTGROUPS_FEMALES_ON_ART'),
				'members' => JText::_('COM_SUPPORTGROUPS_TOTAL_MEMBERS'),
				'children' => JText::_('COM_SUPPORTGROUPS_TOTAL_CHILDREN'),
				'on_art' => JText::_('COM_SUPPORTGROUPS_TOTAL_ON_ART'));
			$special = array('on_art' => 'members', 'male_art' => 'male', 'female_art' => 'female');
			// loop given details block array
			foreach ($detailsBlock as $key => $lang)
			{
				if (isset($object->$key))
				{
					if (isset($special[$key]))
					{
						$temp = round(($object->$key * 100) / $object->{$special[$key]});
						if ($temp > 100)
						{
							$temp = 100;
						}
						$diplay[] = $lang.': '.$temp.'%';
					}
					else
					{
						// now build the display
						$diplay[] = $lang.': '.$object->$key;
					}
				}
			}
			// This is the details block
 			$this->mapDetailsBlock[$object->id] = '<ul><li>'.implode('</li><li>',$diplay).'</li></ul>';
		}
		return $this->mapDetailsBlock[$object->id];
	}

	/**
	* get Map Details Block
	*
	* @params    object    $object The Support Group Values
	*
	* @return       string     The Value String
	*
	*/
	protected function getMapDetailsBlock(&$object)
	{
		if (!isset($this->mapDetailsBlock[$object->id]))
		{
 			return $this->setMapDetailsBlock($object);
		}
		return $this->mapDetailsBlock[$object->id];
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
	protected function clean($string,$tags = true)
	{
		return $string;
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
	* Get Totals
	*
	* @params      int       $id The target id
	*
	* @ return     string    The totals object
	*
	*/
	protected function getTotals(&$id)
	{
		if (!isset($this->totals[$this->type][$id]))
		{
			// check if we already have set the totals Globally
			$this->totals[$this->type][$id] = SupportgroupsHelper::getTotals($this->type,$id);
		}
		// check again just to make sure
		if (isset($this->totals[$this->type][$id]) && SupportgroupsHelper::checkArray($this->totals[$this->type][$id]))
		{
			// the totals object
			$results = new stdClass;
			// the totals get array
			$keys = array('members','children','on_art','male','female','male_children','female_children','male_art','female_art');
			$special = array('on_art','male_art','female_art');
			foreach ($keys as $key)
			{
				if (isset($this->totals[$this->type][$id][$key]))
				{
					if (in_array($key,$special))
					{
						$results->$key = round((array_sum($this->totals[$this->type][$id][$key]) * 100) / array_sum($this->totals[$this->type][$id][$key.'_persent']));
						if ($results->$key > 100)
						{
							$results->$key = 100;
						}
						$results->$key = $results->$key.'%';
					}
					else
					{
						$results->$key = (int) array_sum($this->totals[$this->type][$id][$key]);
					}
				}
			}
			return $results;
		}
		return false;
	}
	
	/**
	* Calculates the great-circle distance between two points, with
	* the Vincenty formula.
	*
	* @param     float   $latitudeFrom   Latitude of start point in [deg decimal]
	* @param     float   $longitudeFrom  Longitude of start point in [deg decimal]
	* @param     float   $latitudeTo     Latitude of target point in [deg decimal]
	* @param     float   $longitudeTo    Longitude of target point in [deg decimal]
	* @param     string  $type           The type of return value [km|m|mi]
	* @param     float   $earthRadius    Mean earth radius in [m|mi|other]
	*
	* @return float Distance between points in [$type] (same as earthRadius)
	*/
	protected function getDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $type = 'km', $earthRadius = 6371000)
	{
		$type = strtolower($type);
		if ('km' == $type || 'm' == $type) //  for kilometer & meter
		{
			$earthRadius = 6371000;
		}
		elseif ('mi' == $type) //  for miles
		{
			$earthRadius = 3959;
		}
		// convert from degrees to radians
		$latFrom = deg2rad($latitudeFrom);
		$lonFrom = deg2rad($longitudeFrom);
		$latTo = deg2rad($latitudeTo);
		$lonTo = deg2rad($longitudeTo);

		$lonDelta = $lonTo - $lonFrom;
		$a = pow(cos($latTo) * sin($lonDelta), 2) +
		pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
		$b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

		$angle = atan2(sqrt($a), $b);
		// set distance
		$distance = $angle * $earthRadius;
		if ('km' == $type)
		{
			$km = bcdiv($distance,1000);
			if (0 == $km)
			{
				return '0.'.floor($distance);
			}
			return $km;
		}
		return $distance;
	}

	/**
	 * Filter data switches.
	 *
	 * @var        strings
	 */
	protected $totals = array();
	protected $country = false;
	protected $region = false;
	protected $area = false;
	protected $facility = false;
	protected $group = false;

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Get the current user for authorisation checks
		$this->user		= JFactory::getUser();
		$this->levels		= $this->user->getAuthorisedViewLevels();
		// Make sure all records load, since no pagination allowed.
		$this->setState('list.limit', 0);
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		if ($this->type == 'groups')
		{
			// Get from #__supportgroups_support_group as a
			$query->select($db->quoteName(
				array('a.id','a.name','a.alias','a.phone','a.male','a.male_children','a.male_art','a.female','a.female_children','a.female_art','a.marker','a.published','a.ordering'),
				array('id','name','alias','phone','male','male_children','male_art','female','female_children','female_art','marker','published','ordering')));
			$query->from($db->quoteName('#__supportgroups_support_group', 'a'));

			// Get from #__supportgroups_area as b
			$query->select($db->quoteName(
				array('b.id','b.name','b.alias','b.color'),
				array('area_id','area_name','area_alias','area_color')));
			$query->join('LEFT', ($db->quoteName('#__supportgroups_area', 'b')) . ' ON (' . $db->quoteName('a.area') . ' = ' . $db->quoteName('b.id') . ')');

			// Get from #__supportgroups_area_type as g
			$query->select($db->quoteName(
				array('g.name'),
				array('area_type')));
			$query->join('LEFT', ($db->quoteName('#__supportgroups_area_type', 'g')) . ' ON (' . $db->quoteName('g.id') . ' = ' . $db->quoteName('b.area_type') . ')');

			// Get from #__supportgroups_region as c
			$query->select($db->quoteName(
				array('c.id','c.alias','c.name'),
				array('region_id','region_alias','region_name')));
			$query->join('LEFT', ($db->quoteName('#__supportgroups_region', 'c')) . ' ON (' . $db->quoteName('b.region') . ' = ' . $db->quoteName('c.id') . ')');

			// Get from #__supportgroups_country as d
			$query->select($db->quoteName(
				array('d.id','d.name','d.alias'),
				array('country_id','country_name','country_alias')));
			$query->join('LEFT', ($db->quoteName('#__supportgroups_country', 'd')) . ' ON (' . $db->quoteName('c.country') . ' = ' . $db->quoteName('d.id') . ')');

			// Get from #__supportgroups_facility as e
			$query->select($db->quoteName(
				array('e.id','e.alias','e.name','e.phone','e.marker'),
				array('facility_id','facility_alias','facility_name','facility_phone','facility_marker')));
			$query->join('LEFT', ($db->quoteName('#__supportgroups_facility', 'e')) . ' ON (' . $db->quoteName('a.facility') . ' = ' . $db->quoteName('e.id') . ')');

			// Get from #__supportgroups_facility_type as f
			$query->select($db->quoteName(
				array('f.name'),
				array('facility_type')));
			$query->join('LEFT', ($db->quoteName('#__supportgroups_facility_type', 'f')) . ' ON (' . $db->quoteName('f.id') . ' = ' . $db->quoteName('e.facility_type') . ')');
		}
		else
		{
			// Get from #__supportgroups_support_group as a
			$query->select($db->quoteName(
				array('a.id','a.name','a.alias','a.phone','a.info','a.male','a.male_children','a.male_art','a.female','a.female_children','a.female_art','a.marker','a.published','a.ordering','a.details'),
				array('id','name','alias','phone','info','male','male_children','male_art','female','female_children','female_art','marker','published','ordering','details')));
			$query->from($db->quoteName('#__supportgroups_support_group', 'a'));

			// Get from #__supportgroups_area as b
			$query->select($db->quoteName(
				array('b.id','b.name','b.alias','b.color','b.details'),
				array('area_id','area_name','area_alias','area_color','area_details')));
			$query->join('LEFT', ($db->quoteName('#__supportgroups_area', 'b')) . ' ON (' . $db->quoteName('a.area') . ' = ' . $db->quoteName('b.id') . ')');

			// Get from #__supportgroups_area_type as g
			$query->select($db->quoteName(
				array('g.name'),
				array('area_type')));
			$query->join('LEFT', ($db->quoteName('#__supportgroups_area_type', 'g')) . ' ON (' . $db->quoteName('g.id') . ' = ' . $db->quoteName('b.area_type') . ')');

			// Get from #__supportgroups_region as c
			$query->select($db->quoteName(
				array('c.id','c.alias','c.name'),
				array('region_id','region_alias','region_name')));
			$query->join('LEFT', ($db->quoteName('#__supportgroups_region', 'c')) . ' ON (' . $db->quoteName('b.region') . ' = ' . $db->quoteName('c.id') . ')');

			// Get from #__supportgroups_country as d
			$query->select($db->quoteName(
				array('d.id','d.name','d.alias','d.codethree','d.codetwo'),
				array('country_id','country_name','country_alias','country_codethree','country_codetwo')));
			$query->join('LEFT', ($db->quoteName('#__supportgroups_country', 'd')) . ' ON (' . $db->quoteName('c.country') . ' = ' . $db->quoteName('d.id') . ')');

			// Get from #__supportgroups_facility as e
			$query->select($db->quoteName(
				array('e.id','e.alias','e.name','e.phone','e.details','e.marker'),
				array('facility_id','facility_alias','facility_name','facility_phone','facility_details','facility_marker')));
			$query->join('LEFT', ($db->quoteName('#__supportgroups_facility', 'e')) . ' ON (' . $db->quoteName('a.facility') . ' = ' . $db->quoteName('e.id') . ')');

			// Get from #__supportgroups_facility_type as f
			$query->select($db->quoteName(
				array('f.name'),
				array('facility_type')));
			$query->join('LEFT', ($db->quoteName('#__supportgroups_facility_type', 'f')) . ' ON (' . $db->quoteName('f.id') . ' = ' . $db->quoteName('e.facility_type') . ')');
		}
		$query->where('a.published = 1');
		$query->where('a.access IN (' . implode(',', $this->levels) . ')');
		$query->order('a.ordering ASC');
		
		// filter by country
		if (isset($this->country) && $this->country)
		{
			$query->where('d.id = '. (int) $this->country );
		}
		// filter by region
		if (isset($this->region) && $this->region)
		{
			$query->where('c.id = '. (int) $this->region );
		}
		// filter by area
		if (isset($this->area) && $this->area)
		{
			$query->where('b.id = '. (int) $this->area );
		}		
		// filter by facility
		if (isset($this->facility) && $this->facility)
		{
			$query->where('e.id = '. (int) $this->facility );
		}
		// filter by facility
		if (isset($this->group) && $this->group)
		{
			$query->where('a.id = '. (int) $this->group );
		}
		// filter by ids
		if (SupportgroupsHelper::checkArray($this->idArray))
		{
			$query->where('a.id IN ('. implode(',',$this->idArray).')' );
		}

		// return the query object
		return $query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems(&$id = null)
	{
		$user = JFactory::getUser();
                // check if this user has permission to access items
                if (!$user->authorise('site.map.access', 'com_supportgroups'))
                {
			return false;
                } 
		// load parent items
		$items = parent::getItems();
		// make sure we have items to work with
		if (isset($items) && SupportgroupsHelper::checkArray($items))
		{
			// set totals only if not set already (or is group)
			$setTotals = false;
			if ('groups' != $this->type && 'group' != $this->type && SupportgroupsHelper::checkString($this->type) && !isset($this->totals[$this->type][$id]))
			{
				// check if we already have set the totals globally
				$this->totals[$this->type][$id] = SupportgroupsHelper::getTotals($this->type,$id);
				if (SupportgroupsHelper::checkArray($this->totals[$this->type][$id]))
				{
					$setTotals = false;
				}
				else
				{
					// to avoid the (Cannot use a scalar value as an array)
					$this->totals[$this->type][$id] = array();
					$setTotals = true;
					$keyBuckets = array('members','children','on_art','male','female','male_children','female_children','male_art','female_art');
				}
			}
			// Convert the parameter fields into objects.
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias)) ? $item->id.':'.$item->alias : $item->id;
				if (isset($item->info) && SupportgroupsHelper::checkString($item->info))
				{
					// Decode Additional Info
					$item->info = json_decode($item->info, true);
					// set Additional Info Data to the $item object.
					$item->additionalInfo = $this->getAdditionalInfoData($item->info);
				}
				// do some calculations here
				$item->members = (int) $item->male + (int) $item->female;
				$item->children = (int) $item->male_children + (int) $item->female_children;
				$item->on_art = (int) $item->male_art + (int) $item->female_art;
				// only show ART if numbers set
				if (0 == $item->on_art)
				{
					unset($item->on_art);
					unset($item->male_art);
					unset($item->female_art);
				}
				if ($setTotals)
				{
					foreach ($keyBuckets as $nnn => &$key)
					{
						if (isset($item->$key))
						{
							if (!isset($this->totals[$this->type][$id][$key]))
							{
								// to avoid the (Cannot use a scalar value as an array)
								$this->totals[$this->type][$id][$key] = array();
							}
							$this->totals[$this->type][$id][$key][] = (int) $item->$key;
							
							// now count the members who has RT set
							if ('on_art' == $key)
							{
								if (!isset($this->totals[$this->type][$id]['on_art_persent']))
								{
									// to avoid the (Cannot use a scalar value as an array)
									$this->totals[$this->type][$id]['on_art_persent'] = array();
								}
								$this->totals[$this->type][$id]['on_art_persent'][] = (int) $item->members;
							}
							elseif ('male_art' == $key)
							{
								if (!isset($this->totals[$this->type][$id]['male_art_persent']))
								{
									// to avoid the (Cannot use a scalar value as an array)
									$this->totals[$this->type][$id]['male_art_persent'] = array();
								}
								$this->totals[$this->type][$id]['male_art_persent'][] = (int) $item->male;
							}
							elseif ('female_art' == $key)
							{
								if (!isset($this->totals[$this->type][$id]['female_art_persent']))
								{
									// to avoid the (Cannot use a scalar value as an array)
									$this->totals[$this->type][$id]['female_art_persent'] = array();
								}
								$this->totals[$this->type][$id]['female_art_persent'][] = (int) $item->female;
							}
						}
					}
				}
			}
			if ($setTotals && isset($this->totals[$this->type][$id]))
			{
				if(!SupportgroupsHelper::setTotals($this->totals[$this->type][$id],$this->type,$id))
				{
					return false;
				}
			}
		} 
		// return items
		return $items;
	} 

	/**
	* Method to get an array of Additional Info Objects.
	*
	* @return mixed  An array of Additional Info Objects on success, false on failure.
	*
	*/
	public function getAdditionalInfoData($info)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__supportgroups_additional_info as d
		$query->select($db->quoteName(
			array('d.id','d.alias','d.name','d.details'),
			array('id','alias','name','details')));
		$query->from($db->quoteName('#__supportgroups_additional_info', 'd'));
		
		// Get from #__supportgroups_info_type as g
		$query->select($db->quoteName(
			array('g.name'),
			array('type')));
		$query->join('LEFT', ($db->quoteName('#__supportgroups_info_type', 'g')) . ' ON (' . $db->quoteName('d.info_type') . ' = ' . $db->quoteName('g.id') . ')');

		// Check if $info is an array with values.
		if (isset($info) && SupportgroupsHelper::checkArray($info))
		{
			$query->where('d.id IN (' . implode(',', $info) . ')');
		}
		else
		{
			return false;
		}

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();

		// check if there was data returned
		if ($db->getNumRows())
		{
			return $db->loadObjectList();
		}
		return false;
	}

	// Used in supportgroups
	/**
	* The Ids to fetch with query
	* @var         array
	*/
	protected $idArray = array();

	/**
	* Get Rows of Group data
	* 
	* @return    string    Formatted html table row
	*/
	public function getColumns(&$page)
	{
		// return columns
		return array(
			array( 'name' => 'name', 'title' => JText::_('COM_SUPPORTGROUPS_NAME'), 'type' => 'text', 'sorted' => true, 'direction' => 'ASC'),
			array( 'name' => 'phone', 'title' => JText::_('COM_SUPPORTGROUPS_PHONE'), 'type' => 'number', 'breakpoints' => 'xs'),
			array( 'name' => 'members', 'title' => JText::_('COM_SUPPORTGROUPS_TOTAL_MEMBERS'), 'type' => 'number', 'breakpoints' => 'xs sm'),
			array( 'name' => 'children', 'title' => JText::_('COM_SUPPORTGROUPS_TOTAL_CHILDREN'), 'type' => 'number', 'breakpoints' => 'all'),
			array( 'name' => 'on_art', 'title' => JText::_('COM_SUPPORTGROUPS_TOTAL_ON_ART'), 'type' => 'number', 'breakpoints' => 'all'),
			array( 'name' => 'male', 'title' => JText::_('COM_SUPPORTGROUPS_MALES'), 'type' => 'number', 'breakpoints' => 'all'),
			array( 'name' => 'female', 'title' => JText::_('COM_SUPPORTGROUPS_FEMALES'), 'type' => 'number', 'breakpoints' => 'all'),
			array( 'name' => 'male_children', 'title' => JText::_('COM_SUPPORTGROUPS_MALE_CHILDREN'), 'type' => 'number', 'breakpoints' => 'all'),
			array( 'name' => 'female_children', 'title' => JText::_('COM_SUPPORTGROUPS_FEMALE_CHILDREN'), 'type' => 'number', 'breakpoints' => 'all'),
			array( 'name' => 'male_art', 'title' => JText::_('COM_SUPPORTGROUPS_MALES_ART'), 'type' => 'number', 'breakpoints' => 'all'),
			array( 'name' => 'female_art', 'title' => JText::_('COM_SUPPORTGROUPS_FEMALES_ART'), 'type' => 'number', 'breakpoints' => 'all'),
			array( 'name' => 'facility_name', 'title' => JText::_('COM_SUPPORTGROUPS_FACILITIES'), 'type' => 'html', 'sort-use' => 'text', 'breakpoints' => 'xs'),
			array( 'name' => 'area_name', 'title' => JText::_('COM_SUPPORTGROUPS_AREAS'), 'type' => 'html', 'sort-use' => 'text', 'breakpoints' => 'xs sm'),
			array( 'name' => 'region_name', 'title' => JText::_('COM_SUPPORTGROUPS_REGIONS'), 'type' => 'html', 'sort-use' => 'text', 'breakpoints' => 'xs sm'),
			array( 'name' => 'country_name', 'title' => JText::_('COM_SUPPORTGROUPS_COUNTRY'), 'type' => 'html', 'sort-use' => 'text', 'breakpoints' => 'xs sm')
		);
	}

	/**
	* Get Rows of Group data
	* 
	* @return    string    Formatted html table row
	*/
	public function getRows(&$key,&$page)
	{
		$session = JFactory::getSession();
		$groups = $session->get($key, null);
		// check if this is valid json
		if (SupportgroupsHelper::checkJson($groups))
		{
			$array = json_decode($groups, true);
			// now check that array is all numbers, and set to int
			if (SupportgroupsHelper::checkArray($array))
			{
				$this->idArray = $array;
			}
			// at last lets get started
			if (SupportgroupsHelper::checkArray($this->idArray))
			{
				$rowArray = array(
					'name','phone','members','children','on_art','male','female','male_children','female_children','male_art','female_art',
					'facility_name','area_name','region_name','country_name'
					);
				$clickArray = array(
					'facility_name' => array('id' => 'facility_id','type' => 'facility'),
					'area_name' => array('id' => 'area_id','type' => 'area'),
					'region_name' => array('id' => 'region_id','type' => 'region'),
					'country_name' => array('id' => 'country_id','type' => 'country')
					);
				$urlArray = array(
					'facility_name' => 'facility',
					'area_name' => 'area',
					'region_name' => 'region',
					'country_name' => 'country'
					);
				// set the map route
				$mapRoute = 'index.php?option=com_supportgroups&view=map';
				$items = $this->getItems();
				if ($items)
				{
					// start row builder
					$rows = array();
					foreach($items as $nr => $item)
					{
						// build the row
						$rows[$nr] = array();
						foreach($rowArray as $value)
						{
							if (isset($item->$value))
							{
								// build a click-able button
								if (array_key_exists($value, $clickArray))
								{
									$typeAdded = '';
									if ('facility_name' == $value && isset($item->facility_type))
									{
										$typeAdded = ' data-uk-tooltip="{pos:\'left\'}" title="'.$item->facility_type.'"';
									}
									$mapUrl = $mapRoute.'&'.$urlArray[$value].'='.$item->$clickArray[$value]['id'];
									$key = $clickArray[$value]['type'].'__'.$item->$clickArray[$value]['id'];
									$buttons = '<div class="uk-button-group uk-width-1-1"><button class="uk-button uk-width-4-5 '
										.'uk-button-primary"'.$typeAdded.' data-uk-modal="{target:\'#item-info\'}"type="button" onclick="getItemData(\''.$key.'\')">'
										.$item->$value.'</button><a href="'.$mapUrl.'" class="modal uk-button uk-width-1-5 ">'
										.'<i class="uk-icon-map-marker"></i></a></div>';
									$rows[$nr][$value]['value']  = $buttons;
									$rows[$nr][$value]['options']  = array('filterValue' => $item->$value);
								}
								else
								{
									$rows[$nr][$value]['value'] = $item->$value;
									$rows[$nr][$value]['options']  = array('filterValue' => $item->$value);
								}
							}
							else
							{
								$rows[$nr][$value] = '';
							}
						}
					}
				}
			}
			// just return this for now :)
			return $rows;
		}

		return false;
	}
		
	/**
	* Get Table Totals
	* 
	* @return    string    Formatted html string
	*/
	public function getTableTotals(&$id,$requestView = 'table')
	{
		// force totals
		$this->type = 'totals';
		// set the request view
		$this->requestView = $requestView;
		$id = 1;
		// get group
		$groups = $this->getItems($id);
		if (isset($groups) && SupportgroupsHelper::checkArray($groups))
		{
			return $this->getDisplay($groups,$id);
		}
		return false;
	}
}
