var current_item = 0;
var total_item = 0;
var items_per_time = 5;

get_action_user_data = function(start_item_no, end_item_no){
	var action_user_data_id = new Array;
	action_user_data_id = action_user_data_id_array.slice(start_item_no, end_item_no);
	//console.log(action_user_data_id);
	$.ajax({
		type: 'POST',
		url: base_url+'actions/feedback/read_action_user_data/',
		data:{
			action_user_data_id : action_user_data_id
			},
		dataType: 'json',
		success: function(data){
			//console.log(data);
			if(data.result == 'ok'){

				action_user_data = data.data;
				
				
				for(x in action_user_data){
					//console.log(x);
					feedback_action_user_data = jQuery('.feedback_action_user_data_template').clone();

					jQuery(feedback_action_user_data).find('#user_feedback').html(action_user_data[x].user_data.user_feedback);
					jQuery(feedback_action_user_data).find('#user_score').html(action_user_data[x].user_data.user_score);
					jQuery(feedback_action_user_data).find('#timestamp').html(action_user_data[x].user_data.timestamp);
					jQuery(feedback_action_user_data).find('#user').html(action_user_data[x].user.user_first_name + ' ' + action_user_data[x].user.user_last_name);

					feedback_action_user_data = jQuery(feedback_action_user_data).children();
					/*
					//single feedback per time -> clear container
					jQuery('.feedback_action_user_data_container').append(feedback_action_user_data); 
					*/
					//multiple feedback per time -> append container
					jQuery('.feedback_action_user_data_container').append(feedback_action_user_data);
					feedback_action_user_data = null;
					
				}

				
			}
			

		}
	});

}

step_action_user_data = function(direction){
	if(direction == 'prev'){
		if(current_item > 0){
			current_item--;
			get_action_user_data(current_item, current_item+1);
		}
	}else if(direction == 'next'){
		if(current_item < action_user_data_id_array.length - 1 ){
			current_item++;
			get_action_user_data(current_item, current_item+1);
		}
		
	}else if(direction == 'more'){
		if(current_item < action_user_data_id_array.length  ){
			
			get_action_user_data(current_item, current_item + items_per_time);
			current_item = current_item + items_per_time;
		}
		
	}

	
	console.log(current_item+ ' ' + total_item);
	if(current_item > total_item){
		jQuery('.current_item').html(total_item);

	}else{
		jQuery('.current_item').html(current_item);
	}

}

jQuery(document).ready(function(){
	
	/*
	for(x in action_user_data_id_array){
		console.log(action_user_data_id_array[x]);

	}*/

	jQuery('.step').click(function(){
		step_action_user_data(jQuery(this).attr('data-direction'));
	});

	get_action_user_data(current_item, current_item + items_per_time);
	current_item = current_item + items_per_time;

	jQuery('.current_item').html(current_item);
	total_item = action_user_data_id_array.length;
	jQuery('.total_item').html(action_user_data_id_array.length);
});