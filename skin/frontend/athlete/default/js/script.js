var mobleBreakpoint = 960,
	measureElement = null,
	animation_text_space = 0,
	mobile = false;

var getGridBreakpoint = function()
{
	var width = measureElement.width();
	for (var i=0; i<Athlete.breakpoints.length; i++ ) {
		if ( width < Athlete.breakpoints[i] ) {
			break;
		}
	}
	return i;
}

if (!("ontouchstart" in document.documentElement)) {
	document.documentElement.className += " no-touch";
} else {
	//enable :active class for links
	document.addEventListener("touchstart", function(){}, true);
}

window.onorientationchange = function() { jQuery(window).trigger('resize'); }

// Set pixelRatio to 1 if the browser doesn't offer it up.
var pixelRatio = !!window.devicePixelRatio ? window.devicePixelRatio : 1;
jQuery(window).on("load", function() {

	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
		jQuery('.mousetrap, #cloud-big-zoom').css({display:'none'});
	}

	if (pixelRatio == 1) return;

	var elements = {
		pager_left: jQuery('.pages li a.i-previous img'),
		pager_right: jQuery('.pages li a.i-next img'),
		sort_asc: jQuery('a.sort-by-arrow img.i_asc_arrow'),
		sort_desc: jQuery('a.sort-by-arrow img.i_desc_arrow')
	};
	for (var key in elements) {
		if (elements[key].length) {
			elements[key].attr('src', elements[key].attr('src').replace(/^(.*)\.png$/,"$1@2x.png"));
		}
	}
	//product images
	jQuery('img[data-srcX2]').each(function(){
		jQuery(this).attr('src',jQuery(this).attr('data-srcX2'));
	});
	//custom block images.
	jQuery('img.retina').each(function(){
		var file_ext = jQuery(this).attr('src').split('.').pop();
		var pattern = new RegExp("^(.*)\."+file_ext+"+$");
		jQuery(this).attr('src',jQuery(this).attr('src').replace(pattern,"$1_2x."+file_ext));
	});

});

// Returns a function, that, as long as it continues to be invoked, will not
// be triggered. The function will be called after it stops being called for
// N milliseconds. If `immediate` is passed, trigger the function on the
// leading edge, instead of the trailing.
debounce = function(func, wait, immediate) {
	var timeout, args, context, timestamp, result;
	return function() {
		context = this;
		args = arguments;
		timestamp = new Date();
		var later = function() {
			var last = (new Date()) - timestamp;
			if (last < wait) {
				timeout = setTimeout(later, wait - last);
			} else {
				timeout = null;
				if (!immediate) result = func.apply(context, args);
			}
		};
		var callNow = immediate && !timeout;
		if (!timeout) {
			timeout = setTimeout(later, wait);
		}
		if (callNow) result = func.apply(context, args);
		return result;
	};
};

jQuery.fn.extend({
	scrollToMe: function () {
		jQuery('html,body').animate({scrollTop: (jQuery(this).offset().top - 100)}, 500);
	}});

