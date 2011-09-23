$(function(){
	
	function signup_fields(){
		$('form.signup-fields ul li a.bt-remove-field').live('click', function(){
			$(this).parents('form ul li').appendTo('div#no-submit ul');
		});
		
		$('a.bt-add-field-from-list').live('click', function(){
			//Uncheck all and check only showing fields
			$('#default-fields li input:checkbox').attr('checked', false);
			var fields = $('form.signup-fields ul li').map(function(){
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
				var selected = $('#default-fields li input:checkbox:checked').map(function(){
					return this.name;
				}).get();
				for(i in selected){
					$('div#no-submit ul li.'+selected[i]).appendTo('form ul');
				}
				
				var deselected = $('#default-fields li input:checkbox:not(:checked)').map(function(){
					return this.name;
				}).get();			
				for(i in deselected){
					$('form ul li.'+deselected[i]).appendTo('div#no-submit ul');
				}
			});
		});
	
		$('a.bt-create-own-field').live('click', function(){
			$.fancybox({
				content: $('#custom-fields').appendTo(this),
				onCleanup: function(){ 
					$('#custom-fields').clone().appendTo($('#fancy')); //Clone to make ux better, it'll be remove by fancybox
			});
			
			$('#custom-fields ul li div.options ul li.add-option input').click(function(){
				add_more_option(this).find('input[type="text"]').val('').focus();
			});
			$('#custom-fields ul li div.options ul li.add-option a.add-other').click(function(){
				add_more_option(this).find('input[type="text"]').val('Other');
			});
			$('#custom-fields ul li div.options ul li a.bt-remove-option').live('click',function(){
				if($(this).parent().siblings('li.option').length > 0){ //if at least one option left
					$(this).parent().remove(); //remove option
				}
			});
			
			$('#custom-fields ul li select[name="field-type"]').live('change', function(){
				var selected_type = $(this).find('option:selected').val();
				var old_value = $(this)
				switch (selected_type){
					case 'radio':
						$(this).parents('#custom-fields ul li').find('div.options').show().find('input.option-type').replaceWith('<input type="radio" name="option-type-example" class="option-type" />');
					break;
					case 'checkbox':
						$(this).parents('#custom-fields ul li').find('div.options').show().find('input.option-type').replaceWith('<input type="checkbox" name="option-type-example" class="option-type" />');	
					break;
					case 'textarea':
						$(this).parents('#custom-fields ul li').find('div.options').hide();
					break;
					case 'text':
					default:
						$(this).parents('#custom-fields ul li').find('div.options').hide();
					break;
				}
			});
			
			$('#custom-fields ul li a.bt-remove-field').live('click',function(){
				if($(this).parents('li.field').siblings('li.field').length == 0){ //if no option left
					add_more_field();
				}$(this).parents('li.field').remove();
			});
			$('#custom-fields a.add-more-field').click(add_more_field);
			$('#custom-fields p.apply a.bt-apply-to-signup-form').click(function (){ alert('aa');
				add_new_fields();
				$.fancybox.close();
			});
		});
		
		function add_new_fields(){
			var new_fields = $('#custom-fields ul.fields li.field').get();
			for(i in new_fields){console.log(new_fields[i]);
				var this_field = $(new_fields[i]);
				var data = new Object();
				data.title = this_field.find('input[name="new-field"]').val();
				data.name = data.title; //TODO : underscore & lowercase
				data.type = this_field.find('input select[name="field-type"] option:selected').val();
				data.required = this_field.find('input[name="new-field-required"]').val();
				data.options = this_field.find('div.options ul li.option').map(function(){
					//TODO : get options
				});
				console.log(data);
			}
		}
		
		function add_more_option(obj){
			return $(obj).parent().prev().clone(true).insertBefore($(obj).parent()); 
		}
		
		function add_more_field(){
			$('#custom-fields ul.fields-template').clone().children('li').appendTo('#custom-fields ul.fields').find('input[type="text"][name="new-field"]').focus();
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