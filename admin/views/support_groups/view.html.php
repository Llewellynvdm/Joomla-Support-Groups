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
 * Supportgroups View class for the Support_groups
 */
class SupportgroupsViewSupport_groups extends JViewLegacy
{
	/**
	 * Support_groups view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			SupportgroupsHelper::addSubmenu('support_groups');
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
		$this->canDo = SupportgroupsHelper::getActions('support_group');
		$this->canEdit = $this->canDo->get('support_group.edit');
		$this->canState = $this->canDo->get('support_group.edit.state');
		$this->canCreate = $this->canDo->get('support_group.create');
		$this->canDelete = $this->canDo->get('support_group.delete');
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
		JToolBarHelper::title(JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUPS'), 'eye-open');
		JHtmlSidebar::setAction('index.php?option=com_supportgroups&view=support_groups');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('support_group.add');
		}

		// Only load if there are items
		if (SupportgroupsHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('support_group.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('support_groups.publish');
				JToolBarHelper::unpublishList('support_groups.unpublish');
				JToolBarHelper::archiveList('support_groups.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('support_groups.checkin');
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
			if ($this->user->authorise('support_group.smart_export', 'com_supportgroups'))
			{
				// add Smart Export button.
				JToolBarHelper::custom('support_groups.smartExport', 'download', '', 'COM_SUPPORTGROUPS_SMART_EXPORT', 'true');
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				JToolbarHelper::deleteList('', 'support_groups.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('support_groups.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('support_group.export'))
			{
				JToolBarHelper::custom('support_groups.exportData', 'download', '', 'COM_SUPPORTGROUPS_EXPORT_DATA', true);
			}
		}

		if ($this->canDo->get('core.import') && $this->canDo->get('support_group.import'))
		{
			JToolBarHelper::custom('support_groups.importData', 'upload', '', 'COM_SUPPORTGROUPS_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$help_url = SupportgroupsHelper::getHelpUrl('support_groups');
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

		// Set Area Name Selection
		$this->areaNameOptions = JFormHelper::loadFieldType('Areas')->options;
		// We do some sanitation for Area Name filter
		if (SupportgroupsHelper::checkArray($this->areaNameOptions) &&
			isset($this->areaNameOptions[0]->value) &&
			!SupportgroupsHelper::checkString($this->areaNameOptions[0]->value))
		{
			unset($this->areaNameOptions[0]);
		}
		// Only load Area Name filter if it has values
		if (SupportgroupsHelper::checkArray($this->areaNameOptions))
		{
			// Area Name Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_AREA_LABEL').' -',
				'filter_area',
				JHtml::_('select.options', $this->areaNameOptions, 'value', 'text', $this->state->get('filter.area'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Area Name Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_AREA_LABEL').' -',
					'batch[area]',
					JHtml::_('select.options', $this->areaNameOptions, 'value', 'text')
				);
			}
		}

		// Set Facility Name Selection
		$this->facilityNameOptions = JFormHelper::loadFieldType('Facilities')->options;
		// We do some sanitation for Facility Name filter
		if (SupportgroupsHelper::checkArray($this->facilityNameOptions) &&
			isset($this->facilityNameOptions[0]->value) &&
			!SupportgroupsHelper::checkString($this->facilityNameOptions[0]->value))
		{
			unset($this->facilityNameOptions[0]);
		}
		// Only load Facility Name filter if it has values
		if (SupportgroupsHelper::checkArray($this->facilityNameOptions))
		{
			// Facility Name Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_FACILITY_LABEL').' -',
				'filter_facility',
				JHtml::_('select.options', $this->facilityNameOptions, 'value', 'text', $this->state->get('filter.facility'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Facility Name Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_FACILITY_LABEL').' -',
					'batch[facility]',
					JHtml::_('select.options', $this->facilityNameOptions, 'value', 'text')
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
		$this->document->setTitle(JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUPS'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_supportgroups/assets/css/support_groups.css", (SupportgroupsHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
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
			'a.name' => JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_NAME_LABEL'),
			'a.phone' => JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_PHONE_LABEL'),
			'g.name' => JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_AREA_LABEL'),
			'h.name' => JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_FACILITY_LABEL'),
			'a.male' => JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_MALE_LABEL'),
			'a.female' => JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_FEMALE_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
