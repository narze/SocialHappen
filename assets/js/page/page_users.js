$(function(){	
	function get_date(date)
	{
		//var M_names = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		var m_names = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

		var d = new Date(date);
		var curr_date = d.getDate();
		var curr_month = d.getMonth();
		var curr_year = d.getFullYear();
		return curr_date + " " + m_names[curr_month] + " " + curr_year;
	}
	
	function get_page_users(page_index, jq){
		set_loading();
		$.getJSON(base_url+'page/json_get_users/'+page_id+'/'+per_page+'/'+(page_index * per_page),function(json){ console.log(json);
			$('.wrapper-details-member.users .details table tbody tr.hidden-template').siblings().addClass('old-result');
			if(json.length == 0) {
				$('.wrapper-details-member.users .details').html(
					'<p>No user yet</p> <p><a>+ add new user</a> | <a>help</a></p>'
				);
			} else {
				for(i in json){
					var row = $('.wrapper-details-member.users .details table tr.hidden-template').clone()
					.removeClass('hidden-template')
					.appendTo('.wrapper-details-member.users .details table');

					var member_list = row.find('td.member-list div');
					member_list.find('p.thumb img').attr('src', imgsize(json[i].user_image,'square')).addClass('user-image');
					member_list.find('h2').append('<a href="'+base_url+'user/page/'+json[i].user_id+'/'+page_id+'">'+json[i].user_first_name+' '+json[i].user_last_name+'</a>');
					member_list.find('p a.view-fb').attr('href', 'http://www.facebook.com/profile.php?id='+json[i].user_facebook_id); //Facebook profile link

					row.find('td.last-active b').append( get_date(json[i].user_last_seen) ); //Last active
					row.find('td.joined-date b').append( get_date(json[i].user_register_date)); //Join since
					row.find('td.star-point b').append('0'); //star point
					row.find('td.happy-point b').append('0'); //happy point
					row.find('td.friends b').append('0'); //friends count					
					row.find('td.bt-icon a.icon-user-card').attr('href', base_url+'user/page/'+json[i].user_id+'/'+page_id);
					row.find('td.bt-icon a.bt-go').attr('data-pageuserid', json[i].user_id);
				}
				$('.wrapper-details-member.users .details table tr:even').addClass('next');
				
			}
			$('.old-result').remove();
		});
		
		if($('div.pagination-users').find('a').length == 0) {
			$('div.pagination-users').find('div.pagination').remove();
		}
		
		return false;
	}
	$('.tab-content ul li.users a').click(function(){
		$.getJSON(base_url+"page/json_count_users/"+page_id,function(count){
			$('.pagination-users').pagination(count, {
				items_per_page:per_page,
				callback:get_page_users,
				load_first_page:true,
				next_text:'>',
				prev_text:'<'
			});
		});
		return false;
	});
	
	$('.wrapper-details-member.users .details a.bt-go').live('click', function(){
		page_user_id = $(this).attr('data-pageuserid');
		$.fancybox({
			href: base_url+'page/json_get_page_user_data/'+ page_user_id + '/' + page_id
		});
		return false;
	});
});
