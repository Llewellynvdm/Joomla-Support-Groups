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
	@subpackage		view.html.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html 
	
	Support Groups 
                                                             
/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Region View class
 */
class SupportgroupsViewRegion extends JViewLegacy
{
	/**
	 * display method of View
	 * @return void
	 */
	public function display($tpl = null)
	{
		// Check for errors.
		if (count($errors = $this->get('Errors')))
                {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Assign the variables
		$this->form 		= $this->get('Form');
		$this->item 		= $this->get('Item');
		$this->script 		= $this->get('Script');
		$this->state		= $this->get('State');
                // get action permissions
		$this->canDo		= SupportgroupsHelper::getActions('region',$this->item);
		// get input
		$jinput = JFactory::getApplication()->input;
		$this->ref 		= $jinput->get('ref', 0, 'word');
		$this->refid            = $jinput->get('refid', 0, 'int');
		$this->referral         = '';
		if ($this->refid)
                {
                        // return to the item that refered to this item
                        $this->referral = '&ref='.(string)$this->ref.'&refid='.(int)$this->refid;
                }
                elseif($this->ref)
                {
                        // return to the list view that refered to this item
                        $this->referral = '&ref='.(string)$this->ref;
                }

		// Get Linked view data
		$this->vvxlocations		= $this->get('Vvxlocations');

		// Set the toolbar
		$this->addToolBar();

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
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId	= $user->id;
		$isNew = $this->item->id == 0;

		JToolbarHelper::title( JText::_($isNew ? 'COM_SUPPORTGROUPS_REGION_NEW' : 'COM_SUPPORTGROUPS_REGION_EDIT'), 'pencil-2 article-add');
		// Built the actions for new and existing records.
		if ($this->refid || $this->ref)
		{
			if ($this->canDo->get('region.create') && $isNew)
			{
				// We can create the record.
				JToolBarHelper::save('region.save', 'JTOOLBAR_SAVE');
			}
			elseif ($this->canDo->get('region.edit'))
			{
				// We can save the record.
				JToolBarHelper::save('region.save', 'JTOOLBAR_SAVE');
			}
			if ($isNew)
			{
				// Do not creat but cancel.
				JToolBarHelper::cancel('region.cancel', 'JTOOLBAR_CANCEL');
			}
			else
			{
				// We can close it.
				JToolBarHelper::cancel('region.cancel', 'JTOOLBAR_CLOSE');
			}
		}
		else
		{
			if ($isNew)
			{
				// For new records, check the create permission.
				if ($this->canDo->get('region.create'))
				{
					JToolBarHelper::apply('region.apply', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('region.save', 'JTOOLBAR_SAVE');
					JToolBarHelper::custom('region.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				};
				JToolBarHelper::cancel('region.cancel', 'JTOOLBAR_CANCEL');
			}
			else
			{
				if ($this->canDo->get('region.edit'))
				{
					// We can save the new record
					JToolBarHelper::apply('region.apply', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('region.save', 'JTOOLBAR_SAVE');
					// We can save this record, but check the create permission to see
					// if we can return to make a new one.
					if ($this->canDo->get('region.create'))
					{
						JToolBarHelper::custom('region.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
					}
				}
				$canVersion = ($this->canDo->get('core.version') && $this->canDo->get('region.version'));
				if ($this->state->params->get('save_history', 1) && $this->canDo->get('region.edit') && $canVersion)
				{
					JToolbarHelper::versions('com_supportgroups.region', $this->item->id);
				}
				if ($this->canDo->get('region.create'))
				{
					JToolBarHelper::custom('region.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
				}
				JToolBarHelper::cancel('region.cancel', 'JTOOLBAR_CLOSE');
			}
		}
		JToolbarHelper::divider();
		// set help url for this view if found
		$help_url = SupportgroupsHelper::getHelpUrl('region');
		if (SupportgroupsHelper::checkString($help_url))
		{
			JToolbarHelper::help('COM_SUPPORTGROUPS_HELP_MANAGER', false, $help_url);
		}
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
		if(strlen($var) > 30)
		{
    		// use the helper htmlEscape method instead and shorten the string
			return SupportgroupsHelper::htmlEscape($var, $this->_charset, true, 30);
		}
                // use the helper htmlEscape method instead.
		return SupportgroupsHelper::htmlEscape($var, $this->_charset);
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$isNew = ($this->item->id < 1);
		$document = JFactory::getDocument();
		$document->setTitle(JText::_($isNew ? 'COM_SUPPORTGROUPS_REGION_NEW' : 'COM_SUPPORTGROUPS_REGION_EDIT'));
		$document->addStyleSheet(JURI::root() . "administrator/components/com_supportgroups/assets/css/region.css"); 

		// Add the CSS for Footable.
		$document->addStyleSheet(JURI::root() .'media/com_supportgroups/footable/css/footable.core.min.css');

		// Use the Metro Style
		if (!isset($this->fooTableStyle) || 0 == $this->fooTableStyle)
		{
			$document->addStyleSheet(JURI::root() .'media/com_supportgroups/footable/css/footable.metro.min.css');
		}
		// Use the Legacy Style.
		elseif (isset($this->fooTableStyle) && 1 == $this->fooTableStyle)
		{
			$document->addStyleSheet(JURI::root() .'media/com_supportgroups/footable/css/footable.standalone.min.css');
		}

		// Add the JavaScript for Footable
		$document->addScript(JURI::root() .'media/com_supportgroups/footable/js/footable.js');
		$document->addScript(JURI::root() .'media/com_supportgroups/footable/js/footable.sort.js');
		$document->addScript(JURI::root() .'media/com_supportgroups/footable/js/footable.filter.js');
		$document->addScript(JURI::root() .'media/com_supportgroups/footable/js/footable.paginate.js');

		$footable = "jQuery(document).ready(function() { jQuery(function () { jQuery('.footable').footable(); }); jQuery('.nav-tabs').on('click', 'li', function() { setTimeout(tableFix, 10); }); }); function tableFix() { jQuery('.footable').trigger('footable_resize'); }";
		$document->addScriptDeclaration($footable);

		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "administrator/components/com_supportgroups/views/region/submitbutton.js");
		JText::script('view not acceptable. Error');
	}
}
