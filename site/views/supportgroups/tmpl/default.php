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
	@subpackage		default.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// get modules
$model_behind_map = $this->getModules('behind-table','div','uk-panel');

?>
<?php echo $this->toolbar->render(); ?>
<?php if (isset($this->items) && SupportgroupsHelper::checkArray($this->items)): ?>
	<div id="totals"></div>
	<table id="table" class="footable uk-table" data-show-toggle="true" data-toggle-column="first" data-paging="true" data-filtering="true" data-paging-size="100" data-sorting="true"></table>
	<!-- This is the modal -->
	<div id="item-info" class="uk-modal">
		<div class="uk-modal-dialog">
			<a class="uk-modal-close uk-close"></a>
			<button type="button" onclick="printMe('<?php echo JFactory::getConfig()->get( 'sitename' ); ?>', 'info')" class="uk-button uk-button-primary uk-button-mini uk-align-center"><i class="uk-icon-print"></i> <?php echo JText::_('COM_SUPPORTGROUPS_PRINT_PREVIEW'); ?></button>
			<div id="info" data-uk-observe></div>
			<div id="modal-spin" >
				<i class="uk-icon-circle-o-notch uk-icon-spin uk-icon-large"> </i>
				<?php echo JText::_('COM_SUPPORTGROUPS_LOADING_DETAILS_ONE_MOMENT_PLEASE'); ?>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		// page name
		var pageName = 'groups';
		// token 
		var token = '<?php echo JSession::getFormToken(); ?>';
		// get the totals
		getTotals();
		// set the key
		var key = '<?php echo $this->groupBundlesKey; ?>';
		// the get url
		var columnsUrl = "<?php echo JURI::root(); ?>index.php?option=com_supportgroups&task=ajax.getColumns&format=json&raw=true&page=groups&token="+token;
		var rowsUrl = "<?php echo JURI::root(); ?>index.php?option=com_supportgroups&task=ajax.getRows&format=json&raw=true&page=groups&token="+token+"&key="+key;
		jQuery(function($){
			// set the Facility filter
			FooTable.facilityFiltering = FooTable.Filtering.extend({
				construct: function(instance){
					this._super(instance);
					this.facilities = ['<?php echo implode("','",$this->facilitiesArray); ?>'];
					this.def = '<?php echo JText::_('COM_SUPPORTGROUPS_ALL_FACILITIES'); ?>';
					this.$facility = null;
				},
				$create: function(){
					this._super(); 
					var self = this,
						$form_grp = $('<div/>', {'class': 'form-group'})
							.append($('<label/>', {'class': 'sr-only', text: 'facilities'}))
							.prependTo(self.$form);

					self.$facility = $('<select/>', { 'class': 'form-control' })
						.on('change', function(){
							self.filter();
						})
						.append($('<option/>', {text: self.def}))
						.appendTo($form_grp);

					$.each(self.facilities, function(i, facility){
						self.$facility.append($('<option/>').text(facility));
					});
				},
				filter: function(query, columns){
					var val = this.$facility.val();
					if (val != this.def) this.addFilter('facility_name', val, ['facility_name']);
					else this.removeFilter('facility_name');
					return this._super(query, columns);
				},
				clear: function(){
					this.$facility.val(this.def);
					this.removeFilter('facility_name');
					return this._super();
				}
			});
			FooTable.components.core.register('filtering', FooTable.facilityFiltering);

			$('.footable').footable({
				"columns": $.get(columnsUrl),
				"rows":  $.get(rowsUrl),
			});
		});		
		function printMe(name, printDivId) {
			printWindow = window.open('','printwindow', "location=1,status=1,scrollbars=1");
			if(!printWindow)alert('<?php echo JText::_('COM_SUPPORTGROUPS_PLEASE_ENABLE_POPUPS_TO_PRINT_THESE_DETAILS'); ?>');
			printWindow.document.write('<html><head><title>'+name+'</title><link rel="stylesheet" type="text/css" href="<?php echo JURI::root(); ?>media/com_supportgroups/uikit/css/uikit.css">');
			//Print and cancel button
			printWindow.document.write('<style type="text/css">');
			printWindow.document.write('@media print{.no-print, .no-print *{display: none !important;} } @page { margin: 2cm }');
			
			printWindow.document.write('</style></head><body >');
			printWindow.document.write('<div class="uk-button-group uk-width-1-1 no-print"><button type="button" class="uk-button uk-width-1-2 uk-button-success" onclick="window.print(); window.close();" ><i class="uk-icon-print"></i> <?php echo JText::_('COM_SUPPORTGROUPS_PRINT_CLOSE'); ?></button>');
			printWindow.document.write('<button type="button" class="uk-button uk-width-1-2 uk-button-danger" onclick="window.close();"><i class="uk-icon-close"></i> <?php echo JText::_('COM_SUPPORTGROUPS_CLOSE'); ?></button></div><div>');
			printWindow.document.write(jQuery('#'+printDivId).html());
			printWindow.document.write('</div></body></html>');
			printWindow.document.close();
			printWindow.focus();
		}
	</script>
<?php else: ?>
	<div class="uk-alert"><?php echo JText::_('COM_SUPPORTGROUPS_NO_SUPPORT_GROUPS_FOUND'); ?></div>
<?php endif; ?>	 
