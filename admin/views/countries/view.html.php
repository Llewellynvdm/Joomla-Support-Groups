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
	@subpackage		view.html.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Supportgroups View class for the Countries
 */
class SupportgroupsViewCountries extends JViewLegacy
{
	/**
	 * Countries view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			SupportgroupsHelper::addSubmenu('countries');
		}

		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = JFactory::getUser();
		// Load the filter form from xml.
		$this->filterForm = $this->get('FilterForm');
		// Load the active filters.
		$this->activeFilters = $this->get('ActiveFilters');
		// Add the list ordering clause.
		$this->listOrder = $this->escape($this->state->get('list.ordering', 'a.id'));
		$this->listDirn = $this->escape($this->state->get('list.direction', 'DESC'));
		$this->saveOrder = $this->listOrder == 'a.ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) JUri::getInstance()));
		// get global action permissions
		$this->canDo = SupportgroupsHelper::getActions('country');
		$this->canEdit = $this->canDo->get('country.edit');
		$this->canState = $this->canDo->get('country.edit.state');
		$this->canCreate = $this->canDo->get('country.create');
		$this->canDelete = $this->canDo->get('country.delete');
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
		JToolBarHelper::title(JText::_('COM_SUPPORTGROUPS_COUNTRIES'), 'flag');
		JHtmlSidebar::setAction('index.php?option=com_supportgroups&view=countries');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('country.add');
		}

		// Only load if there are items
		if (SupportgroupsHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('country.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('countries.publish');
				JToolBarHelper::unpublishList('countries.unpublish');
				JToolBarHelper::archiveList('countries.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('countries.checkin');
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
				JToolbarHelper::deleteList('', 'countries.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('countries.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('country.export'))
			{
				JToolBarHelper::custom('countries.exportData', 'download', '', 'COM_SUPPORTGROUPS_EXPORT_DATA', true);
			}
		}

		if ($this->canDo->get('core.import') && $this->canDo->get('country.import'))
		{
			JToolBarHelper::custom('countries.importData', 'upload', '', 'COM_SUPPORTGROUPS_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$this->help_url = SupportgroupsHelper::getHelpUrl('countries');
		if (SupportgroupsHelper::checkString($this->help_url))
		{
				JToolbarHelper::help('COM_SUPPORTGROUPS_HELP_MANAGER', false, $this->help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_supportgroups');
		}

		// Only load published batch if state and batch is allowed
		if ($this->canState && $this->canBatch)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_SUPPORTGROUPS_KEEP_ORIGINAL_STATE'),
				'batch[published]',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text', '', true)
			);
		}

		// Only load access batch if create, edit and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_SUPPORTGROUPS_KEEP_ORIGINAL_ACCESS'),
				'batch[access]',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text')
			);
		}

		// Only load Currency Name batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Currency Name Selection
			$this->currencyNameOptions = JFormHelper::loadFieldType('Currency')->options;
			// We do some sanitation for Currency Name filter
			if (SupportgroupsHelper::checkArray($this->currencyNameOptions) &&
				isset($this->currencyNameOptions[0]->value) &&
				!SupportgroupsHelper::checkString($this->currencyNameOptions[0]->value))
			{
				unset($this->currencyNameOptions[0]);
			}
			// Currency Name Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_SUPPORTGROUPS_COUNTRY_CURRENCY_LABEL').' -',
				'batch[currency]',
				JHtml::_('select.options', $this->currencyNameOptions, 'value', 'text')
			);
		}

		// Only load Worldzone batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Worldzone Selection
			$this->worldzoneOptions = JFormHelper::loadFieldType('countriesfilterworldzone')->options;
			// We do some sanitation for Worldzone filter
			if (SupportgroupsHelper::checkArray($this->worldzoneOptions) &&
				isset($this->worldzoneOptions[0]->value) &&
				!SupportgroupsHelper::checkString($this->worldzoneOptions[0]->value))
			{
				unset($this->worldzoneOptions[0]);
			}
			// Worldzone Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_SUPPORTGROUPS_COUNTRY_WORLDZONE_LABEL').' -',
				'batch[worldzone]',
				JHtml::_('select.options', $this->worldzoneOptions, 'value', 'text')
			);
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
		$this->document->setTitle(JText::_('COM_SUPPORTGROUPS_COUNTRIES'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_supportgroups/assets/css/countries.css", (SupportgroupsHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
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
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.name' => JText::_('COM_SUPPORTGROUPS_COUNTRY_NAME_LABEL'),
			'g.name' => JText::_('COM_SUPPORTGROUPS_COUNTRY_CURRENCY_LABEL'),
			'a.worldzone' => JText::_('COM_SUPPORTGROUPS_COUNTRY_WORLDZONE_LABEL'),
			'a.codethree' => JText::_('COM_SUPPORTGROUPS_COUNTRY_CODETHREE_LABEL'),
			'a.codetwo' => JText::_('COM_SUPPORTGROUPS_COUNTRY_CODETWO_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
