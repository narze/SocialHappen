if(!window.imgsize){
	function imgsize(url,size){
		return url.replace(/(\S+)(\.(jpg|gif|png))/i,"$1_"+size+"$2");
	}
}