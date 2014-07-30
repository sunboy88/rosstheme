if (typeof Olegnax == "undefined") var Olegnax = {};
if (typeof Olegnax.Ajaxcart == "undefined") {
	Olegnax.Ajaxcart = {
		translation : {},
		options : {},
		helpers : {}
	};
}

Olegnax.Ajaxcart.helpers.showMessage = function(message)
{
	jQuery.fancybox({
			'content'           : message,
			'autoDimensions'	: false,
			'width'        		: 300,
			'height'       		: 'auto',
			'padding'		    : 20,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'autoScale'         : true,
			'centerOnScroll'	: true
		}
	);
}
Olegnax.Ajaxcart.helpers.ajax = function(url, successFunc)
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
}

Olegnax.Ajaxcart.helpers.compareSuccessFunc = function(data)
{
	if (data.status != 'ERROR' ) {
		jQuery('.block-compare').replaceWith(data.sidebar);
	}
}
Olegnax.Ajaxcart.helpers.cartSuccessFunc = function(data)
{
	if (data.status != 'ERROR') {
		jQuery('.header-container .links').replaceWith(data.toplink);
		jQuery('.block-cart').replaceWith(data.sidebar);
	}
}
Olegnax.Ajaxcart.helpers.wishlistSuccessFunc = function(data)
{
	if (data.status != 'ERROR') {
		jQuery('.header-container .links').replaceWith(data.toplink);
		jQuery('.block-wishlist').replaceWith(data.sidebar);
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

		$('.col-main li.item a.product-image').prepend('<button type="button" class="button quick-view" ><span><span>'+Olegnax.Ajaxcart.translation.quick_view+'</span></span></button>');
		if ( Olegnax.Ajaxcart.options.quick_view ) {
			$('li.item').on({
				mouseenter: function(){ $(this).find('.quick-view').css('display', 'block'); },
				mouseleave: function(){ $(this).find('.quick-view').hide(); }
			});
		}
		$('.quick-view').on('click', function() {
			if ( $(window).width() < 769 )  {
				window.location = $(this).parent().attr('href');
				return false;
			}
			var $this = $(this);
			var quick_view_href = $this.parent().attr('href');
			if ('https:' == document.location.protocol) {
				quick_view_href = quick_view_href.replace('http:', 'https:');
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
			$.fancybox({
				hideOnContentClick:true,
				autoDimensions:true,
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
				Olegnax.Ajaxcart.helpers.ajax(
					url.replace("checkout/cart", "oxajax/cart"),
					Olegnax.Ajaxcart.helpers.cartSuccessFunc
				);
				return false;
			} else {
				//show quick view popup
				$(this).closest('li.item').find('.quick-view').trigger('click');
				return false;
			}
		});
});