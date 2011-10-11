$(function(){
	$('a.bt-create-account').live('click', function() {
		$('#signup-form').ajaxSubmit({target:'div.form'});
		return false;
	});
	
	//Manual slide
	$('div.slide-ctrl ul li').click(function() {
		$(this).siblings().removeClass('active');
		$(this).addClass('active');
		$('div.slide-wrapper').animate({
			marginLeft: ($(this).index() * 590 * -1)
		}, 500);
	});
	
	//Auto slide
	timerId = setInterval( function() { autoSlide(); }, 5000 );
	$('div.slide').hover(
		function () { clearInterval(timerId); },
		function () { timerId = setInterval( function() { autoSlide(); }, 5000 ); }
	);
});

function autoSlide() {
	var slides = $('div.slide-ctrl ul');
	var current = ( slides.find('li.active')?  slides.find('li.active') : slides.find('li:first') );
	var next = ( current.next().length > 0 ? current.next() : slides.find('li:first') );
	slides.find('li').eq(next.index()).siblings().removeClass('active');
	slides.find('li').eq(next.index()).addClass('active');
	$('div.slide-wrapper').animate({
		marginLeft: (next.index() * 590 * -1)
	}, 500);
}