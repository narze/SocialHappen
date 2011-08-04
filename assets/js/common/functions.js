if(!window.imgsize){
	function imgsize(url,size){
		if(size == 'square'){
			size = 'q';
		} else if(size == 'small'){
			size = 't';
		} else if(size == 'normal'){
			size = 's';
		} else if(size == 'large'){
			size = 'n';
		} else if(size == 'original'){
			size = 'o';
		} else {
			return url;
		}
		return url.replace(/(\S+)_\w(\.(jpg|gif|png))/i,"$1_"+size+"$2");
	}
}

if(!window.set_loading){
	function set_loading(message){
		if(!message) {
			message = "Loading";
		}
		//$.fancybox.init(); //force init
		
		var height = $(window).height();
		var width = $(document).width();

		if(!$('div.loading-popup').length){ //Add loading div once
			popup = $('<div class="loading-popup"><img src="'+base_url+'assets/images/loading.gif" /> '+message+'</div>');
			$(popup).css('position', 'fixed')
				.css('display', 'block')
				.css('font-weight', 'bold')
				.css('width', '200px')
				.css('height', '100px')
				.css('left', width/2 - (popup.width() / 2))
				.css('top', height/2 - (popup.height() / 2))
				.css('margin', 'auto')
				.css('zIndex', '100')
				.css('backgroundColor', 'white')
				.css('border', 'solid 1px')
				.css('text-align', 'center')

          
			.hide().prependTo('body');
		}
		
		// $('div.loading-popup').ajaxStart(function() {
			// if(!$('#fancybox-content').html().length){ //if fancybox is hidden : show it and hide when ajax is loaded
				// $.fancybox({
					// content: $(this).show()
				// });
				// $(this).ajaxStop(function() {
					// setTimeout(function() {
						// $.fancybox.close();
						// $('div.loading-popup').remove();
					// }, 500);
				// });
			// }
		// });
		
		$('div.loading-popup').ajaxStart(function() {
			$(this).fadeIn('slow');
		}).ajaxStop(function() {
			$(this).fadeOut('slow');
		});
	}
}

if(!window.check_login){
	function check_login(redirect_path,callback){
		if(!redirect_path) redirect_path = '';
		$.getJSON(base_url+'home/json_check_login/'+redirect_path,function(response){
			if(!response.logged_in){
				window.location.replace(response.redirect+'?next='+encodeURIComponent(window.location.href));
			} else {
				callback();
			}
		});
	}
}