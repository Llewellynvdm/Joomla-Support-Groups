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
	@subpackage		help_document.js
	@author			Llewellyn van der Merwe <http://www.vdm.io>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html 
	
	Support Groups 
                                                             
/-----------------------------------------------------------------------------------------------------------------------------*/

// Some Global Values
jform_vvvvvvyvvz_required = false;
jform_vvvvvvzvwa_required = false;
jform_vvvvvwavwb_required = false;
jform_vvvvvwbvwc_required = false;

// Initial Script
jQuery(document).ready(function()
{
	var location_vvvvvvw = jQuery("#jform_location input[type='radio']:checked").val();
	vvvvvvw(location_vvvvvvw);

	var location_vvvvvvx = jQuery("#jform_location input[type='radio']:checked").val();
	vvvvvvx(location_vvvvvvx);

	var type_vvvvvvy = jQuery("#jform_type").val();
	vvvvvvy(type_vvvvvvy);

	var type_vvvvvvz = jQuery("#jform_type").val();
	vvvvvvz(type_vvvvvvz);

	var type_vvvvvwa = jQuery("#jform_type").val();
	vvvvvwa(type_vvvvvwa);

	var target_vvvvvwb = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvvwb(target_vvvvvwb);
});

// the vvvvvvw function
function vvvvvvw(location_vvvvvvw)
{
	// set the function logic
	if (location_vvvvvvw == 1)
	{
		jQuery('#jform_admin_view').closest('.control-group').show();
	}
	else
	{
		jQuery('#jform_admin_view').closest('.control-group').hide();
	}
}

// the vvvvvvx function
function vvvvvvx(location_vvvvvvx)
{
	// set the function logic
	if (location_vvvvvvx == 2)
	{
		jQuery('#jform_site_view').closest('.control-group').show();
	}
	else
	{
		jQuery('#jform_site_view').closest('.control-group').hide();
	}
}

// the vvvvvvy function
function vvvvvvy(type_vvvvvvy)
{
	if (isSet(type_vvvvvvy) && type_vvvvvvy.constructor !== Array)
	{
		var temp_vvvvvvy = type_vvvvvvy;
		var type_vvvvvvy = [];
		type_vvvvvvy.push(temp_vvvvvvy);
	}
	else if (!isSet(type_vvvvvvy))
	{
		var type_vvvvvvy = [];
	}
	var type = type_vvvvvvy.some(type_vvvvvvy_SomeFunc);


	// set this function logic
	if (type)
	{
		jQuery('#jform_url').closest('.control-group').show();
		if (jform_vvvvvvyvvz_required)
		{
			updateFieldRequired('url',0);
			jQuery('#jform_url').prop('required','required');
			jQuery('#jform_url').attr('aria-required',true);
			jQuery('#jform_url').addClass('required');
			jform_vvvvvvyvvz_required = false;
		}

	}
	else
	{
		jQuery('#jform_url').closest('.control-group').hide();
		if (!jform_vvvvvvyvvz_required)
		{
			updateFieldRequired('url',1);
			jQuery('#jform_url').removeAttr('required');
			jQuery('#jform_url').removeAttr('aria-required');
			jQuery('#jform_url').removeClass('required');
			jform_vvvvvvyvvz_required = true;
		}
	}
}

// the vvvvvvy Some function
function type_vvvvvvy_SomeFunc(type_vvvvvvy)
{
	// set the function logic
	if (type_vvvvvvy == 3)
	{
		return true;
	}
	return false;
}

