jQuery(function($){

	var _height = function( o ) {
		return $(o).actual('height') + parseInt($(o).css('padding-top')) + parseInt($(o).css('padding-bottom'));
	};

	if (typeof ATHLETE_SLIDESHOW === 'undefined') {
		return;
	}

	var slideshow = $('#athlete-slideshow').hide(),
		slideshowInit = false,
		slideMaxHeight = 0,
		content_padding = 20;

	var getMaxSlideHeight = function( w )
	{
		slideMaxHeight = 0;
		//reset padding
		$('.slide-content-container', slideshow).css({'padding': content_padding + 'px 0'});
		$('.controls-title', slideshow).css({'margin-top': 0});
		$('.slide-banners', slideshow).css({'margin-top': 0, 'padding-top': 0});

		$(slideshow).find(".slide").each(function(){

			if ( w < 756 && $('.slide-banners', this).children().length ) {
				$('.slide-banners', this).css({'padding-top': content_padding + 'px'});
			}

			var slide_content_height = _height($('.slide-content-container', this));
			var slide_img_height = $('.slide-img', this).attr('data-height') || 0;
			if (mobile) {
				slide_img_height = Math.ceil( w * slide_img_height  / $(slideshow).attr('data-width') );
				$('.slide-img', this).css({'background-size': 'cover'});
			} else {
				$('.slide-img', this).css({'background-size': 'auto'});
			}
			$('.slide-img', this).css({'height': ( slide_img_height ) + 'px'});

			var height = Math.max(slide_content_height, slide_img_height);
			if ( height > slideMaxHeight ) slideMaxHeight = height;
		});
	}

	var initSlideTitle = function(slide)
	{
		$('.controls-title, .slide-title .text, .slide-title .link', slide).removeAttr('style');
		$('.slide-title .text, .slide-title .link', slide).each(function(){
			$(this).attr('style', $(this).attr('data-style'));
		});
		$('.animation-wrapper', slide).removeAttr('style').css({visibility: 'hidden'}).attr({'data-width': '', 'data-height': ''});

		$('.slide-title', slide).show();
		$('.slide-title .text, .slide-title .link', slide).each(function(){
			var w = $(this).actual('outerWidth'),
				h = $(this).actual('outerHeight');

			$(this).width( $( this ).actual( 'width', { absolute : true } ) );

			$(this).parent()
				.attr('data-width', w )
				.attr('data-height', h )
				.width( 0 )
				.height( h )
		});

		$('.controls-title', slide).height( $('.controls', slide).actual('height') );
        if ( $('.slide-title', slide).actual('height') > $('.controls', slide).actual('height') ) {
            $('.controls-title', slide).height( $('.slide-title', slide).actual('height') );
        }
	}

	var slideAnimationShow = function( slide )
	{
		var delayTime = 100;

		//prev / next
		$('.slide-prev', slide).css('margin-top', '-10px').animate({opacity: 1, marginTop: 0}, 256, 'easeOutExpo');
		$('.slide-next', slide).css('margin-top', '20px').animate({opacity: 1, marginTop: 0}, 256, 'easeOutExpo', function(){});

		//title and link
		$('.animation-wrapper', slide)
			.css({visibility: 'visible', width: '0px'})
			.each(function(i){
				$(this)
					.delay(delayTime * (i))
					.queue(function(next){
						$(this).animate({ width: $(this).attr('data-width') }, 600, 'easeOutExpo');
						next();
					});
			});

		//banners
		$('.slide-banner', slide).each(function(i){
			$(this)
				.delay(256 * (i))
				.queue(function(next){
					$(this)
						.animate({opacity: 1, marginRight: 0}, 100, 'easeInExpo');
					next();
				});
		});

	}

	var slideAnimationHide = function( slide )
	{
		var delayTime = 64;

		//prev / next
		$('.slide-prev', slide).animate({opacity: 0, marginTop: '-10px'}, 64, 'easeOutExpo');
		$('.slide-next', slide).animate({opacity: 0, marginTop: '10px'}, 64, 'easeOutExpo');

		//title and link
		$('.animation-wrapper', slide).each(function(i){
			$(this)
				.delay(delayTime * (i))
				.queue(function(next){
					$(this).animate({ width: 0 }, 128, 'easeOutExpo');
					next();
				});
		});

		//banners
		$('.slide-banner', slide).each(function(i){
			$(this)
				.delay(100 * (i))
				.queue(function(next){
					$(this)
						.animate({opacity: 0, marginRight: '-10px'}, 100, 'easeOutExpo');
					next();
				});
		});

	}

	var slideContentHide = function( slide )
	{
		//prev / next
		$('.slide-prev', slide).css({opacity: 0, marginTop: '-10px'});
		$('.slide-next', slide).css({opacity: 0, marginTop: '10px'});

		//title and link
		$('.animation-wrapper', slide).css({ width: 0 });

		//banners
		$('.slide-banner', slide).css({opacity: 0, marginRight: '-10px'});

	}

	////////////////////////////////////////////////////////
	//slide animation
	slideshow.on( 'cycle-initialized', function( e, opts ) {
		slideshowInit = true;
		slideAnimationShow( opts.slides.get( opts.currSlide) );
	});

	slideshow.on( 'cycle-before', function( e, opts ) {
		slideAnimationHide( opts.slides.get( opts.currSlide) );
	});

	slideshow.on( 'cycle-after', function( e, opts ) {
		slideAnimationShow( opts.slides.get( opts.nextSlide) );
	});
	////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////
	//progress bar
	var first_run = true;
	slideshow.on( 'cycle-initialized cycle-before', function( e, opts ) {
		if ( !ATHLETE_SLIDESHOW.timeout ) return;
		var progress = $('.progress', opts.slides.get( opts.currSlide)) ;
			$(progress).stop(true).css( 'width', 0 );
	});

	slideshow.on( 'cycle-initialized cycle-after', function( e, opts ) {
		if ( !ATHLETE_SLIDESHOW.timeout ) return;
		if ( ! slideshow.is('.cycle-paused') ) {
			if (first_run) {
				var progress = $('.progress', opts.slides.get( opts.currSlide)) ;
				$(progress).css( 'width', 0 ).animate({ width: '100%' }, opts.timeout, 'linear' );
				first_run = false;
			} else {
				var progress = $('.progress', opts.slides.get( opts.nextSlide)) ;
				$(progress).css( 'width', 0 ).animate({ width: '100%' }, opts.timeout, 'linear' );
			}
		}
	});

	slideshow.on( 'cycle-paused', function( e, opts ) {
		if ( !ATHLETE_SLIDESHOW.timeout ) return;
		var progress = $('.progress', opts.slides.get( opts.currSlide)) ;
		$(progress).stop();
	});

	slideshow.on( 'cycle-resumed', function( e, opts, timeoutRemaining ) {
		if ( !ATHLETE_SLIDESHOW.timeout ) return;
		var progress = $('.progress', opts.slides.get( opts.currSlide)) ;
		$(progress).animate({ width: '100%' }, timeoutRemaining, 'linear' );
	});
	////////////////////////////////////////////////////////



	////////////////////////////////////////////////////////
	// scale

	$(window).resize(function(){
		if ( !slideshowInit ) return;
		slideContentHide( $(slideshow).data("cycle.opts").slides.get( $(slideshow).data("cycle.opts").currSlide )  );
		$(slideshow).cycle('pause');
	});

	var scaleSlideshow = function(){

		$(slideshow).find(".slide").each(function(){
			initSlideTitle(this);
		});

		var w = jQuery('.main-container .row').first().width();
		getMaxSlideHeight(w);

		$(slideshow).find(".slide").each(function(){

			var slide_img_height = $('.slide-img', this).height() || 0;
			var slide_title = $('.controls-title', this).height();
			var slide_content_height = $('.slide-content-container', this).actual('height');

			if ( w > 755 ) {

				if ( ATHLETE_SLIDESHOW.autoHeight != 'container' ) {
					//v-align content by maxHeight
					if ( (slide_content_height+content_padding*2) < slideMaxHeight ) {
						$('.slide-content-container', this).css({'padding-top': Math.round( (slideMaxHeight - slide_content_height) / 2 ) + 'px'});
					}
				} else {
					//v-align content inside slide
					if ( (slide_content_height+content_padding*2) < slide_img_height ) {
						$('.slide-content-container', this).css({'padding': Math.round( (slide_img_height - slide_content_height) / 2 ) + 'px 0'});
					}
				}

				//v-align title
				if ( slide_title < slide_content_height ) {
					$('.controls-title', this).css({'margin-top': Math.round( (slide_content_height - slide_title) / 2 ) + 'px'});
				}
				//v-align banners
				var slide_banners = $('.slide-banners', this).actual('height');
				if ( $('.slide-banners', this).children().length && slide_banners < slide_content_height ) {
					$('.slide-banners', this).css({'margin-top': Math.round( (slide_content_height - slide_banners) / 2 ) + 'px'});
				}
			} else {

				if ( slide_img_height ) {
					//v-align title over the image, move banners under the image
					if ( (slide_title+content_padding*2) < slide_img_height ) {
						$('.controls-title', this).css({'margin': Math.round( (slide_img_height - slide_title)/2 - content_padding ) + 'px auto'});
						if ( $('.slide-banners', this).children().length ) {
							$('.slide-banners', this).css({'padding-top': Math.round( content_padding*2 ) + 'px'});
						}
					}
				} else if ( ATHLETE_SLIDESHOW.autoHeight != 'container' ) {
					//v-align content by maxHeight
					if ( (slide_content_height+content_padding*2) < slideMaxHeight ) {
						$('.slide-content-container', this).css({'padding-top': Math.round( (slideMaxHeight - slide_content_height) / 2 ) + 'px'});
					}
				}

			}

		});

		if ( !slideshowInit ) return;

		var currSlide = $(slideshow).data("cycle.opts").slides.get( $(slideshow).data("cycle.opts").currSlide );

        if ( ATHLETE_SLIDESHOW.autoHeight != 'container' ) {
            slideshow.animate({ height : slideMaxHeight +'px' }, 600, 'easeOutExpo', function(){
                slideAnimationShow( currSlide );
                $(slideshow).cycle('resume');
            });
        } else {
	        slideshow.animate({ height : $(currSlide).height() +'px' }, 600, 'easeOutExpo', function(){
		        slideAnimationShow( currSlide );
		        $(slideshow).cycle('resume');
	        });
        }
	};
	////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////
	// init

	$(window).load(function(){
		setTimeout(function(){

			$('.slide-title .text, .slide-title .link', slideshow).each(function(){
				$(this).attr('data-style', $(this).attr('style'));
			});

			$('.slide-title .text', slideshow).wrap('<div class="animation-wrapper animation-text" />');
			$('.slide-title .link', slideshow).wrap('<div class="animation-wrapper animation-link" />');
			$('.slide-title br', slideshow).hide();

			scaleSlideshow();
			$(window).resize(debounce(scaleSlideshow, 200));

			slideshow
				.css('height', '0px')
				.show()
				.stop()
				.animate(
					{ height : slideMaxHeight +'px' },
					1000,
					'easeOutExpo',
					function(){ slideshow.cycle( ATHLETE_SLIDESHOW ); }
				);
		}, 1000);
	});

});