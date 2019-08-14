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
$model_behind_map = $this->getModules('behind-map','div','uk-panel');

?>
<?php echo $this->toolbar->render(); ?>
<?php echo $this->backToRef; ?>
<div data-uk-grid-margin="" class="uk-grid">
	<div class="uk-width-small-1-1 uk-width-medium-2-6">
		<div class="uk-panel">
			<div id="getting">
				<h1 class="uk-h1"><i class="uk-icon-info"></i> <?php echo JText::_('COM_SUPPORTGROUPS_LOADING'); ?>...</h1>
				<br /><br />
				<div class="uk-container-center uk-width-medium-1-4">
					<div class="uk-panel">
						<i class="uk-icon-circle-o-notch uk-icon-hover uk-icon-spin uk-icon-large"></i>
					</div>
				</div>
			</div>
			<div id="map_info" data-uk-observe></div>
		</div>
	</div>
	<div id="map_wrapper" class="switcher uk-width-small-1-1 uk-width-medium-4-6">
		<center>
			<a href="#" class="uk-icon-angle-double-down uk-margin-bottom-remove" data-uk-toggle="{target:'.switcher', animation:'uk-animation-scale-down, uk-animation-scale-up'}"></a>
		</center>
		<div id="map_canvas" class="mapping"></div>
	</div>
	<div class="switcher uk-width-small-1-1 uk-width-medium-4-6 uk-hidden">
		<h2><a data-uk-toggle="{target:'.switcher', animation:'uk-animation-scale-down, uk-animation-scale-up'}" href="#" class="uk-icon-close uk-float-right"></a></h2>
		<div id="global">
			<div class="uk-grid" data-uk-grid-margin>
				<?php if ($model_behind_map): ?>
					<?php echo $model_behind_map; ?>
				<?php else: ?>
					<center><h2 class="uk-panel-title"><?php echo JText::_('COM_SUPPORTGROUPS_A_MODULE_POSITION'); ?></h2></center><br />
					<div class="uk-placeholder uk-width-1-1">behind-map</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<br /><br />
<div id="script"></div>
<script type="text/javascript">
// Add Ajax Token
var token = '<?php echo JSession::getFormToken(); ?>';
// Script for getting the initial details
<?php echo $this->initScript; ?>
// the function to print a div
function printMe(name, printDivId) {
	printWindow = window.open('','printwindow', "location=1,status=1,scrollbars=1");
	if(!printWindow)alert('<?php echo JText::_('COM_SUPPORTGROUPS_PLEASE_ENABLE_POPUPS_TO_PRINT_THESE_DETAILS'); ?>');
	printWindow.document.write('<html><head><title>'+name+'</title><link rel="stylesheet" type="text/css" href="<?php echo JURI::root(); ?>media/com_supportgroups/uikit/css/uikit.css">');
	//Print and cancel button
	printWindow.document.write('<style type="text/css">');
	printWindow.document.write('@media print{.no-print, .no-print *{display: none !important;} } @page { margin: 2cm } .uk-overflow-container {overflow: visible; }');
	
	printWindow.document.write('</style></head><body >');
	printWindow.document.write('<div class="uk-button-group uk-width-1-1 no-print"><button type="button" class="uk-button uk-width-1-2 uk-button-success" onclick="window.print(); window.close();" ><i class="uk-icon-print"></i> <?php echo JText::_('COM_SUPPORTGROUPS_PRINT_CLOSE'); ?></button>');
	printWindow.document.write('<button type="button" class="uk-button uk-width-1-2 uk-button-danger" onclick="window.close();"><i class="uk-icon-close"></i> <?php echo JText::_('COM_SUPPORTGROUPS_CLOSE'); ?></button></div><div>');
	printWindow.document.write(jQuery('#'+printDivId).html());
	printWindow.document.write('</div></body></html>');
	printWindow.document.close();
	printWindow.focus();
}	
</script>
<script async defer src="<?php echo $this->getGoogleAPI(); ?>"></script> 
