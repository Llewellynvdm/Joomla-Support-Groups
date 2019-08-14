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
	@subpackage		view.html.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Supportgroups View class for the Payments
 */
class SupportgroupsViewPayments extends JViewLegacy
{
	/**
	 * Payments view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			SupportgroupsHelper::addSubmenu('payments');
		}

		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = JFactory::getUser();
		$this->listOrder = $this->escape($this->state->get('list.ordering'));
		$this->listDirn = $this->escape($this->state->get('list.direction'));
		$this->saveOrder = $this->listOrder == 'ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) JUri::getInstance()));
		// get global action permissions
		$this->canDo = SupportgroupsHelper::getActions('payment');
		$this->canEdit = $this->canDo->get('payment.edit');
		$this->canState = $this->canDo->get('payment.edit.state');
		$this->canCreate = $this->canDo->get('payment.create');
		$this->canDelete = $this->canDo->get('payment.delete');
		$this->canBatch = $this->canDo->get('core.batch');

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
			// load the batch html
			if ($this->canCreate && $this->canEdit && $this->canState)
			{
				$this->batchDisplay = JHtmlBatch_::render();
			}
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_SUPPORTGROUPS_PAYMENTS'), 'archive');
		JHtmlSidebar::setAction('index.php?option=com_supportgroups&view=payments');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('payment.add');
		}

		// Only load if there are items
		if (SupportgroupsHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('payment.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('payments.publish');
				JToolBarHelper::unpublishList('payments.unpublish');
				JToolBarHelper::archiveList('payments.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('payments.checkin');
				}
			}

			// Add a batch button
			if ($this->canBatch && $this->canCreate && $this->canEdit && $this->canState)
			{
				// Get the toolbar object instance
				$bar = JToolBar::getInstance('toolbar');
				// set the batch button name
				$title = JText::_('JTOOLBAR_BATCH');
				// Instantiate a new JLayoutFile instance and render the batch button
				$layout = new JLayoutFile('joomla.toolbar.batch');
				// add the button to the page
				$dhtml = $layout->render(array('title' => $title));
				$bar->appendButton('Custom', $dhtml, 'batch');
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				JToolbarHelper::deleteList('', 'payments.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('payments.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('payment.export'))
			{
				JToolBarHelper::custom('payments.exportData', 'download', '', 'COM_SUPPORTGROUPS_EXPORT_DATA', true);
			}
		}

		if ($this->canDo->get('core.import') && $this->canDo->get('payment.import'))
		{
			JToolBarHelper::custom('payments.importData', 'upload', '', 'COM_SUPPORTGROUPS_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$help_url = SupportgroupsHelper::getHelpUrl('payments');
		if (SupportgroupsHelper::checkString($help_url))
		{
				JToolbarHelper::help('COM_SUPPORTGROUPS_HELP_MANAGER', false, $help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_supportgroups');
		}

		if ($this->canState)
		{
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
			);
			// only load if batch allowed
			if ($this->canBatch)
			{
				JHtmlBatch_::addListSelection(
					JText::_('COM_SUPPORTGROUPS_KEEP_ORIGINAL_STATE'),
					'batch[published]',
					JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text', '', true)
				);
			}
		}

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);

		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_SUPPORTGROUPS_KEEP_ORIGINAL_ACCESS'),
				'batch[access]',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text')
			);
		}

		// Set Support Group Name Selection
		$this->support_groupNameOptions = JFormHelper::loadFieldType('Supportgroups')->options;
		// We do some sanitation for Support Group Name filter
		if (SupportgroupsHelper::checkArray($this->support_groupNameOptions) &&
			isset($this->support_groupNameOptions[0]->value) &&
			!SupportgroupsHelper::checkString($this->support_groupNameOptions[0]->value))
		{
			unset($this->support_groupNameOptions[0]);
		}
		// Only load Support Group Name filter if it has values
		if (SupportgroupsHelper::checkArray($this->support_groupNameOptions))
		{
			// Support Group Name Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_SUPPORTGROUPS_PAYMENT_SUPPORT_GROUP_LABEL').' -',
				'filter_support_group',
				JHtml::_('select.options', $this->support_groupNameOptions, 'value', 'text', $this->state->get('filter.support_group'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Support Group Name Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_SUPPORTGROUPS_PAYMENT_SUPPORT_GROUP_LABEL').' -',
					'batch[support_group]',
					JHtml::_('select.options', $this->support_groupNameOptions, 'value', 'text')
				);
			}
		}

		// Set Year Selection
		$this->yearOptions = $this->getTheYearSelections();
		// We do some sanitation for Year filter
		if (SupportgroupsHelper::checkArray($this->yearOptions) &&
			isset($this->yearOptions[0]->value) &&
			!SupportgroupsHelper::checkString($this->yearOptions[0]->value))
		{
			unset($this->yearOptions[0]);
		}
		// Only load Year filter if it has values
		if (SupportgroupsHelper::checkArray($this->yearOptions))
		{
			// Year Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_SUPPORTGROUPS_PAYMENT_YEAR_LABEL').' -',
				'filter_year',
				JHtml::_('select.options', $this->yearOptions, 'value', 'text', $this->state->get('filter.year'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Year Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_SUPPORTGROUPS_PAYMENT_YEAR_LABEL').' -',
					'batch[year]',
					JHtml::_('select.options', $this->yearOptions, 'value', 'text')
				);
			}
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		if (!isset($this->document))
		{
			$this->document = JFactory::getDocument();
		}
		$this->document->setTitle(JText::_('COM_SUPPORTGROUPS_PAYMENTS'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_supportgroups/assets/css/payments.css", (SupportgroupsHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if(strlen($var) > 50)
		{
			// use the helper htmlEscape method instead and shorten the string
			return SupportgroupsHelper::htmlEscape($var, $this->_charset, true);
		}
		// use the helper htmlEscape method instead.
		return SupportgroupsHelper::htmlEscape($var, $this->_charset);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.sorting' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'g.name' => JText::_('COM_SUPPORTGROUPS_PAYMENT_SUPPORT_GROUP_LABEL'),
			'a.year' => JText::_('COM_SUPPORTGROUPS_PAYMENT_YEAR_LABEL'),
			'a.amount' => JText::_('COM_SUPPORTGROUPS_PAYMENT_AMOUNT_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}

	protected function getTheYearSelections()
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('year'));
		$query->from($db->quoteName('#__supportgroups_payment'));
		$query->order($db->quoteName('year') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$results = $db->loadColumn();

		if ($results)
		{
			// get model
			$model = $this->getModel();
			$results = array_unique($results);
			$_filter = array();
			foreach ($results as $year)
			{
				// Translate the year selection
				$text = $model->selectionTranslation($year,'year');
				// Now add the year and its text to the options array
				$_filter[] = JHtml::_('select.option', $year, JText::_($text));
			}
			return $_filter;
		}
		return false;
	}
}
