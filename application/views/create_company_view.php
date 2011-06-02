<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Create company</title>
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
	<?php echo link_tag('css/smoothness/jquery-ui-1.8.9.custom.css'); ?>
	<script>
		$(function(){
			$('#create-company-form').load('<?php echo base_url().'home/create_company_form'; ?>');

			$('form').live('submit',function(){
				formData = $(this).serializeArray();
				$.post('<?php echo base_url().'home/create_company_form'; ?>',formData,function(returnData){
					$('#create-company-form').html(returnData);
				});
				return false;
			});
		});
	</script>
</head>
<body>
	<div id="create-company"><h2>Create Company</h2></div>
	<div id="create-company-form">
		
	</div>
</body>
</html>