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
	@subpackage		support_group.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;

/**
 * Supportgroups Support_group Model
 */
class SupportgroupsModelSupport_group extends JModelAdmin
{
	/**
	 * The tab layout fields array.
	 *
	 * @var      array
	 */
	protected $tabLayoutFields = array(
		'details' => array(
			'left' => array(
				'phone',
				'facility',
				'info'
			),
			'right' => array(
				'male',
				'male_children',
				'male_art',
				'female',
				'female_children',
				'female_art'
			),
			'fullwidth' => array(
				'details'
			),
			'above' => array(
				'name',
				'alias'
			),
			'under' => array(
				'marker'
			)
		),
		'location' => array(
			'left' => array(
				'area'
			),
			'fullwidth' => array(
				'note_set_marker'
			)
		)
	);

	/**
	 * @var        string    The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_SUPPORTGROUPS';

	/**
	 * The type alias for this content type.
	 *
	 * @var      string
	 * @since    3.2
	 */
	public $typeAlias = 'com_supportgroups.support_group';

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'support_group', $prefix = 'SupportgroupsTable', $config = array())
	{
		// add table path for when model gets used from other component
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_supportgroups/tables');
		// get instance of the table
		return JTable::getInstance($type, $prefix, $config);
	}
    
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			if (!empty($item->params) && !is_array($item->params))
			{
				// Convert the params field to an array.
				$registry = new Registry;
				$registry->loadString($item->params);
				$item->params = $registry->toArray();
			}

			if (!empty($item->metadata))
			{
				// Convert the metadata field to an array.
				$registry = new Registry;
				$registry->loadString($item->metadata);
				$item->metadata = $registry->toArray();
			}

			if (!empty($item->info))
			{
				// JSON Decode info.
				$item->info = json_decode($item->info);
			}
			
			if (!empty($item->id))
			{
				$item->tags = new JHelperTags;
				$item->tags->getTagIds($item->id, 'com_supportgroups.support_group');
			}
		}
		$this->support_groupvvvv = $item->id;

		return $item;
	}

	/**
	 * Method to get list data.
	 *
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getVvvpayments()
	{
		// Get the user object.
		$user = JFactory::getUser();
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the supportgroups_payment table
		$query->from($db->quoteName('#__supportgroups_payment', 'a'));

		// From the supportgroups_support_group table.
		$query->select($db->quoteName('g.name','support_group_name'));
		$query->join('LEFT', $db->quoteName('#__supportgroups_support_group', 'g') . ' ON (' . $db->quoteName('a.support_group') . ' = ' . $db->quoteName('g.id') . ')');

		// Filter by support_groupvvvv global.
		$support_groupvvvv = $this->support_groupvvvv;
		if (is_numeric($support_groupvvvv ))
		{
			$query->where('a.support_group = ' . (int) $support_groupvvvv );
		}
		elseif (is_string($support_groupvvvv))
		{
			$query->where('a.support_group = ' . $db->quote($support_groupvvvv));
		}
		else
		{
			$query->where('a.support_group = -5');
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

		// Order the results by ordering
		$query->order('a.published  ASC');
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
					$access = (JFactory::getUser()->authorise('payment.access', 'com_supportgroups.payment.' . (int) $item->id) && JFactory::getUser()->authorise('payment.access', 'com_supportgroups'));
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
					$item->year = $this->selectionTranslationVvvpayments($item->year, 'year');
				}
			}

			return $items;
		}
		return false;
	}

	/**
	 * Method to convert selection values to translatable string.
	 *
	 * @return translatable string
	 */
	public function selectionTranslationVvvpayments($value,$name)
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
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * @param   array    $options   Optional array of options for the form creation.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true, $options = array('control' => 'jform'))
	{
		// set load data option
		$options['load_data'] = $loadData;
		// Get the form.
		$form = $this->loadForm('com_supportgroups.support_group', 'support_group', $options);

		if (empty($form))
		{
			return false;
		}

		$jinput = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('a_id'))
		{
			$id = $jinput->get('a_id', 0, 'INT');
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id = $jinput->get('id', 0, 'INT');
		}

		$user = JFactory::getUser();

		// Check for existing item.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.state', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.state', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');
			// Disable fields while saving.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}
		// If this is a new item insure the greated by is set.
		if (0 == $id)
		{
			// Set the created_by to this user
			$form->setValue('created_by', null, $user->id);
		}
		// Modify the form based on Edit Creaded By access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.created_by', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.created_by', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('created_by', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('created_by', 'readonly', 'true');
			// Disable fields while saving.
			$form->setFieldAttribute('created_by', 'filter', 'unset');
		}
		// Modify the form based on Edit Creaded Date access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.created', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.created', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('created', 'disabled', 'true');
			// Disable fields while saving.
			$form->setFieldAttribute('created', 'filter', 'unset');
		}
		// Modify the form based on Edit Name access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.name', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.name', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('name', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('name', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('name'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('name', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('name', 'required', 'false');
			}
		}
		// Modify the form based on Edit Phone access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.phone', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.phone', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('phone', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('phone', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('phone'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('phone', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('phone', 'required', 'false');
			}
		}
		// Modify the form based on Edit Area access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.area', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.area', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('area', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('area', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('area'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('area', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('area', 'required', 'false');
			}
		}
		// Modify the form based on Edit Facility access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.facility', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.facility', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('facility', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('facility', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('facility'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('facility', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('facility', 'required', 'false');
			}
		}
		// Modify the form based on Edit Male access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.male', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.male', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('male', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('male', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('male'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('male', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('male', 'required', 'false');
			}
		}
		// Modify the form based on Edit Female access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.female', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.female', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('female', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('female', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('female'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('female', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('female', 'required', 'false');
			}
		}
		// Modify the form based on Edit Alias access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.alias', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.alias', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('alias', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('alias', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('alias'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('alias', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('alias', 'required', 'false');
			}
		}
		// Modify the form based on Edit Details access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.details', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.details', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('details', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('details', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('details'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('details', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('details', 'required', 'false');
			}
		}
		// Modify the form based on Edit Female Art access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.female_art', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.female_art', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('female_art', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('female_art', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('female_art'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('female_art', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('female_art', 'required', 'false');
			}
		}
		// Modify the form based on Edit Male Art access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.male_art', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.male_art', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('male_art', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('male_art', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('male_art'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('male_art', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('male_art', 'required', 'false');
			}
		}
		// Modify the form based on Edit Male Children access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.male_children', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.male_children', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('male_children', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('male_children', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('male_children'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('male_children', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('male_children', 'required', 'false');
			}
		}
		// Modify the form based on Edit Info access controls.
		if ($id != 0 && (!$user->authorise('support_group.edit.info', 'com_supportgroups.support_group.' . (int) $id))
			|| ($id == 0 && !$user->authorise('support_group.edit.info', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('info', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('info', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('info'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('info', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('info', 'required', 'false');
			}
		}
		// Only load these values if no id is found
		if (0 == $id)
		{
			// Set redirected view name
			$redirectedView = $jinput->get('ref', null, 'STRING');
			// Set field name (or fall back to view name)
			$redirectedField = $jinput->get('field', $redirectedView, 'STRING');
			// Set redirected view id
			$redirectedId = $jinput->get('refid', 0, 'INT');
			// Set field id (or fall back to redirected view id)
			$redirectedValue = $jinput->get('field_id', $redirectedId, 'INT');
			if (0 != $redirectedValue && $redirectedField)
			{
				// Now set the local-redirected field default value
				$form->setValue($redirectedField, null, $redirectedValue);
			}
		}
		return $form;
	}

	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	script files
	 */
	public function getScript()
	{
		return 'administrator/components/com_supportgroups/models/forms/support_group.js';
	}
    
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->published != -2)
			{
				return;
			}

			$user = JFactory::getUser();
			// The record has been set. Check the record permissions.
			return $user->authorise('support_group.delete', 'com_supportgroups.support_group.' . (int) $record->id);
		}
		return false;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();
		$recordId = (!empty($record->id)) ? $record->id : 0;

		if ($recordId)
		{
			// The record has been set. Check the record permissions.
			$permission = $user->authorise('support_group.edit.state', 'com_supportgroups.support_group.' . (int) $recordId);
			if (!$permission && !is_null($permission))
			{
				return false;
			}
		}
		// In the absense of better information, revert to the component permissions.
		return $user->authorise('support_group.edit.state', 'com_supportgroups');
	}
    
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	2.5
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.
		$user = JFactory::getUser();

		return $user->authorise('support_group.edit', 'com_supportgroups.support_group.'. ((int) isset($data[$key]) ? $data[$key] : 0)) or $user->authorise('support_group.edit',  'com_supportgroups');
	}
    
	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();
		
		if (isset($table->name))
		{
			$table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);
		}
		
		if (isset($table->alias) && empty($table->alias))
		{
			$table->generateAlias();
		}
		
		if (empty($table->id))
		{
			$table->created = $date->toSql();
			// set the user
			if ($table->created_by == 0 || empty($table->created_by))
			{
				$table->created_by = $user->id;
			}
			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select('MAX(ordering)')
					->from($db->quoteName('#__supportgroups_support_group'));
				$db->setQuery($query);
				$max = $db->loadResult();

				$table->ordering = $max + 1;
			}
		}
		else
		{
			$table->modified = $date->toSql();
			$table->modified_by = $user->id;
		}
        
		if (!empty($table->id))
		{
			// Increment the items version number.
			$table->version++;
		}
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_supportgroups.edit.support_group.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get the unique fields of this table.
	 *
	 * @return  mixed  An array of field names, boolean false if none is set.
	 *
	 * @since   3.0
	 */
	protected function getUniqeFields()
	{
		return false;
	}
	
	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   12.2
	 */
	public function delete(&$pks)
	{
		if (!parent::delete($pks))
		{
			return false;
		}
		
		return true;
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   12.2
	 */
	public function publish(&$pks, $value = 1)
	{
		if (!parent::publish($pks, $value))
		{
			return false;
		}
		
		return true;
        }
    
	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array  $commands  An array of commands to perform.
	 * @param   array  $pks       An array of item ids.
	 * @param   array  $contexts  An array of item contexts.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function batch($commands, $pks, $contexts)
	{
		// Sanitize ids.
		$pks = array_unique($pks);
		JArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		$done = false;

		// Set some needed variables.
		$this->user			= JFactory::getUser();
		$this->table			= $this->getTable();
		$this->tableClassName		= get_class($this->table);
		$this->contentType		= new JUcmType;
		$this->type			= $this->contentType->getTypeByTable($this->tableClassName);
		$this->canDo			= SupportgroupsHelper::getActions('support_group');
		$this->batchSet			= true;

		if (!$this->canDo->get('core.batch'))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}
        
		if ($this->type == false)
		{
			$type = new JUcmType;
			$this->type = $type->getTypeByAlias($this->typeAlias);
		}

		$this->tagsObserver = $this->table->getObserverOfClass('JTableObserverTags');

		if (!empty($commands['move_copy']))
		{
			$cmd = JArrayHelper::getValue($commands, 'move_copy', 'c');

			if ($cmd == 'c')
			{
				$result = $this->batchCopy($commands, $pks, $contexts);

				if (is_array($result))
				{
					foreach ($result as $old => $new)
					{
						$contexts[$new] = $contexts[$old];
					}
					$pks = array_values($result);
				}
				else
				{
					return false;
				}
			}
			elseif ($cmd == 'm' && !$this->batchMove($commands, $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!$done)
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));

			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Batch copy items to a new category or current.
	 *
	 * @param   integer  $values    The new values.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 * @since 12.2
	 */
	protected function batchCopy($values, $pks, $contexts)
	{
		if (empty($this->batchSet))
		{
			// Set some needed variables.
			$this->user 		= JFactory::getUser();
			$this->table 		= $this->getTable();
			$this->tableClassName	= get_class($this->table);
			$this->canDo		= SupportgroupsHelper::getActions('support_group');
		}

		if (!$this->canDo->get('support_group.create') && !$this->canDo->get('support_group.batch'))
		{
			return false;
		}

		// get list of uniqe fields
		$uniqeFields = $this->getUniqeFields();
		// remove move_copy from array
		unset($values['move_copy']);

		// make sure published is set
		if (!isset($values['published']))
		{
			$values['published'] = 0;
		}
		elseif (isset($values['published']) && !$this->canDo->get('support_group.edit.state'))
		{
				$values['published'] = 0;
		}

		$newIds = array();
		// Parent exists so let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$this->table->reset();

			// only allow copy if user may edit this item.
			if (!$this->user->authorise('support_group.edit', $contexts[$pk]))
			{
				// Not fatal error
				$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
				continue;
			}

			// Check that the row actually exists
			if (!$this->table->load($pk))
			{
				if ($error = $this->table->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}
			list($this->table->name, $this->table->alias) = $this->_generateNewTitle($this->table->alias, $this->table->name);

			// insert all set values
			if (SupportgroupsHelper::checkArray($values))
			{
				foreach ($values as $key => $value)
				{
					if (strlen($value) > 0 && isset($this->table->$key))
					{
						$this->table->$key = $value;
					}
				}
			}

			// update all uniqe fields
			if (SupportgroupsHelper::checkArray($uniqeFields))
			{
				foreach ($uniqeFields as $uniqeField)
				{
					$this->table->$uniqeField = $this->generateUniqe($uniqeField,$this->table->$uniqeField);
				}
			}

			// Reset the ID because we are making a copy
			$this->table->id = 0;

			// TODO: Deal with ordering?
			// $this->table->ordering = 1;

			// Check the row.
			if (!$this->table->check())
			{
				$this->setError($this->table->getError());

				return false;
			}

			if (!empty($this->type))
			{
				$this->createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);
			}

			// Store the row.
			if (!$this->table->store())
			{
				$this->setError($this->table->getError());

				return false;
			}

			// Get the new item ID
			$newId = $this->table->get('id');

			// Add the new ID to the array
			$newIds[$pk] = $newId;
		}

		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	/**
	 * Batch move items to a new category
	 *
	 * @param   integer  $value     The new category ID.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 *
	 * @since 12.2
	 */
	protected function batchMove($values, $pks, $contexts)
	{
		if (empty($this->batchSet))
		{
			// Set some needed variables.
			$this->user		= JFactory::getUser();
			$this->table		= $this->getTable();
			$this->tableClassName	= get_class($this->table);
			$this->canDo		= SupportgroupsHelper::getActions('support_group');
		}

		if (!$this->canDo->get('support_group.edit') && !$this->canDo->get('support_group.batch'))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
			return false;
		}

		// make sure published only updates if user has the permission.
		if (isset($values['published']) && !$this->canDo->get('support_group.edit.state'))
		{
			unset($values['published']);
		}
		// remove move_copy from array
		unset($values['move_copy']);

		// Parent exists so we proceed
		foreach ($pks as $pk)
		{
			if (!$this->user->authorise('support_group.edit', $contexts[$pk]))
			{
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
				return false;
			}

			// Check that the row actually exists
			if (!$this->table->load($pk))
			{
				if ($error = $this->table->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// insert all set values.
			if (SupportgroupsHelper::checkArray($values))
			{
				foreach ($values as $key => $value)
				{
					// Do special action for access.
					if ('access' === $key && strlen($value) > 0)
					{
						$this->table->$key = $value;
					}
					elseif (strlen($value) > 0 && isset($this->table->$key))
					{
						$this->table->$key = $value;
					}
				}
			}


			// Check the row.
			if (!$this->table->check())
			{
				$this->setError($this->table->getError());

				return false;
			}

			if (!empty($this->type))
			{
				$this->createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);
			}

			// Store the row.
			if (!$this->table->store())
			{
				$this->setError($this->table->getError());

				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		$input	= JFactory::getApplication()->input;
		$filter	= JFilterInput::getInstance();
        
		// set the metadata to the Item Data
		if (isset($data['metadata']) && isset($data['metadata']['author']))
		{
			$data['metadata']['author'] = $filter->clean($data['metadata']['author'], 'TRIM');
            
			$metadata = new JRegistry;
			$metadata->loadArray($data['metadata']);
			$data['metadata'] = (string) $metadata;
		}

		// Set the empty info item to data
		if (!isset($data['info']))
		{
			$data['info'] = '';
		}

		// Set the info string to JSON string.
		if (isset($data['info']))
		{
			$data['info'] = (string) json_encode($data['info']);
		}
        
		// Set the Params Items to data
		if (isset($data['params']) && is_array($data['params']))
		{
			$params = new JRegistry;
			$params->loadArray($data['params']);
			$data['params'] = (string) $params;
		}

		// Alter the name for save as copy
		if ($input->get('task') === 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));

			if ($data['name'] == $origTable->name)
			{
				list($name, $alias) = $this->_generateNewTitle($data['alias'], $data['name']);
				$data['name'] = $name;
				$data['alias'] = $alias;
			}
			else
			{
				if ($data['alias'] == $origTable->alias)
				{
					$data['alias'] = '';
				}
			}

			$data['published'] = 0;
		}

		// Automatic handling of alias for empty fields
		if (in_array($input->get('task'), array('apply', 'save', 'save2new')) && (int) $input->get('id') == 0)
		{
			if ($data['alias'] == null || empty($data['alias']))
			{
				if (JFactory::getConfig()->get('unicodeslugs') == 1)
				{
					$data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['name']);
				}
				else
				{
					$data['alias'] = JFilterOutput::stringURLSafe($data['name']);
				}

				$table = JTable::getInstance('support_group', 'supportgroupsTable');

				if ($table->load(array('alias' => $data['alias'])) && ($table->id != $data['id'] || $data['id'] == 0))
				{
					$msg = JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_SAVE_WARNING');
				}

				$data['alias'] = $this->_generateNewTitle($data['alias']);

				if (isset($msg))
				{
					JFactory::getApplication()->enqueueMessage($msg, 'warning');
				}
			}
		}

		// Alter the uniqe field for save as copy
		if ($input->get('task') === 'save2copy')
		{
			// Automatic handling of other uniqe fields
			$uniqeFields = $this->getUniqeFields();
			if (SupportgroupsHelper::checkArray($uniqeFields))
			{
				foreach ($uniqeFields as $uniqeField)
				{
					$data[$uniqeField] = $this->generateUniqe($uniqeField,$data[$uniqeField]);
				}
			}
		}
		
		if (parent::save($data))
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Method to generate a uniqe value.
	 *
	 * @param   string  $field name.
	 * @param   string  $value data.
	 *
	 * @return  string  New value.
	 *
	 * @since   3.0
	 */
	protected function generateUniqe($field,$value)
	{

		// set field value uniqe 
		$table = $this->getTable();

		while ($table->load(array($field => $value)))
		{
			$value = JString::increment($value);
		}

		return $value;
	}

	/**
	 * Method to change the title/s & alias.
	 *
	 * @param   string         $alias        The alias.
	 * @param   string/array   $title        The title.
	 *
	 * @return	array/string  Contains the modified title/s and/or alias.
	 *
	 */
	protected function _generateNewTitle($alias, $title = null)
	{

		// Alter the title/s & alias
		$table = $this->getTable();

		while ($table->load(array('alias' => $alias)))
		{
			// Check if this is an array of titles
			if (SupportgroupsHelper::checkArray($title))
			{
				foreach($title as $nr => &$_title)
				{
					$_title = JString::increment($_title);
				}
			}
			// Make sure we have a title
			elseif ($title)
			{
				$title = JString::increment($title);
			}
			$alias = JString::increment($alias, 'dash');
		}
		// Check if this is an array of titles
		if (SupportgroupsHelper::checkArray($title))
		{
			$title[] = $alias;
			return $title;
		}
		// Make sure we have a title
		elseif ($title)
		{
			return array($title, $alias);
		}
		// We only had an alias
		return $alias;
	}
}
