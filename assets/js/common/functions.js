if(!window.imgsize){
	function imgsize(url,size){
		if(url.indexOf('graph.facebook.com') == -1){
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
		} else {
			return url + "?type=" + size;
		}
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

		if(!$('div.overlay').length){ //Add loading div once
			
			overlay = $('<div class="overlay"></div>')
				.css('zIndex', '999')
				.hide().prependTo('body');
				
			overlaybg = $('<div class="overlay-bg"></div>');
			$(overlaybg)
				.css('position', 'fixed')
				.css('zIndex', '1000')
				.css('display', 'block')
				.css('width', '100%')
				.css('height', '100%')
				.css('margin', 0)
				.css('backgroundColor', 'black')
				.css('opacity', 0.75)
				.css('filter', 'alpha(opacity=75)')
			.appendTo(overlay);
			
			poptext = $('<p class="loading-text"><img src="'+base_url+'assets/images/loading.gif" /><br /> '+message+'</p>');
			$(poptext)
				.css('position', 'fixed')
				.css('zIndex', '1001')
				.css('backgroundColor', '#FFFFFF')
				.css('borderRadius', '5px')
				.css('font-weight', 'bold')
				.css('text-align', 'center')
				.css('width', '80px')
				.css('height', '60px')
				.css('padding-top', '6px')
				.css('left', width/2 - (poptext.width() / 2))
				.css('top', height/2 - (poptext.height() / 2))
			.appendTo(overlay);
			
			
			$(overlay).ajaxStart(function() {
				$(this).fadeIn(0);
			}).ajaxStop(function() {
				$(this).fadeOut('slow',function(){$(this).remove()});
			});
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