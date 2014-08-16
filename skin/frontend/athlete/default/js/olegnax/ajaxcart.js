if (typeof Olegnax == "undefined") var Olegnax = {};
if (typeof Olegnax.Ajaxcart == "undefined") {
	Olegnax.Ajaxcart = {
		translation : {},
		options : {},
		helpers : {}
	};
}

Olegnax.Ajaxcart.helpers = {
	showMessage: function(message)
	{
		jQuery.fancybox({
				'content'           : '<div class="ajax-message"><p>'+message+'</p></div>',
				'autoDimensions'	: true,
				'padding'		    : 30,
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'autoScale'         : true,
				'centerOnScroll'	: true
			}
		);
	},
	ajax: function(url, successFunc)
	{
		//matches = url.match(/product\/(\d+)/i);
		//id = matches[1];
		url += 'isAjax/1/';
		if ('https:' == document.location.protocol) {
			url = url.replace('http:', 'https:');
		}
		jQuery.fancybox.showActivity();
		jQuery.ajax({
			url:url,
			dataType:'jsonp',
			success:function(data) {
				Olegnax.Ajaxcart.helpers.showMessage(data.message);
				successFunc(data);
			}
		});
	},
	compareSuccessFunc: function(data)
	{
		if (data.status != 'ERROR' ) {
			jQuery('.block-compare').replaceWith(data.sidebar);
		}
	},
	cartSuccessFunc: function(data)
	{
		if (data.status != 'ERROR') {
			jQuery('.header-container .links').replaceWith(data.toplink);
			jQuery('.header-cart').replaceWith(data.cart_top);
		}
	},
	wishlistSuccessFunc: function(data)
	{
		if (data.status != 'ERROR') {
			jQuery('.header-container .links').replaceWith(data.toplink);
			jQuery('.block-wishlist').replaceWith(data.sidebar);
		}
	}
}

jQuery(function($) {

	if ( Olegnax.Ajaxcart.options.compare ) {
		$('.add-to-links .link-compare').on('click', function () {
			Olegnax.Ajaxcart.helpers.ajax(
				$(this).attr('href').replace("catalog/product_compare/add", "oxajax/compare/compare"),
				Olegnax.Ajaxcart.helpers.compareSuccessFunc
			);
			return false;
		});
	}

	if ( Olegnax.Ajaxcart.options.wishlist ) {
		$('.add-to-links .link-wishlist').attr('onclick', '').on('click', function () {
			Olegnax.Ajaxcart.helpers.ajax(
				$(this).attr('href').replace("wishlist/index", "oxajax/wishlist"),
				Olegnax.Ajaxcart.helpers.wishlistSuccessFunc
			);
			return false;
		});
	}

	if ( Olegnax.Ajaxcart.options.quick_view ) {
		$('.products-grid li.item .actions').append('<div class="clear"></div><button type="button" class="button quick-view" ><span><span>'+Olegnax.Ajaxcart.translation.quick_view+'</span></span></button>');
		$('.products-grid li.item .actions').data('quickViewHeight', $('.products-grid li.item .actions').height() + $('.products-grid li.item .actions .quick-view span').height() );
		$('.products-list li.item ').prepend('<button type="button" class="button quick-view" ><span><span>'+Olegnax.Ajaxcart.translation.quick_view+'</span></span></button>');
		$('.col-main  li.item, .megamenu-dropdown li.item').addClass('quick-view-container');
	}
	$('.quick-view').on('click', function() {
		var $this = $(this);
		var quick_view_href = $this.closest('li.item').find('.product-name a').attr('href');
		if ( $(window).width() < 1024 )  {
			window.location = quick_view_href;
			return false;
		}

		if ( quick_view_href.indexOf('catalog/product/view') != -1 ) {
			//non seo url
			matches = url.match(/id\/(\d+)/i);
			id = matches[1];
			quick_view_href = Olegnax.Ajaxcart.baseUrl + 'oxajax/cart/options/id/' + id;
		} else {
			//seo url
			var path = quick_view_href.replace(Olegnax.Ajaxcart.baseUrl, '');
			quick_view_href = Olegnax.Ajaxcart.baseUrl + 'oxajax/cart/options/path/' + Base64.encode(path);
		}
		if ('https:' == document.location.protocol) {
			quick_view_href = quick_view_href.replace('http:', 'https:');
		}
		$.fancybox({
			hideOnContentClick:true,
			autoDimensions:true,
			width: 786,
			type:'iframe',
			href: quick_view_href,
			showTitle:true,
			scrolling:'no',
			onComplete:function () {
				$('#fancybox-frame').load(function () { // wait for frame to load and then gets it's height
					$('#fancybox-content').height($(this).contents().find('body').height() + 30);
					$.fancybox.resize();
				});
			}
		});
		return false;
	});

	$('.btn-cart').each(function () {
		$(this).attr('data-click', $(this).attr('onclick'));
		$(this).attr('onclick', '');
	});
	$('.btn-cart').on('click', function () {
		var onclick = $(this).attr('data-click');
		if ( onclick == '' ) {
			return true;
		}
		if ( $(this).closest("form").length || onclick.indexOf("submit") != -1 ) {
			$(this).attr('onclick', $(this).attr('data-click'));
			$(this).attr('data-click', '');
			$(this).trigger('click');
			return false;
		}
		var url = onclick.replace("setLocation('",'').replace("')",'');
		if ( url.indexOf("checkout/cart") != -1) {
			if(jQuery(this).attr('data-qty') != ''){
				url = url+"qty/"+jQuery('#qty_'+jQuery(this).attr('data-qty')).val()+"/";
			}			
			Olegnax.Ajaxcart.helpers.ajax(
				url.replace("checkout/cart", "oxajax/cart"),
				Olegnax.Ajaxcart.helpers.cartSuccessFunc
			);
			return false;
		} else {
			if ( Olegnax.Ajaxcart.options.quick_view ) {
				//show quick view popup
				$(this).closest('li.item').find('button.quick-view').trigger('click');
				return false;
			} else {
				$(this).attr('onclick', $(this).attr('data-click'));
				$(this).attr('data-click', '');
				$(this).trigger('click');
				return false;
			}
		}
	});
});