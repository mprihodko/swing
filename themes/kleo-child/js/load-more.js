jQuery(function($){

	$('#feedback-stream').append( '<li class="load-more"></li>' );
	var button = $('#feedback-stream .load-more');
	var page = 2;
	var loading = false;
	var __post_id = $('#feedback-stream').data("postid");
	var scrollHandling = {
	    allow: true,
	    reallow: function() {
	        scrollHandling.allow = true;
	    },
	    delay: 0 //(milliseconds) adjust to the highest acceptable value
	};

	$(window).scroll(function(){
		
		if(button.length==0)
			return;
		if( ! loading && scrollHandling.allow ) {
			scrollHandling.allow = false;
			setTimeout(scrollHandling.reallow, scrollHandling.delay);
			var offset = $(button).offset().top - $(window).scrollTop();			 
			if( 2000 > offset ) {
				loading = true;
				var data = {
					action: $('#feedback-stream').data("type"),
					nonce: beloadmore.nonce,
					page: page,
					__post_id: __post_id,
					query: beloadmore.query,
				};
				$.post(beloadmore.url, data, function(res) {
					if( res.success) {
						$('#feedback-stream').append( res.data );
						$('#feedback-stream').append( button );
						page = page + 1;
						loading = false;
					} else {
						// console.log(res);
					}
				}).fail(function(xhr, textStatus, e) {
					// console.log(xhr.responseText);
				});

			}
		}
	});
});