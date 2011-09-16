$(function(){
	function failsafeImg(){
		var badImg = new Image();
		badImg.src = base_url +'assets/images/blank.png';
		for(var i=0;i<document.images.length;i++){
			var cpyImg = new Image();
			cpyImg.src = document.images[i].src;
			if(!cpyImg.width){
				document.images[i].src = badImg.src;
			}
		}
	}
	$("*").ajaxStop(failsafeImg);
});