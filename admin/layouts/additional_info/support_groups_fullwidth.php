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
	@subpackage		support_groups_fullwidth.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// set the defaults
$items = $displayData->vvwsupport_groups;
$user = JFactory::getUser();
$id = $displayData->item->id;
// set the edit URL
$edit = "index.php?option=com_supportgroups&view=support_groups&task=support_group.edit";
// set a return value
$return = ($id) ? "index.php?option=com_supportgroups&view=additional_info&layout=edit&id=" . $id : "";
// check for a return value
$jinput = JFactory::getApplication()->input;
if ($_return = $jinput->get('return', null, 'base64'))
{
	$return .= "&return=" . $_return;
}
// check if return value was set
if (SupportgroupsHelper::checkString($return))
{
	// set the referral values
	$ref = ($id) ? "&ref=additional_info&refid=" . $id . "&return=" . urlencode(base64_encode($return)) : "&return=" . urlencode(base64_encode($return));
}
else
{
	$ref = ($id) ? "&ref=additional_info&refid=" . $id : "";
}

?>
<div class="form-vertical">
<?php if (SupportgroupsHelper::checkArray($items)): ?>
<table class="footable table data support_groups" data-show-toggle="true" data-toggle-column="first" data-sorting="true" data-paging="true" data-paging-size="20" data-filtering="true">
<thead>
	<tr>
		<th data-type="html" data-sort-use="text">
			<?php echo JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_NAME_LABEL'); ?>
		</th>
		<th data-breakpoints="xs sm" data-type="html" data-sort-use="text">
			<?php echo JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_PHONE_LABEL'); ?>
		</th>
		<th data-breakpoints="xs sm" data-type="html" data-sort-use="text">
			<?php echo JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_AREA_LABEL'); ?>
		</th>
		<th data-breakpoints="xs sm md" data-type="html" data-sort-use="text">
			<?php echo JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_FACILITY_LABEL'); ?>
		</th>
		<th data-breakpoints="xs sm md" data-type="html" data-sort-use="text">
			<?php echo JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_MALE_LABEL'); ?>
		</th>
		<th data-breakpoints="xs sm md" data-type="html" data-sort-use="text">
			<?php echo JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_FEMALE_LABEL'); ?>
		</th>
		<th width="10" data-breakpoints="xs sm md">
			<?php echo JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_STATUS'); ?>
		</th>
		<th width="5" data-type="number" data-breakpoints="xs sm md">
			<?php echo JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_ID'); ?>
		</th>
	</tr>
</thead>
<tbody>
<?php foreach ($items as $i => $item): ?>
	<?php
		$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->id || $item->checked_out == 0;
		$userChkOut = JFactory::getUser($item->checked_out);
		$canDo = SupportgroupsHelper::getActions('support_group',$item,'support_groups');
	?>
	<tr>
		<td>
			<?php if ($canDo->get('support_group.edit')): ?>
				<a href="<?php echo $edit; ?>&id=<?php echo $item->id; ?><?php echo $ref; ?>"><?php echo $displayData->escape($item->name); ?></a>
				<?php if ($item->checked_out): ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $userChkOut->name, $item->checked_out_time, 'support_groups.', $canCheckin); ?>
				<?php endif; ?>
			<?php else: ?>
				<?php echo $displayData->escape($item->name); ?>
			<?php endif; ?>
		</td>
		<td>
			<?php echo $displayData->escape($item->phone); ?>
		</td>
		<td>
			<?php if ($user->authorise('area.edit', 'com_supportgroups.area.' . (int)$item->area)): ?>
				<a href="index.php?option=com_supportgroups&view=areas&task=area.edit&id=<?php echo $item->area; ?><?php echo $ref; ?>"><?php echo $displayData->escape($item->area_name); ?></a>
			<?php else: ?>
				<?php echo $displayData->escape($item->area_name); ?>
			<?php endif; ?>
		</td>
		<td>
			<?php if ($user->authorise('facility.edit', 'com_supportgroups.facility.' . (int)$item->facility)): ?>
				<a href="index.php?option=com_supportgroups&view=facilities&task=facility.edit&id=<?php echo $item->facility; ?><?php echo $ref; ?>"><?php echo $displayData->escape($item->facility_name); ?></a>
			<?php else: ?>
				<?php echo $displayData->escape($item->facility_name); ?>
			<?php endif; ?>
		</td>
		<td>
			<?php echo $displayData->escape($item->male); ?>
		</td>
		<td>
			<?php echo $displayData->escape($item->female); ?>
		</td>
		<?php if ($item->published == 1):?>
			<td class="center"  data-sort-value="1">
				<span class="status-metro status-published" title="<?php echo JText::_('COM_SUPPORTGROUPS_PUBLISHED');  ?>">
					<?php echo JText::_('COM_SUPPORTGROUPS_PUBLISHED'); ?>
				</span>
			</td>
		<?php elseif ($item->published == 0):?>
			<td class="center"  data-sort-value="2">
				<span class="status-metro status-inactive" title="<?php echo JText::_('COM_SUPPORTGROUPS_INACTIVE');  ?>">
					<?php echo JText::_('COM_SUPPORTGROUPS_INACTIVE'); ?>
				</span>
			</td>
		<?php elseif ($item->published == 2):?>
			<td class="center"  data-sort-value="3">
				<span class="status-metro status-archived" title="<?php echo JText::_('COM_SUPPORTGROUPS_ARCHIVED');  ?>">
					<?php echo JText::_('COM_SUPPORTGROUPS_ARCHIVED'); ?>
				</span>
			</td>
		<?php elseif ($item->published == -2):?>
			<td class="center"  data-sort-value="4">
				<span class="status-metro status-trashed" title="<?php echo JText::_('COM_SUPPORTGROUPS_TRASHED');  ?>">
					<?php echo JText::_('COM_SUPPORTGROUPS_TRASHED'); ?>
				</span>
			</td>
		<?php endif; ?>
		<td class="nowrap center hidden-phone">
			<?php echo $item->id; ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
	<div class="alert alert-no-items">
		<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
<?php endif; ?>
</div>
