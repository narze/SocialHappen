if(!window.imgsize){
	function imgsize(url,size){
		if(size == 'square'){
			size = '_q';
		} else if(size == 'small'){
			size = '_t';
		} else if(size == 'normal'){
			size = '_s';
		} else if(size == 'large'){
			size = '_n';
		} else if(size == 'original'){
			size = '_o';
		} else {
			return url;
		}
		return url.replace(/(\S+)_\w(\.(jpg|gif|png))/i,"$1_"+size+"$2");
	}
}