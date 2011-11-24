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
	
	function set_campaign_range(){
		var dates = $( "input#campaign_start_date, input#campaign_end_date" ).datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "campaign_start_date" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	}
	set_campaign_range();

});