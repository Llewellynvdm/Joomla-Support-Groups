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

	@version		1.0.3
	@build			6th March, 2016
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
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');

/**
 * Script File of Supportgroups Component
 */
class com_supportgroupsInstallerScript
{
	/**
	 * method to install the component
	 *
	 *
	 * @return void
	 */
	function install($parent)
	{

	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent)
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
			if ($support_group_done);
			{
				// If succesfully remove Support_group add queued success message.
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
			if ($support_group_done);
			{
				// If succesfully remove Support_group add queued success message.
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
			if ($support_group_done);
			{
				// If succesfully remove Support_group add queued success message.
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
			if ($payment_done);
			{
				// If succesfully remove Payment add queued success message.
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
			if ($payment_done);
			{
				// If succesfully remove Payment add queued success message.
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
			if ($payment_done);
			{
				// If succesfully remove Payment add queued success message.
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
		// Where Clinic alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.clinic') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$clinic_found = $db->getNumRows();
		// Now check if there were any rows
		if ($clinic_found)
		{
			// Since there are load the needed  clinic type ids
			$clinic_ids = $db->loadColumn();
			// Remove Clinic from the content type table
			$clinic_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.clinic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($clinic_condition);
			$db->setQuery($query);
			// Execute the query to remove Clinic items
			$clinic_done = $db->execute();
			if ($clinic_done);
			{
				// If succesfully remove Clinic add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.clinic) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Clinic items from the contentitem tag map table
			$clinic_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.clinic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($clinic_condition);
			$db->setQuery($query);
			// Execute the query to remove Clinic items
			$clinic_done = $db->execute();
			if ($clinic_done);
			{
				// If succesfully remove Clinic add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.clinic) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Clinic items from the ucm content table
			$clinic_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_supportgroups.clinic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($clinic_condition);
			$db->setQuery($query);
			// Execute the query to remove Clinic items
			$clinic_done = $db->execute();
			if ($clinic_done);
			{
				// If succesfully remove Clinic add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.clinic) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Clinic items are cleared from DB
			foreach ($clinic_ids as $clinic_id)
			{
				// Remove Clinic items from the ucm base table
				$clinic_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $clinic_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($clinic_condition);
				$db->setQuery($query);
				// Execute the query to remove Clinic items
				$db->execute();

				// Remove Clinic items from the ucm history table
				$clinic_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $clinic_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($clinic_condition);
				$db->setQuery($query);
				// Execute the query to remove Clinic items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Location alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.location') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$location_found = $db->getNumRows();
		// Now check if there were any rows
		if ($location_found)
		{
			// Since there are load the needed  location type ids
			$location_ids = $db->loadColumn();
			// Remove Location from the content type table
			$location_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.location') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($location_condition);
			$db->setQuery($query);
			// Execute the query to remove Location items
			$location_done = $db->execute();
			if ($location_done);
			{
				// If succesfully remove Location add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.location) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Location items from the contentitem tag map table
			$location_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_supportgroups.location') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($location_condition);
			$db->setQuery($query);
			// Execute the query to remove Location items
			$location_done = $db->execute();
			if ($location_done);
			{
				// If succesfully remove Location add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.location) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Location items from the ucm content table
			$location_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_supportgroups.location') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($location_condition);
			$db->setQuery($query);
			// Execute the query to remove Location items
			$location_done = $db->execute();
			if ($location_done);
			{
				// If succesfully remove Location add queued success message.
				$app->enqueueMessage(JText::_('The (com_supportgroups.location) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Location items are cleared from DB
			foreach ($location_ids as $location_id)
			{
				// Remove Location items from the ucm base table
				$location_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $location_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($location_condition);
				$db->setQuery($query);
				// Execute the query to remove Location items
				$db->execute();

				// Remove Location items from the ucm history table
				$location_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $location_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($location_condition);
				$db->setQuery($query);
				// Execute the query to remove Location items
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
			if ($region_done);
			{
				// If succesfully remove Region add queued success message.
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
			if ($region_done);
			{
				// If succesfully remove Region add queued success message.
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
			if ($region_done);
			{
				// If succesfully remove Region add queued success message.
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
			if ($country_done);
			{
				// If succesfully remove Country add queued success message.
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
			if ($country_done);
			{
				// If succesfully remove Country add queued success message.
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
			if ($country_done);
			{
				// If succesfully remove Country add queued success message.
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
			if ($currency_done);
			{
				// If succesfully remove Currency add queued success message.
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
			if ($currency_done);
			{
				// If succesfully remove Currency add queued success message.
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
			if ($currency_done);
			{
				// If succesfully remove Currency add queued success message.
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
			if ($help_document_done);
			{
				// If succesfully remove Help_document add queued success message.
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
			if ($help_document_done);
			{
				// If succesfully remove Help_document add queued success message.
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
			if ($help_document_done);
			{
				// If succesfully remove Help_document add queued success message.
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
		if ($help_document_done);
		{
			// If succesfully remove supportgroups add queued success message.
			$app->enqueueMessage(JText::_('All related items was removed from the <b>#__assets</b> table'));
		}

		// little notice as after service, in case of bad experience with component.
		echo '<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:llewellyn@vdm.io">llewellyn@vdm.io</a>.
		<br />We at Vast Development Method are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="http://www.vdm.io" target="_blank">http://www.vdm.io</a> today!</p>';
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent)
	{
		
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		if ($type == 'uninstall')
		{        	
			return true;
		}
		
		$app = JFactory::getApplication();
		$jversion = new JVersion();
		if (!$jversion->isCompatible('3.4.1'))
		{
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.4.1 before continuing!', 'error');
			return false;
		}
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		// set the default component settings
		if ($type == 'install')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the support_group content type object.
			$support_group = new stdClass();
			$support_group->type_title = 'Supportgroups Support_group';
			$support_group->type_alias = 'com_supportgroups.support_group';
			$support_group->table = '{"special": {"dbtable": "#__supportgroups_support_group","key": "id","type": "Support_group","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$support_group->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","phone":"phone","location":"location","clinic":"clinic","male":"male","female":"female","female_art":"female_art","male_art":"male_art","female_children":"female_children","male_children":"male_children","area":"area"}}';
			$support_group->router = 'SupportgroupsHelperRoute::getSupport_groupRoute';
			$support_group->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/support_group.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","area"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","location","clinic","male","female","female_art","male_art","female_children","male_children"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "location","targetTable": "#__supportgroups_location","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "clinic","targetTable": "#__supportgroups_clinic","targetColumn": "id","displayColumn": "name"}]}';

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

			// Create the clinic content type object.
			$clinic = new stdClass();
			$clinic->type_title = 'Supportgroups Clinic';
			$clinic->type_alias = 'com_supportgroups.clinic';
			$clinic->table = '{"special": {"dbtable": "#__supportgroups_clinic","key": "id","type": "Clinic","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$clinic->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","phone":"phone","area":"area"}}';
			$clinic->router = 'SupportgroupsHelperRoute::getClinicRoute';
			$clinic->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/clinic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","area"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$clinic_Inserted = $db->insertObject('#__content_types', $clinic);

			// Create the location content type object.
			$location = new stdClass();
			$location->type_title = 'Supportgroups Location';
			$location->type_alias = 'com_supportgroups.location';
			$location->table = '{"special": {"dbtable": "#__supportgroups_location","key": "id","type": "Location","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$location->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","region":"region","area":"area"}}';
			$location->router = 'SupportgroupsHelperRoute::getLocationRoute';
			$location->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/location.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","area"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","region"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "region","targetTable": "#__supportgroups_region","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$location_Inserted = $db->insertObject('#__content_types', $location);

			// Create the region content type object.
			$region = new stdClass();
			$region->type_title = 'Supportgroups Region';
			$region->type_alias = 'com_supportgroups.region';
			$region->table = '{"special": {"dbtable": "#__supportgroups_region","key": "id","type": "Region","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$region->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","country":"country"}}';
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
			$currency->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","codethree":"codethree","numericcode":"numericcode","symbol":"symbol","alias":"alias","positivestyle":"positivestyle","thousands":"thousands","decimalsymbol":"decimalsymbol","decimalplace":"decimalplace","negativestyle":"negativestyle"}}';
			$currency->router = 'SupportgroupsHelperRoute::getCurrencyRoute';
			$currency->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/currency.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","numericcode","decimalplace"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$currency_Inserted = $db->insertObject('#__content_types', $currency);

			// Create the help_document content type object.
			$help_document = new stdClass();
			$help_document->type_title = 'Supportgroups Help_document';
			$help_document->type_alias = 'com_supportgroups.help_document';
			$help_document->table = '{"special": {"dbtable": "#__supportgroups_help_document","key": "id","type": "Help_document","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$help_document->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "title","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "content","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"title":"title","type":"type","groups":"groups","location":"location","admin_view":"admin_view","site_view":"site_view","target":"target","content":"content","alias":"alias","article":"article","url":"url","not_required":"not_required"}}';
			$help_document->router = 'SupportgroupsHelperRoute::getHelp_documentRoute';
			$help_document->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/help_document.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","not_required"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","type","location","target","article","not_required"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "article","targetTable": "#__content","targetColumn": "id","displayColumn": "title"}]}';

			// Set the object into the content types table.
			$help_document_Inserted = $db->insertObject('#__content_types', $help_document);


			// Install the global extenstion params.
			$query = $db->getQuery(true);

			// Field to update.
			$fields = array(
				$db->quoteName('params') . ' = ' . $db->quote('{"autorName":"Llewellyn van der Merwe","autorEmail":"llewellyn@vdm.io","check_in":"-1 day","save_history":"1","history_limit":"10","uikit_load":"1","uikit_min":"","uikit_style":""}'),
			);

			// Condition.
			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('com_supportgroups')
			);

			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$allDone = $db->execute();
			echo '<a target="_blank" href="http://www.vdm.io" title="Support Groups">
				<img src="components/com_supportgroups/assets/images/component-300.jpg"/>
				</a>';
		}
		// do any updates needed
		if ($type == 'update')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the support_group content type object.
			$support_group = new stdClass();
			$support_group->type_title = 'Supportgroups Support_group';
			$support_group->type_alias = 'com_supportgroups.support_group';
			$support_group->table = '{"special": {"dbtable": "#__supportgroups_support_group","key": "id","type": "Support_group","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$support_group->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","phone":"phone","location":"location","clinic":"clinic","male":"male","female":"female","female_art":"female_art","male_art":"male_art","female_children":"female_children","male_children":"male_children","area":"area"}}';
			$support_group->router = 'SupportgroupsHelperRoute::getSupport_groupRoute';
			$support_group->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/support_group.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","area"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","location","clinic","male","female","female_art","male_art","female_children","male_children"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "location","targetTable": "#__supportgroups_location","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "clinic","targetTable": "#__supportgroups_clinic","targetColumn": "id","displayColumn": "name"}]}';

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

			// Create the clinic content type object.
			$clinic = new stdClass();
			$clinic->type_title = 'Supportgroups Clinic';
			$clinic->type_alias = 'com_supportgroups.clinic';
			$clinic->table = '{"special": {"dbtable": "#__supportgroups_clinic","key": "id","type": "Clinic","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$clinic->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","phone":"phone","area":"area"}}';
			$clinic->router = 'SupportgroupsHelperRoute::getClinicRoute';
			$clinic->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/clinic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","area"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if clinic type is already in content_type DB.
			$clinic_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($clinic->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$clinic->type_id = $db->loadResult();
				$clinic_Updated = $db->updateObject('#__content_types', $clinic, 'type_id');
			}
			else
			{
				$clinic_Inserted = $db->insertObject('#__content_types', $clinic);
			}

			// Create the location content type object.
			$location = new stdClass();
			$location->type_title = 'Supportgroups Location';
			$location->type_alias = 'com_supportgroups.location';
			$location->table = '{"special": {"dbtable": "#__supportgroups_location","key": "id","type": "Location","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$location->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","region":"region","area":"area"}}';
			$location->router = 'SupportgroupsHelperRoute::getLocationRoute';
			$location->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/location.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","area"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","region"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "region","targetTable": "#__supportgroups_region","targetColumn": "id","displayColumn": "name"}]}';

			// Check if location type is already in content_type DB.
			$location_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($location->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$location->type_id = $db->loadResult();
				$location_Updated = $db->updateObject('#__content_types', $location, 'type_id');
			}
			else
			{
				$location_Inserted = $db->insertObject('#__content_types', $location);
			}

			// Create the region content type object.
			$region = new stdClass();
			$region->type_title = 'Supportgroups Region';
			$region->type_alias = 'com_supportgroups.region';
			$region->table = '{"special": {"dbtable": "#__supportgroups_region","key": "id","type": "Region","prefix": "supportgroupsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$region->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","country":"country"}}';
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
			$currency->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","codethree":"codethree","numericcode":"numericcode","symbol":"symbol","alias":"alias","positivestyle":"positivestyle","thousands":"thousands","decimalsymbol":"decimalsymbol","decimalplace":"decimalplace","negativestyle":"negativestyle"}}';
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
			$help_document->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "title","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "content","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"title":"title","type":"type","groups":"groups","location":"location","admin_view":"admin_view","site_view":"site_view","target":"target","content":"content","alias":"alias","article":"article","url":"url","not_required":"not_required"}}';
			$help_document->router = 'SupportgroupsHelperRoute::getHelp_documentRoute';
			$help_document->content_history_options = '{"formFile": "administrator/components/com_supportgroups/models/forms/help_document.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","not_required"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","type","location","target","article","not_required"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "article","targetTable": "#__content","targetColumn": "id","displayColumn": "title"}]}';

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
				<img src="components/com_supportgroups/assets/images/component-300.jpg"/>
				</a>
				<h3>Upgrade to Version 1.0.3 Was Successful! Let us know if anything is not working as expected.</h3>';
		}
	}
}
