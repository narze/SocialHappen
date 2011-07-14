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
		$('<div class="loading-popup"><img src="'+base_url+'assets/images/loading.gif" /> '+message+'</div>').appendTo('body').ajaxStart(function() {
			$.fancybox({
				content: $(this)
				//modal: true
			});
		})
		.ajaxStop(function() {
			setTimeout(function() {
				$.fancybox.close();
			}, 500);
		});
	}
}