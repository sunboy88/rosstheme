var wishlist = {
		options : null,

		init : function (options) {
			 this.options = options;
	        jQuery('ul.header-tabs').delegate('li:not(.current)', 'click', function() {
	            jQuery(this).addClass('current')
	                .siblings().
	                removeClass('current')
	                .parents('div.wrapper-tabs')
	                .find('div.body-tabs')
	                .eq(jQuery(this).index())
	                .slideDown(0)
	                .siblings('div.body-tabs')
	                .hide();
	        });
		},

		selectAll : function (form,elem) {
			checked = jQuery(elem).prop('checked');
			jQuery(form).find('input[type=checkbox]').each(function(){
				jQuery(this).prop('checked',checked);
			});
		},
		moveItems : function (form, elem) {

			items = [];

			jQuery(form).find('input[type=checkbox]:checked').each(function(){
				if (jQuery(this).attr('name')=='item[]')
					items.push(jQuery(this).val());
			});

			if (items.length==0) {
				alert(this.options.noItemSelectMess);
			} else {
				form.action = this.options.moveUrl;
				form.submit();
			}
		},

		deleteTab : function (form) {
			if (confirm(this.options.deleteTabMess)) {
				form.action = this.options.deleteTabUrl;
				form.submit();
			}
		}
};