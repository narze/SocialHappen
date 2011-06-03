<hr />
header
<div id="logo"><h2><a href="<?php echo base_url();?>home">SocialHappen</a></h2></div>
<div id="goto"><h2><a href="#">Go to</a></h2><div id="goto-list"></div></div>
<div id="user">
	<h2><a href="#"><?php echo $user->user_first_name.' '.$user->user_last_name;?></a></h2>
	<div id="user-list">
		<div id="profile-setting"><?php echo anchor('path/to/profilesetting','Profile Setting');?></div>
		<div id="logout"><?php echo anchor('home/logout','Logout');?></div>
	</div>
</div>
end of header
<hr />
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script>
	$(function(){
		
		//Go to button
		$('#goto').one('click',function(){
			<?php foreach($user_companies as $company) : ?>
			$.ajaxSetup({'async': false});
			$.getJSON('<?php echo base_url().'company/json_get_profile/' . $company->company_id; ?>', function(data) {
				$.each(data, function(i,item){
					$('#goto-list').append('<div class="goto-list-company-<?php echo $company->company_id;?>">===Company : '+item.company_name+'</div>');
				});
			});
			$.getJSON('<?php echo base_url().'company/json_get_pages/' . $company->company_id; ?>', function(data) {
				$.each(data, function(i,item){
					$('.goto-list-company-<?php echo $company->company_id;?>').append('<div class="goto-list-company-page-'+item.page_id+'">======Page : '+item.page_name+'</div>');
				});
			});
			<?php endforeach; ?>
			$('#goto').bind('click',function(){
				$('#goto-list').toggle();
			});
		});
	
		//User button
		$('#user-list').hide();
		$('#user').click(function(){
			$('#user-list').toggle();
		});
	});
</script>