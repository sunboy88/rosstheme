jQuery(function($){

	$('#olegnaxmegamenu li').hover(
		function(){
			$(this).addClass('over');
			$(this).children().addClass('shown-sub');
		},
		function(){
			$(this).removeClass('over');
			$(this).children().removeClass('shown-sub');
		}
	);

	$('.olegnaxmegamenu-sidebar li.parent > a').prepend('<em>+</em>');
	$('.olegnaxmegamenu-sidebar li.parent > a em').click(function(){
		if ( $(this).text() == '+') {
			/*$(this).parent().parent().addClass('over');*/
			$(this).parent().next().slideToggle();
			$(this).text('-');
		} else {
			/*$(this).parent().parent().removeClass('over');*/
			$(this).parent().next().slideToggle();
			$(this).text('+');
		}
		return false;
	});

});