(function( $ ) {
	"use strict";
	jQuery(document).on("click", ".at-messageoption_btn", function (e) {
		jQuery(this).parent('.at-messageoption').toggleClass("at-messageoption_open");
	});

	jQuery(document).on('click', '.at-replay_upload > a', function (event) {
		jQuery(".at-replay_upload").toggleClass("at-uploadoption_open");
	});
	
	// OUTSIDE CLICK CLOSE
	jQuery(document).on('click','body',function(e) {
		jQuery('.at-replay_upload').not(jQuery('.at-replay_upload').has(e.target)).removeClass("at-uploadoption_open");
		jQuery('.at-messageoption').not(jQuery('.at-messageoption').has(e.target)).removeClass("at-messageoption_open");
		jQuery('.at-sidebarhead_menu').not(jQuery('.at-sidebarhead_menu').has(e.target)).removeClass("at-uploadoption_open");
	});
	jQuery(document).on('click', '.at-sidebarhead_menu > a', function (event) {
		jQuery(".at-sidebarhead_menu").toggleClass("at-uploadoption_open");
	});
	jQuery(document).on('click', '.at-uploadoption a', function (event) {
		jQuery(".at-sidebarhead_menu").removeClass("at-uploadoption_open");
	});
	// PROFILE SIDEBAR OPEN
	jQuery(document).on('click','.at-sidebarhead_profile > a',function(e){
		e.stopPropagation();
		jQuery(this).next().toggleClass('at-chat_profilesettingopen')
	});
	
	// OPEN SIDEBAR SETTING
	jQuery(document).on('click','.at-userinfo_settings > a,.at-moreuser',function(e){
		jQuery(".at-chat_messages").toggleClass("at-chat_messagesslide");
		jQuery(".at-chat_sidebarsetting").toggleClass("at-chat_sidebarsettingopen");
	});
	jQuery(document).on('click','.at-userinfo .at-chat_sidebarsettingtitle > a',function(e){
		jQuery(this).parents('.at-chat_messages').removeClass("at-chat_messagesslide");
		jQuery(this).closest('.at-chat_sidebarsetting').removeClass("at-chat_sidebarsettingopen");
	});

	// dropdown
	jQuery(document).on('click','.at-dropdownholder .at-form-control',function(e){
		jQuery(this).next().slideToggle()
	})
	
	jQuery(document).on('click','.at-dropdown li',function(e){
		jQuery(this).closest('.at-dropdownholder').children().children('span').removeClass()
		jQuery(this).closest('ul').slideUp()
	})
	
	// OPEN MODAL
	jQuery(document).on('click','[guppy-data-target]',function(e){
		e.stopPropagation();
		var dt = jQuery(this).attr("guppy-data-target")
		jQuery(dt).addClass('at-modalopen')
		setTimeout(function(){
		  jQuery(dt).addClass('at-fadin')
		  jQuery('.at-overlaymodal').addClass('at-fadin')
		}, 1);
		jQuery('body').css('overflow', 'hidden')
		jQuery('body').append("<div class='at-overlaymodal'></div>")
	})
	 
	// REMOVE MODAL
	jQuery(document).on('click','.at-guppy-removemodal',function(e){
		jQuery('.at-modal').removeClass('at-fadin')
		jQuery('.at-overlaymodal').removeClass('at-fadin')
		setTimeout(function(){
		  jQuery('.at-modal').removeClass('at-modalopen')
		  jQuery('.at-overlaymodal').remove()
		}, 300);
		jQuery('body').css({'overflow' : ''})
	})
})( jQuery );
