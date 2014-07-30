jQuery(function($){
	$(window).load(function(){
		$('.postDetails a > img, .postContent a > img, .blog-recent-thumb > img').each(function(){
			if ( $(this).hasClass('alignleft') || $(this).hasClass('alignright') ) return;
			$(this).parent().addClass('img-container');
		});
	});
});