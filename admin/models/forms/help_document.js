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
	@subpackage		help_document.js
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// Some Global Values
jform_vvvvvvwvvz_required = false;
jform_vvvvvvxvwa_required = false;
jform_vvvvvvyvwb_required = false;
jform_vvvvvvzvwc_required = false;
jform_vvvvvwbvwd_required = false;

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
		// add required attribute to admin_view field
		if (jform_vvvvvvwvvz_required)
		{
			updateFieldRequired('admin_view',0);
			jQuery('#jform_admin_view').prop('required','required');
			jQuery('#jform_admin_view').attr('aria-required',true);
			jQuery('#jform_admin_view').addClass('required');
			jform_vvvvvvwvvz_required = false;
		}
	}
	else
	{
		jQuery('#jform_admin_view').closest('.control-group').hide();
		// remove required attribute from admin_view field
		if (!jform_vvvvvvwvvz_required)
		{
			updateFieldRequired('admin_view',1);
			jQuery('#jform_admin_view').removeAttr('required');
			jQuery('#jform_admin_view').removeAttr('aria-required');
			jQuery('#jform_admin_view').removeClass('required');
			jform_vvvvvvwvvz_required = true;
		}
	}
}

// the vvvvvvx function
function vvvvvvx(location_vvvvvvx)
{
	// set the function logic
	if (location_vvvvvvx == 2)
	{
		jQuery('#jform_site_view').closest('.control-group').show();
		// add required attribute to site_view field
		if (jform_vvvvvvxvwa_required)
		{
			updateFieldRequired('site_view',0);
			jQuery('#jform_site_view').prop('required','required');
			jQuery('#jform_site_view').attr('aria-required',true);
			jQuery('#jform_site_view').addClass('required');
			jform_vvvvvvxvwa_required = false;
		}
	}
	else
	{
		jQuery('#jform_site_view').closest('.control-group').hide();
		// remove required attribute from site_view field
		if (!jform_vvvvvvxvwa_required)
		{
			updateFieldRequired('site_view',1);
			jQuery('#jform_site_view').removeAttr('required');
			jQuery('#jform_site_view').removeAttr('aria-required');
			jQuery('#jform_site_view').removeClass('required');
			jform_vvvvvvxvwa_required = true;
		}
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
		// add required attribute to url field
		if (jform_vvvvvvyvwb_required)
		{
			updateFieldRequired('url',0);
			jQuery('#jform_url').prop('required','required');
			jQuery('#jform_url').attr('aria-required',true);
			jQuery('#jform_url').addClass('required');
			jform_vvvvvvyvwb_required = false;
		}
	}
	else
	{
		jQuery('#jform_url').closest('.control-group').hide();
		// remove required attribute from url field
		if (!jform_vvvvvvyvwb_required)
		{
			updateFieldRequired('url',1);
			jQuery('#jform_url').removeAttr('required');
			jQuery('#jform_url').removeAttr('aria-required');
			jQuery('#jform_url').removeClass('required');
			jform_vvvvvvyvwb_required = true;
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
		// add required attribute to article field
		if (jform_vvvvvvzvwc_required)
		{
			updateFieldRequired('article',0);
			jQuery('#jform_article').prop('required','required');
			jQuery('#jform_article').attr('aria-required',true);
			jQuery('#jform_article').addClass('required');
			jform_vvvvvvzvwc_required = false;
		}
	}
	else
	{
		jQuery('#jform_article').closest('.control-group').hide();
		// remove required attribute from article field
		if (!jform_vvvvvvzvwc_required)
		{
			updateFieldRequired('article',1);
			jQuery('#jform_article').removeAttr('required');
			jQuery('#jform_article').removeAttr('aria-required');
			jQuery('#jform_article').removeClass('required');
			jform_vvvvvvzvwc_required = true;
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
	}
	else
	{
		jQuery('#jform_content-lbl').closest('.control-group').hide();
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
		// add required attribute to groups field
		if (jform_vvvvvwbvwd_required)
		{
			updateFieldRequired('groups',0);
			jQuery('#jform_groups').prop('required','required');
			jQuery('#jform_groups').attr('aria-required',true);
			jQuery('#jform_groups').addClass('required');
			jform_vvvvvwbvwd_required = false;
		}
	}
	else
	{
		jQuery('#jform_groups').closest('.control-group').hide();
		// remove required attribute from groups field
		if (!jform_vvvvvwbvwd_required)
		{
			updateFieldRequired('groups',1);
			jQuery('#jform_groups').removeAttr('required');
			jQuery('#jform_groups').removeAttr('aria-required');
			jQuery('#jform_groups').removeClass('required');
			jform_vvvvvwbvwd_required = true;
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
