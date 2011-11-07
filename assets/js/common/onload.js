
	function failsafeImg(img) {
		switch ( $(img).attr('class') ) {
			case 'app-image' : $(img).attr('src', base_url + 'assets/images/default/app.png'); break;
			case 'company-image' : $(img).attr('src', base_url + 'assets/images/default/company.png'); break;
			case 'campaign-image' : $(img).attr('src', base_url + 'assets/images/default/campaign.png'); break;
			case 'user-image' : $(img).attr('src', base_url + 'assets/images/default/user.png'); break;
			case 'page-image' : break; //do nothing
			default : break;
		}
	}
	
	$(document).ready(function() {
		var img = $(this).find('img.app-image, img.company-image, img.campaign-image, img.user-image, img.page-image').attr('onerror', 'failsafeImg(this)');
		$("*").ajaxStop(function() {
			$(this).find('img.app-image, img.company-image, img.campaign-image, img.user-image, img.page-image').attr('onerror', 'failsafeImg(this)');
		});
	});

