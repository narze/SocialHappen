$(function(){
	Date.createFromMysql = function(mysql_string){ 
	   if(typeof mysql_string === 'string')
	   {
		  var t = mysql_string.split(/[- :]/);
		  //when t[3], t[4] and t[5] are missing they defaults to zero
		  return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);          
	   }
	   return null;   
	}

	$('div#main').load(base_url+'tab/dashboard/'+page_id+'/'+user_id+'/'+token,function(){
		$('.campaign-end-time').each(function(){
			end_time = Date.createFromMysql($(this).text());
			$(this).replaceWith($("<p></p>").countdown({
				until: end_time,
				format: 'HMS',
				layout: '<strong>{hnn}h {sep} {mnn}m {sep} {snn}s</strong>'})
			.removeClass('hasCountdown'));
		});
	});
});