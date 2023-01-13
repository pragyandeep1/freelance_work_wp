(function( $ ) {
	"use strict";
	//copy the code
    jQuery('.gp-copy-code').on('click', function(){
        var copy_text   = jQuery(this).data('code');
        jQuery('#gp-shortcode-coppy').select();
    });

    jQuery('#gb-add-reson').on('click', function(){
        var newRowContent = '<tr><td></td><td><input name="wpguppy_settings[reporting_reasons][]" type="text"  value=""/><a href="javascript:;" class="gb-remove-reason"><span class="dashicons dashicons-trash"></span></a></td></tr>';
        jQuery("#gb-report-user tbody").append(newRowContent);
        wpguppy_remove_reason();
    });
	//change settings tab
    jQuery('.gp-tabs-settings').on('click', function(){
        let _this   = jQuery(this);
        let tab_id  = _this.data('tab_id');
        let url     = window.location.href; 
        let new_url = wpguppy_UpdateParam(url,'tab',tab_id);
        jQuery('.gp-tabs-settings').removeClass('nav-tab-active');
        _this.addClass('nav-tab-active');
        jQuery('.gb-tab-content').addClass('hide-if-js');
        jQuery('#tb-content-'+tab_id).removeClass('hide-if-js');
        window.history.replaceState({},document.title, new_url); 
    });

    jQuery('.at-chatroletabs_list a').on('click', function(e){
        e.preventDefault();
        let _this   = jQuery(this);
        let tab_id  = _this.data('tab_id');
        _this.parents('.at-chatroletabs_list').find('a').removeClass('nav-tab-active')
        _this.addClass('nav-tab-active');
        _this.parents('.at-chatroletabs').find('.gp-role-content').addClass('hide-if-js');
        _this.parents('.at-chatroletabs').find('#'+tab_id).removeClass('hide-if-js');
    });
	
	//database reset
    jQuery('#gb-rest-db').on('click', function(){
        if( confirm( scripts_constants.rest_db_message ) ) {
            var dataString = 'security='+scripts_constants.ajax_nonce+'&action=wpguppy_rest_database';
            jQuery.ajax({
                type: "POST",
                url: scripts_constants.ajaxurl,
                dataType:"json",
                data: dataString,
                success: function(response) {
                    if (response.type) {
                        alert(response.message);
                    }
                }
            });
        } 
    });
    // Verify item purchase
    jQuery(document).on('click', '#epv_verify_btn', function(e){
        e.preventDefault();
        let _this	= jQuery(this);
        let epv_purchase_code = jQuery('#epv_purchase_code').val();

        if(epv_purchase_code == '' || epv_purchase_code == null){
            let epv_purchase_code_title = jQuery('#epv_purchase_code').attr('title');
            StickyAlert('', epv_purchase_code_title, {classList: 'important', autoclose: 3000});
            return false;
        } else {
            _this.attr('disabled', 'disabled');
        }
        jQuery.ajax({
            type: "POST",
            url: scripts_constants.ajaxurl,
            data: {
                purchase_code:	epv_purchase_code,
                security:	scripts_constants.ajax_nonce,
                action:	'epv_verifypurchase',
            },
            dataType: "json",
            success: function (response) {
                if (response.type === 'success') {					
                    StickyAlert(response.title, response.message, {classList: 'success', autoclose: 3000});
                    setTimeout(function(){ 
                        window.location.reload();
                        }, 2000);
                } else {
                    _this.removeAttr("disabled");
                    StickyAlert(response.title, response.message, {classList: 'important', autoclose: 3000});
                }
            },
            error: function(requestObject, error, errorThrown) {
                _this.removeAttr('disabled');
                StickyAlert('', error, {classList: 'important', autoclose: 3000});
            }
        });
    });

    //Remove license
    jQuery(document).on('click', '#epv_remove_license_btn', function(e){
        e.preventDefault();
        let _this	= jQuery(this);
        let epv_purchase_code = jQuery('#epv_purchase_code').val();

        if(epv_purchase_code == '' || epv_purchase_code == null){
            let epv_purchase_code_title = jQuery('#epv_purchase_code').attr('title');
            StickyAlert('', epv_purchase_code_title, {classList: 'important', autoclose: 3000});
            return false;
        } else {
            _this.attr('disabled', 'disabled');
        }

        jQuery.ajax({
            type: "POST",
            url: scripts_constants.ajaxurl,
            data: {
                purchase_code:	epv_purchase_code,
                security:	scripts_constants.ajax_nonce,
                action:	'epv_remove_license',
            },
            dataType: "json",
            success: function (response) {
                if (response.type === 'success') {					
                    StickyAlert(response.title, response.message, {classList: 'success', autoclose: 3000});
                    setTimeout(function(){ 
                        window.location.reload();
                        }, 2000);
                } else {
                    _this.removeAttr("disabled");
                    StickyAlert(response.title, response.message, {classList: 'important', autoclose: 3000});
                }
            },
            error: function(requestObject, error, errorThrown) {
                _this.removeAttr('disabled');
                StickyAlert('', error, {classList: 'important', autoclose: 3000});
            }
        });
    });

    //WP is guppy admin change status
    jQuery('.wpguppy-is-admin').on('click','input[name=is_guppy_admin]', function(){
        let _this       = jQuery(this);
        let _value      = 0;
        if (_this.is(":checked")){
            _value      = 1;
        }

        let _id         = _this.parents('.wpguppy-is-admin').data('id');
        var dataString = 'user_id='+_id+'&status='+_value+'&security='+scripts_constants.ajax_nonce+'&action=wpguppy_update_guppy_admin_status';
        jQuery.ajax({
            type: "POST",
            url: scripts_constants.ajaxurl,
            dataType:"json",
            data: dataString,
            success: function(response) {
                if (response.type === 'success') {
                    if(_value == 1){
                        _this.parents('.wpguppy-is-admin').addClass('guppy-dashicons-yes').removeClass('guppy-dashicons-no-alt');
                        _this.parents('.wpguppy-is-admin').find('i').addClass('dashicons-no-alt').removeClass('dashicons-yes');
                    }else{
                        _this.parents('.wpguppy-is-admin').addClass('guppy-dashicons-no-alt').removeClass('guppy-dashicons-yes');
                        _this.parents('.wpguppy-is-admin').find('i').addClass('dashicons-no-alt').removeClass('dashicons-yes');
                    }
                }
            }
        });
    });

	
	//color picker
    jQuery('.gp-color-field').wpColorPicker();
	
	//pusher settings
    jQuery('.rt-chat-settings').on('change',function() {
        let rt_chat_val  = jQuery(this).val();
        
        if(rt_chat_val == 'pusher'){
            jQuery('.gp-socket-settings').prop('checked',false);
            jQuery('.rt-pusher').removeClass('hide-if-js');
            jQuery('.rt-socket, .gp-socket-options').addClass('hide-if-js');
        } else if(rt_chat_val == 'socket') {
            jQuery('.gp-pusher-settings').prop('checked',false);
            jQuery('.rt-pusher, .gp-pusher-options').addClass('hide-if-js');
            jQuery('.rt-socket').removeClass('hide-if-js');
        }
    });
    jQuery('.gp-pusher-settings').on('change',function() {
        let pusher_val  = jQuery(this).val();
        jQuery('.gp-socket-options, .rt-socket').addClass('hide-if-js');
        if(pusher_val == 'enable'){
            jQuery('.gp-pusher-options').removeClass('hide-if-js');
        } else {
            jQuery('.gp-pusher-options').addClass('hide-if-js');
        }
    });

    jQuery('.gp-socket-settings').on('change',function() {
        jQuery('.gp-pusher-options, .rt-pusher').addClass('hide-if-js');
        let socket_val  = jQuery(this).val();
        if(socket_val == 'enable'){
            jQuery('.gp-socket-options').removeClass('hide-if-js');
        } else {
            jQuery('.gp-socket-options').addClass('hide-if-js');
        }
    });
    jQuery('.guppy-search-filter').on('keyup',function() {
        let _this   = jQuery(this);
        let searchVal = _this.val().toUpperCase();
        let data = _this.parent('.at-roletabs_search').next('.at-roletabs_list').find('li .at-checkbox label');
        guppySearchFilter(searchVal, data)
    });
    
	wpguppy_remove_reason();
})( jQuery );

