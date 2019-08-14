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
	@subpackage		support_groups.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Support_groups Model
 */
class SupportgroupsModelSupport_groups extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
        {
			$config['filter_fields'] = array(
				'a.id','id',
				'a.published','published',
				'a.ordering','ordering',
				'a.created_by','created_by',
				'a.modified_by','modified_by',
				'a.name','name',
				'a.phone','phone',
				'a.area','area',
				'a.facility','facility',
				'a.male','male',
				'a.female','female'
			);
		}

		parent::__construct($config);
	}

	/**
	* Method to get list export data.
	*
	* @return mixed  An array of data items on success, false on failure.
	*/
	public function getSmartExport($pks)
	{
		// setup the query
		if (SupportgroupsHelper::checkArray($pks))
		{
			// Get the user object.
			$user = JFactory::getUser();
			// Create a new query object.
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			// Select some fields
			$query->select($db->quoteName(
				array('a.id','a.name','a.phone','g.name','r.name','c.name','h.name','a.female','a.female_art','a.female_children','a.male','a.male_art','a.male_children','a.info'),
				array('id','name','phone','area','region','country','facility','female','female_art','female_children','male','male_art','male_children','info')));

			$query->select($db->quoteName(
				array('at.name','ft.name'),
				array('area_type','facility_type')));
			
			// From the supportgroups_support_group table
			$query->from($db->quoteName('#__supportgroups_support_group', 'a'));
			$query->where('a.id IN (' . implode(',',$pks) . ')');

			// we convert all ids to the actual names for export
			$query->join('LEFT', $db->quoteName('#__supportgroups_area', 'g') . ' ON (' . $db->quoteName('a.area') . ' = ' . $db->quoteName('g.id') . ')');
			$query->join('LEFT', $db->quoteName('#__supportgroups_facility', 'h') . ' ON (' . $db->quoteName('a.facility') . ' = ' . $db->quoteName('h.id') . ')');
			$query->join('LEFT', $db->quoteName('#__supportgroups_area_type', 'at') . ' ON (' . $db->quoteName('g.area_type') . ' = ' . $db->quoteName('at.id') . ')');
			$query->join('LEFT', $db->quoteName('#__supportgroups_region', 'r') . ' ON (' . $db->quoteName('g.region') . ' = ' . $db->quoteName('r.id') . ')');
			$query->join('LEFT', $db->quoteName('#__supportgroups_country', 'c') . ' ON (' . $db->quoteName('r.country') . ' = ' . $db->quoteName('c.id') . ')');
			$query->join('LEFT', $db->quoteName('#__supportgroups_facility_type', 'ft') . ' ON (' . $db->quoteName('h.facility_type') . ' = ' . $db->quoteName('ft.id') . ')');
			
			// Implement View Level Access
			if (!$user->authorise('core.options', 'com_supportgroups'))
			{
				$groups = implode(',', $user->getAuthorisedViewLevels());
				$query->where('a.access IN (' . $groups . ')');
			}

			// Order the results by ordering
			$query->order('a.ordering  ASC');

			// Load the items
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				$items = $db->loadObjectList();
				$information = $this->getInformationKeys();
				// set values to display correctly.
				if (SupportgroupsHelper::checkArray($items))
				{
					foreach ($items as $nr => &$item)
					{
						$access = ($user->authorise('support_group.access', 'com_supportgroups.support_group.' . (int) $item->id) && $user->authorise('support_group.access', 'com_supportgroups'));
						if (!$access)
						{
							unset($items[$nr]);
							continue;
						}
						// set the area type
						$item->area = $item->area . ' (' . $item->area_type . ')';
						// set the facility type
						$item->facility = $item->facility . ' (' . $item->facility_type . ')';
						// clear those values
						unset($item->area_type);
						unset($item->facility_type);
						if (SupportgroupsHelper::checkArray($information))
						{
							// load information
							if (SupportgroupsHelper::checkJson($item->info))
							{
								$item->info = json_decode($item->info, true);
							}
							foreach ($information as $info => $name)
							{
								if (in_array($info, $item->info))
								{
									$item->$name = 'yes';
								}
								else
								{
									$item->$name = 'no';
								}									
							}
						}
						unset($item->info);
					}
				}
				// Add headers to items array.
				$headers = new stdClass();
				$headers->id = 'ID';
				$headers->name = 'Name';
				$headers->phone = 'Phone';
				$headers->area = 'Area';
				$headers->region = 'Region';
				$headers->country = 'Country';
				$headers->facility = 'Facility';
				$headers->female = 'Female';
				$headers->female_art = 'Female art';
				$headers->female_children = 'Female children';
				$headers->male = 'Male';
				$headers->male_art = 'Male art';
				$headers->male_children = 'Male children';
				if (SupportgroupsHelper::checkArray($information))
				{
					// load information Headers
					foreach ($information as $info => $name)
					{
						$key = SupportgroupsHelper::safeString($name);
						$headers->$key = $name;							
					}
				}
				if (SupportgroupsHelper::checkObject($headers))
				{
					array_unshift($items,$headers);
				}
				return $items;
			}
		}
		return false;
	}
	
	/**
	 * Method to get the information keys
	 *
	 * @return  array
	 */
	protected function getInformationKeys()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select($db->quoteName(
			array('a.id','a.name','b.name'),
			array('id','name','type')));

		// From the supportgroups_support_group table
		$query->from($db->quoteName('#__supportgroups_additional_info', 'a'));

		// we convert all ids to the actual names for export
		$query->join('LEFT', $db->quoteName('#__supportgroups_info_type', 'b') . ' ON (' . $db->quoteName('a.info_type') . ' = ' . $db->quoteName('b.id') . ')');

		// Load the items
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			$items = $db->loadObjectList();
			$bucket = array();
			foreach ($items as $item)
			{
				$bucket[$item->id] = $item->name .' (' . $item->type . ')';
			}
			return $bucket;
		}
		return false;
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}
		$name = $this->getUserStateFromRequest($this->context . '.filter.name', 'filter_name');
		$this->setState('filter.name', $name);

		$phone = $this->getUserStateFromRequest($this->context . '.filter.phone', 'filter_phone');
		$this->setState('filter.phone', $phone);

		$area = $this->getUserStateFromRequest($this->context . '.filter.area', 'filter_area');
		$this->setState('filter.area', $area);

		$facility = $this->getUserStateFromRequest($this->context . '.filter.facility', 'filter_facility');
		$this->setState('filter.facility', $facility);

		$male = $this->getUserStateFromRequest($this->context . '.filter.male', 'filter_male');
		$this->setState('filter.male', $male);

		$female = $this->getUserStateFromRequest($this->context . '.filter.female', 'filter_female');
		$this->setState('filter.female', $female);
        
		$sorting = $this->getUserStateFromRequest($this->context . '.filter.sorting', 'filter_sorting', 0, 'int');
		$this->setState('filter.sorting', $sorting);
        
		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);
        
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
        
		$created_by = $this->getUserStateFromRequest($this->context . '.filter.created_by', 'filter_created_by', '');
		$this->setState('filter.created_by', $created_by);

		$created = $this->getUserStateFromRequest($this->context . '.filter.created', 'filter_created');
		$this->setState('filter.created', $created);

		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		// check in items
		$this->checkInNow();

		// load parent items
		$items = parent::getItems();

		// set values to display correctly.
		if (SupportgroupsHelper::checkArray($items))
		{
			foreach ($items as $nr => &$item)
			{
				$access = (JFactory::getUser()->authorise('support_group.access', 'com_supportgroups.support_group.' . (int) $item->id) && JFactory::getUser()->authorise('support_group.access', 'com_supportgroups'));
				if (!$access)
				{
					unset($items[$nr]);
					continue;
				}

			}
		}
        
		// return items
		return $items;
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Get the user object.
		$user = JFactory::getUser();
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the supportgroups_item table
		$query->from($db->quoteName('#__supportgroups_support_group', 'a'));

		// From the supportgroups_area table.
		$query->select($db->quoteName('g.name','area_name'));
		$query->join('LEFT', $db->quoteName('#__supportgroups_area', 'g') . ' ON (' . $db->quoteName('a.area') . ' = ' . $db->quoteName('g.id') . ')');

		// From the supportgroups_facility table.
		$query->select($db->quoteName('h.name','facility_name'));
		$query->join('LEFT', $db->quoteName('#__supportgroups_facility', 'h') . ' ON (' . $db->quoteName('a.facility') . ' = ' . $db->quoteName('h.id') . ')');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
		}

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where('a.access = ' . (int) $access);
		}
		// Implement View Level Access
		if (!$user->authorise('core.options', 'com_supportgroups'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}
		// Filter by search.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search) . '%');
				$query->where('(a.name LIKE '.$search.' OR a.phone LIKE '.$search.' OR a.area LIKE '.$search.' OR g.name LIKE '.$search.' OR a.facility LIKE '.$search.' OR h.name LIKE '.$search.' OR a.details LIKE '.$search.')');
			}
		}

		// Filter by area.
		if ($area = $this->getState('filter.area'))
		{
			$query->where('a.area = ' . $db->quote($db->escape($area)));
		}
		// Filter by facility.
		if ($facility = $this->getState('filter.facility'))
		{
			$query->where('a.facility = ' . $db->quote($db->escape($facility)));
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'asc');	
		if ($orderCol != '')
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get list export data.
	 *
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getExportData($pks)
	{
		// setup the query
		if (SupportgroupsHelper::checkArray($pks))
		{
			// Set a value to know this is exporting method.
			$_export = true;
			// Get the user object.
			$user = JFactory::getUser();
			// Create a new query object.
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			// Select some fields
			$query->select('a.*');

			// From the supportgroups_support_group table
			$query->from($db->quoteName('#__supportgroups_support_group', 'a'));
			$query->where('a.id IN (' . implode(',',$pks) . ')');
			// Implement View Level Access
			if (!$user->authorise('core.options', 'com_supportgroups'))
			{
				$groups = implode(',', $user->getAuthorisedViewLevels());
				$query->where('a.access IN (' . $groups . ')');
			}

			// Order the results by ordering
			$query->order('a.ordering  ASC');

			// Load the items
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				$items = $db->loadObjectList();

				// set values to display correctly.
				if (SupportgroupsHelper::checkArray($items))
				{
					foreach ($items as $nr => &$item)
					{
						$access = (JFactory::getUser()->authorise('support_group.access', 'com_supportgroups.support_group.' . (int) $item->id) && JFactory::getUser()->authorise('support_group.access', 'com_supportgroups'));
						if (!$access)
						{
							unset($items[$nr]);
							continue;
						}

						// unset the values we don't want exported.
						unset($item->asset_id);
						unset($item->checked_out);
						unset($item->checked_out_time);
					}
				}
				// Add headers to items array.
				$headers = $this->getExImPortHeaders();
				if (SupportgroupsHelper::checkObject($headers))
				{
					array_unshift($items,$headers);
				}
				return $items;
			}
		}
		return false;
	}

	/**
	* Method to get header.
	*
	* @return mixed  An array of data items on success, false on failure.
	*/
	public function getExImPortHeaders()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// get the columns
		$columns = $db->getTableColumns("#__supportgroups_support_group");
		if (SupportgroupsHelper::checkArray($columns))
		{
			// remove the headers you don't import/export.
			unset($columns['asset_id']);
			unset($columns['checked_out']);
			unset($columns['checked_out_time']);
			$headers = new stdClass();
			foreach ($columns as $column => $type)
			{
				$headers->{$column} = $column;
			}
			return $headers;
		}
		return false;
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @return  string  A store id.
	 *
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.id');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.ordering');
		$id .= ':' . $this->getState('filter.created_by');
		$id .= ':' . $this->getState('filter.modified_by');
		$id .= ':' . $this->getState('filter.name');
		$id .= ':' . $this->getState('filter.phone');
		$id .= ':' . $this->getState('filter.area');
		$id .= ':' . $this->getState('filter.facility');
		$id .= ':' . $this->getState('filter.male');
		$id .= ':' . $this->getState('filter.female');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to checkin all items left checked out longer then a set time.
	 *
	 * @return  a bool
	 *
	 */
	protected function checkInNow()
	{
		// Get set check in time
		$time = JComponentHelper::getParams('com_supportgroups')->get('check_in');

		if ($time)
		{

			// Get a db connection.
			$db = JFactory::getDbo();
			// reset query
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__supportgroups_support_group'));
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				// Get Yesterdays date
				$date = JFactory::getDate()->modify($time)->toSql();
				// reset query
				$query = $db->getQuery(true);

				// Fields to update.
				$fields = array(
					$db->quoteName('checked_out_time') . '=\'0000-00-00 00:00:00\'',
					$db->quoteName('checked_out') . '=0'
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('checked_out') . '!=0', 
					$db->quoteName('checked_out_time') . '<\''.$date.'\''
				);

				// Check table
				$query->update($db->quoteName('#__supportgroups_support_group'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				$db->execute();
			}
		}

		return false;
	}
}
