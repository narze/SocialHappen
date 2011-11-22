$(function(){
	$('a.sh-share-twitter-login').one('click',twitter_login);

	function twitter_login(){
		$('body').append('<span class="loading">loading icon</span>');
		var sh_twitter_popup = window.open('https://socialhappen.dyndns.org/socialhappen/share/twitter_connect', '_blank', 'sh_twitter_popup');
		window.setTimeout(wait_for_popup, 1000);

		function wait_for_popup(){
			if(!sh_twitter_popup.closed){
				window.setTimeout(wait_for_popup, 1000);
			} else {
				console.log('popup closed');
				window.location.reload();
			}
		}
	}

	
});