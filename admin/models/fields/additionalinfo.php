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
	@subpackage		additionalinfo.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('checkboxes');

/**
 * Additionalinfo Form Field class for the Supportgroups component
 */
class JFormFieldAdditionalinfo extends JFormFieldCheckboxes
{
	/**
	 * The additionalinfo field type.
	 *
	 * @var		string
	 */
	public $type = 'additionalinfo';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array    An array of JHtml options.
	 */
	protected function getOptions()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('a.id','a.name','b.name'),array('id','info_name','type')));
		$query->from($db->quoteName('#__supportgroups_additional_info', 'a'));
		$query->join('INNER', $db->quoteName('#__supportgroups_info_type', 'b') . ' ON (' . $db->quoteName('a.info_type') . ' = ' . $db->quoteName('b.id') . ')');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->order('b.name ASC');
		$query->order('a.name ASC');
		$db->setQuery((string)$query);
		$items = $db->loadObjectList();
		$options = array();
		if ($items)
		{
			foreach($items as $item)
			{
				$tmp = array(
					'value'    => $item->id,
					'text'     => '&nbsp;&nbsp;<b>'.$item->info_name.'</b>&nbsp;<small>('.$item->type.')</small>',
					'checked'  => false
				);
				$options[] = (object) $tmp;
			}
		}
		return $options;
	}
}
