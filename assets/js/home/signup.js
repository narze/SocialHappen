$(function(){

	var firstname;
	var lastname;
	var email;
	var companyname;
	var companydetail;
	
	label();
	
	function label() {
		//First name
		firstname = $('#first_name').val('First name').addClass('inactive');
		firstname.focus(function () {
			if($(this).val() == 'First name') $(this).val('').removeClass('inactive');
		});
		firstname.blur(function () {
			if($(this).val() == '') $(this).val('First name').addClass('inactive');
		});
		
		//Last name
		lastname = $('#last_name').val('Last name').addClass('inactive');
		lastname.focus(function () {
			if($(this).val() == 'Last name') $(this).val('').removeClass('inactive');
		});
		lastname.blur(function () {
			if($(this).val() == '') $(this).val('Last name').addClass('inactive');
		});
		
		//Email
		email = $('#email').val('Email').addClass('inactive');
		email.focus(function () {
			if($(this).val() == 'Email') $(this).val('').removeClass('inactive');
		});
		email.blur(function () {
			if($(this).val() == '') $(this).val('Email').addClass('inactive');
		});
		
		//Company name
		companyname = $('#company_name').val('Company name').addClass('inactive');
		companyname.focus(function () {
			if($(this).val() == 'Company name') $(this).val('').removeClass('inactive');
		});
		companyname.blur(function () {
			if($(this).val() == '') $(this).val('Company name').addClass('inactive');
		});
		
		//Company detail
		companydetail = $('#company_detail').val('Company detail').addClass('inactive');
		companydetail.focus(function () {
			if($(this).val() == 'Company detail') $(this).val('').removeClass('inactive');
		});
		companydetail.blur(function () {
			if($(this).val() == '') $(this).val('Company detail').addClass('inactive');
		});
	}
	
	function reset_blank_field()
	{
		if(firstname.attr('class') == 'inactive') firstname.val('');
		if(lastname.attr('class') == 'inactive') lastname.val('');
		if(email.attr('class') == 'inactive') email.val('');
		if(companyname.attr('class') == 'inactive') companyname.val('');
		if(companydetail.attr('class') == 'inactive') companydetail.val('');
	}
	
	$('a.bt-continue').live('click', function() {
		reset_blank_field();
		$('#signup-form').ajaxSubmit({target:'div.form',success:label});
		return false;
	});
});