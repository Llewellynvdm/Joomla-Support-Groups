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
	@subpackage		supportgroups.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Supportgroups Model
 */
class SupportgroupsModelSupportgroups extends JModelList
{
	public function getIcons()
	{
		// load user for access menus
		$user = JFactory::getUser();
		// reset icon array
		$icons  = array();
		// view groups array
		$viewGroups = array(
			'main' => array('png.support_group.add', 'png.support_groups', 'png.payment.add', 'png.payments', 'png.facility.add', 'png.facilities', 'png.additional_info.add', 'png.additional_information', 'png.area.add', 'png.areas', 'png.region.add', 'png.regions', 'png.countries', 'png.currencies', 'png.help_documents')
		);
		// view access array
		$viewAccess = array(
			'support_group.create' => 'support_group.create',
			'support_groups.access' => 'support_group.access',
			'support_group.access' => 'support_group.access',
			'support_groups.submenu' => 'support_group.submenu',
			'support_groups.dashboard_list' => 'support_group.dashboard_list',
			'support_group.dashboard_add' => 'support_group.dashboard_add',
			'payment.create' => 'payment.create',
			'payments.access' => 'payment.access',
			'payment.access' => 'payment.access',
			'payments.submenu' => 'payment.submenu',
			'payments.dashboard_list' => 'payment.dashboard_list',
			'payment.dashboard_add' => 'payment.dashboard_add',
			'facility.create' => 'facility.create',
			'facilities.access' => 'facility.access',
			'facility.access' => 'facility.access',
			'facilities.submenu' => 'facility.submenu',
			'facilities.dashboard_list' => 'facility.dashboard_list',
			'facility.dashboard_add' => 'facility.dashboard_add',
			'facility_type.create' => 'facility_type.create',
			'facility_types.access' => 'facility_type.access',
			'facility_type.access' => 'facility_type.access',
			'additional_info.create' => 'additional_info.create',
			'additional_information.access' => 'additional_info.access',
			'additional_info.access' => 'additional_info.access',
			'additional_information.submenu' => 'additional_info.submenu',
			'additional_information.dashboard_list' => 'additional_info.dashboard_list',
			'additional_info.dashboard_add' => 'additional_info.dashboard_add',
			'info_type.create' => 'info_type.create',
			'info_types.access' => 'info_type.access',
			'info_type.access' => 'info_type.access',
			'area.create' => 'area.create',
			'areas.access' => 'area.access',
			'area.access' => 'area.access',
			'areas.submenu' => 'area.submenu',
			'areas.dashboard_list' => 'area.dashboard_list',
			'area.dashboard_add' => 'area.dashboard_add',
			'area_type.create' => 'area_type.create',
			'area_types.access' => 'area_type.access',
			'area_type.access' => 'area_type.access',
			'region.create' => 'region.create',
			'regions.access' => 'region.access',
			'region.access' => 'region.access',
			'regions.submenu' => 'region.submenu',
			'regions.dashboard_list' => 'region.dashboard_list',
			'region.dashboard_add' => 'region.dashboard_add',
			'country.create' => 'country.create',
			'countries.access' => 'country.access',
			'country.access' => 'country.access',
			'countries.submenu' => 'country.submenu',
			'countries.dashboard_list' => 'country.dashboard_list',
			'currency.create' => 'currency.create',
			'currencies.access' => 'currency.access',
			'currency.access' => 'currency.access',
			'currencies.submenu' => 'currency.submenu',
			'currencies.dashboard_list' => 'currency.dashboard_list',
			'help_document.create' => 'help_document.create',
			'help_documents.access' => 'help_document.access',
			'help_document.access' => 'help_document.access',
			'help_documents.submenu' => 'help_document.submenu',
			'help_documents.dashboard_list' => 'help_document.dashboard_list');
		// loop over the $views
		foreach($viewGroups as $group => $views)
		{
			$i = 0;
			if (SupportgroupsHelper::checkArray($views))
			{
				foreach($views as $view)
				{
					$add = false;
					// external views (links)
					if (strpos($view,'||') !== false)
					{
						$dwd = explode('||', $view);
						if (count($dwd) == 3)
						{
							list($type, $name, $url) = $dwd;
							$viewName 	= $name;
							$alt 		= $name;
							$url 		= $url;
							$image 		= $name.'.'.$type;
							$name 		= 'COM_SUPPORTGROUPS_DASHBOARD_'.SupportgroupsHelper::safeString($name,'U');
						}
					}
					// internal views
					elseif (strpos($view,'.') !== false)
					{
						$dwd = explode('.', $view);
						if (count($dwd) == 3)
						{
							list($type, $name, $action) = $dwd;
						}
						elseif (count($dwd) == 2)
						{
							list($type, $name) = $dwd;
							$action = false;
						}
						if ($action)
						{
							$viewName = $name;
							switch($action)
							{
								case 'add':
									$url 	= 'index.php?option=com_supportgroups&view='.$name.'&layout=edit';
									$image 	= $name.'_'.$action.'.'.$type;
									$alt 	= $name.'&nbsp;'.$action;
									$name	= 'COM_SUPPORTGROUPS_DASHBOARD_'.SupportgroupsHelper::safeString($name,'U').'_ADD';
									$add	= true;
								break;
								default:
									$url 	= 'index.php?option=com_categories&view=categories&extension=com_supportgroups.'.$name;
									$image 	= $name.'_'.$action.'.'.$type;
									$alt 	= $name.'&nbsp;'.$action;
									$name	= 'COM_SUPPORTGROUPS_DASHBOARD_'.SupportgroupsHelper::safeString($name,'U').'_'.SupportgroupsHelper::safeString($action,'U');
								break;
							}
						}
						else
						{
							$viewName 	= $name;
							$alt 		= $name;
							$url 		= 'index.php?option=com_supportgroups&view='.$name;
							$image 		= $name.'.'.$type;
							$name 		= 'COM_SUPPORTGROUPS_DASHBOARD_'.SupportgroupsHelper::safeString($name,'U');
							$hover		= false;
						}
					}
					else
					{
						$viewName 	= $view;
						$alt 		= $view;
						$url 		= 'index.php?option=com_supportgroups&view='.$view;
						$image 		= $view.'.png';
						$name 		= ucwords($view).'<br /><br />';
						$hover		= false;
					}
					// first make sure the view access is set
					if (SupportgroupsHelper::checkArray($viewAccess))
					{
						// setup some defaults
						$dashboard_add = false;
						$dashboard_list = false;
						$accessTo = '';
						$accessAdd = '';
						// acces checking start
						$accessCreate = (isset($viewAccess[$viewName.'.create'])) ? SupportgroupsHelper::checkString($viewAccess[$viewName.'.create']):false;
						$accessAccess = (isset($viewAccess[$viewName.'.access'])) ? SupportgroupsHelper::checkString($viewAccess[$viewName.'.access']):false;
						// set main controllers
						$accessDashboard_add = (isset($viewAccess[$viewName.'.dashboard_add'])) ? SupportgroupsHelper::checkString($viewAccess[$viewName.'.dashboard_add']):false;
						$accessDashboard_list = (isset($viewAccess[$viewName.'.dashboard_list'])) ? SupportgroupsHelper::checkString($viewAccess[$viewName.'.dashboard_list']):false;
						// check for adding access
						if ($add && $accessCreate)
						{
							$accessAdd = $viewAccess[$viewName.'.create'];
						}
						elseif ($add)
						{
							$accessAdd = 'core.create';
						}
						// check if acces to view is set
						if ($accessAccess)
						{
							$accessTo = $viewAccess[$viewName.'.access'];
						}
						// set main access controllers
						if ($accessDashboard_add)
						{
							$dashboard_add	= $user->authorise($viewAccess[$viewName.'.dashboard_add'], 'com_supportgroups');
						}
						if ($accessDashboard_list)
						{
							$dashboard_list = $user->authorise($viewAccess[$viewName.'.dashboard_list'], 'com_supportgroups');
						}
						if (SupportgroupsHelper::checkString($accessAdd) && SupportgroupsHelper::checkString($accessTo))
						{
							// check access
							if($user->authorise($accessAdd, 'com_supportgroups') && $user->authorise($accessTo, 'com_supportgroups') && $dashboard_add)
							{
								$icons[$group][$i]			= new StdClass;
								$icons[$group][$i]->url 	= $url;
								$icons[$group][$i]->name 	= $name;
								$icons[$group][$i]->image 	= $image;
								$icons[$group][$i]->alt 	= $alt;
							}
						}
						elseif (SupportgroupsHelper::checkString($accessTo))
						{
							// check access
							if($user->authorise($accessTo, 'com_supportgroups') && $dashboard_list)
							{
								$icons[$group][$i]			= new StdClass;
								$icons[$group][$i]->url 	= $url;
								$icons[$group][$i]->name 	= $name;
								$icons[$group][$i]->image 	= $image;
								$icons[$group][$i]->alt 	= $alt;
							}
						}
						elseif (SupportgroupsHelper::checkString($accessAdd))
						{
							// check access
							if($user->authorise($accessAdd, 'com_supportgroups') && $dashboard_add)
							{
								$icons[$group][$i]			= new StdClass;
								$icons[$group][$i]->url 	= $url;
								$icons[$group][$i]->name 	= $name;
								$icons[$group][$i]->image 	= $image;
								$icons[$group][$i]->alt 	= $alt;
							}
						}
						else
						{
							$icons[$group][$i]			= new StdClass;
							$icons[$group][$i]->url 	= $url;
							$icons[$group][$i]->name 	= $name;
							$icons[$group][$i]->image 	= $image;
							$icons[$group][$i]->alt 	= $alt;
						}
					}
					else
					{
						$icons[$group][$i]			= new StdClass;
						$icons[$group][$i]->url 	= $url;
						$icons[$group][$i]->name 	= $name;
						$icons[$group][$i]->image 	= $image;
						$icons[$group][$i]->alt 	= $alt;
					}
					$i++;
				}
			}
			else
			{
					$icons[$group][$i] = false;
			}
		}
		return $icons;
	}


