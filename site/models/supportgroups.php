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
 * Supportgroups Model for Supportgroups
 */
class SupportgroupsModelSupportgroups extends JModelList
{
	/**
	 * Model user data.
	 *
	 * @var        strings
	 */
	protected $user;
	protected $userId;
	protected $guest;
	protected $groups;
	protected $levels;
	protected $app;
	protected $input;
	protected $uikitComp;

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Get the current user for authorisation checks
		$this->user = JFactory::getUser();
		$this->userId = $this->user->get('id');
		$this->guest = $this->user->get('guest');
		$this->groups = $this->user->get('groups');
		$this->authorisedGroups = $this->user->getAuthorisedGroups();
		$this->levels = $this->user->getAuthorisedViewLevels();
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->initSet = true; 
		// Make sure all records load, since no pagination allowed.
		$this->setState('list.limit', 0);
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__supportgroups_support_group as a
		$query->select($db->quoteName(
			array('a.id','a.name','a.alias','a.phone','a.info','a.male','a.male_children','a.male_art','a.female','a.female_children','a.female_art','a.marker','a.published','a.ordering'),
			array('id','name','alias','phone','info','male','male_children','male_art','female','female_children','female_art','marker','published','ordering')));
		$query->from($db->quoteName('#__supportgroups_support_group', 'a'));

		// Get from #__supportgroups_area as b
		$query->select($db->quoteName(
			array('b.id','b.name','b.alias','b.color'),
			array('area_id','area_name','area_alias','area_color')));
		$query->join('LEFT', ($db->quoteName('#__supportgroups_area', 'b')) . ' ON (' . $db->quoteName('a.area') . ' = ' . $db->quoteName('b.id') . ')');

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
			array('e.id','e.alias','e.name','e.phone','e.marker'),
			array('facility_id','facility_alias','facility_name','facility_phone','facility_marker')));
		$query->join('LEFT', ($db->quoteName('#__supportgroups_facility', 'e')) . ' ON (' . $db->quoteName('a.facility') . ' = ' . $db->quoteName('e.id') . ')');

		// Filtering.

		$input = array('facility' => 'int','area' => 'int','region' => 'int','country' => 'int');
		$where = array('facility' => 'e.id','area' => 'b.id','region' => 'c.id','country' => 'd.id');
		$filtering = $this->input->getArray($input);
		foreach ($filtering as $key => $filter)
		{
			if ($filter)
			{
				$query->where($where[$key].' = '. (int) $filter);
			}
		}
		$query->where('a.access IN (' . implode(',', $this->levels) . ')');
		// Get where a.published is 1
		$query->where('a.published = 1');
		$query->order('a.ordering ASC');

		// return the query object
		return $query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$user = JFactory::getUser();
		// check if this user has permission to access item
		if (!$user->authorise('site.supportgroups.access', 'com_supportgroups'))
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('COM_SUPPORTGROUPS_NOT_AUTHORISED_TO_VIEW_SUPPORTGROUPS'), 'error');
			// redirect away to the home page if no access allowed.
			$app->redirect(JURI::root());
			return false;
		}


		// Does not work on all servers
		// But may help to insure huge data sets load on map page
		ini_set('pcre.backtrack_limit', 10000000);
		// load parent items
		$items = parent::getItems();

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_supportgroups', true);

		// Insure all item fields are adapted where needed.
		if (SupportgroupsHelper::checkArray($items))
		{
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
				// Check if we can decode info
				if (SupportgroupsHelper::checkJson($item->info))
				{
					// Decode info
					$item->info = json_decode($item->info, true);
				}
				$item->members = (int) $item->male + (int) $item->female;
				$item->children = (int) $item->male_children + (int) $item->female_children;
				$item->on_art = (int) $item->male_art + (int) $item->female_art;
			}
		}

		// return items
		return $items;
	}

	/**
	 * Get the uikit needed components
	 *
	 * @return mixed  An array of objects on success.
	 *
	 */
	public function getUikitComp()
	{
		if (isset($this->uikitComp) && SupportgroupsHelper::checkArray($this->uikitComp))
		{
			return $this->uikitComp;
		}
		return false;
	}
}
