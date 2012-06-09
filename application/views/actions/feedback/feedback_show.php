<div class="well">
	<h1>User Feedback of <?php echo $action_data_name; ?></h1>
	<p>
		<div class="feedback_action_user_data_container">
		</div>

		<div class="feedback_action_user_data_template" style="display:none">
			<div class="user_data_wrapper" style="padding-bottom:5px;">
				<div><span style="font-weight:bold">User: </span><span id="user"></span></div>
				<div><span style="font-weight:bold">User Feedback: </span><span id="user_feedback"></span></div>
				<div><span style="font-weight:bold">User Score: </span><span id="user_score"></span></div>
				<div><span style="font-weight:bold">Timestamp: </span><span id="timestamp"></span></div>
			</div>
		</div>
	</p>
	<div style="display:none;">
		<div class="step" data-direction="prev" style="display:inline-block;cursor:pointer">Prev</div>
		<div style="display:inline-block;"> <span class="current_item"></span>/<span class="total_item"></span> </div>
		<div class="step" data-direction="next" style="display:inline-block;cursor:pointer">Next</div>
	</div>
	<p>
		<div style="display:inline-block;"> <span class="current_item"></span>/<span class="total_item"></span> </div>
		<div class="step" data-direction="more" style="display:inline-block;cursor:pointer">More</div>
	</p>
</div>

<script>
	var action_user_data_id_array = <?php echo $action_user_data_id_array ?>;
	var base_url = '<?php echo base_url() ?>';
</script>