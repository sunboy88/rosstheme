
	var wishlistPopup = {

		selector : '#popup-wishlist',
		selectorTab : 'wishlist-id-',
		selectorButtonAdd : '#popup-wishlist #popup-add-to-wish',
		selectorButtonClose : '#popup-wishlist #popup-cancel',
		classRadio : '.wishlist-tab-radio',
		selectorLink : '.link-wishlist',
		flagListenLink : false,
		form : null,
		ajax: false,

		init : function(config) {

			jQuery(this.selectorButtonAdd).on('click', function() {
				wishlistPopup.addToWishlist();
			});

			jQuery(this.selectorButtonClose).on('click', function() {
				wishlistPopup.hide();
			});

			jQuery(this.classRadio).on('click', function() {
				wishlistPopup.showNewTabField(jQuery(this).prop('checked'), this);
			});

		},
		show : function(form) {
			this.form = form;
			jQuery(this.selector).show();
		},
		hide : function() {
			jQuery(this.selector).hide();
		},
		addToWishlist : function() {

			tab_id = jQuery(this.classRadio+':checked').val();

			param = '?tab_id='+tab_id;

			if (tab_id=='-1') {
				param += '&wishlist_name='+jQuery(this.selector+' #wishlist-name').val();
			}

			if (!this.flagListenLink) {
				url= this.form.action+param;
				this.form.action = url;
				this.form.submit();

			} else {
				url = jQuery(this.form).attr('href')+param;
				window.location = url;
			}

		},
		showNewTabField : function(checked, elem) {
			if (checked && jQuery(elem).attr('id')==(this.selectorTab+'-1')) {
				jQuery(this.selector+' #wishlist-name').show();
			} else {
				jQuery(this.selector+' #wishlist-name').hide();
			}
		},
		listenLink : function() {
			this.flagListenLink = true;
			jQuery(this.selectorLink).on('click',function(event){
				event.preventDefault();
				wishlistPopup.show(this);
			});
		}

	};
