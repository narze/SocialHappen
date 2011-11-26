$(function(){
	$('input.cb-share-twitter').change(share_twitter);
	$('input.cb-share-facebook').change(share_facebook);
	$('form').submit(submit_share);
	var sh_twitter_popup;

	function share_twitter(){
		if(twitter_enable == 0){
			checkbox = $(this);
			if(checkbox.attr('checked')){
				sh_twitter_popup = window.open(null, '_blank', 'sh_twitter_popup');
				twitter_no_access_token(twitter_login);
			}
		}
	}

	function share_facebook(){
		
	}

	function submit_share(){
		if(!$('input.cb-share-twitter').attr('checked') && !$('input.cb-share-facebook').attr('checked')){
			alert('please share something');
			return false;
		}
	}

	function twitter_no_access_token(callback){
		$.getJSON(base_url + 'share/twitter_check_access_token/'+user_id,function(response){
			if(response.status != 1){
				callback();
			} else {
				sh_twitter_popup.close();
				twitter_enable = 1;
			}
		});
	}

	function twitter_login(){
		$('.loading').remove();
		$('body').append('<span class="loading">loading icon</span>');
		sh_twitter_popup.location.replace(base_url+'share/twitter_connect');
		window.setTimeout(wait_for_popup, 1000);

		function wait_for_popup(){
			if(!sh_twitter_popup.closed){
				window.setTimeout(wait_for_popup, 1000);
			} else {
				twitter_no_access_token(twitter_uncheck);
			}
		}
	}

	function twitter_uncheck(){
		$('input.cb-share-twitter').attr('checked', false);
	}

});