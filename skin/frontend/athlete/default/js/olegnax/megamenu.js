jQuery(function($){

	//fix right block border height
	$('#nav div.border-left').each(function(){
		$height = $(this).closest('.megamenu-block-col').prev().height();
		if ( $(this).height() < $height )
			$(this).height($height);
	});

	$('.olegnaxmegamenu-sidebar li.parent').prepend('<em class="toggle toggle-plus" href="#"></em>');
	//open active category
	$('.olegnaxmegamenu-sidebar li.active').parent().show();
	$('.olegnaxmegamenu-sidebar li.active > ul').show();
	$('.olegnaxmegamenu-sidebar li.active > .toggle').removeClass('toggle-plus').addClass('toggle-minus');

	$('.olegnaxmegamenu-sidebar li.parent .toggle').click(function(){
		if ( $(this).hasClass('toggle-plus') ) {
			$(this).parent().children('ul').stop( true, true ).slideToggle();
			$(this).removeClass('toggle-plus').addClass('toggle-minus');
		} else {
			$(this).parent().children('ul').stop( true, true ).slideToggle();
			$(this).addClass('toggle-plus').removeClass('toggle-minus');
		}
		return false;
	});

});