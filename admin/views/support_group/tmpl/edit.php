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
$componentParams = $this->params; // will be removed just use $this->params instead
?>
<script type="text/javascript">
	// waiting spinner
	var outerDiv = jQuery('body');
	jQuery('<div id="loading"></div>')
		.css("background", "rgba(255, 255, 255, .8) url('components/com_supportgroups/assets/images/import.gif') 50% 15% no-repeat")
		.css("top", outerDiv.position().top - jQuery(window).scrollTop())
		.css("left", outerDiv.position().left - jQuery(window).scrollLeft())
		.css("width", outerDiv.width())
		.css("height", outerDiv.height())
		.css("position", "fixed")
		.css("opacity", "0.80")
		.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
		.css("filter", "alpha(opacity = 80)")
		.css("display", "none")
		.appendTo(outerDiv);
	jQuery('#loading').show();
	// when page is ready remove and show
	jQuery(window).load(function() {
		jQuery('#supportgroups_loader').fadeIn('fast');
		jQuery('#loading').hide();
	});
</script>
<div id="supportgroups_loader" style="display: none;">
<form action="<?php echo JRoute::_('index.php?option=com_supportgroups&layout=edit&id='. (int) $this->item->id . $this->referral); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

	<?php echo JLayoutHelper::render('support_group.details_above', $this); ?>
<div class="form-horizontal">

	<?php echo JHtml::_('bootstrap.startTabSet', 'support_groupTab', array('active' => 'details')); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'support_groupTab', 'details', JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_DETAILS', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('support_group.details_left', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('support_group.details_right', $this); ?>
			</div>
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('support_group.details_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'support_groupTab', 'location', JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_LOCATION', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('support_group.location_left', $this); ?>
			</div>
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('support_group.location_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php if ($this->canDo->get('payment.access')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'support_groupTab', 'payments', JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_PAYMENTS', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('support_group.payments_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php $this->ignore_fieldsets = array('details','metadata','vdmmetadata','accesscontrol'); ?>
	<?php $this->tab_name = 'support_groupTab'; ?>
	<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

	<?php if ($this->canDo->get('support_group.delete') || $this->canDo->get('support_group.edit.created_by') || $this->canDo->get('support_group.edit.state') || $this->canDo->get('support_group.edit.created')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'support_groupTab', 'publishing', JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('support_group.publishing', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('support_group.metadata', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php if ($this->canDo->get('core.admin')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'support_groupTab', 'permissions', JText::_('COM_SUPPORTGROUPS_SUPPORT_GROUP_PERMISSION', true)); ?>
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
		<input type="hidden" name="task" value="support_group.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</div>

<div class="clearfix"></div>
<?php echo JLayoutHelper::render('support_group.details_under', $this); ?>
</form>
</div>

<script type="text/javascript">



    function initMap() {
        var originalMapCenter = new google.maps.LatLng(<?php echo (strpos($this->item->marker,')')) ? str_replace(array( '(', ')' ), '', $this->item->marker) :"-22.553775132834964, 17.07106809008792"; ?>);
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 7,
          center: originalMapCenter
        });

	marker = new google.maps.Marker({
		     position: originalMapCenter,
		     draggable: true,
		     map: map,
		     title:"<?php echo JText::_('COM_SUPPORTGROUPS_DRAG_ME_TO_THE_GROUP_LOCATION'); ?>"                   
	}); //end marker

	// set the marker to map
	marker.setMap(map);
	//Add listener
	google.maps.event.addListener(marker, 'dragend', function (event) {
                    jQuery("#jform_marker").val(this.position);
	});

      }
      jQuery(function() {
            jQuery('.nav-tabs li').bind('click', function (e) {
                  initMap();
            });
      });
</script>
<?php $api_key = $componentParams->get('api_key', null); if ($api_key): ?>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $api_key; ?>">
<?php else: ?>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?sensor=false">
<?php endif; ?>
</script>
<script type="text/javascript">
<?php if (!$api_key): ?>
<?php 
	// setup the return url
	$uri = (string) JUri::getInstance();
	$return = urlencode(base64_encode($uri));
	$optionsURL = 'index.php?option=com_config&view=component&component=com_supportgroups&return='.$return;
?>
	jQuery(function() {
            jQuery('#map_note').html("<div class='alert alert-success'><?php echo JText::_('COM_SUPPORTGROUPS_PLEASE_ADD_YOUR_API_KEY_TO_THE_COMPONENT_GLOBAL'); ?> <a class='btn btn-small options-link' href='<?php echo $optionsURL;?>'><span class='icon-options'></span><?php echo JText::_('COM_SUPPORTGROUPS_OPTIONS'); ?></a> <?php echo JText::_('COM_SUPPORTGROUPS_UNDER_GOOGLE_API_TAP'); ?></div>");
      });
<?php endif; ?>
</script>
