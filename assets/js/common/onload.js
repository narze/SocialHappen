$(function(){
	function failsafeImg(){
		for(var i=0;i<document.images.length;i++){
			var cpyImg = new Image();
			cpyImg.src = document.images[i].src;
			if(!cpyImg.width){
				className = document.images[i].getAttribute('class');
				if(className == 'app-image'){
					document.images[i].src = base_url + 'assets/images/default/app.png';
				} else if(className == 'company-image'){
					document.images[i].src = base_url + 'assets/images/default/company.png';
				} else if(className == 'campaign-image'){
					document.images[i].src = base_url + 'assets/images/default/campaign.png';
				} else if(className == 'user-image'){
					document.images[i].src = base_url + 'assets/images/default/user.png';
				} else if(className == 'page-image'){
					//do nothing
				} else { 
					// $.get(document.images[i].src).error(function(){console.log('ss');
						// $(this).attr('src', cpyImg.src);
					// });
					document.images[i].src = base_url +'assets/images/blank.png';
				}
			};
		}
	}
	//failsafeImg();
	$("*").ajaxStop(failsafeImg);
});