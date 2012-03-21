$(function(){
	Date.createFromMysql = function(mysql_string){
		if(typeof mysql_string === 'string') {
			var t = mysql_string.split(/[- :]/);
			return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);
		}
		return null;
	};

	function signup_fields(){
		$('form.signup-fields ul li a.bt-remove-field').die().live('click', function(){
			$(this).parents('form ul li').appendTo('div#no-submit ul');
			trigger_empty_field_message();
		});
		
		//Choose template fields
		$('a.bt-add-field-from-list').die().live('click', function(){
			//Uncheck all and check only showing fields
			$('#default-fields li input:checkbox').attr('checked', false);
			var fields = $('form.signup-fields ul li:not(".no-field")').map(function(){
				return this.className;
			}).get();
			for(var i in fields){
				$('#default-fields li input:checkbox[name="'+fields[i]+'"]').attr('checked', true);
			}
			
			//Call fancybox and show checkboxes
			$.fancybox({
				content: $('#default-fields').appendTo(this),
				onCleanup: function() {
					$('#default-fields').clone().appendTo($('#fancy')); //Clone to make ux better, it'll be remove by fancybox
				}
			});
			
			//Add/remove select/deselect fields
			$('#default-fields a.bt-apply-to-signup-form').click(function(){
				$.fancybox.close();
				//Move selected into form
				var selected = $('#default-fields li input:checkbox:checked').map(function(){
					return this.name;
				}).get();
				for(var i in selected){
					if($('form ul li.'+selected[i]).length === 0){ //if not exists
						$('div#no-submit ul li.'+selected[i]).appendTo('form ul');
					}
				}
				
				//Move deselected out of form
				var deselected = $('#default-fields li input:checkbox:not(:checked)').map(function(){
					return this.name;
				}).get();
				for(i in deselected){
					$('form ul li.'+deselected[i]).appendTo('div#no-submit ul');
				}
				
				trigger_empty_field_message();
			});
		});
		
		//Create custom field
		$('a.bt-create-own-field').die().live('click', function(){
			//Open fancybox with one blank field form
			$.fancybox({
				content: $('#custom-fields-template').clone().appendTo(this).attr('id','custom-fields')
			});
			
			//Click bottom text field to add a new option
			$('#custom-fields ul li div.options ul li.add-option input').die().live('click',function(){
				add_more_option(this).find('input[type="text"]').val('').focus();
			});
			//Click 'Other' to add a new option named 'Other'
			$('#custom-fields ul li div.options ul li.add-option a.add-other').die().live('click',function(){
				add_more_option(this).find('input[type="text"]').val('Other');
			});
			//Click remove option
			$('#custom-fields ul li div.options ul li a.bt-remove-option').die().live('click',function(){
				if($(this).parent().siblings('li.option').length > 0){ //if at least one option left
					$(this).parent().remove(); //remove option
				}
			});
			
			//Change field type
			$('#custom-fields ul li select[name="field-type"]').die().live('change', function(){
				var selected_type = $(this).find('option:selected').val();
				var old_value = $(this);
				switch (selected_type){
					case 'radio':
					case 'checkbox':
						$(this).parents('#custom-fields ul li').find('div.options').show().find('input.option-type').replaceWith('<input type="'+selected_type+'" name="option-type-example" class="option-type" />');
					break;
					case 'textarea':
					case 'text':
						$(this).parents('#custom-fields ul li').find('div.options').hide();
					break;
					default:
						$(this).parents('#custom-fields ul li').find('div.options').hide();
					break;
				}
			});
			
			//Remove a field
			$('#custom-fields ul li a.bt-remove-field').die().live('click',function(){
				if($(this).parents('li.field').siblings('li.field').length === 0){ //if no option left
					add_more_field();
				}$(this).parents('li.field').remove();
			});
			
			//Add a field
			$('#custom-fields a.add-more-field').click(add_more_field);
			
			//Apply custom fields
			$('#custom-fields p.apply a.bt-apply-to-signup-form').click(function () {
				if(add_new_fields()){
					$.fancybox.close();
					trigger_empty_field_message();
				}
			});
		});
		
		function add_new_fields(){
			var new_fields = $('#custom-fields ul.fields li.field');
			//Clear error
			new_fields.find('div.options ul li.option input.option-item').removeClass('form-error'); //Clear error
			new_fields.find('input[name="new-field"]').removeClass('form-error');
			var new_fields_data = [];
			var names = [];
			var verified = true;
			for(i=0; i<new_fields.length; i++){ //For all new custom fields to add
				var this_field = new_fields.eq(i);
				new_fields_data[i] = {};
				new_fields_data[i].label = this_field.find('input[name="new-field"]').val();
				new_fields_data[i].name = new_fields_data[i].label.toLowerCase().replace(/ /,'_');
				new_fields_data[i].type = this_field.find('select[name="field-type"] option:selected').val();
				new_fields_data[i].required = this_field.find('input[name="new-field-required"]').is(':checked');
				new_fields_data[i].options = this_field.find('div.options ul li.option').map(function(){
					return $(this).find('input.option-item').val();
				}).get();
				
				var options_temp = new_fields_data[i].options.slice(); //Copy options
				if(has_duplicate_or_empty(options_temp)){ //if error, options_temps will contains error names
					var items = new_fields_data[i].options;
					for(j in items){ //For all options
						if($.inArray(items[j], options_temp) != -1) { //if each is found in error names -> error
							this_field.find('div.options ul li.option input.option-item').eq(j).addClass('form-error');
						}
					}
					verified = false;
				}
				names[i] = new_fields_data[i].name;
			}
			//Get fields in form to check duplicate
			submitting_arrays = $('ul.submitting.fields li').map(function(){
				return $(this).attr('class');
			}).get();
			
			var names_temp = names.slice().concat(submitting_arrays); //Copy names+submitting_arrays
			if(has_duplicate_or_empty(names_temp)){ //if error, names_temp will contains error names 
				for(j in names){ //For all names
					if($.inArray(names[j], names_temp) != -1) { //if each is found in error names -> error
						$('#custom-fields ul.fields li.field input[name="new-field"]').eq(j).addClass('form-error');
					}
				}
				verified = false;
			}
			if(!verified){
				//TODO show main error message
			} else {
				for(i in new_fields_data){
					//Add new_field into form
					new_field = $('div#no-submit li.template').clone().removeClass('template').addClass(new_fields_data[i].name).appendTo('ul.submitting.fields'); //Clone the template
					new_field.find('label.title').prepend(new_fields_data[i].label); //Show title
					new_field.find('div.inputs input.name').attr('name', new_fields_data[i].name+'[name]').val(new_fields_data[i].name); //Add name
					new_field.find('div.inputs input.type').attr('name', new_fields_data[i].name+'[type]').val(new_fields_data[i].type); //Add type
					new_field.find('div.inputs input.label').attr('name', new_fields_data[i].name+'[label]').val(new_fields_data[i].label); //Add label (title)
					new_field.find('label.required input.required:checkbox').attr('name', new_fields_data[i].name+'[required]'); //Add required
					if(new_fields_data[i].required){
						new_field.find('label.required input.required:checkbox').attr('checked', 'checked'); //Check required if is set
					}
					switch (new_fields_data[i].type){
						case 'radio':
						case 'checkbox':
							for(j in new_fields_data[i].options){ //If radio or checkbox, add items (options)
								option = new_fields_data[i].options[j];
								new_field.find('div.inputs').append('<label><input type="'+new_fields_data[i].type+'" name="'+new_fields_data[i].name+'"> '+option+'</input></label>'); //Show items
								new_field.find('div.inputs').append('<input class="items" type="hidden" name="'+new_fields_data[i].name+'[items][]" value="'+option+'"></input>'); //Add list of items
							}
						break;
						case 'textarea':
							new_field.find('div.inputs').append('<textarea></textarea>');
						break;
						case 'text':
							new_field.find('div.inputs').append('<input type="text"></input>');
						break;
						default:
							new_field.find('div.inputs').append('<input type="text"></input>');
						break;
					}
				}
			}
			return verified;
		}
		
		function add_more_option(obj){
			return $(obj).parent().prev().clone(true).insertBefore($(obj).parent());
		}
		
		function add_more_field(){
			$('#custom-fields ul.fields-template').clone().children('li').appendTo('#custom-fields ul.fields').find('input[type="text"][name="new-field"]').focus();
		}
		
		function has_duplicate_or_empty(arr){
			var has = false;
			var error_names = arr;
			
			if(arr.length == 1 && arr[0] === '') {
				has = true;
			} else {
				var sorted_arr = arr.slice();
				sorted_arr.sort();
				for (var i = 0; i < sorted_arr.length - 1; i += 1) {
					if (sorted_arr[i] === '' || sorted_arr[i + 1] == sorted_arr[i]) {
						if(has != true){ //Empty arr once
							error_names.splice(0,error_names.length);
							has = true;
						}
						error_names.push(sorted_arr[i]); //Add error names (linked to arr that is passed by reference)
					}
				}
			}
			return has;
		}
		
		function trigger_empty_field_message(){
			var field_count = $('form ul.submitting.fields li:not(.no-field)').length;
			if(field_count === 0){
				$('form ul.submitting.fields li.no-field').show();
			} else {
				$('form ul.submitting.fields li.no-field').hide();
			}
		}
	}

	function reward(){
		$('body').off()
			.on('click','.add-reward-item',add_reward)
			.on('click','.remove-reward-item',remove_reward)
			.on('click','.edit-reward-item',edit_reward);
		$('.tab.sort').unbind('click').click(sort_reward);
		trigger_countdown();

		function trigger_countdown(){
			$('.end-time-countdown').each(function(){
				end_time = Date.createFromMysql($(this).text());
				$(this).countdown({
					until: end_time,
					format: 'DHMS',
					layout: '{dn}days {hnn}h {sep} {mnn}m {sep} {snn}s'});
			});
		}
		
		function add_reward(){
			if($('.reward-item-form').length > 0) {
				return false;
			}
			if($('.notice').length > 0) {
				$('.notice').hide();
			}
			$.get(base_url+'settings/page_reward/add_item/'+page_id, function(data){
				var form_div = $(data).prependTo('.reward-item-list').removeClass('reward-item-template')
				.addClass('reward-item-form');
				
				// var form =
				form_events();
				function form_events(){
					var cancel = $('.btn.cancel').bind('click', cancel);
					function cancel(){
						form_div.remove();
						if($('.reward-item').length == 0) {
							$('.notice').show();
						}
					}
					if(form_div.find('form').length > 0){
						$('.start-date, .end-date').datepicker({
						dateFormat: "yy-mm-dd"});
						form_div.find('.btn.save').click(function(){
							$(this).parents('form').ajaxSubmit({
								success: function (result) {
									result = $(result);
									if(result.find('form').length == 0) {
										$('.reward-item-form').replaceWith(result);
										trigger_countdown();
									} else {
										$('.reward-item-form').html(result.find('form'));
										form_events();
									}
								}
							});
							return false;
						});
					} else {
						form_div.removeClass('reward-item-form');
					}
				}	
			});
		}

		function edit_reward(){
			var item = $(this).parents('.reward-item').hide();
			var item_id = item.data('itemId');
			$.get(base_url+'settings/page_reward/update_item/'+page_id, 
				{
					reward_item_id: item_id
				},
				function(data){
					var form_div = $(data).insertBefore(item).removeClass('reward-item-template')
					.addClass('reward-item-form');
					// var form =
					form_events();
					function form_events(){
						var cancel = $('.btn.cancel').bind('click', cancel);
						function cancel(){
							form_div.remove();
							item.show();
						}
						if(form_div.find('form').length == 0){
							return false;
						}
						$('.start-date, .end-date').datepicker({
							dateFormat: "yy-mm-dd"});
						form_div.find('.btn.save').click(function(){
							$(this).parents('form').ajaxSubmit({
								success: function (result) {
									result = $(result);
									if(result.find('form').length == 0) {
										$('.reward-item-form').replaceWith(result);
										trigger_countdown();
										item.remove();
									} else {
										$('.reward-item-form').html(result.find('form'));
										form_events();
									}
								}
							});
							return false;
						});
					}
				}
			);	
		}

		function remove_reward(){
			var item = $(this).parents('.reward-item');
			var item_id = item.data('itemId');
			answer = confirm("Delete this reward?");

			if(answer) {
				$.getJSON(
					base_url+'settings/page_reward/remove_item/'+page_id,
					{reward_item_id : item_id},
					function(data){
						if(data.success==true){
							item.remove();
						}
					}
				);
			}
		}

		function sort_reward(){
			$(this).addClass('active').siblings('.tab').removeClass('active');
			var sort = $(this).data('sort');
			if(!sort){ return false; }
			else {
				var query;
				switch(sort){
					case "value" : query = '?sort=value&order=desc';
					break;
					case "status" : query = '?sort=status';
					break;
					case "point" : query = '?sort=redeem.point&order=desc';
					break;
					case "date" : 
					default : query = '?sort=start_timestamp&order=desc';
					break;
				}
				$('.reward-item-list').load(base_url+'settings/page_reward/view/'+page_id+query+' .reward-item-list>*', trigger_countdown);
			}
			return false;
		}
	}

	function terms_and_conditions(){
		$('.terms-and-conditions .btn.save').click(function(){
			$(this).parents('form').ajaxSubmit({
				target:'#main #reward-terms-and-conditions',
				replaceTarget:true,
				success:terms_and_conditions
			});
			return false;
		});
	}

	function badges(){

	}

	function challenge() {
		$('body').off()
			.on('click', 'a.add-challenge', add_update_challenge)
			.on('click', 'a.update-challenge', add_update_challenge)
			.on('click', '.add-criteria', add_criteria)
			.on('click', '.remove-criteria', remove_criteria)
			.on('click', '.challenge-property-name', show_challenge_property);
		function add_update_challenge() {
			url = $(this).attr('href');
			$('div#main').load(url, add_challenge_form);

			function add_challenge_form(){
				$('.start-date, .end-date').datepicker({
					dateFormat: "yy-mm-dd"});
				$('#select_page').change(reload_apps);

				$('form.new-challenge-form').on('submit',function(e) {
					e.stopPropagation();
					$(this).ajaxSubmit({
						target:'#new-challenge-form',
						replaceTarget:true,
						success:add_challenge_form
					});
					return false;
				});

				function reload_apps() {
					var page_id = $('#select_page>select[name="select_page"]').val();
					if(page_id === '') {
						return;
					}
					$.post(base_url+'settings/page_challenge/ajax_get_page_apps',
						{page_id:page_id},
						function(data) {
							$('#select_app').html(data);
							$('#select_app').unbind('change').change(reload_actions);
						});

					$.post(base_url+'settings/page_challenge/ajax_get_app_actions',
						{},
						function(data) {
							$('#select_action').html(data);
						});
				}

				function reload_actions() {
					var app_id = $('#select_app select[name="select_app"]').val();
					if(app_id === '') {
						return;
					}
					console.log(app_id);
					$.post(base_url+'settings/page_challenge/ajax_get_app_actions',
						{app_id:app_id},
						function(data) {
							$('#select_action').html(data);
						});
				}
			}
			return false;
		}
		function remove_criteria() {
			$(this).parents('.criteria').remove();
		}
		function add_criteria() {
			var name = $('#name').val();
			var page_id = $('#select_page>select[name="select_page"]').val();
			var app_id = $('#select_app>select[name="select_app"]').val();
			var action_id = $('#select_action>select[name="select_action"]').val();
			var count = $('#count').val();
			if(name && page_id && app_id && action_id && count){
				var next_nth = $('.criteria[data-nth]:last').data('nth') + 1 ;
				var new_criteria = $('.criteria-template').clone()
					.removeClass('criteria-template').addClass('criteria')
					.data('nth', next_nth).show().appendTo('.criteria_list');
				new_criteria.find('.name').val(name).attr('name', 'criteria['+next_nth+'][name]');
				new_criteria.find('.page_id').val(page_id).attr('name', 'criteria['+next_nth+'][query][page_id]');
				new_criteria.find('.app_id').val(app_id).attr('name', 'criteria['+next_nth+'][query][app_id]');
				new_criteria.find('.action_id').val(action_id).attr('name', 'criteria['+next_nth+'][query][action_id]');
				new_criteria.find('.count').val(count).attr('name', 'criteria['+next_nth+'][count]');
			}
		}
		function show_challenge_property() {
			$(this).parent().addClass('active').siblings().removeClass('active');
		}
	}
	
	$('ul.platform-apps li a').live('click',function() {
		element = $(this);
		url = element.attr('href');
		// page_id = get_query(url, 'p');
		// config_name = get_query(url, 'c');
		set_loading();
		check_login(null,function(){
			$('div#main').load(url,function(){
				if( element.attr('id') == 'signup-fields'){
					signup_fields();
				} else if (element.attr('id') == 'reward'){
					reward();
				} else if (element.attr('id') == 'badges'){
					badges();
				} else if (element.attr('id') == 'reward-terms-and-conditions'){
					terms_and_conditions();
				} else if (element.attr('id') == 'challenge'){
					challenge();
				}
			});
			make_form(element);
		});
		
		return false;
	});
	
	$('ul.page-apps li a').live('click',function(){
		element = $(this);			
		url = element.attr('href');
		set_loading();
		check_login(null,function(){
			$('div#main').load(url, app_settings);
			make_form(element);
		});
		return false;
	});
	
	
	function make_form(element){
		$('form').die('submit');
		$('form').live('submit', function() {
			var targetSelector = 'div#main #'+$(this).attr('class');
			var srcSelector = '#'+$(this).attr('class');
			set_loading();
			$(this).ajaxSubmit({success:function(response){
				$(targetSelector).replaceWith($(response).filter(srcSelector));
			}});
			return false;
		});
	}
	
	if(config_name == 'signup_fields'){
		$('ul.platform-apps li a#signup-fields').click();
	} else if(config_name == 'badges'){
		$('ul.platform-apps li a#badges').click();
	} else if(config_name == 'reward'){
		$('ul.platform-apps li a#reward').click();
	} else if(config_name == 'user_class'){
		$('ul.platform-apps li a#user_class').click();
	} else if(config_name == 'challenge'){
		$('ul.platform-apps li a#challenge').click();
	} else if(config_name == 'app'){
		$('ul.page-apps li a.app[data-appinstallid="'+app_install_id+'"]').click();
	}
	



	function app_settings(){
		$('a.a-campaign-settings').click(app_campaign);
		$('a.a-homepage-settings').click(app_homepage);

		function app_campaign(){
			url = $(this).attr('href');

			$('div#main').load(url, function(){	
				$(this).off('click')
				.on('click', 'a.a-new-campaign', new_campaign)
				.on('click', 'a.a-update-campaign', update_campaign)
				.on('click', 'a.a-component-invite', invite_component)
				.on('click', 'a.a-component-sharebutton', sharebutton_component)
				.on('click', 'a.a-back-to-campaign-list', back_to_campaign_list)
				.on('click', 'a.a-back-to-app-settings', back_to_app_settings);
			});

			function new_campaign(){
				url = $(this).attr('href');
				$('div#main').load(url, new_campaign_form);

				function new_campaign_form(){
					set_campaign_range();
					$('form.new-campaign-form').on('submit',function(e) {
						e.stopPropagation();
						$(this).ajaxSubmit({target:'#new-campaign-form',replaceTarget:true,success:new_campaign_form});
						return false;
					});
				}
				return false;
			}

			function update_campaign(){
				url = $(this).attr('href');
				$('div#main').load(url, update_campaign_form);

				function update_campaign_form(){
					set_campaign_range();
					$('form.update-campaign-form').on('submit', function(e) {
						e.stopPropagation();
						$(this).ajaxSubmit({target:'#update-campaign-form',replaceTarget:true,success:update_campaign_form});
						return false;
					});
				}
				return false;
			}

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

			function invite_component(){
				url = $(this).attr('href');
				$('div#main').load(url, invite_component_form);
				
				function invite_component_form(){
					$('form.component-invite-form').on('submit', function(e) {
						e.preventDefault();
						$(this).ajaxSubmit({target:'#component-invite-form',replaceTarget:true,success:invite_component_form});
						return false;
					});
				}
				return false;
			}

			function sharebutton_component(){
				url = $(this).attr('href');
				$('div#main').load(url, sharebutton_component_form);
				
				function sharebutton_component_form(){
					$('form.component-sharebutton-form').on('submit', function(e) {
						e.preventDefault();
						$(this).ajaxSubmit({target:'#component-sharebutton-form',replaceTarget:true});
						return false;
					});
				}
				return false;
			}
			
			function back_to_campaign_list(){
				url = $(this).attr('href');
				$('div#main').load(url, app_campaign);
				return false;
			}
			return false;
		}


		function app_homepage(){
			url = $(this).attr('href');
			$('div#main').load(url, homepage_component_form);
			
			function homepage_component_form(){
				$(this).off('click').on('click', 'a.a-back-to-app-settings', back_to_app_settings);
				$('form.component-homepage-form').on('submit', function(e) {
					e.preventDefault();
					$(this).ajaxSubmit({target:'#component-homepage-form',replaceTarget:true,success:homepage_component_form});
					return false;
				});
			}
			return false;
		}

		function back_to_app_settings(){
			app_install_id = $(this).data('appInstallId');
			$('ul.page-apps li a.app[data-appinstallid="'+app_install_id+'"]').click();
			return false;
		}
	}
});