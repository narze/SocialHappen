$(function(){
	
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
			for(i in fields){
				$('#default-fields li input:checkbox[name="'+fields[i]+'"]').attr('checked', true);
			}
			
			//Call fancybox and show checkboxes
			$.fancybox({
				content: $('#default-fields').appendTo(this),
				onCleanup: function(){ 
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
				for(i in selected){
					if($('form ul li.'+selected[i]).length == 0){ //if not exists
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
				var old_value = $(this)
				switch (selected_type){
					case 'radio':
					case 'checkbox':
						$(this).parents('#custom-fields ul li').find('div.options').show().find('input.option-type').replaceWith('<input type="'+selected_type+'" name="option-type-example" class="option-type" />');
					break;
					case 'textarea':
					case 'text':
					default:
						$(this).parents('#custom-fields ul li').find('div.options').hide();
					break;
				}
			});
			
			//Remove a field
			$('#custom-fields ul li a.bt-remove-field').die().live('click',function(){
				if($(this).parents('li.field').siblings('li.field').length == 0){ //if no option left
					add_more_field();
				}$(this).parents('li.field').remove();
			});
			
			//Add a field
			$('#custom-fields a.add-more-field').click(add_more_field);
			
			//Apply custom fields
			$('#custom-fields p.apply a.bt-apply-to-signup-form').click(function (){ 
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
			var new_fields_data = new Array();
			var names = new Array();
			var verified = true;
			for(i=0; i<new_fields.length; i++){ //For all new custom fields to add
				var this_field = new_fields.eq(i);
				new_fields_data[i] = new Object();
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
			
			if(arr.length == 1 && arr[0] == '') {
				has = true;
			} else {
				var sorted_arr = arr.slice();
				sorted_arr.sort();
				for (var i = 0; i < sorted_arr.length - 1; i += 1) {
					if (sorted_arr[i] == '' || sorted_arr[i + 1] == sorted_arr[i]) {
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
			if(field_count == 0){
				$('form ul.submitting.fields li.no-field').show();
			} else {
				$('form ul.submitting.fields li.no-field').hide();
			}
		}
	}
	
	$('ul.platform-apps li a').live('click',function(){
		element = $(this);			
		url = element.attr('href');
		page_id = get_query(url, 'p');
		config_name = get_query(url, 'c');
		set_loading();
		check_login(null,function(){
			$('div#main').load(base_url+"configs/"+config_name+"/"+page_id,function(){
				if( config_name == 'signup_fields'){
					signup_fields();
				}
			});
			make_form(element);
		});
		
		return false;
	});
	
	$('ul.page-apps li a').live('click',function(){
		element = $(this);			
		url = element.attr('href');
		app_install_id = get_query(url, 'id');
		set_loading();
		check_login(null,function(){
			$('div#main').load(base_url+"configs/app/"+app_install_id);
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
	} else if(config_name == 'app'){
		$('ul.page-apps li a.app[data-appinstallid="'+app_install_id+'"]').click();
	}
	

});