jQuery(function($){

	//init measure element
	measureElement = $('.main-container .row').first();

	$(window).resize(function(){
		var width = measureElement.width();
		mobile = (width < mobleBreakpoint);
	});

	/******* ToTop ******/
	if ( Athlete.totop ) {
	$("body").append('<a href="#" id="toTop" class="icon-'+Athlete.button_icons+'" style="display: none;"><span id="toTopHover" style="opacity: 0;"></span><small>To Top<small></a>');

		$(window).scroll(function () {
			if ($(this).scrollTop() > 100)
				$('a#toTop').show();
			else
				$('a#toTop').hide();
		});
		$('a#toTop').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 256);
			return false;
		});
	}

	/******* sticky header ******/
	if( !/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) && Athlete.sticky ) {

		var scroll_critical = parseInt($(".header .top-links").height());

		function header_transform(){
			if ( $('div.wrapper').width() < 1024 ) {
				$(".header-container").removeClass("header-fixed");
				$('div.header-nav-wide').css('margin-left', 0);
				return;
			}
			window_y = $(window).scrollTop();
			if (window_y > scroll_critical) {
				$(".header-container").addClass("header-fixed");
				$('div.header-nav-wide').css('margin-left', $('div.logo-container').width());
			} else {
				$(".header-container").removeClass("header-fixed");
				$('div.header-nav-wide').css('margin-left', 0);
			}
		}

		$(window).resize(debounce(function(){
			header_transform();
		}, 64));
		$(window).scroll(debounce(function(){
			header_transform();
		}, 64));
	}

	/******* owl carousel ******/

	var initSlider = function(sliderID) {

		(function () {

			var carouselId = '#'+sliderID;
			if ( !$(carouselId).length ) return;

			var carouselPrev = carouselId + '_nav .prev',
				carouselNext = carouselId + '_nav .next',
				owl = $(carouselId),
				options = {
					responsiveBaseWidth: measureElement,
					responsiveRefreshRate: 128,
					slideSpeed: 400,
					stopOnHover: true,
					pagination: false,
					itemsScaleUp: false,
					rewindNav: false,
					afterAction: function(){
						if ( this.itemsAmount > this.visibleItems.length ) {
							$(carouselNext).show();
							$(carouselPrev).show();

							$(carouselNext).removeClass('disabled');
							$(carouselPrev).removeClass('disabled');
							if ( this.currentItem == 0 ) {
								$(carouselPrev).addClass('disabled');
							}
							if ( this.currentItem == this.maximumItem ) {
								$(carouselNext).addClass('disabled');
							}

						} else {
							$(carouselNext).hide();
							$(carouselPrev).hide();
						}
					}
				};

			if ( $(carouselId).closest('.megamenu-block').length ) {
				var items, classList = $(carouselId).closest('.megamenu-block-col').attr('class').split(/\s+/);
				$.each( classList, function(index, item){
					if (item === 'megamenu-block-col') {
						return;
					}
					items = parseInt(item.replace('megamenu-block-col-',''));
				});
				options.itemsCustom = [ [0, items] ];
			} else {
				options.itemsCustom = $(carouselId).data('items');
			}

			if ( $(carouselId).data('autoscroll') ) {
				options.autoPlay = parseInt($(carouselId).data('autoscroll')) * 1000;
			}
			if ( $(carouselId).data('scroll') == 'page' ) {
				options.scrollPerPage = true;
			}
			if ( $(carouselId).data('rewind') ) {
				options.rewindNav = true;
			}
			owl.owlCarousel(options);

			$(carouselNext).click(function(){
				owl.trigger('owl.next');
				return false;
			})
			$(carouselPrev).click(function(){
				owl.trigger('owl.prev');
				return false;
			})
		}());
	};

	$('.carousel-slider').each(function(){
		initSlider( $(this).attr('id') );
	});

	initSlider('brands_slider');

	var $loginSlider = $('.block-login ul.slides');
	if ( $loginSlider.length ) {
		$loginSlider.owlCarousel({
			itemsCustom: [ [0,1] ],
			responsiveBaseWidth: measureElement,
			responsiveRefreshRate: 50,
			slideSpeed: 400,
			stopOnHover: true,
			pagination: false,
			itemsScaleUp: false,
			rewindNav: false
		});
		$("#forgot-password").click(function(){
			$loginSlider.trigger('owl.next');
			return false;
		});
		$("#back-login").click(function(){
			$loginSlider.trigger('owl.prev');
			return false;
		});
	}

	$(window).load(function(){

		var initBannerText = function(carouselID)
		{
			var carouselHeight = $(carouselID).height();
			$('.animation-wrapper', carouselID).removeAttr('style').attr({'data-width': '', 'data-height': ''});
			$('.text-container .text, .text-container .link', carouselID).each(function(){
				var w = $(this).outerWidth(!0) + animation_text_space,
					h = $(this).outerHeight();

				$(this).parent()
					.attr('data-width', w )
					.attr('data-height', h )
					.width( 0 )
					.height( h );
			});
			$('.text-container.center', carouselID).each(function(){
				$(this)
					.css('margin', $(this).attr('data-margin') )
					.css('margin-top', parseInt((carouselHeight-$(this).height())/2) + 'px');
			});
		}

		var afterMove = function( that ) {
			setTimeout(function(){
				for (var i=0; i<that.owl.owlItems.length; i++) {
					if ( $.inArray( i, that.owl.visibleItems ) > -1 ) {
						$('.text-container .animation-wrapper', that.owl.owlItems[i]).each(function(i){
							$(this)
								.delay(32 * i)
								.animate({width:$(this).attr('data-width')}, 256, 'easeOutExpo');
						});
					} else {
						$('.text-container .animation-wrapper', that.owl.owlItems[i]).css('width', '0px');
					}
				}
			}, 400);
		}

		var resizeSlides = function(that) {
			var newWidth = $(that.owl.owlItems[0]).width(),
				newHeight = Math.round( newWidth * $(carouselId).data('slideHeight') / $(carouselId).data('slideWidth')),
				ratio = $(that.owl.owlItems[0]).width() / $(carouselId).data('slideWidth'),
				newTextSize = Math.ceil( $(carouselId).data('textSize') * ratio),
				newTextLineHeight = Math.ceil( $(carouselId).data('textLineHeight') * ratio)+1,
				newLinkSize = Math.ceil( $(carouselId).data('linkSize') * ratio);

			$(carouselId+' li a').width(newWidth).height(newHeight);
			$(carouselId+' li a img').css({
				'transform' : 'scale('+ratio+')',
				'-ms-transform' : 'scale('+ratio+')',
				'-webkit-transform' : 'scale('+ratio+')'
			});

			$(carouselId+' li .text-container .text').css('font-size', newTextSize+'px');
			$(carouselId+' li .text-container .text').css('line-height', newTextLineHeight+'px');
			$(carouselId+' li .text-container .link').css('font-size', newLinkSize+'px');

			$(carouselId).animate( { height : newHeight +'px' }, 1000, 'easeOutExpo' );
		}


		var carouselId = '#' + $('.banners-slider-container .banners').attr('id');
		if ( !$(carouselId).length ) return;

		var carouselPrev = carouselId + '_nav .prev',
			carouselNext = carouselId + '_nav .next',
			owl = $(carouselId),
			options = {
				responsiveBaseWidth: $(carouselId).parent(),
				responsiveRefreshRate: 164,
				slideSpeed: 400,
				stopOnHover: true,
				pagination: false,
				itemsScaleUp: false,
				rewindNav: false,
				afterAction: function(){
					if ( this.itemsAmount > this.visibleItems.length ) {
						$(carouselNext).show();
						$(carouselPrev).show();

						$(carouselNext).removeClass('disabled');
						$(carouselPrev).removeClass('disabled');
						if ( this.currentItem == 0 ) {
							$(carouselPrev).addClass('disabled');
						}
						if ( this.currentItem == this.maximumItem ) {
							$(carouselNext).addClass('disabled');
						}

					} else {
						$(carouselNext).hide();
						$(carouselPrev).hide();
					}
				},
				beforeInit: function() {
					$(carouselId).data('slideWidth', $(carouselId+' li').width() );
					$(carouselId).data('slideHeight', $(carouselId+' li').height() );
					$(carouselId).data('textSize', parseInt($(carouselId+' li .text-container .text').css('font-size'), 10) );
					$(carouselId).data('textLineHeight', parseInt($(carouselId+' li .text-container .text').css('line-height'), 10) );
					$(carouselId).data('linkSize', parseInt($(carouselId+' li .text-container .link').css('font-size'), 10) );

					$('.text-container', carouselId).show();
					$('.text-container .text', carouselId).wrap('<div class="animation-wrapper animation-text" />');
					$('.text-container .link', carouselId).wrap('<div class="animation-wrapper animation-link" />');
					$('.text-container br', carouselId).hide();

					$('.text-container.center', carouselId).each(function(){
						$(this).attr('data-margin', $(this).css('margin'));
					});

					initBannerText(carouselId);

					//banner hover
					$('.no-touch '+carouselId+' > li').hover(
						function(){
							$('.text-container .animation-wrapper', this).each(function(i){
								$(this)
									.delay(64 * (i))
									.queue(function(next){
										$(this).addClass('animate-me');
										next();
									});
							});
						},
						function(){
							$('.text-container .animation-wrapper', this).each(function(i){
								$(this)
									.delay(64 * i)
									.queue(function(next){
										$(this).removeClass('animate-me');
										next();
									});
							});
						}
					);
				},
				afterInit: function() {
					resizeSlides(this);
					setTimeout(function(){ initBannerText(carouselId); }, 200);
					afterMove(this);
				},
				beforeUpdate: function() {
					$('.text-container .animation-wrapper', carouselId).css('width', '0px');
				},
				afterUpdate: function() {
					resizeSlides(this);
					setTimeout(function(){ initBannerText(carouselId); }, 200);
					afterMove(this);
				},
				afterMove: function() {
					afterMove(this);
				}
			};

		options.itemsCustom = $(carouselId).data('items');
		if ( $(carouselId).data('autoscroll') ) {
			options.autoPlay = parseInt($(carouselId).data('autoscroll')) * 1000;
		}
		if ( $(carouselId).data('scroll') == 'page' ) {
			options.scrollPerPage = true;
		}
		if ( $(carouselId).data('rewind') ) {
			options.rewindNav = true;
		}
		owl.owlCarousel(options);

		$(carouselNext).click(function(){
			owl.trigger('owl.next');
			return false;
		});
		$(carouselPrev).click(function(){
			owl.trigger('owl.prev');
			return false;
		});
	});

	
	$(document).on("mouseenter","div.header-switch", function() {
		$('div.header-dropdown', this).stop( true, true ).animate({opacity:1, height:'toggle'}, 100);
	});
	$(document).on("mouseleave","div.header-switch", function() {
		$('div.header-dropdown', this).stop( true, true ).animate({opacity:0, height:'toggle'}, 100);
	});

	$("div.header-switch").on("touchend", function(e) {
		e.stopPropagation();
		$('div.header-dropdown:visible').each(function(){
			if ($(this).has(e.target).length > 0){ return}
			$(this).stop( true, true ).css({opacity:0, display:'none'});
		});
		$('div.header-dropdown', this).stop( true, true ).animate({opacity:1, height:'toggle'}, 100);
	});
	$("div.header-dropdown").on("touchend", function(e) {
		e.stopPropagation();
	});
	$("div.header-cart a.summary").on("touchend", function(e) {
		if ( $(this).next().css('display') != 'block' ) { e.preventDefault(); }
	});


	var navTopHover = function(el)
	{
		if ( $('div.wrapper').width() < 1024 ) return;

		$(el).addClass('over');
		$(el).children(':not(a)').addClass('shown-sub');

		var ul = $(el).children(':not(a)');
		if ( !ul.length || !$('div.nav-container').hasClass('default') ) return;
		var docWidth = $(document).width();
		var divWidth = ul.actual('width') + parseInt(ul.css('padding-left'), 10)*2 + 30;
		if ( divWidth + parseInt($(ul).offset().left, 10) > docWidth  ) {
			ul.css('left', -($(el).offset().left + divWidth - docWidth)+'px' );
		}
	}

	$('#nav li').bind('touchend', function(e) {
		e.stopPropagation();
	});
	$('#nav>li').bind('touchend', function(e) {
		if ( !$(e.target).is( "li.level0" ) ) {
			return false;
		}
		if ( $(this).hasClass('over') || !$(this).children(':not(a)').length ) {
			return true;
		}
		e.preventDefault();
		$('#nav>li').removeClass('over');
		$('#nav>li').children(':not(a)').removeClass('shown-sub').removeAttr('style');
		navTopHover(this);
	});

	$('#nav>li').on({
		mouseenter: function() {
			if ( !$('html').hasClass('no-touch') ) { return false; }
			navTopHover(this);

		}, mouseleave: function() {
			if ( !$('html').hasClass('no-touch') ) { return false; }
			if ( $('div.wrapper').width() < 1024 ) return;

			$(this).removeClass('over');
			$(this).children(':not(a)').removeClass('shown-sub').removeAttr('style');
		}
	});

	$('#nav ul.level0 li').hover(
		function(){
			if ( $('div.wrapper').width() < 1024 ) return;

			$(this).addClass('over');
			$(this).children(':not(a)').addClass('shown-sub');
		},
		function(){
			if ( $('div.wrapper').width() < 1024 ) return;

			$(this).removeClass('over');
			$(this).children(':not(a)').removeClass('shown-sub');
		}
	);

	//for images in content
	$('.std, .info-content').find('img[style*="float: left"]').addClass('alignleft');
	$('img.alignleft').closest('a').addClass('alignleft');
	$('a.alignleft').find('.alignleft').removeClass('alignleft');

	$('.std, .info-content').find('img[style*="float: right"]').addClass('alignright');
	$('img.alignright').closest('a').addClass('alignright');
	$('a.alignright').find('.alignright').removeClass('alignright');

	$('.std, .info-content').find('img[style*="display: block; margin-left: auto; margin-right: auto;"]').addClass('aligncenter');
	$('img.aligncenter').closest('a').addClass('aligncenter');
	$('a.aligncenter').find('.aligncenter').removeClass('aligncenter');

	$(function(){
	    $(".lt-ie8 .hb-left").hover(function(){
	        $(this).stop().animate({left: "0" }, 300, 'easeOutQuint');
        }, function(){
	        $(this).stop().animate({left: "-245" }, 600, 'easeInQuint');
	    },1000);

	   $(".lt-ie8 .hb-right").hover(function(){
            $(this).stop(true, false).animate({right: "0" }, 300, 'easeOutQuint');
        }, function(){
            $(this).stop(true, false).animate({right: "-245" }, 600, 'easeInQuint');
        },1000);
    });

	//mobile navigation
	var toggleMobileNav = function( el )
	{
		if ( $(el).text() == '+') {
			$(el).parent().parent().addClass('over');
			$(el).parent().next().slideToggle();
			$(el).text('-');
		} else {
			$(el).parent().parent().removeClass('over');
			$(el).parent().next().slideToggle();
			$(el).text('+');
		}
	}

	$('.nav-container li.parent > a').prepend('<em>+</em>');
	$('.nav-container li.parent-fake > a em').detach();
	$('.nav-container li.parent > a em').click(function(){
		if ( !$('html').hasClass('no-touch') ) { return false; }
		toggleMobileNav(this);
		return false;
	});
	$('.nav-container li.parent > a em').bind('touchend', function(e) {
		toggleMobileNav(this);
	});
	$('.nav-container .nav-top-title').click(function(){
		$(this).toggleClass('over').next().toggle();
		return false;
	});
	$(window).resize(debounce(function(){
		if ( $('div.wrapper').width() >= 1024 ) {
			$('#nav, #nav li.parent, #nav li.parent > ul, #nav li.parent > div').removeAttr('style');
			$('#nav li.parent').removeClass('over');
			$('.nav-container li.parent > a em').text('+');
		}
	}, 128));

	var form_search_over = false;
	$('.header .form-search').on({
		click: function(event){
			if ( Athlete.header_search && !mobile ) { return; }
			event.stopPropagation();
			if ( form_search_over ) {
				return true;
			}
			form_search_over = true;
			$(this).addClass('form-search-over');
			$('#search').stop( true, true).css('opacity', 0).animate({width:'toggle', opacity:1}, 200, 'easeOutExpo');
			return false;
		}
	});
	//Hide search if visible
	$('html').click(function() {
		if ( Athlete.header_search && !mobile ) { return; }
		if ( form_search_over ) {
			form_search_over = false;
			$('#search').stop( true, true ).animate({width:'toggle', opacity:0}, 300, 'easeInExpo', function(){
				$(this).parent().parent().removeClass('form-search-over');
			});
		}
	});
	$('.header .form-search, #search').on("touchend", function(e) {
		e.stopPropagation();
	});

	$(window).resize(function(){
		var $navWide = $('div.header-nav-wide');
		if ( $('div.wrapper').width() >= 1024 ) {
			if ( Athlete.header_search ) {
				form_search_over = false;
				$('#search').stop( true, true ).removeAttr('style');
				$('#search').parent().parent().removeClass('form-search-over');
			}
			if ( $navWide.length ) {
				$navWide.attr('style', '');
				var x = Math.round( ( ($('.header-info-container').height() - $('.top-links-container').height() - $('.header .form-search button.button').height()) / 2) , 10) ;
				$('.header .form-search').css('top', (x-5)+'px')
			}
		} else {
			if ( $navWide.length ) {
				$('.header .form-search').attr('style', '');
				if ( measureElement.width() > 755 ) {
					$navWide.css({
						top: $('.top-links-container').height() + 'px',
						bottom: ''
					});
				} else {
					$navWide.attr('style', '');
				}
			}
		}
	});

	$('.toolbar-switch').on({
		mouseenter: function() {
			var $dropdown = $('.toolbar-dropdown', this), width;
			$(this).addClass('over');
			if ( $(this).closest('.sorter').length ) {
				width = $(this).width()+50;
			} else {
				width = $(this).width() - parseInt($dropdown.css('padding-left'))*2 ;
			}
			$dropdown
				.css('width', width)
				.stop( true, true )
				.animate({opacity:1, height:'toggle'}, 100);
		}, mouseleave: function() {
			var that = this;
			$('.toolbar-dropdown', this).stop( true, true ).animate({opacity:0, height:'toggle'}, 100, function(){
				$(that).removeClass('over');
			});
		}
	});

	/* category banner animation */
	var cbc = $('.category-banner-container');
	var cbctc = cbc.find('.text-container');

	$(window).load(function(){

		if ( !cbctc.length ) return;

		$('.text', cbc).wrap('<div class="animation-wrapper animation-text" />');
		$('.link', cbc).wrap('<div class="animation-wrapper animation-link" />');
		$(' br', cbctc).hide();

		$('.text, .link', cbc).each(function(){
			$(this).attr('data-style', $(this).attr('style'));
		});

		var initTitle = function()
		{
			$('.text, .link', cbc).removeAttr('style');
			$('.text, .link', cbc).each(function(){
				$(this).attr('style', $(this).attr('data-style'));
			});
			$('.animation-wrapper', cbc).removeAttr('style').css({visibility: 'hidden'}).attr({'data-width': '', 'data-height': ''});

			cbctc.css('visibility', 'visible');
			$('.text, .link', cbc).each(function(){
				var w = $(this).actual('outerWidth'),
					h = $(this).actual('outerHeight');

				$(this).parent()
					.attr('data-width', w )
					.attr('data-height', h )
					.width( 0 )
					.height( h )
			});

			cbctc.css('marginTop', parseInt((cbc.height()-cbctc.height())/2)+'px');
		}

		var showTitle = function()
		{
			initTitle();
			$('.animation-wrapper', cbctc).each(function(i){
				$(this)
					.css('visibility', 'visible')
					.delay(32 * i)
					.queue(function(next){
						$(this).animate({width:$(this).attr('data-width')}, 256, 'easeOutExpo');
						next();
					});
			});
		}

		setTimeout(function(){
			showTitle();
			$(window).resize(function(){ $('.animation-wrapper', cbctc).css({width: 0}) });
			$(window).resize(debounce(showTitle, 400));
		}, 1000);

	});


	/* cms banner animation */
	var cmsBanner = $('.cms-banner');
	var cmsBannerText = cmsBanner.find('.text-container');

	$(window).load(function(){

		if ( !cmsBannerText.length ) return;

		$('.text', cmsBanner).wrap('<div class="animation-wrapper animation-text" />');
		$('.link', cmsBanner).wrap('<div class="animation-wrapper animation-link" />');
		$(' br', cmsBannerText).hide();

		var initTitle = function()
		{
			//$('.text, .link', cmsBanner).removeAttr('style');
			$('.animation-wrapper', cmsBanner).removeAttr('style').css({visibility: 'hidden'}).attr({'data-width': '', 'data-height': ''});

			cmsBannerText.css('visibility', 'visible');
			$('.text, .link', cmsBanner).each(function(){
				var w = $(this).actual('outerWidth'),
					h = $(this).actual('outerHeight');

				$(this).parent()
					.attr('data-width', w )
					.attr('data-height', h )
					.width( 0 )
					.height( h )
			});

			$('.text-container.center', cmsBanner).each(function(){
				$(this).css('marginTop', parseInt(($(this).parent().height()-$(this).height())/2)+'px');
			});
		}

		var showTitle = function()
		{
			initTitle();
			$('.animation-wrapper', cmsBannerText).each(function(i){
				$(this)
					.css('visibility', 'visible')
					.delay(32 * i)
					.queue(function(next){
						$(this).animate({width:$(this).attr('data-width')}, 256, 'easeOutExpo');
						next();
					});
			});
		}

		//banner hover
		$('.no-touch .cms-banner').hover(
			function(){
				$('.text-container .animation-wrapper', this).each(function(i){
					$(this)
						.delay(64 * (i))
						.queue(function(next){
							$(this).addClass('animate-me');
							next();
						});
				});
			},
			function(){
				$('.text-container .animation-wrapper', this).each(function(i){
					$(this)
						.delay(64 * i)
						.queue(function(next){
							$(this).removeClass('animate-me');
							next();
						});
				});
			}
		);

		setTimeout(function(){

			showTitle();
			$(window).resize(function(){ $('.animation-wrapper', cmsBannerText).css({width: 0}) });
			$(window).resize(debounce(showTitle, 400));
		}, 1000);

	});

	//set default opacity to additional img
	$('img.additional_img').css({opacity: 0, display: 'block'});

	var gridAnimateEnter = function(element)
	{
		//product name
		$('.product-name, .price-box', element).each(function(i){
			$(this)
				.delay(64 * (i))
				.queue(function(next){
					$(this).addClass('animate-me');
					next();
				});
		});
		//actions
		var actionsHeight = $('.actions', element).height();
		if ( $('.actions .quick-view', element).length && $('.actions .quick-view', element).css('display') != 'none' ) {
			if ( $('.actions .add-to-links', element).length ) {
				actionsHeight = $('.actions', element).data('quickViewHeight');
			} else {

			}
			setTimeout(function(){$('.actions .quick-view', element).stop(true, true).animate({opacity: 1}, 64, 'linear');}, 100);
		}

		$('.actions', element).css('top', parseInt( ($('.product-image', element).height() - actionsHeight)/2 + parseInt($('.item-wrap', element).css('padding-top')) ) );
		$('.actions .add-to-links li', element).each(function(i){
			$(this)
				.delay(64 * (i))
				.queue(function(next){
					$(this).addClass('animate-me');
					next();
				});
		});

		if ( $('img.additional_img', element).length ) {
			$('img.additional_img', element).stop(true, true).animate({opacity: 1}, 150, 'linear');
			$('img.regular_img', element).stop(true, true).animate({opacity: 0}, 150, 'linear');
		}
	}

	var gridAnimateLeave = function(element)
	{
		$('.actions .quick-view', element).stop(true, true).animate({opacity: 0}, 100, 'linear');
		$('.product-name, .price-box', element).each(function(i){
			$(this)
				.delay(64 * (i))
				.queue(function(next){
					$(this).removeClass('animate-me');
					next();
				});
		});

		$('.actions .add-to-links li', element).each(function(i){
			$(this)
				.delay(64 * (i))
				.queue(function(next){
					$(this).removeClass('animate-me');
					next();
				});
		});

		if ( $('img.additional_img', element).length ) {
			$('img.additional_img', element).stop( true, true).animate({opacity: 0}, 150, 'linear');
			$('img.regular_img', element).stop( true, true).animate({opacity: 1}, 150, 'linear');
		}
	}

	//product hover - grid
	$('ul.products-grid li.item').bind('touchend', function(e) {
		if ( $(this).hasClass('hover') ) {
			return true;
		}
		e.preventDefault();
		$('ul.products-grid li.item').removeClass('hover');
		$('ul.products-grid h2.product-name, ul.products-grid div.price-box, ul.products-grid .add-to-links li').removeClass('animate-me');
		$('ul.products-grid .actions .quick-view').stop(true, true).css({opacity: 0});
		$(this).addClass('hover');
		gridAnimateEnter(this);
	});

	$("ul.products-grid li.item").on({
		mouseenter: function() {
			if ( !$('html').hasClass('no-touch') ) { return false; }
			$(this).toggleClass('hover');
			gridAnimateEnter(this);

		}, mouseleave: function() {
			if ( !$('html').hasClass('no-touch') ) { return false; }
			$(this).toggleClass('hover');
			gridAnimateLeave(this);
		}
	});
	//product hover - list
	$("ol.products-list li.item a.product-image").hover(function(e) {
		if ( $('img.additional_img', this).length ) {
			$('img.additional_img', this).stop( true, true).animate({opacity: 1}, 150, 'linear');
			$('img.regular_img', this).stop( true, true).animate({opacity: 0}, 150, 'linear');
		}
	}, function(e) {
		if ( $('img.additional_img', this).length ) {
			$('img.additional_img', this).stop( true, true).animate({opacity: 0}, 150, 'linear');
			$('img.regular_img', this).stop( true, true).animate({opacity: 1}, 150, 'linear');
		}
	});

	//qty
	$('.qty-container .qty-inc').click(function(){
		var $qty = $(this).parent().next(), $qtyVal;
		$qtyVal = parseInt($qty.val(), 10);
		if ( $qtyVal < 0 || !$.isNumeric($qtyVal) ) $qtyVal = 0;
		$qty.val(++$qtyVal);
		return false;
	});
	$('.qty-container .qty-dec').click(function(){
		var $qty = $(this).parent().next(), $qtyVal;
		$qtyVal = parseInt($qty.val(), 10);
		if ( $qtyVal < 2 || !$.isNumeric($qtyVal) ) $qtyVal = 2;
		$qty.val(--$qtyVal);
		return false;
	});

	//product accordion
	$('.product-tabs-container h2.tab-heading a').click(function () {
		$('.product-tabs li.active').toggleClass('active');
		$('#'+$(this).parent().attr('id').replace("product_acc_", "product_tabs_")).toggleClass('active');
		that = $(this).parent();
		if($(that).is('.active')) {
			$(that).toggleClass('active');
			$(that).next().slideToggle(function(){ $(that).scrollToMe(); });
		} else {
			$('.product-tabs-container h2.tab-heading.active').toggleClass('active').next().slideToggle();
			$(that).toggleClass('active');
			$(that).next().slideToggle(function(){ $(that).scrollToMe(); });
		}
		return false;
	});
	$('.product-tabs-container h2:first').toggleClass('active');
	$('.product-tabs a').click(function(){
		$('.product-tabs-container h2.tab-heading.active').toggleClass('active');
		$('#'+$(this).parent().attr('id').replace("product_tabs_", "product_acc_")).toggleClass('active');
	});

	//add review link on product page open review tab
	$('div.product-view p.no-rating a, div.product-view .rating-links a:last-child, .dedicated-review-box .title-container button').click(function(){
		$('#review-form').scrollToMe();
		return false;
	});
	$('div.product-view .rating-links a:first-child').click(function(){
		$('#product-customer-reviews').scrollToMe();
		return false;
	});

	if ( Athlete.login_bg != '' ) {
		jQuery('.customer-account-create .content-container, .customer-account-forgotpassword .content-container, .customer-account-resetpassword .content-container, .customer-account-login .content-container, .checkout-multishipping-login .content-container')
			.anystretch( Athlete.login_bg );
		jQuery('.customer-account-create .content-container, .customer-account-forgotpassword .content-container, ' +
		'.customer-account-resetpassword .content-container, .customer-account-login .content-container, ' +
		'.customer-account-create .main-container, .customer-account-forgotpassword .main-container, ' +
		'.customer-account-resetpassword .main-container, .customer-account-login .main-container, .checkout-multishipping-login .main-container')
			.css('background', 'transparent');
	}

	$thumbContainer = $('.content-container .product-view .more-views .carousel-slider a');
	if ( $thumbContainer.length ) {
		$(window).resize(debounce(function(){
			$('span', $thumbContainer)
				.width( $thumbContainer.width()-18 )
				.height( $thumbContainer.height()-18 );
		}, 128));
	}

	$(document).on("touchend", function() {

		$('.header-dropdown:visible').stop( true, true ).css({opacity:0, display:'none'});

		$('#nav>li').removeClass('over');
		$('#nav>li').children(':not(a)').removeClass('shown-sub').removeAttr('style');
		$('.nav-container .nav-top-title').removeClass('over').next().toggle(false);

		if ( form_search_over ) {
			form_search_over = false;
			$('#search').stop( true, true ).animate({width:'toggle', opacity:0}, 300, 'easeInExpo', function(){
				$(this).parent().parent().removeClass('form-search-over');
			});
		}

	});

	$(window).load(function(){
		$(window).trigger('resize');
	});

});

//actual width
(function(a){a.fn.addBack=a.fn.addBack||a.fn.andSelf;a.fn.extend({actual:function(b,l){if(!this[b]){throw'$.actual => The jQuery method "'+b+'" you called does not exist';}var f={absolute:false,clone:false,includeMargin:false};var i=a.extend(f,l);var e=this.eq(0);var h,j;if(i.clone===true){h=function(){var m="position: absolute !important; top: -1000 !important; ";e=e.clone().attr("style",m).appendTo("body");};j=function(){e.remove();};}else{var g=[];var d="";var c;h=function(){c=e.parents().addBack().filter(":hidden");d+="visibility: hidden !important; display: block !important; ";if(i.absolute===true){d+="position: absolute !important; ";}c.each(function(){var m=a(this);g.push(m.attr("style"));m.attr("style",d);});};j=function(){c.each(function(m){var o=a(this);var n=g[m];if(n===undefined){o.removeAttr("style");}else{o.attr("style",n);}});};}h();var k=/(outer)/.test(b)?e[b](i.includeMargin):e[b]();j();return k;}});})(jQuery);