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

	@version		1.0.11
	@build			2nd March, 2022
	@created		24th February, 2016
	@package		Support Groups
	@subpackage		payments.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

/**
 * Payments Model
 */
class SupportgroupsModelPayments extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
        {
			$config['filter_fields'] = array(
				'a.id','id',
				'a.published','published',
				'a.access','access',
				'a.ordering','ordering',
				'a.created_by','created_by',
				'a.modified_by','modified_by',
				'g.name','support_group',
				'a.year','year',
				'a.amount','amount'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		// Check if the form was submitted
		$formSubmited = $app->input->post->get('form_submited');

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
		if ($formSubmited)
		{
			$access = $app->input->post->get('access');
			$this->setState('filter.access', $access);
		}

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$created_by = $this->getUserStateFromRequest($this->context . '.filter.created_by', 'filter_created_by', '');
		$this->setState('filter.created_by', $created_by);

		$created = $this->getUserStateFromRequest($this->context . '.filter.created', 'filter_created');
		$this->setState('filter.created', $created);

		$sorting = $this->getUserStateFromRequest($this->context . '.filter.sorting', 'filter_sorting', 0, 'int');
		$this->setState('filter.sorting', $sorting);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$support_group = $this->getUserStateFromRequest($this->context . '.filter.support_group', 'filter_support_group');
		if ($formSubmited)
		{
			$support_group = $app->input->post->get('support_group');
			$this->setState('filter.support_group', $support_group);
		}

		$year = $this->getUserStateFromRequest($this->context . '.filter.year', 'filter_year');
		if ($formSubmited)
		{
			$year = $app->input->post->get('year');
			$this->setState('filter.year', $year);
		}

		$amount = $this->getUserStateFromRequest($this->context . '.filter.amount', 'filter_amount');
		if ($formSubmited)
		{
			$amount = $app->input->post->get('amount');
			$this->setState('filter.amount', $amount);
		}

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
		// Check in items
		$this->checkInNow();

		// load parent items
		$items = parent::getItems();

		// Set values to display correctly.
		if (SupportgroupsHelper::checkArray($items))
		{
			// Get the user object if not set.
			if (!isset($user) || !SupportgroupsHelper::checkObject($user))
			{
				$user = JFactory::getUser();
			}
			foreach ($items as $nr => &$item)
			{
				// Remove items the user can't access.
				$access = ($user->authorise('payment.access', 'com_supportgroups.payment.' . (int) $item->id) && $user->authorise('payment.access', 'com_supportgroups'));
				if (!$access)
				{
					unset($items[$nr]);
					continue;
				}

			}
		}

		// Try Convert the Amount to the Currency value
		if (SupportgroupsHelper::checkArray($items))
		{
			foreach ($items as $nr => &$item)
			{
				// convert to currency here
				$item->amount = SupportgroupsHelper::setCurrency($item->amount, $item->support_group);
			}
		}

		// set selection value to a translatable value
		if (SupportgroupsHelper::checkArray($items))
		{
			foreach ($items as $nr => &$item)
			{
				// convert year
				$item->year = $this->selectionTranslation($item->year, 'year');
			}
		}

        
		// return items
		return $items;
	}

	/**
	 * Method to convert selection values to translatable string.
	 *
	 * @return translatable string
	 */
	public function selectionTranslation($value,$name)
	{
		// Array of year language strings
		if ($name === 'year')
		{
			$yearArray = array(
				0 => 'COM_SUPPORTGROUPS_PAYMENT_SELECT_A_YEAR',
				2010 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_TEN',
				2011 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_ELEVEN',
				2012 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_TWELVE',
				2013 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_THIRTEEN',
				2014 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_FOURTEEN',
				2015 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_FIFTEEN',
				2016 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_SIXTEEN',
				2017 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_SEVENTEEN',
				2018 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_EIGHTEEN',
				2019 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_NINETEEN',
				2020 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_TWENTY',
				2021 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_TWENTY_ONE',
				2022 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_TWENTY_TWO',
				2023 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_TWENTY_THREE',
				2024 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_TWENTY_FOUR',
				2025 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_TWENTY_FIVE',
				2026 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_TWENTY_SIX',
				2027 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_TWENTY_SEVEN',
				2028 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_TWENTY_EIGHT',
				2029 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_TWENTY_NINE',
				2030 => 'COM_SUPPORTGROUPS_PAYMENT_TWO_THOUSAND_AND_THIRTY'
			);
			// Now check if value is found in this array
			if (isset($yearArray[$value]) && SupportgroupsHelper::checkString($yearArray[$value]))
			{
				return $yearArray[$value];
			}
		}
		return $value;
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
		$query->from($db->quoteName('#__supportgroups_payment', 'a'));

		// From the supportgroups_support_group table.
		$query->select($db->quoteName('g.name','support_group_name'));
		$query->join('LEFT', $db->quoteName('#__supportgroups_support_group', 'g') . ' ON (' . $db->quoteName('a.support_group') . ' = ' . $db->quoteName('g.id') . ')');

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
		$_access = $this->getState('filter.access');
		if ($_access && is_numeric($_access))
		{
			$query->where('a.access = ' . (int) $_access);
		}
		elseif (SupportgroupsHelper::checkArray($_access))
		{
			// Secure the array for the query
			$_access = ArrayHelper::toInteger($_access);
			// Filter by the Access Array.
			$query->where('a.access IN (' . implode(',', $_access) . ')');
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
				$query->where('(a.support_group LIKE '.$search.' OR g.name LIKE '.$search.' OR a.year LIKE '.$search.' OR a.amount LIKE '.$search.')');
			}
		}

		// Filter by Support_group.
		$_support_group = $this->getState('filter.support_group');
		if (is_numeric($_support_group))
		{
			if (is_float($_support_group))
			{
				$query->where('a.support_group = ' . (float) $_support_group);
			}
			else
			{
				$query->where('a.support_group = ' . (int) $_support_group);
			}
		}
		elseif (SupportgroupsHelper::checkString($_support_group))
		{
			$query->where('a.support_group = ' . $db->quote($db->escape($_support_group)));
		}
		// Filter by Year.
		$_year = $this->getState('filter.year');
		if (is_numeric($_year))
		{
			if (is_float($_year))
			{
				$query->where('a.year = ' . (float) $_year);
			}
			else
			{
				$query->where('a.year = ' . (int) $_year);
			}
		}
		elseif (SupportgroupsHelper::checkString($_year))
		{
			$query->where('a.year = ' . $db->quote($db->escape($_year)));
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'desc');
		if ($orderCol != '')
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get list export data.
	 *
	 * @param   array  $pks  The ids of the items to get
	 * @param   JUser  $user  The user making the request
	 *
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getExportData($pks, $user = null)
	{
		// setup the query
		if (($pks_size = SupportgroupsHelper::checkArray($pks)) !== false || 'bulk' === $pks)
		{
			// Set a value to know this is export method. (USE IN CUSTOM CODE TO ALTER OUTCOME)
			$_export = true;
			// Get the user object if not set.
			if (!isset($user) || !SupportgroupsHelper::checkObject($user))
			{
				$user = JFactory::getUser();
			}
			// Create a new query object.
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			// Select some fields
			$query->select('a.*');

			// From the supportgroups_payment table
			$query->from($db->quoteName('#__supportgroups_payment', 'a'));
			// The bulk export path
			if ('bulk' === $pks)
			{
				$query->where('a.id > 0');
			}
			// A large array of ID's will not work out well
			elseif ($pks_size > 500)
			{
				// Use lowest ID
				$query->where('a.id >= ' . (int) min($pks));
				// Use highest ID
				$query->where('a.id <= ' . (int) max($pks));
			}
			// The normal default path
			else
			{
				$query->where('a.id IN (' . implode(',',$pks) . ')');
			}
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

				// Set values to display correctly.
				if (SupportgroupsHelper::checkArray($items))
				{
					foreach ($items as $nr => &$item)
					{
						// Remove items the user can't access.
						$access = ($user->authorise('payment.access', 'com_supportgroups.payment.' . (int) $item->id) && $user->authorise('payment.access', 'com_supportgroups'));
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

				// Try Convert the Amount to the Currency value
		if (SupportgroupsHelper::checkArray($items))
		{
			foreach ($items as $nr => &$item)
			{
				// convert to currency here
				$item->amount = SupportgroupsHelper::setCurrency($item->amount, $item->support_group);
			}
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
		$columns = $db->getTableColumns("#__supportgroups_payment");
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
		// Check if the value is an array
		$_access = $this->getState('filter.access');
		if (SupportgroupsHelper::checkArray($_access))
		{
			$id .= ':' . implode(':', $_access);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_access)
		 || SupportgroupsHelper::checkString($_access))
		{
			$id .= ':' . $_access;
		}
		$id .= ':' . $this->getState('filter.ordering');
		$id .= ':' . $this->getState('filter.created_by');
		$id .= ':' . $this->getState('filter.modified_by');
		$id .= ':' . $this->getState('filter.support_group');
		$id .= ':' . $this->getState('filter.year');
		$id .= ':' . $this->getState('filter.amount');

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
			// Reset query.
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__supportgroups_payment'));
			// Only select items that are checked out.
			$query->where($db->quoteName('checked_out') . '!=0');
			$db->setQuery($query, 0, 1);
			$db->execute();
			if ($db->getNumRows())
			{
				// Get Yesterdays date.
				$date = JFactory::getDate()->modify($time)->toSql();
				// Reset query.
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

				// Check table.
				$query->update($db->quoteName('#__supportgroups_payment'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				$db->execute();
			}
		}

		return false;
	}
}
