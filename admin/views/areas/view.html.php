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
 * Supportgroups View class for the Areas
 */
class SupportgroupsViewAreas extends JViewLegacy
{
	/**
	 * Areas view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			SupportgroupsHelper::addSubmenu('areas');
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
		$this->canDo = SupportgroupsHelper::getActions('area');
		$this->canEdit = $this->canDo->get('area.edit');
		$this->canState = $this->canDo->get('area.edit.state');
		$this->canCreate = $this->canDo->get('area.create');
		$this->canDelete = $this->canDo->get('area.delete');
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
		JToolBarHelper::title(JText::_('COM_SUPPORTGROUPS_AREAS'), 'home');
		JHtmlSidebar::setAction('index.php?option=com_supportgroups&view=areas');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('area.add');
		}

		// Only load if there are items
		if (SupportgroupsHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('area.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('areas.publish');
				JToolBarHelper::unpublishList('areas.unpublish');
				JToolBarHelper::archiveList('areas.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('areas.checkin');
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
				JToolbarHelper::deleteList('', 'areas.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('areas.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('area.export'))
			{
				JToolBarHelper::custom('areas.exportData', 'download', '', 'COM_SUPPORTGROUPS_EXPORT_DATA', true);
			}
		}

		if ($this->canDo->get('core.import') && $this->canDo->get('area.import'))
		{
			JToolBarHelper::custom('areas.importData', 'upload', '', 'COM_SUPPORTGROUPS_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$help_url = SupportgroupsHelper::getHelpUrl('areas');
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

		// Set Area Type Name Selection
		$this->area_typeNameOptions = JFormHelper::loadFieldType('Areastype')->options;
		// We do some sanitation for Area Type Name filter
		if (SupportgroupsHelper::checkArray($this->area_typeNameOptions) &&
			isset($this->area_typeNameOptions[0]->value) &&
			!SupportgroupsHelper::checkString($this->area_typeNameOptions[0]->value))
		{
			unset($this->area_typeNameOptions[0]);
		}
		// Only load Area Type Name filter if it has values
		if (SupportgroupsHelper::checkArray($this->area_typeNameOptions))
		{
			// Area Type Name Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_SUPPORTGROUPS_AREA_AREA_TYPE_LABEL').' -',
				'filter_area_type',
				JHtml::_('select.options', $this->area_typeNameOptions, 'value', 'text', $this->state->get('filter.area_type'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Area Type Name Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_SUPPORTGROUPS_AREA_AREA_TYPE_LABEL').' -',
					'batch[area_type]',
					JHtml::_('select.options', $this->area_typeNameOptions, 'value', 'text')
				);
			}
		}

		// Set Region Name Selection
		$this->regionNameOptions = JFormHelper::loadFieldType('Regions')->options;
		// We do some sanitation for Region Name filter
		if (SupportgroupsHelper::checkArray($this->regionNameOptions) &&
			isset($this->regionNameOptions[0]->value) &&
			!SupportgroupsHelper::checkString($this->regionNameOptions[0]->value))
		{
			unset($this->regionNameOptions[0]);
		}
		// Only load Region Name filter if it has values
		if (SupportgroupsHelper::checkArray($this->regionNameOptions))
		{
			// Region Name Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_SUPPORTGROUPS_AREA_REGION_LABEL').' -',
				'filter_region',
				JHtml::_('select.options', $this->regionNameOptions, 'value', 'text', $this->state->get('filter.region'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Region Name Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_SUPPORTGROUPS_AREA_REGION_LABEL').' -',
					'batch[region]',
					JHtml::_('select.options', $this->regionNameOptions, 'value', 'text')
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
		$this->document->setTitle(JText::_('COM_SUPPORTGROUPS_AREAS'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_supportgroups/assets/css/areas.css", (SupportgroupsHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
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
			'a.name' => JText::_('COM_SUPPORTGROUPS_AREA_NAME_LABEL'),
			'g.name' => JText::_('COM_SUPPORTGROUPS_AREA_AREA_TYPE_LABEL'),
			'h.name' => JText::_('COM_SUPPORTGROUPS_AREA_REGION_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