function guppySearchFilter(searchVal, data) {
    let i;
    for (i = 0; i < data.length; i++) {
      txtValue = data[i].textContent || data[i].innerText;
      if (txtValue.toUpperCase().indexOf(searchVal) > -1) {
        data[i].style.display = "";
      } else {
        data[i].style.display = "none";
      }
    }
}
function wpguppy_remove_reason() {
    jQuery('.gb-remove-reason').on('click', function(){
        let _this   = jQuery(this);
        _this.closest("tr").remove();
    });
}

function wpguppy_UpdateParam(currentUrl,key,val) {
    var url = new URL(currentUrl);
    url.searchParams.set(key, val);
    return url.href; 
}

// Alert the notification
function StickyAlert($title = '', $message = '', data) {
    var $icon = 'ti-face-sad';
    var $class = 'dark';

    if (data.classList === 'success') {
        $icon = 'icon-check';
        $class = 'green';
    } else if (data.classList === 'danger') {
        $icon = 'icon-x';
        $class = 'red';
    }

    jQuery.confirm({
        icon: $icon,
        closeIcon: true,
        theme: 'modern',
        animation: 'scale',
        type: $class, //red, green, dark, orange
        title: $title,
        content: $message,
        autoClose: 'close|' + data.autoclose,
        buttons: {
            close: {btnClass: 'tb-sticky-alert'}
        }
    });
}