// the vvvvvvz function
function vvvvvvz(type_vvvvvvz)
{
	if (isSet(type_vvvvvvz) && type_vvvvvvz.constructor !== Array)
	{
		var temp_vvvvvvz = type_vvvvvvz;
		var type_vvvvvvz = [];
		type_vvvvvvz.push(temp_vvvvvvz);
	}
	else if (!isSet(type_vvvvvvz))
	{
		var type_vvvvvvz = [];
	}
	var type = type_vvvvvvz.some(type_vvvvvvz_SomeFunc);


	// set this function logic
	if (type)
	{
		jQuery('#jform_article').closest('.control-group').show();
		if (jform_vvvvvvzvwa_required)
		{
			updateFieldRequired('article',0);
			jQuery('#jform_article').prop('required','required');
			jQuery('#jform_article').attr('aria-required',true);
			jQuery('#jform_article').addClass('required');
			jform_vvvvvvzvwa_required = false;
		}

	}
	else
	{
		jQuery('#jform_article').closest('.control-group').hide();
		if (!jform_vvvvvvzvwa_required)
		{
			updateFieldRequired('article',1);
			jQuery('#jform_article').removeAttr('required');
			jQuery('#jform_article').removeAttr('aria-required');
			jQuery('#jform_article').removeClass('required');
			jform_vvvvvvzvwa_required = true;
		}
	}
}

// the vvvvvvz Some function
function type_vvvvvvz_SomeFunc(type_vvvvvvz)
{
	// set the function logic
	if (type_vvvvvvz == 1)
	{
		return true;
	}
	return false;
}

// the vvvvvwa function
function vvvvvwa(type_vvvvvwa)
{
	if (isSet(type_vvvvvwa) && type_vvvvvwa.constructor !== Array)
	{
		var temp_vvvvvwa = type_vvvvvwa;
		var type_vvvvvwa = [];
		type_vvvvvwa.push(temp_vvvvvwa);
	}
	else if (!isSet(type_vvvvvwa))
	{
		var type_vvvvvwa = [];
	}
	var type = type_vvvvvwa.some(type_vvvvvwa_SomeFunc);


	// set this function logic
	if (type)
	{
		jQuery('#jform_content-lbl').closest('.control-group').show();
		if (jform_vvvvvwavwb_required)
		{
			updateFieldRequired('content',0);
			jQuery('#jform_content').prop('required','required');
			jQuery('#jform_content').attr('aria-required',true);
			jQuery('#jform_content').addClass('required');
			jform_vvvvvwavwb_required = false;
		}

	}
	else
	{
		jQuery('#jform_content-lbl').closest('.control-group').hide();
		if (!jform_vvvvvwavwb_required)
		{
			updateFieldRequired('content',1);
			jQuery('#jform_content').removeAttr('required');
			jQuery('#jform_content').removeAttr('aria-required');
			jQuery('#jform_content').removeClass('required');
			jform_vvvvvwavwb_required = true;
		}
	}
}

// the vvvvvwa Some function
function type_vvvvvwa_SomeFunc(type_vvvvvwa)
{
	// set the function logic
	if (type_vvvvvwa == 2)
	{
		return true;
	}
	return false;
}

// the vvvvvwb function
function vvvvvwb(target_vvvvvwb)
{
	// set the function logic
	if (target_vvvvvwb == 1)
	{
		jQuery('#jform_groups').closest('.control-group').show();
		if (jform_vvvvvwbvwc_required)
		{
			updateFieldRequired('groups',0);
			jQuery('#jform_groups').prop('required','required');
			jQuery('#jform_groups').attr('aria-required',true);
			jQuery('#jform_groups').addClass('required');
			jform_vvvvvwbvwc_required = false;
		}

	}
	else
	{
		jQuery('#jform_groups').closest('.control-group').hide();
		if (!jform_vvvvvwbvwc_required)
		{
			updateFieldRequired('groups',1);
			jQuery('#jform_groups').removeAttr('required');
			jQuery('#jform_groups').removeAttr('aria-required');
			jQuery('#jform_groups').removeClass('required');
			jform_vvvvvwbvwc_required = true;
		}
	}
}

// update required fields
function updateFieldRequired(name,status)
{
	var not_required = jQuery('#jform_not_required').val();

	if(status == 1)
	{
		if (isSet(not_required) && not_required != 0)
		{
			not_required = not_required+','+name;
		}
		else
		{
			not_required = ','+name;
		}
	}
	else
	{
		if (isSet(not_required) && not_required != 0)
		{
			not_required = not_required.replace(','+name,'');
		}
	}

	jQuery('#jform_not_required').val(not_required);
}

// the isSet function
function isSet(val)
{
	if ((val != undefined) && (val != null) && 0 !== val.length){
		return true;
	}
	return false;
} 
