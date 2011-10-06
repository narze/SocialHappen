	<h2><span><?php echo $user['user_first_name'].' '.$user['user_last_name']; ?>'s Information</span></h2>
	<div class="user-info white-box-01">
		<fieldset>
			<legend>Perosnal Information</legend>
			<ul>
				<li><label>Firstname :</label><?php echo $user['user_first_name']; ?></li>
				<li><label>Lastname :</label><?php echo $user['user_last_name']; ?></li>
				<li><label>Email :</label><?php echo issetor($user['user_email'], '-'); ?></li>
			</ul>
		</fieldset><?php
			
		if(isset($user_data))
		{ ?>
			<fieldset>
				<legend>Additional Information</legend>
				<ul><?php
					foreach($user_data as $key=>$field) { ?>
					<li><label><?php echo ucfirst(str_replace('_', ' ', $key)); ?> :</label><?php 
						if (is_array($field)) {
							echo implode(', ', $field);
						} else {
							echo issetor(nl2br($field), '-');
						} ?>
					</li><?php
					} ?>
				</ul>
			</fieldset><?php				
		} ?>
	</div>
