	<h2><span><?php echo $user['user_first_name'].' '.$user['user_last_name']; ?>'s Information</span></h2>
	<div class="user-info white-box-01">
		<ul>
			<li><label>Firstname :</label><?php echo $user['user_first_name']; ?></li>
			<li><label>Lastname :</label><?php echo $user['user_last_name']; ?></li>
			<li><label>Email :</label><?php echo issetor($user['user_email'], '-'); ?></li>
			<li><label>Gender :</label><?php echo issetor($user['user_gender'], '-'); ?></li>
			<li><label>Date of Birth :</label><?php echo issetor($user['user_birth_date'], '-'); ?></li><?php
			foreach($user['user_data'] as $key=>$field) { ?>
			<li><label><?php echo ucfirst($key); ?> :</label><?php echo issetor($field, '-'); ?></li><?php
			} ?>
		</ul>
	</div>
