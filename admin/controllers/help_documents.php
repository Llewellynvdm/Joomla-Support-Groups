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
	@subpackage		help_documents.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Help_documents Controller
 */
class SupportgroupsControllerHelp_documents extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_SUPPORTGROUPS_HELP_DOCUMENTS';

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
	public function getModel($name = 'Help_document', $prefix = 'SupportgroupsModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('help_document.export', 'com_supportgroups') && $user->authorise('core.export', 'com_supportgroups'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			JArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Help_documents');
			// get the data to export
			$data = $model->getExportData($pks);
			if (SupportgroupsHelper::checkArray($data))
			{
				// now set the data to the spreadsheet
				$date = JFactory::getDate();
				SupportgroupsHelper::xls($data,'Help_documents_'.$date->format('jS_F_Y'),'Help documents exported ('.$date->format('jS F, Y').')','help documents');
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_SUPPORTGROUPS_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_supportgroups&view=help_documents', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('help_document.import', 'com_supportgroups') && $user->authorise('core.import', 'com_supportgroups'))
		{
			// Get the import model
			$model = $this->getModel('Help_documents');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (SupportgroupsHelper::checkObject($headers))
			{
				// Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('help_document_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'help_documents');
				$session->set('dataType_VDM_IMPORTINTO', 'help_document');
				// Redirect to import view.
				$message = JText::_('COM_SUPPORTGROUPS_IMPORT_SELECT_FILE_FOR_HELP_DOCUMENTS');
				$this->setRedirect(JRoute::_('index.php?option=com_supportgroups&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_SUPPORTGROUPS_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_supportgroups&view=help_documents', false), $message, 'error');
		return;
	}
}
