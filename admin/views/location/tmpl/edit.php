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
	@subpackage		edit.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html 
	
	Support Groups 
                                                             
/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
$componentParams = JComponentHelper::getParams('com_supportgroups');
?>

<form action="<?php echo JRoute::_('index.php?option=com_supportgroups&layout=edit&id='.(int) $this->item->id.$this->referral); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

	<?php echo JLayoutHelper::render('location.settings_above', $this); ?><div class="form-horizontal">

	<?php echo JHtml::_('bootstrap.startTabSet', 'locationTab', array('active' => 'settings')); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'locationTab', 'settings', JText::_('COM_SUPPORTGROUPS_LOCATION_SETTINGS', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('location.settings_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php if ($this->canDo->get('support_group.access')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'locationTab', 'support_groups', JText::_('COM_SUPPORTGROUPS_LOCATION_SUPPORT_GROUPS', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('location.support_groups_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php if ($this->canDo->get('location.delete') || $this->canDo->get('location.edit.created_by') || $this->canDo->get('location.edit.state') || $this->canDo->get('location.edit.created')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'locationTab', 'publishing', JText::_('COM_SUPPORTGROUPS_LOCATION_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('location.publishing', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('location.metadata', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php if ($this->canDo->get('core.admin')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'locationTab', 'permissions', JText::_('COM_SUPPORTGROUPS_LOCATION_PERMISSION', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<fieldset class="adminform">
					<div class="adminformlist">
					<?php foreach ($this->form->getFieldset('accesscontrol') as $field): ?>
						<div>
							<?php echo $field->label; echo $field->input;?>
						</div>
						<div class="clearfix"></div>
					<?php endforeach; ?>
					</div>
				</fieldset>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	<div>
		<input type="hidden" name="task" value="location.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</div>
</form>

<script type="text/javascript">

// #jform_region listeners for region_vvvvvvv function
jQuery('#jform_region').on('keyup',function()
{
	var region_vvvvvvv = jQuery("#jform_region").val();
	vvvvvvv(region_vvvvvvv);

});
jQuery('#adminForm').on('change', '#jform_region',function (e)
{
	e.preventDefault();
	var region_vvvvvvv = jQuery("#jform_region").val();
	vvvvvvv(region_vvvvvvv);

});

</script>
