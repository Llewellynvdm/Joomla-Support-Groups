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
 * Payments Controller
 */
class SupportgroupsControllerPayments extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_SUPPORTGROUPS_PAYMENTS';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelLegacy  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Payment', $prefix = 'SupportgroupsModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('payment.export', 'com_supportgroups') && $user->authorise('core.export', 'com_supportgroups'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			$pks = ArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Payments');
			// get the data to export
			$data = $model->getExportData($pks);
			if (SupportgroupsHelper::checkArray($data))
			{
				// now set the data to the spreadsheet
				$date = JFactory::getDate();
				SupportgroupsHelper::xls($data,'Payments_'.$date->format('jS_F_Y'),'Payments exported ('.$date->format('jS F, Y').')','payments');
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_SUPPORTGROUPS_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_supportgroups&view=payments', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('payment.import', 'com_supportgroups') && $user->authorise('core.import', 'com_supportgroups'))
		{
			// Get the import model
			$model = $this->getModel('Payments');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (SupportgroupsHelper::checkObject($headers))
			{
				// Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('payment_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'payments');
				$session->set('dataType_VDM_IMPORTINTO', 'payment');
				// Redirect to import view.
				$message = JText::_('COM_SUPPORTGROUPS_IMPORT_SELECT_FILE_FOR_PAYMENTS');
				$this->setRedirect(JRoute::_('index.php?option=com_supportgroups&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_SUPPORTGROUPS_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_supportgroups&view=payments', false), $message, 'error');
		return;
	}
}
