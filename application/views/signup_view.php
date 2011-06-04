<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Sign Up</title>
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
	<?php echo link_tag('css/smoothness/jquery-ui-1.8.9.custom.css'); ?>
	<script>
		$(function(){
			$('#signup-form').load('<?php echo base_url().'signup/form'; ?>');

			$('form').live('submit',function(){
				formData = $(this).serializeArray();					
				$.post('<?php echo base_url().'signup/form'; ?>',formData,function(returnData){
					$('#signup-form').html(returnData);
				});
				return false;
			});
		});
	</script>
</head>
<body>
	<div id="header"><h2>Header</h2></div>
	<div id="tutorial"><h2>Tutorial</h2></div>
	<div id="signup-form">
		
	</div>
</body>
</html>