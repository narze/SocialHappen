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
					$('#default-fields').clone().appendTo($('#fancy'));
				}
			});
			
			//Add/remove select/deselect fields
			$('a.bt-add-these-field').click(function(){
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
				content: $('#custom-fields').clone().appendTo(this)
			});
			
			$('a.bt-add-these-custom-field').live('click', add_new_fields);
		});
		
		function add_new_fields(){
			var new_fields = $('#custom-fields ul li');
			for(i in new_fields){
				var this_field = $(new_fields[i]);
				var title = this_field.find('input[name="new-field"]').val();
				var type = this_field.find('input').attr('type');
				var required = this_field.find('input[name="new-field-required"]').val();
				var options = this_field.find('div.options').map(function(){
					
				});
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