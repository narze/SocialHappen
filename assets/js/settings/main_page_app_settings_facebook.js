$(function(){

	//On-Off switch wrap
	$(':checkbox').each(function() {
		$(this).wrap(function() {
			return ($(this).is(':checked')) ? '<div class="checkbox_switch on" />' : '<div class="checkbox_switch" />';
		});
	}).click(function(e) { e.stopPropagation(); });
	
	//Click On-Off switch
	$('div.checkbox_switch').click(function () {
		$(this).toggleClass('on').find(':checkbox').click();
	});

	//Add ?tab=true for tab
	$('form').submit(function() {
		$(this).attr('action', $(this).attr('action') + '?tab=true');
	});
	$('a').click(function(){
		$(this).attr('href', $(this).attr('href') + '?tab=true' );
	});
	

});