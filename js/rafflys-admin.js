jQuery(document).ready(function($) {
    $('#js-rafflys-logout').on('click', Rafflys_Admin.logout);
    $('.rafflys-js-connect-with-api-key').on('click', Rafflys_Admin.show_api_key);
	$('input[type=radio][name=rafflys_display]').change(Rafflys_Admin.settings_display_change);
	$('.rafflys-js-domain').each(function (index, el) {
        $(el).text(window.location.hostname);
    });
	$('.js-disable-prom').on('click', Rafflys_Admin.disable_promotion);
	$('.js-enable-prom').on('click', Rafflys_Admin.enable_promotion);
});

Rafflys_Admin = {};

Rafflys_Admin.logout = function () {
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
          action: 'rafflys_logout',
        },
        success: function(response) {
            location.reload();
        },
        error: function(xhr, status, error) {
        }
    });
};

Rafflys_Admin.settings_display_change = function () {
	jQuery('#rafflys_display_page').hide();
	jQuery('#rafflys_display_url').hide();
	jQuery('#rafflys_display_url_help').hide();
	if (this.value === 'url') {
		jQuery('#rafflys_display_url').show();
		jQuery('#rafflys_display_url_help').show();
	}
	if (this.value === 'page') {
		jQuery('#rafflys_display_page').show();
	}
};

Rafflys_Admin.show_api_key = function () {
	jQuery('.rafflys-js-connect-with-api-key').hide();
	jQuery('.enter-api-key').show();
};

Rafflys_Admin.disable_promotion = function () {
    if (confirm('Disable this promotion?')){
        $el = jQuery(this);
        var id = $el.data('id');
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
              action: 'rafflys_promotion_status',
              id: id,
              is_active: 0,
              nonce: (typeof window.rafflys_ajax_nonce) ? window.rafflys_ajax_nonce : '',
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr, status, error) {
            }
        });
    }
};

Rafflys_Admin.enable_promotion = function () {
    $el = jQuery(this);
    var id = $el.data('id');
    jQuery('#promotions-list').hide();
    jQuery('#promotion-config').show();
    jQuery('#promotion-config-id').val(id);
};

Rafflys_Admin.cancel_edit_promotion = function () {
    jQuery('#promotions-list').show();
    jQuery('#promotion-config').hide();
};
