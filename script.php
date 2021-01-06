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
	@build			6th January, 2021
	@created		24th February, 2016
	@package		Support Groups
	@subpackage		script.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal');

/**
 * Script File of Supportgroups Component
 */
class com_supportgroupsInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function __construct(JAdapterInstance $parent) {}

	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $parent) {}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $parent)
	{
		// Get Application object
		$app = JFactory::getApplication();

		// Get The Database object
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Support_group alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.support_group') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$support_group_found = $db->getNumRows();
		// Now check if there were any rows
		if ($support_group_found)
		{
			// Since there are load the needed  support_group type ids
			$support_group_ids = $db->loadColumn();
			// Remove Support_group from the content type table
			$support_group_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.support_group') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($support_group_condition);
			$db->setQuery($query);
			// Execute the query to remove Support_group items
			$support_group_done = $db->execute();
			if ($support_group_done)
			{
				// If successfully remove Support_group add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.support_group) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Support_group items from the contentitem tag map table
			$support_group_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.support_group') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($support_group_condition);
			$db->setQuery($query);
			// Execute the query to remove Support_group items
			$support_group_done = $db->execute();
			if ($support_group_done)
			{
				// If successfully remove Support_group add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.support_group) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Support_group items from the ucm content table
			$support_group_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_supportgroups.support_group') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($support_group_condition);
			$db->setQuery($query);
			// Execute the query to remove Support_group items
			$support_group_done = $db->execute();
			if ($support_group_done)
			{
				// If successfully removed Support_group add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.support_group) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Support_group items are cleared from DB
			foreach ($support_group_ids as $support_group_id)
			{
				// Remove Support_group items from the ucm base table
				$support_group_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $support_group_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($support_group_condition);
				$db->setQuery($query);
				// Execute the query to remove Support_group items
				$db->execute();

				// Remove Support_group items from the ucm history table
				$support_group_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $support_group_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($support_group_condition);
				$db->setQuery($query);
				// Execute the query to remove Support_group items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Payment alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.payment') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$payment_found = $db->getNumRows();
		// Now check if there were any rows
		if ($payment_found)
		{
			// Since there are load the needed  payment type ids
			$payment_ids = $db->loadColumn();
			// Remove Payment from the content type table
			$payment_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.payment') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($payment_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment items
			$payment_done = $db->execute();
			if ($payment_done)
			{
				// If successfully remove Payment add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.payment) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Payment items from the contentitem tag map table
			$payment_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.payment') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($payment_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment items
			$payment_done = $db->execute();
			if ($payment_done)
			{
				// If successfully remove Payment add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.payment) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Payment items from the ucm content table
			$payment_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_supportgroups.payment') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($payment_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment items
			$payment_done = $db->execute();
			if ($payment_done)
			{
				// If successfully removed Payment add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.payment) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Payment items are cleared from DB
			foreach ($payment_ids as $payment_id)
			{
				// Remove Payment items from the ucm base table
				$payment_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $payment_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($payment_condition);
				$db->setQuery($query);
				// Execute the query to remove Payment items
				$db->execute();

				// Remove Payment items from the ucm history table
				$payment_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $payment_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($payment_condition);
				$db->setQuery($query);
				// Execute the query to remove Payment items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Facility alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.facility') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$facility_found = $db->getNumRows();
		// Now check if there were any rows
		if ($facility_found)
		{
			// Since there are load the needed  facility type ids
			$facility_ids = $db->loadColumn();
			// Remove Facility from the content type table
			$facility_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.facility') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($facility_condition);
			$db->setQuery($query);
			// Execute the query to remove Facility items
			$facility_done = $db->execute();
			if ($facility_done)
			{
				// If successfully remove Facility add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.facility) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Facility items from the contentitem tag map table
			$facility_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.facility') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($facility_condition);
			$db->setQuery($query);
			// Execute the query to remove Facility items
			$facility_done = $db->execute();
			if ($facility_done)
			{
				// If successfully remove Facility add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.facility) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Facility items from the ucm content table
			$facility_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_supportgroups.facility') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($facility_condition);
			$db->setQuery($query);
			// Execute the query to remove Facility items
			$facility_done = $db->execute();
			if ($facility_done)
			{
				// If successfully removed Facility add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.facility) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Facility items are cleared from DB
			foreach ($facility_ids as $facility_id)
			{
				// Remove Facility items from the ucm base table
				$facility_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $facility_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($facility_condition);
				$db->setQuery($query);
				// Execute the query to remove Facility items
				$db->execute();

				// Remove Facility items from the ucm history table
				$facility_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $facility_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($facility_condition);
				$db->setQuery($query);
				// Execute the query to remove Facility items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Facility_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.facility_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$facility_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($facility_type_found)
		{
			// Since there are load the needed  facility_type type ids
			$facility_type_ids = $db->loadColumn();
			// Remove Facility_type from the content type table
			$facility_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.facility_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($facility_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Facility_type items
			$facility_type_done = $db->execute();
			if ($facility_type_done)
			{
				// If successfully remove Facility_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.facility_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Facility_type items from the contentitem tag map table
			$facility_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.facility_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($facility_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Facility_type items
			$facility_type_done = $db->execute();
			if ($facility_type_done)
			{
				// If successfully remove Facility_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.facility_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Facility_type items from the ucm content table
			$facility_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_supportgroups.facility_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($facility_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Facility_type items
			$facility_type_done = $db->execute();
			if ($facility_type_done)
			{
				// If successfully removed Facility_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.facility_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Facility_type items are cleared from DB
			foreach ($facility_type_ids as $facility_type_id)
			{
				// Remove Facility_type items from the ucm base table
				$facility_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $facility_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($facility_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Facility_type items
				$db->execute();

				// Remove Facility_type items from the ucm history table
				$facility_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $facility_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($facility_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Facility_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Additional_info alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.additional_info') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$additional_info_found = $db->getNumRows();
		// Now check if there were any rows
		if ($additional_info_found)
		{
			// Since there are load the needed  additional_info type ids
			$additional_info_ids = $db->loadColumn();
			// Remove Additional_info from the content type table
			$additional_info_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.additional_info') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($additional_info_condition);
			$db->setQuery($query);
			// Execute the query to remove Additional_info items
			$additional_info_done = $db->execute();
			if ($additional_info_done)
			{
				// If successfully remove Additional_info add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.additional_info) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Additional_info items from the contentitem tag map table
			$additional_info_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.additional_info') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($additional_info_condition);
			$db->setQuery($query);
			// Execute the query to remove Additional_info items
			$additional_info_done = $db->execute();
			if ($additional_info_done)
			{
				// If successfully remove Additional_info add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.additional_info) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Additional_info items from the ucm content table
			$additional_info_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_supportgroups.additional_info') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($additional_info_condition);
			$db->setQuery($query);
			// Execute the query to remove Additional_info items
			$additional_info_done = $db->execute();
			if ($additional_info_done)
			{
				// If successfully removed Additional_info add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.additional_info) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Additional_info items are cleared from DB
			foreach ($additional_info_ids as $additional_info_id)
			{
				// Remove Additional_info items from the ucm base table
				$additional_info_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $additional_info_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($additional_info_condition);
				$db->setQuery($query);
				// Execute the query to remove Additional_info items
				$db->execute();

				// Remove Additional_info items from the ucm history table
				$additional_info_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $additional_info_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($additional_info_condition);
				$db->setQuery($query);
				// Execute the query to remove Additional_info items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Info_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.info_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$info_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($info_type_found)
		{
			// Since there are load the needed  info_type type ids
			$info_type_ids = $db->loadColumn();
			// Remove Info_type from the content type table
			$info_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.info_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($info_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Info_type items
			$info_type_done = $db->execute();
			if ($info_type_done)
			{
				// If successfully remove Info_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.info_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Info_type items from the contentitem tag map table
			$info_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.info_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($info_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Info_type items
			$info_type_done = $db->execute();
			if ($info_type_done)
			{
				// If successfully remove Info_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.info_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Info_type items from the ucm content table
			$info_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_supportgroups.info_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($info_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Info_type items
			$info_type_done = $db->execute();
			if ($info_type_done)
			{
				// If successfully removed Info_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.info_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Info_type items are cleared from DB
			foreach ($info_type_ids as $info_type_id)
			{
				// Remove Info_type items from the ucm base table
				$info_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $info_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($info_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Info_type items
				$db->execute();

				// Remove Info_type items from the ucm history table
				$info_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $info_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($info_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Info_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Area alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.area') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$area_found = $db->getNumRows();
		// Now check if there were any rows
		if ($area_found)
		{
			// Since there are load the needed  area type ids
			$area_ids = $db->loadColumn();
			// Remove Area from the content type table
			$area_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.area') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($area_condition);
			$db->setQuery($query);
			// Execute the query to remove Area items
			$area_done = $db->execute();
			if ($area_done)
			{
				// If successfully remove Area add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.area) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Area items from the contentitem tag map table
			$area_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.area') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($area_condition);
			$db->setQuery($query);
			// Execute the query to remove Area items
			$area_done = $db->execute();
			if ($area_done)
			{
				// If successfully remove Area add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.area) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Area items from the ucm content table
			$area_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_supportgroups.area') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($area_condition);
			$db->setQuery($query);
			// Execute the query to remove Area items
			$area_done = $db->execute();
			if ($area_done)
			{
				// If successfully removed Area add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.area) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Area items are cleared from DB
			foreach ($area_ids as $area_id)
			{
				// Remove Area items from the ucm base table
				$area_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $area_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($area_condition);
				$db->setQuery($query);
				// Execute the query to remove Area items
				$db->execute();

				// Remove Area items from the ucm history table
				$area_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $area_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($area_condition);
				$db->setQuery($query);
				// Execute the query to remove Area items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Area_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.area_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$area_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($area_type_found)
		{
			// Since there are load the needed  area_type type ids
			$area_type_ids = $db->loadColumn();
			// Remove Area_type from the content type table
			$area_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.area_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($area_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Area_type items
			$area_type_done = $db->execute();
			if ($area_type_done)
			{
				// If successfully remove Area_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.area_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Area_type items from the contentitem tag map table
			$area_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.area_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($area_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Area_type items
			$area_type_done = $db->execute();
			if ($area_type_done)
			{
				// If successfully remove Area_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.area_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Area_type items from the ucm content table
			$area_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_supportgroups.area_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($area_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Area_type items
			$area_type_done = $db->execute();
			if ($area_type_done)
			{
				// If successfully removed Area_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.area_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Area_type items are cleared from DB
			foreach ($area_type_ids as $area_type_id)
			{
				// Remove Area_type items from the ucm base table
				$area_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $area_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($area_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Area_type items
				$db->execute();

				// Remove Area_type items from the ucm history table
				$area_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $area_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($area_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Area_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Region alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.region') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$region_found = $db->getNumRows();
		// Now check if there were any rows
		if ($region_found)
		{
			// Since there are load the needed  region type ids
			$region_ids = $db->loadColumn();
			// Remove Region from the content type table
			$region_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.region') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($region_condition);
			$db->setQuery($query);
			// Execute the query to remove Region items
			$region_done = $db->execute();
			if ($region_done)
			{
				// If successfully remove Region add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.region) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Region items from the contentitem tag map table
			$region_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.region') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($region_condition);
			$db->setQuery($query);
			// Execute the query to remove Region items
			$region_done = $db->execute();
			if ($region_done)
			{
				// If successfully remove Region add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.region) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Region items from the ucm content table
			$region_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_supportgroups.region') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($region_condition);
			$db->setQuery($query);
			// Execute the query to remove Region items
			$region_done = $db->execute();
			if ($region_done)
			{
				// If successfully removed Region add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.region) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Region items are cleared from DB
			foreach ($region_ids as $region_id)
			{
				// Remove Region items from the ucm base table
				$region_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $region_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($region_condition);
				$db->setQuery($query);
				// Execute the query to remove Region items
				$db->execute();

				// Remove Region items from the ucm history table
				$region_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $region_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($region_condition);
				$db->setQuery($query);
				// Execute the query to remove Region items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Country alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.country') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$country_found = $db->getNumRows();
		// Now check if there were any rows
		if ($country_found)
		{
			// Since there are load the needed  country type ids
			$country_ids = $db->loadColumn();
			// Remove Country from the content type table
			$country_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.country') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($country_condition);
			$db->setQuery($query);
			// Execute the query to remove Country items
			$country_done = $db->execute();
			if ($country_done)
			{
				// If successfully remove Country add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.country) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Country items from the contentitem tag map table
			$country_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.country') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($country_condition);
			$db->setQuery($query);
			// Execute the query to remove Country items
			$country_done = $db->execute();
			if ($country_done)
			{
				// If successfully remove Country add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.country) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Country items from the ucm content table
			$country_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_supportgroups.country') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($country_condition);
			$db->setQuery($query);
			// Execute the query to remove Country items
			$country_done = $db->execute();
			if ($country_done)
			{
				// If successfully removed Country add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.country) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Country items are cleared from DB
			foreach ($country_ids as $country_id)
			{
				// Remove Country items from the ucm base table
				$country_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $country_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($country_condition);
				$db->setQuery($query);
				// Execute the query to remove Country items
				$db->execute();

				// Remove Country items from the ucm history table
				$country_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $country_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($country_condition);
				$db->setQuery($query);
				// Execute the query to remove Country items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Currency alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.currency') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$currency_found = $db->getNumRows();
		// Now check if there were any rows
		if ($currency_found)
		{
			// Since there are load the needed  currency type ids
			$currency_ids = $db->loadColumn();
			// Remove Currency from the content type table
			$currency_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.currency') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($currency_condition);
			$db->setQuery($query);
			// Execute the query to remove Currency items
			$currency_done = $db->execute();
			if ($currency_done)
			{
				// If successfully remove Currency add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.currency) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Currency items from the contentitem tag map table
			$currency_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.currency') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($currency_condition);
			$db->setQuery($query);
			// Execute the query to remove Currency items
			$currency_done = $db->execute();
			if ($currency_done)
			{
				// If successfully remove Currency add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.currency) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Currency items from the ucm content table
			$currency_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_supportgroups.currency') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($currency_condition);
			$db->setQuery($query);
			// Execute the query to remove Currency items
			$currency_done = $db->execute();
			if ($currency_done)
			{
				// If successfully removed Currency add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.currency) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Currency items are cleared from DB
			foreach ($currency_ids as $currency_id)
			{
				// Remove Currency items from the ucm base table
				$currency_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $currency_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($currency_condition);
				$db->setQuery($query);
				// Execute the query to remove Currency items
				$db->execute();

				// Remove Currency items from the ucm history table
				$currency_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $currency_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($currency_condition);
				$db->setQuery($query);
				// Execute the query to remove Currency items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Help_document alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.help_document') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$help_document_found = $db->getNumRows();
		// Now check if there were any rows
		if ($help_document_found)
		{
			// Since there are load the needed  help_document type ids
			$help_document_ids = $db->loadColumn();
			// Remove Help_document from the content type table
			$help_document_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.help_document') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($help_document_condition);
			$db->setQuery($query);
			// Execute the query to remove Help_document items
			$help_document_done = $db->execute();
			if ($help_document_done)
			{
				// If successfully remove Help_document add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.help_document) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Help_document items from the contentitem tag map table
			$help_document_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.help_document') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($help_document_condition);
			$db->setQuery($query);
			// Execute the query to remove Help_document items
			$help_document_done = $db->execute();
			if ($help_document_done)
			{
				// If successfully remove Help_document add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.help_document) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Help_document items from the ucm content table
			$help_document_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_supportgroups.help_document') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($help_document_condition);
			$db->setQuery($query);
			// Execute the query to remove Help_document items
			$help_document_done = $db->execute();
			if ($help_document_done)
			{
				// If successfully removed Help_document add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.help_document) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Help_document items are cleared from DB
			foreach ($help_document_ids as $help_document_id)
			{
				// Remove Help_document items from the ucm base table
				$help_document_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $help_document_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($help_document_condition);
				$db->setQuery($query);
				// Execute the query to remove Help_document items
				$db->execute();

				// Remove Help_document items from the ucm history table
				$help_document_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $help_document_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($help_document_condition);
				$db->setQuery($query);
				// Execute the query to remove Help_document items
				$db->execute();
			}
		}

		// If All related items was removed queued success message.
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_base</b> table'));
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_history</b> table'));

		// Remove supportgroups assets from the assets table
		$supportgroups_condition = array( $db->quoteName('name') . ' LIKE ' . $db->quote('com_supportgroups%') );

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__assets'));
		$query->where($supportgroups_condition);
		$db->setQuery($query);
		$help_document_done = $db->execute();
		if ($help_document_done)
		{
			// If successfully removed supportgroups add queued success message.
			$app->enqueueMessage(JText::_('All related items was removed from the <b>#__assets</b> table'));
		}

		// Get the biggest rule column in the assets table at this point.
		$get_rule_length = "SELECT CHAR_LENGTH(`rules`) as rule_size FROM #__assets ORDER BY rule_size DESC LIMIT 1";
		$db->setQuery($get_rule_length);
		if ($db->execute())
		{
			$rule_length = $db->loadResult();
			// Check the size of the rules column
			if ($rule_length < 5120)
			{
				// Revert the assets table rules column back to the default
				$revert_rule = "ALTER TABLE `#__assets` CHANGE `rules` `rules` varchar(5120) NOT NULL COMMENT 'JSON encoded access control.';";
				$db->setQuery($revert_rule);
				$db->execute();
				$app->enqueueMessage(JText::_('Reverted the <b>#__assets</b> table rules column back to its default size of varchar(5120)'));
			}
			else
			{

				$app->enqueueMessage(JText::_('Could not revert the <b>#__assets</b> table rules column back to its default size of varchar(5120), since there is still one or more components that still requires the column to be larger.'));
			}
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Supportgroups from the action_logs_extensions table
		$supportgroups_action_logs_extensions = array( $db->quoteName('extension') . ' = ' . $db->quote('com_supportgroups') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_logs_extensions'));
		$query->where($supportgroups_action_logs_extensions);
		$db->setQuery($query);
		// Execute the query to remove Supportgroups
		$supportgroups_removed_done = $db->execute();
		if ($supportgroups_removed_done)
		{
			// If successfully remove Supportgroups add queued success message.
			$app->enqueueMessage(JText::_('The com_supportgroups extension was removed from the <b>#__action_logs_extensions</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Supportgroups Support_group from the action_log_config table
		$support_group_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.support_group') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($support_group_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_supportgroups.support_group
		$support_group_action_log_config_done = $db->execute();
		if ($support_group_action_log_config_done)
		{
			// If successfully removed Supportgroups Support_group add queued success message.
			$app->enqueueMessage(JText::_('The com_supportgroups.support_group type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Supportgroups Payment from the action_log_config table
		$payment_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.payment') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($payment_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_supportgroups.payment
		$payment_action_log_config_done = $db->execute();
		if ($payment_action_log_config_done)
		{
			// If successfully removed Supportgroups Payment add queued success message.
			$app->enqueueMessage(JText::_('The com_supportgroups.payment type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Supportgroups Facility from the action_log_config table
		$facility_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.facility') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($facility_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_supportgroups.facility
		$facility_action_log_config_done = $db->execute();
		if ($facility_action_log_config_done)
		{
			// If successfully removed Supportgroups Facility add queued success message.
			$app->enqueueMessage(JText::_('The com_supportgroups.facility type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Supportgroups Facility_type from the action_log_config table
		$facility_type_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.facility_type') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($facility_type_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_supportgroups.facility_type
		$facility_type_action_log_config_done = $db->execute();
		if ($facility_type_action_log_config_done)
		{
			// If successfully removed Supportgroups Facility_type add queued success message.
			$app->enqueueMessage(JText::_('The com_supportgroups.facility_type type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Supportgroups Additional_info from the action_log_config table
		$additional_info_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.additional_info') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($additional_info_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_supportgroups.additional_info
		$additional_info_action_log_config_done = $db->execute();
		if ($additional_info_action_log_config_done)
		{
			// If successfully removed Supportgroups Additional_info add queued success message.
			$app->enqueueMessage(JText::_('The com_supportgroups.additional_info type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Supportgroups Info_type from the action_log_config table
		$info_type_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.info_type') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($info_type_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_supportgroups.info_type
		$info_type_action_log_config_done = $db->execute();
		if ($info_type_action_log_config_done)
		{
			// If successfully removed Supportgroups Info_type add queued success message.
			$app->enqueueMessage(JText::_('The com_supportgroups.info_type type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Supportgroups Area from the action_log_config table
		$area_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.area') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($area_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_supportgroups.area
		$area_action_log_config_done = $db->execute();
		if ($area_action_log_config_done)
		{
			// If successfully removed Supportgroups Area add queued success message.
			$app->enqueueMessage(JText::_('The com_supportgroups.area type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Supportgroups Area_type from the action_log_config table
		$area_type_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.area_type') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($area_type_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_supportgroups.area_type
		$area_type_action_log_config_done = $db->execute();
		if ($area_type_action_log_config_done)
		{
			// If successfully removed Supportgroups Area_type add queued success message.
			$app->enqueueMessage(JText::_('The com_supportgroups.area_type type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Supportgroups Region from the action_log_config table
		$region_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.region') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($region_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_supportgroups.region
		$region_action_log_config_done = $db->execute();
		if ($region_action_log_config_done)
		{
			// If successfully removed Supportgroups Region add queued success message.
			$app->enqueueMessage(JText::_('The com_supportgroups.region type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Supportgroups Country from the action_log_config table
		$country_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.country') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($country_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_supportgroups.country
		$country_action_log_config_done = $db->execute();
		if ($country_action_log_config_done)
		{
			// If successfully removed Supportgroups Country add queued success message.
			$app->enqueueMessage(JText::_('The com_supportgroups.country type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Supportgroups Currency from the action_log_config table
		$currency_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.currency') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($currency_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_supportgroups.currency
		$currency_action_log_config_done = $db->execute();
		if ($currency_action_log_config_done)
		{
			// If successfully removed Supportgroups Currency add queued success message.
			$app->enqueueMessage(JText::_('The com_supportgroups.currency type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Supportgroups Help_document from the action_log_config table
		$help_document_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.help_document') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($help_document_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_supportgroups.help_document
		$help_document_action_log_config_done = $db->execute();
		if ($help_document_action_log_config_done)
		{
			// If successfully removed Supportgroups Help_document add queued success message.
			$app->enqueueMessage(JText::_('The com_supportgroups.help_document type alias was removed from the <b>#__action_log_config</b> table'));
		}
		// little notice as after service, in case of bad experience with component.
		echo '<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:joomla@vdm.io">joomla@vdm.io</a>.
		<br />We at Vast Development Method are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="http://www.vdm.io" target="_blank">http://www.vdm.io</a> today!</p>';
	}

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $parent){}

	/**
	 * Called before any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, JAdapterInstance $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// is redundant or so it seems ...hmmm let me know if it works again
		if ($type === 'uninstall')
		{
			return true;
		}
		// the default for both install and update
		$jversion = new JVersion();
		if (!$jversion->isCompatible('3.8.0'))
		{
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.8.0 before continuing!', 'error');
			return false;
		}
		// do any updates needed
		if ($type === 'update')
		{
		}
		// do any install needed
		if ($type === 'install')
		{
		}
		// check if the PHPExcel stuff is still around
		if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_supportgroups/helpers/PHPExcel.php'))
		{
			// We need to remove this old PHPExcel folder
			$this->removeFolder(JPATH_ADMINISTRATOR . '/components/com_supportgroups/helpers/PHPExcel');
			// We need to remove this old PHPExcel file
			JFile::delete(JPATH_ADMINISTRATOR . '/components/com_supportgroups/helpers/PHPExcel.php');
		}
		return true;
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, JAdapterInstance $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// We check if we have dynamic folders to copy
		$this->setDynamicF0ld3rs($app, $parent);
		// set the default component settings
		if ($type === 'install')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the support_group content type object.
			$support_group = new stdClass();
			$support_group->type_title = 'Supportgroups Support_group';
			$support_group->type_alias = 'com_supportgroups.support_group';
			$support_group->table = '{"special": {"dbtable": "#__supportgroups_support_group","key": "id","type": "Support_group","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$support_group->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "details","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","phone":"phone","area":"area","facility":"facility","male":"male","female":"female","marker":"marker","alias":"alias","details":"details","female_art":"female_art","female_children":"female_children","male_art":"male_art","male_children":"male_children","info":"info"}}';
			$support_group->router = 'SupportgroupsHelperRoute::getSupport_groupRoute';
			$support_group->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/support_group.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","marker"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","area","facility","male","female","female_art","female_children","male_art","male_children"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "area","targetTable": "#__supportgroups_area","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "facility","targetTable": "#__supportgroups_facility","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "info","targetTable": "#__supportgroups_additional_info","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$support_group_Inserted = $db->insertObject('#__content_types', $support_group);

			// Create the payment content type object.
			$payment = new stdClass();
			$payment->type_title = 'Supportgroups Payment';
			$payment->type_alias = 'com_supportgroups.payment';
			$payment->table = '{"special": {"dbtable": "#__supportgroups_payment","key": "id","type": "Payment","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$payment->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "support_group","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"support_group":"support_group","year":"year","amount":"amount"}}';
			$payment->router = 'SupportgroupsHelperRoute::getPaymentRoute';
			$payment->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/payment.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","support_group","year"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "support_group","targetTable": "#__supportgroups_support_group","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$payment_Inserted = $db->insertObject('#__content_types', $payment);

			// Create the facility content type object.
			$facility = new stdClass();
			$facility->type_title = 'Supportgroups Facility';
			$facility->type_alias = 'com_supportgroups.facility';
			$facility->table = '{"special": {"dbtable": "#__supportgroups_facility","key": "id","type": "Facility","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$facility->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "details","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","facility_type":"facility_type","phone":"phone","details":"details","marker":"marker","alias":"alias"}}';
			$facility->router = 'SupportgroupsHelperRoute::getFacilityRoute';
			$facility->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/facility.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","marker"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","facility_type"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "facility_type","targetTable": "#__supportgroups_facility_type","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$facility_Inserted = $db->insertObject('#__content_types', $facility);

			// Create the facility_type content type object.
			$facility_type = new stdClass();
			$facility_type->type_title = 'Supportgroups Facility_type';
			$facility_type->type_alias = 'com_supportgroups.facility_type';
			$facility_type->table = '{"special": {"dbtable": "#__supportgroups_facility_type","key": "id","type": "Facility_type","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$facility_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","alias":"alias"}}';
			$facility_type->router = 'SupportgroupsHelperRoute::getFacility_typeRoute';
			$facility_type->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/facility_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$facility_type_Inserted = $db->insertObject('#__content_types', $facility_type);

			// Create the additional_info content type object.
			$additional_info = new stdClass();
			$additional_info->type_title = 'Supportgroups Additional_info';
			$additional_info->type_alias = 'com_supportgroups.additional_info';
			$additional_info->table = '{"special": {"dbtable": "#__supportgroups_additional_info","key": "id","type": "Additional_info","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$additional_info->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "details","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","info_type":"info_type","details":"details","alias":"alias"}}';
			$additional_info->router = 'SupportgroupsHelperRoute::getAdditional_infoRoute';
			$additional_info->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/additional_info.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","info_type"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "info_type","targetTable": "#__supportgroups_info_type","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$additional_info_Inserted = $db->insertObject('#__content_types', $additional_info);

			// Create the info_type content type object.
			$info_type = new stdClass();
			$info_type->type_title = 'Supportgroups Info_type';
			$info_type->type_alias = 'com_supportgroups.info_type';
			$info_type->table = '{"special": {"dbtable": "#__supportgroups_info_type","key": "id","type": "Info_type","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$info_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","alias":"alias"}}';
			$info_type->router = 'SupportgroupsHelperRoute::getInfo_typeRoute';
			$info_type->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/info_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$info_type_Inserted = $db->insertObject('#__content_types', $info_type);

			// Create the area content type object.
			$area = new stdClass();
			$area->type_title = 'Supportgroups Area';
			$area->type_alias = 'com_supportgroups.area';
			$area->table = '{"special": {"dbtable": "#__supportgroups_area","key": "id","type": "Area","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$area->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "details","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","area_type":"area_type","region":"region","details":"details","color":"color","alias":"alias"}}';
			$area->router = 'SupportgroupsHelperRoute::getAreaRoute';
			$area->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/area.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","area_type","region"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "area_type","targetTable": "#__supportgroups_area_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "region","targetTable": "#__supportgroups_region","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$area_Inserted = $db->insertObject('#__content_types', $area);

			// Create the area_type content type object.
			$area_type = new stdClass();
			$area_type->type_title = 'Supportgroups Area_type';
			$area_type->type_alias = 'com_supportgroups.area_type';
			$area_type->table = '{"special": {"dbtable": "#__supportgroups_area_type","key": "id","type": "Area_type","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$area_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","alias":"alias"}}';
			$area_type->router = 'SupportgroupsHelperRoute::getArea_typeRoute';
			$area_type->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/area_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$area_type_Inserted = $db->insertObject('#__content_types', $area_type);

			// Create the region content type object.
			$region = new stdClass();
			$region->type_title = 'Supportgroups Region';
			$region->type_alias = 'com_supportgroups.region';
			$region->table = '{"special": {"dbtable": "#__supportgroups_region","key": "id","type": "Region","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$region->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","country":"country","alias":"alias"}}';
			$region->router = 'SupportgroupsHelperRoute::getRegionRoute';
			$region->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/region.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","country"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "country","targetTable": "#__supportgroups_country","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$region_Inserted = $db->insertObject('#__content_types', $region);

			// Create the country content type object.
			$country = new stdClass();
			$country->type_title = 'Supportgroups Country';
			$country->type_alias = 'com_supportgroups.country';
			$country->table = '{"special": {"dbtable": "#__supportgroups_country","key": "id","type": "Country","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$country->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","currency":"currency","worldzone":"worldzone","codethree":"codethree","codetwo":"codetwo","alias":"alias"}}';
			$country->router = 'SupportgroupsHelperRoute::getCountryRoute';
			$country->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/country.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "currency","targetTable": "#__supportgroups_currency","targetColumn": "codethree","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$country_Inserted = $db->insertObject('#__content_types', $country);

			// Create the currency content type object.
			$currency = new stdClass();
			$currency->type_title = 'Supportgroups Currency';
			$currency->type_alias = 'com_supportgroups.currency';
			$currency->table = '{"special": {"dbtable": "#__supportgroups_currency","key": "id","type": "Currency","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$currency->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","codethree":"codethree","numericcode":"numericcode","symbol":"symbol","alias":"alias","negativestyle":"negativestyle","positivestyle":"positivestyle","decimalsymbol":"decimalsymbol","decimalplace":"decimalplace","thousands":"thousands"}}';
			$currency->router = 'SupportgroupsHelperRoute::getCurrencyRoute';
			$currency->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/currency.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","numericcode","decimalplace"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$currency_Inserted = $db->insertObject('#__content_types', $currency);

			// Create the help_document content type object.
			$help_document = new stdClass();
			$help_document->type_title = 'Supportgroups Help_document';
			$help_document->type_alias = 'com_supportgroups.help_document';
			$help_document->table = '{"special": {"dbtable": "#__supportgroups_help_document","key": "id","type": "Help_document","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$help_document->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "title","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "content","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"title":"title","type":"type","groups":"groups","location":"location","admin_view":"admin_view","site_view":"site_view","alias":"alias","content":"content","article":"article","url":"url","target":"target"}}';
			$help_document->router = 'SupportgroupsHelperRoute::getHelp_documentRoute';
			$help_document->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/help_document.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","type","location","article","target"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "article","targetTable": "#__content","targetColumn": "id","displayColumn": "title"}]}';

			// Set the object into the content types table.
			$help_document_Inserted = $db->insertObject('#__content_types', $help_document);


			// Install the global extenstion params.
			$query = $db->getQuery(true);
			// Field to update.
			$fields = array(
				$db->quoteName('params') . ' = ' . $db->quote('{"autorName":"Llewellyn van der Merwe","autorEmail":"joomla@vdm.io","maptype":"ROADMAP","maxzoom":"6","gotozoom":"8","cluster":"0","cluster_at":"300","clustergridsize":"100","clustermaxzoom":"7","set_browser_storage":"1","storage_time_to_live":"global","check_in":"-1 day","save_history":"1","history_limit":"10","uikit_load":"1","uikit_min":"","uikit_style":""}'),
			);
			// Condition.
			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('com_supportgroups')
			);
			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$allDone = $db->execute();

			// Get the biggest rule column in the assets table at this point.
			$get_rule_length = "SELECT CHAR_LENGTH(`rules`) as rule_size FROM #__assets ORDER BY rule_size DESC LIMIT 1";
			$db->setQuery($get_rule_length);
			if ($db->execute())
			{
				$rule_length = $db->loadResult();
				// Check the size of the rules column
				if ($rule_length <= 40800)
				{
					// Fix the assets table rules column size
					$fix_rules_size = "ALTER TABLE `#__assets` CHANGE `rules` `rules` TEXT NOT NULL COMMENT 'JSON encoded access control. Enlarged to TEXT by JCB';";
					$db->setQuery($fix_rules_size);
					$db->execute();
					$app->enqueueMessage(JText::_('The <b>#__assets</b> table rules column was resized to the TEXT datatype for the components possible large permission rules.'));
				}
			}
			echo '<a target="_blank" href="http://www.vdm.io" title="Support Groups">
				<img src="components/com_supportgroups/assets/images/vdm-component.jpg"/>
				</a>';

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the supportgroups action logs extensions object.
			$supportgroups_action_logs_extensions = new stdClass();
			$supportgroups_action_logs_extensions->extension = 'com_supportgroups';

			// Set the object into the action logs extensions table.
			$supportgroups_action_logs_extensions_Inserted = $db->insertObject('#__action_logs_extensions', $supportgroups_action_logs_extensions);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the support_group action log config object.
			$support_group_action_log_config = new stdClass();
			$support_group_action_log_config->type_title = 'SUPPORT_GROUP';
			$support_group_action_log_config->type_alias = 'com_supportgroups.support_group';
			$support_group_action_log_config->id_holder = 'id';
			$support_group_action_log_config->title_holder = 'name';
			$support_group_action_log_config->table_name = '#__supportgroups_support_group';
			$support_group_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Set the object into the action log config table.
			$support_group_Inserted = $db->insertObject('#__action_log_config', $support_group_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the payment action log config object.
			$payment_action_log_config = new stdClass();
			$payment_action_log_config->type_title = 'PAYMENT';
			$payment_action_log_config->type_alias = 'com_supportgroups.payment';
			$payment_action_log_config->id_holder = 'id';
			$payment_action_log_config->title_holder = 'support_group';
			$payment_action_log_config->table_name = '#__supportgroups_payment';
			$payment_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Set the object into the action log config table.
			$payment_Inserted = $db->insertObject('#__action_log_config', $payment_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the facility action log config object.
			$facility_action_log_config = new stdClass();
			$facility_action_log_config->type_title = 'FACILITY';
			$facility_action_log_config->type_alias = 'com_supportgroups.facility';
			$facility_action_log_config->id_holder = 'id';
			$facility_action_log_config->title_holder = 'name';
			$facility_action_log_config->table_name = '#__supportgroups_facility';
			$facility_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Set the object into the action log config table.
			$facility_Inserted = $db->insertObject('#__action_log_config', $facility_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the facility_type action log config object.
			$facility_type_action_log_config = new stdClass();
			$facility_type_action_log_config->type_title = 'FACILITY_TYPE';
			$facility_type_action_log_config->type_alias = 'com_supportgroups.facility_type';
			$facility_type_action_log_config->id_holder = 'id';
			$facility_type_action_log_config->title_holder = 'name';
			$facility_type_action_log_config->table_name = '#__supportgroups_facility_type';
			$facility_type_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Set the object into the action log config table.
			$facility_type_Inserted = $db->insertObject('#__action_log_config', $facility_type_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the additional_info action log config object.
			$additional_info_action_log_config = new stdClass();
			$additional_info_action_log_config->type_title = 'ADDITIONAL_INFO';
			$additional_info_action_log_config->type_alias = 'com_supportgroups.additional_info';
			$additional_info_action_log_config->id_holder = 'id';
			$additional_info_action_log_config->title_holder = 'name';
			$additional_info_action_log_config->table_name = '#__supportgroups_additional_info';
			$additional_info_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Set the object into the action log config table.
			$additional_info_Inserted = $db->insertObject('#__action_log_config', $additional_info_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the info_type action log config object.
			$info_type_action_log_config = new stdClass();
			$info_type_action_log_config->type_title = 'INFO_TYPE';
			$info_type_action_log_config->type_alias = 'com_supportgroups.info_type';
			$info_type_action_log_config->id_holder = 'id';
			$info_type_action_log_config->title_holder = 'name';
			$info_type_action_log_config->table_name = '#__supportgroups_info_type';
			$info_type_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Set the object into the action log config table.
			$info_type_Inserted = $db->insertObject('#__action_log_config', $info_type_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the area action log config object.
			$area_action_log_config = new stdClass();
			$area_action_log_config->type_title = 'AREA';
			$area_action_log_config->type_alias = 'com_supportgroups.area';
			$area_action_log_config->id_holder = 'id';
			$area_action_log_config->title_holder = 'name';
			$area_action_log_config->table_name = '#__supportgroups_area';
			$area_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Set the object into the action log config table.
			$area_Inserted = $db->insertObject('#__action_log_config', $area_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the area_type action log config object.
			$area_type_action_log_config = new stdClass();
			$area_type_action_log_config->type_title = 'AREA_TYPE';
			$area_type_action_log_config->type_alias = 'com_supportgroups.area_type';
			$area_type_action_log_config->id_holder = 'id';
			$area_type_action_log_config->title_holder = 'name';
			$area_type_action_log_config->table_name = '#__supportgroups_area_type';
			$area_type_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Set the object into the action log config table.
			$area_type_Inserted = $db->insertObject('#__action_log_config', $area_type_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the region action log config object.
			$region_action_log_config = new stdClass();
			$region_action_log_config->type_title = 'REGION';
			$region_action_log_config->type_alias = 'com_supportgroups.region';
			$region_action_log_config->id_holder = 'id';
			$region_action_log_config->title_holder = 'name';
			$region_action_log_config->table_name = '#__supportgroups_region';
			$region_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Set the object into the action log config table.
			$region_Inserted = $db->insertObject('#__action_log_config', $region_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the country action log config object.
			$country_action_log_config = new stdClass();
			$country_action_log_config->type_title = 'COUNTRY';
			$country_action_log_config->type_alias = 'com_supportgroups.country';
			$country_action_log_config->id_holder = 'id';
			$country_action_log_config->title_holder = 'name';
			$country_action_log_config->table_name = '#__supportgroups_country';
			$country_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Set the object into the action log config table.
			$country_Inserted = $db->insertObject('#__action_log_config', $country_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the currency action log config object.
			$currency_action_log_config = new stdClass();
			$currency_action_log_config->type_title = 'CURRENCY';
			$currency_action_log_config->type_alias = 'com_supportgroups.currency';
			$currency_action_log_config->id_holder = 'id';
			$currency_action_log_config->title_holder = 'name';
			$currency_action_log_config->table_name = '#__supportgroups_currency';
			$currency_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Set the object into the action log config table.
			$currency_Inserted = $db->insertObject('#__action_log_config', $currency_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the help_document action log config object.
			$help_document_action_log_config = new stdClass();
			$help_document_action_log_config->type_title = 'HELP_DOCUMENT';
			$help_document_action_log_config->type_alias = 'com_supportgroups.help_document';
			$help_document_action_log_config->id_holder = 'id';
			$help_document_action_log_config->title_holder = 'title';
			$help_document_action_log_config->table_name = '#__supportgroups_help_document';
			$help_document_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Set the object into the action log config table.
			$help_document_Inserted = $db->insertObject('#__action_log_config', $help_document_action_log_config);
		}
		// do any updates needed
		if ($type === 'update')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the support_group content type object.
			$support_group = new stdClass();
			$support_group->type_title = 'Supportgroups Support_group';
			$support_group->type_alias = 'com_supportgroups.support_group';
			$support_group->table = '{"special": {"dbtable": "#__supportgroups_support_group","key": "id","type": "Support_group","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$support_group->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "details","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","phone":"phone","area":"area","facility":"facility","male":"male","female":"female","marker":"marker","alias":"alias","details":"details","female_art":"female_art","female_children":"female_children","male_art":"male_art","male_children":"male_children","info":"info"}}';
			$support_group->router = 'SupportgroupsHelperRoute::getSupport_groupRoute';
			$support_group->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/support_group.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","marker"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","area","facility","male","female","female_art","female_children","male_art","male_children"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "area","targetTable": "#__supportgroups_area","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "facility","targetTable": "#__supportgroups_facility","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "info","targetTable": "#__supportgroups_additional_info","targetColumn": "id","displayColumn": "name"}]}';

			// Check if support_group type is already in content_type DB.
			$support_group_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($support_group->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$support_group->type_id = $db->loadResult();
				$support_group_Updated = $db->updateObject('#__content_types', $support_group, 'type_id');
			}
			else
			{
				$support_group_Inserted = $db->insertObject('#__content_types', $support_group);
			}

			// Create the payment content type object.
			$payment = new stdClass();
			$payment->type_title = 'Supportgroups Payment';
			$payment->type_alias = 'com_supportgroups.payment';
			$payment->table = '{"special": {"dbtable": "#__supportgroups_payment","key": "id","type": "Payment","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$payment->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "support_group","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"support_group":"support_group","year":"year","amount":"amount"}}';
			$payment->router = 'SupportgroupsHelperRoute::getPaymentRoute';
			$payment->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/payment.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","support_group","year"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "support_group","targetTable": "#__supportgroups_support_group","targetColumn": "id","displayColumn": "name"}]}';

			// Check if payment type is already in content_type DB.
			$payment_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($payment->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$payment->type_id = $db->loadResult();
				$payment_Updated = $db->updateObject('#__content_types', $payment, 'type_id');
			}
			else
			{
				$payment_Inserted = $db->insertObject('#__content_types', $payment);
			}

			// Create the facility content type object.
			$facility = new stdClass();
			$facility->type_title = 'Supportgroups Facility';
			$facility->type_alias = 'com_supportgroups.facility';
			$facility->table = '{"special": {"dbtable": "#__supportgroups_facility","key": "id","type": "Facility","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$facility->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "details","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","facility_type":"facility_type","phone":"phone","details":"details","marker":"marker","alias":"alias"}}';
			$facility->router = 'SupportgroupsHelperRoute::getFacilityRoute';
			$facility->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/facility.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","marker"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","facility_type"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "facility_type","targetTable": "#__supportgroups_facility_type","targetColumn": "id","displayColumn": "name"}]}';

			// Check if facility type is already in content_type DB.
			$facility_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($facility->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$facility->type_id = $db->loadResult();
				$facility_Updated = $db->updateObject('#__content_types', $facility, 'type_id');
			}
			else
			{
				$facility_Inserted = $db->insertObject('#__content_types', $facility);
			}

			// Create the facility_type content type object.
			$facility_type = new stdClass();
			$facility_type->type_title = 'Supportgroups Facility_type';
			$facility_type->type_alias = 'com_supportgroups.facility_type';
			$facility_type->table = '{"special": {"dbtable": "#__supportgroups_facility_type","key": "id","type": "Facility_type","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$facility_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","alias":"alias"}}';
			$facility_type->router = 'SupportgroupsHelperRoute::getFacility_typeRoute';
			$facility_type->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/facility_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if facility_type type is already in content_type DB.
			$facility_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($facility_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$facility_type->type_id = $db->loadResult();
				$facility_type_Updated = $db->updateObject('#__content_types', $facility_type, 'type_id');
			}
			else
			{
				$facility_type_Inserted = $db->insertObject('#__content_types', $facility_type);
			}

			// Create the additional_info content type object.
			$additional_info = new stdClass();
			$additional_info->type_title = 'Supportgroups Additional_info';
			$additional_info->type_alias = 'com_supportgroups.additional_info';
			$additional_info->table = '{"special": {"dbtable": "#__supportgroups_additional_info","key": "id","type": "Additional_info","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$additional_info->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "details","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","info_type":"info_type","details":"details","alias":"alias"}}';
			$additional_info->router = 'SupportgroupsHelperRoute::getAdditional_infoRoute';
			$additional_info->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/additional_info.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","info_type"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "info_type","targetTable": "#__supportgroups_info_type","targetColumn": "id","displayColumn": "name"}]}';

			// Check if additional_info type is already in content_type DB.
			$additional_info_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($additional_info->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$additional_info->type_id = $db->loadResult();
				$additional_info_Updated = $db->updateObject('#__content_types', $additional_info, 'type_id');
			}
			else
			{
				$additional_info_Inserted = $db->insertObject('#__content_types', $additional_info);
			}

			// Create the info_type content type object.
			$info_type = new stdClass();
			$info_type->type_title = 'Supportgroups Info_type';
			$info_type->type_alias = 'com_supportgroups.info_type';
			$info_type->table = '{"special": {"dbtable": "#__supportgroups_info_type","key": "id","type": "Info_type","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$info_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","alias":"alias"}}';
			$info_type->router = 'SupportgroupsHelperRoute::getInfo_typeRoute';
			$info_type->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/info_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if info_type type is already in content_type DB.
			$info_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($info_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$info_type->type_id = $db->loadResult();
				$info_type_Updated = $db->updateObject('#__content_types', $info_type, 'type_id');
			}
			else
			{
				$info_type_Inserted = $db->insertObject('#__content_types', $info_type);
			}

			// Create the area content type object.
			$area = new stdClass();
			$area->type_title = 'Supportgroups Area';
			$area->type_alias = 'com_supportgroups.area';
			$area->table = '{"special": {"dbtable": "#__supportgroups_area","key": "id","type": "Area","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$area->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "details","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","area_type":"area_type","region":"region","details":"details","color":"color","alias":"alias"}}';
			$area->router = 'SupportgroupsHelperRoute::getAreaRoute';
			$area->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/area.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","area_type","region"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "area_type","targetTable": "#__supportgroups_area_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "region","targetTable": "#__supportgroups_region","targetColumn": "id","displayColumn": "name"}]}';

			// Check if area type is already in content_type DB.
			$area_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($area->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$area->type_id = $db->loadResult();
				$area_Updated = $db->updateObject('#__content_types', $area, 'type_id');
			}
			else
			{
				$area_Inserted = $db->insertObject('#__content_types', $area);
			}

			// Create the area_type content type object.
			$area_type = new stdClass();
			$area_type->type_title = 'Supportgroups Area_type';
			$area_type->type_alias = 'com_supportgroups.area_type';
			$area_type->table = '{"special": {"dbtable": "#__supportgroups_area_type","key": "id","type": "Area_type","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$area_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","alias":"alias"}}';
			$area_type->router = 'SupportgroupsHelperRoute::getArea_typeRoute';
			$area_type->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/area_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if area_type type is already in content_type DB.
			$area_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($area_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$area_type->type_id = $db->loadResult();
				$area_type_Updated = $db->updateObject('#__content_types', $area_type, 'type_id');
			}
			else
			{
				$area_type_Inserted = $db->insertObject('#__content_types', $area_type);
			}

			// Create the region content type object.
			$region = new stdClass();
			$region->type_title = 'Supportgroups Region';
			$region->type_alias = 'com_supportgroups.region';
			$region->table = '{"special": {"dbtable": "#__supportgroups_region","key": "id","type": "Region","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$region->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","country":"country","alias":"alias"}}';
			$region->router = 'SupportgroupsHelperRoute::getRegionRoute';
			$region->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/region.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","country"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "country","targetTable": "#__supportgroups_country","targetColumn": "id","displayColumn": "name"}]}';

			// Check if region type is already in content_type DB.
			$region_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($region->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$region->type_id = $db->loadResult();
				$region_Updated = $db->updateObject('#__content_types', $region, 'type_id');
			}
			else
			{
				$region_Inserted = $db->insertObject('#__content_types', $region);
			}

			// Create the country content type object.
			$country = new stdClass();
			$country->type_title = 'Supportgroups Country';
			$country->type_alias = 'com_supportgroups.country';
			$country->table = '{"special": {"dbtable": "#__supportgroups_country","key": "id","type": "Country","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$country->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","currency":"currency","worldzone":"worldzone","codethree":"codethree","codetwo":"codetwo","alias":"alias"}}';
			$country->router = 'SupportgroupsHelperRoute::getCountryRoute';
			$country->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/country.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "currency","targetTable": "#__supportgroups_currency","targetColumn": "codethree","displayColumn": "name"}]}';

			// Check if country type is already in content_type DB.
			$country_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($country->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$country->type_id = $db->loadResult();
				$country_Updated = $db->updateObject('#__content_types', $country, 'type_id');
			}
			else
			{
				$country_Inserted = $db->insertObject('#__content_types', $country);
			}

			// Create the currency content type object.
			$currency = new stdClass();
			$currency->type_title = 'Supportgroups Currency';
			$currency->type_alias = 'com_supportgroups.currency';
			$currency->table = '{"special": {"dbtable": "#__supportgroups_currency","key": "id","type": "Currency","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$currency->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","codethree":"codethree","numericcode":"numericcode","symbol":"symbol","alias":"alias","negativestyle":"negativestyle","positivestyle":"positivestyle","decimalsymbol":"decimalsymbol","decimalplace":"decimalplace","thousands":"thousands"}}';
			$currency->router = 'SupportgroupsHelperRoute::getCurrencyRoute';
			$currency->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/currency.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","numericcode","decimalplace"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if currency type is already in content_type DB.
			$currency_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($currency->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$currency->type_id = $db->loadResult();
				$currency_Updated = $db->updateObject('#__content_types', $currency, 'type_id');
			}
			else
			{
				$currency_Inserted = $db->insertObject('#__content_types', $currency);
			}

			// Create the help_document content type object.
			$help_document = new stdClass();
			$help_document->type_title = 'Supportgroups Help_document';
			$help_document->type_alias = 'com_supportgroups.help_document';
			$help_document->table = '{"special": {"dbtable": "#__supportgroups_help_document","key": "id","type": "Help_document","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$help_document->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "title","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "content","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"title":"title","type":"type","groups":"groups","location":"location","admin_view":"admin_view","site_view":"site_view","alias":"alias","content":"content","article":"article","url":"url","target":"target"}}';
			$help_document->router = 'SupportgroupsHelperRoute::getHelp_documentRoute';
			$help_document->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/help_document.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","type","location","article","target"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "article","targetTable": "#__content","targetColumn": "id","displayColumn": "title"}]}';

			// Check if help_document type is already in content_type DB.
			$help_document_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($help_document->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$help_document->type_id = $db->loadResult();
				$help_document_Updated = $db->updateObject('#__content_types', $help_document, 'type_id');
			}
			else
			{
				$help_document_Inserted = $db->insertObject('#__content_types', $help_document);
			}


			echo '<a target="_blank" href="http://www.vdm.io" title="Support Groups">
				<img src="components/com_supportgroups/assets/images/vdm-component.jpg"/>
				</a>
				<h3>Upgrade to Version 1.0.11 Was Successful! Let us know if anything is not working as expected.</h3>';

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the supportgroups action logs extensions object.
			$supportgroups_action_logs_extensions = new stdClass();
			$supportgroups_action_logs_extensions->extension = 'com_supportgroups';

			// Check if supportgroups action log extension is already in action logs extensions DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_logs_extensions'));
			$query->where($db->quoteName('extension') . ' LIKE '. $db->quote($supportgroups_action_logs_extensions->extension));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the action logs extensions table if not found.
			if (!$db->getNumRows())
			{
				$supportgroups_action_logs_extensions_Inserted = $db->insertObject('#__action_logs_extensions', $supportgroups_action_logs_extensions);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the support_group action log config object.
			$support_group_action_log_config = new stdClass();
			$support_group_action_log_config->id = null;
			$support_group_action_log_config->type_title = 'SUPPORT_GROUP';
			$support_group_action_log_config->type_alias = 'com_supportgroups.support_group';
			$support_group_action_log_config->id_holder = 'id';
			$support_group_action_log_config->title_holder = 'name';
			$support_group_action_log_config->table_name = '#__supportgroups_support_group';
			$support_group_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Check if support_group action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($support_group_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$support_group_action_log_config->id = $db->loadResult();
				$support_group_action_log_config_Updated = $db->updateObject('#__action_log_config', $support_group_action_log_config, 'id');
			}
			else
			{
				$support_group_action_log_config_Inserted = $db->insertObject('#__action_log_config', $support_group_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the payment action log config object.
			$payment_action_log_config = new stdClass();
			$payment_action_log_config->id = null;
			$payment_action_log_config->type_title = 'PAYMENT';
			$payment_action_log_config->type_alias = 'com_supportgroups.payment';
			$payment_action_log_config->id_holder = 'id';
			$payment_action_log_config->title_holder = 'support_group';
			$payment_action_log_config->table_name = '#__supportgroups_payment';
			$payment_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Check if payment action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($payment_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$payment_action_log_config->id = $db->loadResult();
				$payment_action_log_config_Updated = $db->updateObject('#__action_log_config', $payment_action_log_config, 'id');
			}
			else
			{
				$payment_action_log_config_Inserted = $db->insertObject('#__action_log_config', $payment_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the facility action log config object.
			$facility_action_log_config = new stdClass();
			$facility_action_log_config->id = null;
			$facility_action_log_config->type_title = 'FACILITY';
			$facility_action_log_config->type_alias = 'com_supportgroups.facility';
			$facility_action_log_config->id_holder = 'id';
			$facility_action_log_config->title_holder = 'name';
			$facility_action_log_config->table_name = '#__supportgroups_facility';
			$facility_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Check if facility action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($facility_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$facility_action_log_config->id = $db->loadResult();
				$facility_action_log_config_Updated = $db->updateObject('#__action_log_config', $facility_action_log_config, 'id');
			}
			else
			{
				$facility_action_log_config_Inserted = $db->insertObject('#__action_log_config', $facility_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the facility_type action log config object.
			$facility_type_action_log_config = new stdClass();
			$facility_type_action_log_config->id = null;
			$facility_type_action_log_config->type_title = 'FACILITY_TYPE';
			$facility_type_action_log_config->type_alias = 'com_supportgroups.facility_type';
			$facility_type_action_log_config->id_holder = 'id';
			$facility_type_action_log_config->title_holder = 'name';
			$facility_type_action_log_config->table_name = '#__supportgroups_facility_type';
			$facility_type_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Check if facility_type action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($facility_type_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$facility_type_action_log_config->id = $db->loadResult();
				$facility_type_action_log_config_Updated = $db->updateObject('#__action_log_config', $facility_type_action_log_config, 'id');
			}
			else
			{
				$facility_type_action_log_config_Inserted = $db->insertObject('#__action_log_config', $facility_type_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the additional_info action log config object.
			$additional_info_action_log_config = new stdClass();
			$additional_info_action_log_config->id = null;
			$additional_info_action_log_config->type_title = 'ADDITIONAL_INFO';
			$additional_info_action_log_config->type_alias = 'com_supportgroups.additional_info';
			$additional_info_action_log_config->id_holder = 'id';
			$additional_info_action_log_config->title_holder = 'name';
			$additional_info_action_log_config->table_name = '#__supportgroups_additional_info';
			$additional_info_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Check if additional_info action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($additional_info_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$additional_info_action_log_config->id = $db->loadResult();
				$additional_info_action_log_config_Updated = $db->updateObject('#__action_log_config', $additional_info_action_log_config, 'id');
			}
			else
			{
				$additional_info_action_log_config_Inserted = $db->insertObject('#__action_log_config', $additional_info_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the info_type action log config object.
			$info_type_action_log_config = new stdClass();
			$info_type_action_log_config->id = null;
			$info_type_action_log_config->type_title = 'INFO_TYPE';
			$info_type_action_log_config->type_alias = 'com_supportgroups.info_type';
			$info_type_action_log_config->id_holder = 'id';
			$info_type_action_log_config->title_holder = 'name';
			$info_type_action_log_config->table_name = '#__supportgroups_info_type';
			$info_type_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Check if info_type action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($info_type_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$info_type_action_log_config->id = $db->loadResult();
				$info_type_action_log_config_Updated = $db->updateObject('#__action_log_config', $info_type_action_log_config, 'id');
			}
			else
			{
				$info_type_action_log_config_Inserted = $db->insertObject('#__action_log_config', $info_type_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the area action log config object.
			$area_action_log_config = new stdClass();
			$area_action_log_config->id = null;
			$area_action_log_config->type_title = 'AREA';
			$area_action_log_config->type_alias = 'com_supportgroups.area';
			$area_action_log_config->id_holder = 'id';
			$area_action_log_config->title_holder = 'name';
			$area_action_log_config->table_name = '#__supportgroups_area';
			$area_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Check if area action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($area_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$area_action_log_config->id = $db->loadResult();
				$area_action_log_config_Updated = $db->updateObject('#__action_log_config', $area_action_log_config, 'id');
			}
			else
			{
				$area_action_log_config_Inserted = $db->insertObject('#__action_log_config', $area_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the area_type action log config object.
			$area_type_action_log_config = new stdClass();
			$area_type_action_log_config->id = null;
			$area_type_action_log_config->type_title = 'AREA_TYPE';
			$area_type_action_log_config->type_alias = 'com_supportgroups.area_type';
			$area_type_action_log_config->id_holder = 'id';
			$area_type_action_log_config->title_holder = 'name';
			$area_type_action_log_config->table_name = '#__supportgroups_area_type';
			$area_type_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Check if area_type action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($area_type_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$area_type_action_log_config->id = $db->loadResult();
				$area_type_action_log_config_Updated = $db->updateObject('#__action_log_config', $area_type_action_log_config, 'id');
			}
			else
			{
				$area_type_action_log_config_Inserted = $db->insertObject('#__action_log_config', $area_type_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the region action log config object.
			$region_action_log_config = new stdClass();
			$region_action_log_config->id = null;
			$region_action_log_config->type_title = 'REGION';
			$region_action_log_config->type_alias = 'com_supportgroups.region';
			$region_action_log_config->id_holder = 'id';
			$region_action_log_config->title_holder = 'name';
			$region_action_log_config->table_name = '#__supportgroups_region';
			$region_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Check if region action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($region_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$region_action_log_config->id = $db->loadResult();
				$region_action_log_config_Updated = $db->updateObject('#__action_log_config', $region_action_log_config, 'id');
			}
			else
			{
				$region_action_log_config_Inserted = $db->insertObject('#__action_log_config', $region_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the country action log config object.
			$country_action_log_config = new stdClass();
			$country_action_log_config->id = null;
			$country_action_log_config->type_title = 'COUNTRY';
			$country_action_log_config->type_alias = 'com_supportgroups.country';
			$country_action_log_config->id_holder = 'id';
			$country_action_log_config->title_holder = 'name';
			$country_action_log_config->table_name = '#__supportgroups_country';
			$country_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Check if country action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($country_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$country_action_log_config->id = $db->loadResult();
				$country_action_log_config_Updated = $db->updateObject('#__action_log_config', $country_action_log_config, 'id');
			}
			else
			{
				$country_action_log_config_Inserted = $db->insertObject('#__action_log_config', $country_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the currency action log config object.
			$currency_action_log_config = new stdClass();
			$currency_action_log_config->id = null;
			$currency_action_log_config->type_title = 'CURRENCY';
			$currency_action_log_config->type_alias = 'com_supportgroups.currency';
			$currency_action_log_config->id_holder = 'id';
			$currency_action_log_config->title_holder = 'name';
			$currency_action_log_config->table_name = '#__supportgroups_currency';
			$currency_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Check if currency action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($currency_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$currency_action_log_config->id = $db->loadResult();
				$currency_action_log_config_Updated = $db->updateObject('#__action_log_config', $currency_action_log_config, 'id');
			}
			else
			{
				$currency_action_log_config_Inserted = $db->insertObject('#__action_log_config', $currency_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the help_document action log config object.
			$help_document_action_log_config = new stdClass();
			$help_document_action_log_config->id = null;
			$help_document_action_log_config->type_title = 'HELP_DOCUMENT';
			$help_document_action_log_config->type_alias = 'com_supportgroups.help_document';
			$help_document_action_log_config->id_holder = 'id';
			$help_document_action_log_config->title_holder = 'title';
			$help_document_action_log_config->table_name = '#__supportgroups_help_document';
			$help_document_action_log_config->text_prefix = 'COM_SUPPORTGROUPS';

			// Check if help_document action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($help_document_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$help_document_action_log_config->id = $db->loadResult();
				$help_document_action_log_config_Updated = $db->updateObject('#__action_log_config', $help_document_action_log_config, 'id');
			}
			else
			{
				$help_document_action_log_config_Inserted = $db->insertObject('#__action_log_config', $help_document_action_log_config);
			}
		}
		return true;
	}

	/**
	 * Remove folders with files
	 * 
	 * @param   string   $dir     The path to folder to remove
	 * @param   boolean  $ignore  The folders and files to ignore and not remove
	 *
	 * @return  boolean   True in all is removed
	 * 
	 */
	protected function removeFolder($dir, $ignore = false)
	{
		if (JFolder::exists($dir))
		{
			$it = new RecursiveDirectoryIterator($dir);
			$it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
			// remove ending /
			$dir = rtrim($dir, '/');
			// now loop the files & folders
			foreach ($it as $file)
			{
				if ('.' === $file->getBasename() || '..' ===  $file->getBasename()) continue;
				// set file dir
				$file_dir = $file->getPathname();
				// check if this is a dir or a file
				if ($file->isDir())
				{
					$keeper = false;
					if ($this->checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					JFolder::delete($file_dir);
				}
				else
				{
					$keeper = false;
					if ($this->checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					JFile::delete($file_dir);
				}
			}
			// delete the root folder if not ignore found
			if (!$this->checkArray($ignore))
			{
				return JFolder::delete($dir);
			}
			return true;
		}
		return false;
	}

	/**
	 * Check if have an array with a length
	 *
	 * @input	array   The array to check
	 *
	 * @returns bool/int  number of items in array on success
	 */
	protected function checkArray($array, $removeEmptyString = false)
	{
		if (isset($array) && is_array($array) && ($nr = count((array)$array)) > 0)
		{
			// also make sure the empty strings are removed
			if ($removeEmptyString)
			{
				foreach ($array as $key => $string)
				{
					if (empty($string))
					{
						unset($array[$key]);
					}
				}
				return $this->checkArray($array, false);
			}
			return $nr;
		}
		return false;
	}

	/**
	 * Method to set/copy dynamic folders into place (use with caution)
	 *
	 * @return void
	 */
	protected function setDynamicF0ld3rs($app, $parent)
	{
		// get the instalation path
		$installer = $parent->getParent();
		$installPath = $installer->getPath('source');
		// get all the folders
		$folders = JFolder::folders($installPath);
		// check if we have folders we may want to copy
		$doNotCopy = array('media','admin','site'); // Joomla already deals with these
		if (count((array) $folders) > 1)
		{
			foreach ($folders as $folder)
			{
				// Only copy if not a standard folders
				if (!in_array($folder, $doNotCopy))
				{
					// set the source path
					$src = $installPath.'/'.$folder;
					// set the destination path
					$dest = JPATH_ROOT.'/'.$folder;
					// now try to copy the folder
					if (!JFolder::copy($src, $dest, '', true))
					{
						$app->enqueueMessage('Could not copy '.$folder.' folder into place, please make sure destination is writable!', 'error');
					}
				}
			}
		}
	}
}
