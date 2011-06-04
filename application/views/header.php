<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="description" content="SocialHappen" />

<title>SocialHappen<?php if (isset($title)) { echo " - $title"; }?></title>
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
<script>
	$(function(){
		
		//Go to button
		$('#goto').one('click',function(){
			<?php foreach($user_companies as $company) : ?>
			$.ajaxSetup({'async': false});
			$.getJSON('<?php echo base_url().'company/json_get_profile/' . $company['company_id']; ?>', function(data) {
				$.each(data, function(i,item){
					$('#goto-list').append('<div class="goto-list-company-<?php echo $company['company_id'];?>">===Company : '+item.company_name+'</div>');
				});
			});
			$.getJSON('<?php echo base_url().'company/json_get_pages/' . $company['company_id']; ?>', function(data) {
				$.each(data, function(i,item){
					$('.goto-list-company-<?php echo $company['company_id'];?>').append('<div class="goto-list-company-page-'+item.page_id+'">======Page : '+item.page_name+'</div>');
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
		
		<?php if(isset($script)) { echo $script; } ?>
	});
</script>
</head>
<body>
	<div id="container">
		<div id="header">
			<?php $this->load->view('bar_view'); ?>
		</div>