	public function getNoticeboard()
	{
		// get the document to load the scripts
		$document = JFactory::getDocument();
		$document->addScript(JURI::root() . "media/com_supportgroups/js/marked.js");
		$document->addScriptDeclaration('
		var token = "'.JSession::getFormToken().'";
		var noticeboard = "https://www.vdm.io/supportgroups-noticeboard-md";
		jQuery(document).ready(function () {
			jQuery.get(noticeboard)
			.success(function(board) { 
				if (board.length > 5) {
					jQuery("#noticeboard-md").html(marked(board));
					getIS(1,board).done(function(result) {
						if (result){
							jQuery("#cpanel_tabTabs a").each(function() {
								if (this.href.indexOf("#vast_development_method") >= 0 || this.href.indexOf("#notice_board") >= 0) {
									var textVDM = jQuery(this).text();
									jQuery(this).html("<span class=\"label label-important vdm-new-notice\">1</span> "+textVDM);
									jQuery(this).attr("id","vdm-new-notice");
									jQuery("#vdm-new-notice").click(function() {
										getIS(2,board).done(function(result) {
												if (result) {
												jQuery(".vdm-new-notice").fadeOut(500);
											}
										});
									});
								}
							});
						}
					});
				} else {
					jQuery("#noticeboard-md").html("'.JText::_('COM_SUPPORTGROUPS_ALL_IS_GOOD_PLEASE_CHECK_AGAIN_LATTER').'");
				}
			})
			.error(function(jqXHR, textStatus, errorThrown) { 
				jQuery("#noticeboard-md").html("'.JText::_('COM_SUPPORTGROUPS_ALL_IS_GOOD_PLEASE_CHECK_AGAIN_LATTER').'");
			});
		});
		// to check is READ/NEW
		function getIS(type,notice){
			if(type == 1){
				var getUrl = "index.php?option=com_supportgroups&task=ajax.isNew&format=json";
			} else if (type == 2) {
				var getUrl = "index.php?option=com_supportgroups&task=ajax.isRead&format=json";
			}	
			if(token.length > 0 && notice.length){
				var request = "token="+token+"&notice="+notice;
			}
			return jQuery.ajax({
				type: "POST",
				url: getUrl,
				dataType: "jsonp",
				data: request,
				jsonp: "callback"
			});
		}
		// nice little dot trick :)
		jQuery(document).ready( function($) {
			var x=0;
			setInterval(function() {
				var dots = "";
				x++;
				for (var y=0; y < x%8; y++) {
					dots+=".";
				}
				$(".loading-dots").text(dots);
			} , 500);
		});');

		return '<div id="noticeboard-md">'.JText::_('COM_SUPPORTGROUPS_THE_NOTICE_BOARD_IS_LOADING').'.<span class="loading-dots">.</span></small></div>';
	}

	public function getReadme()
	{
		$document = JFactory::getDocument();
		$document->addScriptDeclaration('
		var getreadme = "'. JURI::root() . 'administrator/components/com_supportgroups/README.txt";
		jQuery(document).ready(function () {
			jQuery.get(getreadme)
			.success(function(readme) { 
				jQuery("#readme-md").html(marked(readme));
			})
			.error(function(jqXHR, textStatus, errorThrown) { 
				jQuery("#readme-md").html("'.JText::_('COM_SUPPORTGROUPS_PLEASE_CHECK_AGAIN_LATTER').'");
			});
		});');

		return '<div id="readme-md"><small>'.JText::_('COM_SUPPORTGROUPS_THE_README_IS_LOADING').'.<span class="loading-dots">.</span></small></div>';
	}
}
