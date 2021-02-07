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
	@build			7th February, 2021
	@created		24th February, 2016
	@package		Support Groups
	@subpackage		country.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

/**
 * Supportgroups Country Model
 */
class SupportgroupsModelCountry extends JModelAdmin
{
	/**
	 * The tab layout fields array.
	 *
	 * @var      array
	 */
	protected $tabLayoutFields = array(
		'settings' => array(
			'left' => array(
				'currency',
				'worldzone'
			),
			'right' => array(
				'codethree',
				'codetwo'
			),
			'above' => array(
				'name',
				'alias'
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
	public $typeAlias = 'com_supportgroups.country';

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
	public function getTable($type = 'country', $prefix = 'SupportgroupsTable', $config = array())
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
			
			if (!empty($item->id))
			{
				$item->tags = new JHelperTags;
				$item->tags->getTagIds($item->id, 'com_supportgroups.country');
			}
		}
		$this->countryvvvy = $item->id;

		return $item;
	}

	/**
	 * Method to get list data.
	 *
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getVvyregions()
	{
		// Get the user object.
		$user = JFactory::getUser();
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the supportgroups_region table
		$query->from($db->quoteName('#__supportgroups_region', 'a'));

		// From the supportgroups_country table.
		$query->select($db->quoteName('g.name','country_name'));
		$query->join('LEFT', $db->quoteName('#__supportgroups_country', 'g') . ' ON (' . $db->quoteName('a.country') . ' = ' . $db->quoteName('g.id') . ')');

		// Filter by countryvvvy global.
		$countryvvvy = $this->countryvvvy;
		if (is_numeric($countryvvvy ))
		{
			$query->where('a.country = ' . (int) $countryvvvy );
		}
		elseif (is_string($countryvvvy))
		{
			$query->where('a.country = ' . $db->quote($countryvvvy));
		}
		else
		{
			$query->where('a.country = -5');
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

		// Order the results by ordering
		$query->order('a.published  ASC');
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
				// Get the user object if not set.
				if (!isset($user) || !SupportgroupsHelper::checkObject($user))
				{
					$user = JFactory::getUser();
				}
				foreach ($items as $nr => &$item)
				{
					// Remove items the user can't access.
					$access = ($user->authorise('region.access', 'com_supportgroups.region.' . (int) $item->id) && $user->authorise('region.access', 'com_supportgroups'));
					if (!$access)
					{
						unset($items[$nr]);
						continue;
					}

				}
			}
			return $items;
		}
		return false;
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
		// check if xpath was set in options
		$xpath = false;
		if (isset($options['xpath']))
		{
			$xpath = $options['xpath'];
			unset($options['xpath']);
		}
		// check if clear form was set in options
		$clear = false;
		if (isset($options['clear']))
		{
			$clear = $options['clear'];
			unset($options['clear']);
		}

		// Get the form.
		$form = $this->loadForm('com_supportgroups.country', 'country', $options, $clear, $xpath);

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
		if ($id != 0 && (!$user->authorise('country.edit.state', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.edit.state', 'com_supportgroups')))
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
		if (!$user->authorise('core.edit.created_by', 'com_supportgroups'))
		{
			// Disable fields for display.
			$form->setFieldAttribute('created_by', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('created_by', 'readonly', 'true');
			// Disable fields while saving.
			$form->setFieldAttribute('created_by', 'filter', 'unset');
		}
		// Modify the form based on Edit Creaded Date access controls.
		if (!$user->authorise('core.edit.created', 'com_supportgroups'))
		{
			// Disable fields for display.
			$form->setFieldAttribute('created', 'disabled', 'true');
			// Disable fields while saving.
			$form->setFieldAttribute('created', 'filter', 'unset');
		}
		// Modify the form based on Edit Name access controls.
		if ($id != 0 && (!$user->authorise('country.edit.name', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.edit.name', 'com_supportgroups')))
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
		// Modify the from the form based on Name access controls.
		if ($id != 0 && (!$user->authorise('country.access.name', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.access.name', 'com_supportgroups')))
		{
			// Remove the field
			$form->removeField('name');
		}
		// Modify the form based on View Name access controls.
		if ($id != 0 && (!$user->authorise('country.view.name', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.view.name', 'com_supportgroups')))
		{
			// Make the field hidded.
			$form->setFieldAttribute('name', 'type', 'hidden');
			// If there is no value continue.
			if (!($val = $form->getValue('name')))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('name', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('name', 'required', 'false');
				// Make sure
				$form->setValue('name', null, '');
			}
			elseif (SupportgroupsHelper::checkArray($val))
			{
				// We have to unset then (TODO)
				// Hiddend field can not handel array value
				// Even if we convert to json we get an error
				$form->removeField('name');
			}
		}
		// Modify the form based on Edit Currency access controls.
		if ($id != 0 && (!$user->authorise('country.edit.currency', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.edit.currency', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('currency', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('currency', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('currency'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('currency', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('currency', 'required', 'false');
			}
		}
		// Modify the from the form based on Currency access controls.
		if ($id != 0 && (!$user->authorise('country.access.currency', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.access.currency', 'com_supportgroups')))
		{
			// Remove the field
			$form->removeField('currency');
		}
		// Modify the form based on View Currency access controls.
		if ($id != 0 && (!$user->authorise('country.view.currency', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.view.currency', 'com_supportgroups')))
		{
			// Make the field hidded.
			$form->setFieldAttribute('currency', 'type', 'hidden');
			// If there is no value continue.
			if (!($val = $form->getValue('currency')))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('currency', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('currency', 'required', 'false');
				// Make sure
				$form->setValue('currency', null, '');
			}
			elseif (SupportgroupsHelper::checkArray($val))
			{
				// We have to unset then (TODO)
				// Hiddend field can not handel array value
				// Even if we convert to json we get an error
				$form->removeField('currency');
			}
		}
		// Modify the form based on Edit Worldzone access controls.
		if ($id != 0 && (!$user->authorise('country.edit.worldzone', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.edit.worldzone', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('worldzone', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('worldzone', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('worldzone'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('worldzone', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('worldzone', 'required', 'false');
			}
		}
		// Modify the from the form based on Worldzone access controls.
		if ($id != 0 && (!$user->authorise('country.access.worldzone', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.access.worldzone', 'com_supportgroups')))
		{
			// Remove the field
			$form->removeField('worldzone');
		}
		// Modify the form based on View Worldzone access controls.
		if ($id != 0 && (!$user->authorise('country.view.worldzone', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.view.worldzone', 'com_supportgroups')))
		{
			// Make the field hidded.
			$form->setFieldAttribute('worldzone', 'type', 'hidden');
			// If there is no value continue.
			if (!($val = $form->getValue('worldzone')))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('worldzone', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('worldzone', 'required', 'false');
				// Make sure
				$form->setValue('worldzone', null, '');
			}
			elseif (SupportgroupsHelper::checkArray($val))
			{
				// We have to unset then (TODO)
				// Hiddend field can not handel array value
				// Even if we convert to json we get an error
				$form->removeField('worldzone');
			}
		}
		// Modify the form based on Edit Codethree access controls.
		if ($id != 0 && (!$user->authorise('country.edit.codethree', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.edit.codethree', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('codethree', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('codethree', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('codethree'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('codethree', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('codethree', 'required', 'false');
			}
		}
		// Modify the from the form based on Codethree access controls.
		if ($id != 0 && (!$user->authorise('country.access.codethree', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.access.codethree', 'com_supportgroups')))
		{
			// Remove the field
			$form->removeField('codethree');
		}
		// Modify the form based on View Codethree access controls.
		if ($id != 0 && (!$user->authorise('country.view.codethree', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.view.codethree', 'com_supportgroups')))
		{
			// Make the field hidded.
			$form->setFieldAttribute('codethree', 'type', 'hidden');
			// If there is no value continue.
			if (!($val = $form->getValue('codethree')))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('codethree', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('codethree', 'required', 'false');
				// Make sure
				$form->setValue('codethree', null, '');
			}
			elseif (SupportgroupsHelper::checkArray($val))
			{
				// We have to unset then (TODO)
				// Hiddend field can not handel array value
				// Even if we convert to json we get an error
				$form->removeField('codethree');
			}
		}
		// Modify the form based on Edit Codetwo access controls.
		if ($id != 0 && (!$user->authorise('country.edit.codetwo', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.edit.codetwo', 'com_supportgroups')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('codetwo', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('codetwo', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('codetwo'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('codetwo', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('codetwo', 'required', 'false');
			}
		}
		// Modify the from the form based on Codetwo access controls.
		if ($id != 0 && (!$user->authorise('country.access.codetwo', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.access.codetwo', 'com_supportgroups')))
		{
			// Remove the field
			$form->removeField('codetwo');
		}
		// Modify the form based on View Codetwo access controls.
		if ($id != 0 && (!$user->authorise('country.view.codetwo', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.view.codetwo', 'com_supportgroups')))
		{
			// Make the field hidded.
			$form->setFieldAttribute('codetwo', 'type', 'hidden');
			// If there is no value continue.
			if (!($val = $form->getValue('codetwo')))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('codetwo', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('codetwo', 'required', 'false');
				// Make sure
				$form->setValue('codetwo', null, '');
			}
			elseif (SupportgroupsHelper::checkArray($val))
			{
				// We have to unset then (TODO)
				// Hiddend field can not handel array value
				// Even if we convert to json we get an error
				$form->removeField('codetwo');
			}
		}
		// Modify the form based on Edit Alias access controls.
		if ($id != 0 && (!$user->authorise('country.edit.alias', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.edit.alias', 'com_supportgroups')))
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
		// Modify the from the form based on Alias access controls.
		if ($id != 0 && (!$user->authorise('country.access.alias', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.access.alias', 'com_supportgroups')))
		{
			// Remove the field
			$form->removeField('alias');
		}
		// Modify the form based on View Alias access controls.
		if ($id != 0 && (!$user->authorise('country.view.alias', 'com_supportgroups.country.' . (int) $id))
			|| ($id == 0 && !$user->authorise('country.view.alias', 'com_supportgroups')))
		{
			// Make the field hidded.
			$form->setFieldAttribute('alias', 'type', 'hidden');
			// If there is no value continue.
			if (!($val = $form->getValue('alias')))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('alias', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('alias', 'required', 'false');
				// Make sure
				$form->setValue('alias', null, '');
			}
			elseif (SupportgroupsHelper::checkArray($val))
			{
				// We have to unset then (TODO)
				// Hiddend field can not handel array value
				// Even if we convert to json we get an error
				$form->removeField('alias');
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
		return 'administrator/components/com_supportgroups/models/forms/country.js';
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
			return $user->authorise('country.delete', 'com_supportgroups.country.' . (int) $record->id);
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
			$permission = $user->authorise('country.edit.state', 'com_supportgroups.country.' . (int) $recordId);
			if (!$permission && !is_null($permission))
			{
				return false;
			}
		}
		// In the absense of better information, revert to the component permissions.
		return $user->authorise('country.edit.state', 'com_supportgroups');
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

		return $user->authorise('country.edit', 'com_supportgroups.country.'. ((int) isset($data[$key]) ? $data[$key] : 0)) or $user->authorise('country.edit',  'com_supportgroups');
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
					->from($db->quoteName('#__supportgroups_country'));
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
		$data = JFactory::getApplication()->getUserState('com_supportgroups.edit.country.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
			// run the perprocess of the data
			$this->preprocessData('com_supportgroups.country', $data);
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
	protected function getUniqueFields()
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
		ArrayHelper::toInteger($pks);

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
		$this->canDo			= SupportgroupsHelper::getActions('country');
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
			$cmd = ArrayHelper::getValue($commands, 'move_copy', 'c');

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
			$this->canDo		= SupportgroupsHelper::getActions('country');
		}

		if (!$this->canDo->get('country.create') && !$this->canDo->get('country.batch'))
		{
			return false;
		}

		// get list of unique fields
		$uniqueFields = $this->getUniqueFields();
		// remove move_copy from array
		unset($values['move_copy']);

		// make sure published is set
		if (!isset($values['published']))
		{
			$values['published'] = 0;
		}
		elseif (isset($values['published']) && !$this->canDo->get('country.edit.state'))
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
			if (!$this->user->authorise('country.edit', $contexts[$pk]))
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

			// update all unique fields
			if (SupportgroupsHelper::checkArray($uniqueFields))
			{
				foreach ($uniqueFields as $uniqueField)
				{
					$this->table->$uniqueField = $this->generateUnique($uniqueField,$this->table->$uniqueField);
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
			$this->canDo		= SupportgroupsHelper::getActions('country');
		}

		if (!$this->canDo->get('country.edit') && !$this->canDo->get('country.batch'))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
			return false;
		}

		// make sure published only updates if user has the permission.
		if (isset($values['published']) && !$this->canDo->get('country.edit.state'))
		{
			unset($values['published']);
		}
		// remove move_copy from array
		unset($values['move_copy']);

		// Parent exists so we proceed
		foreach ($pks as $pk)
		{
			if (!$this->user->authorise('country.edit', $contexts[$pk]))
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

				$table = JTable::getInstance('country', 'supportgroupsTable');

				if ($table->load(array('alias' => $data['alias'])) && ($table->id != $data['id'] || $data['id'] == 0))
				{
					$msg = JText::_('COM_SUPPORTGROUPS_COUNTRY_SAVE_WARNING');
				}

				$data['alias'] = $this->_generateNewTitle($data['alias']);

				if (isset($msg))
				{
					JFactory::getApplication()->enqueueMessage($msg, 'warning');
				}
			}
		}

		// Alter the unique field for save as copy
		if ($input->get('task') === 'save2copy')
		{
			// Automatic handling of other unique fields
			$uniqueFields = $this->getUniqueFields();
			if (SupportgroupsHelper::checkArray($uniqueFields))
			{
				foreach ($uniqueFields as $uniqueField)
				{
					$data[$uniqueField] = $this->generateUnique($uniqueField,$data[$uniqueField]);
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
	 * Method to generate a unique value.
	 *
	 * @param   string  $field name.
	 * @param   string  $value data.
	 *
	 * @return  string  New value.
	 *
	 * @since   3.0
	 */
	protected function generateUnique($field,$value)
	{

		// set field value unique
		$table = $this->getTable();

		while ($table->load(array($field => $value)))
		{
			$value = StringHelper::increment($value);
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
					$_title = StringHelper::increment($_title);
				}
			}
			// Make sure we have a title
			elseif ($title)
			{
				$title = StringHelper::increment($title);
			}
			$alias = StringHelper::increment($alias, 'dash');
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
