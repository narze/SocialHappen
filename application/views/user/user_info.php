	<h2><span><?php echo $user['user_first_name'].' '.$user['user_last_name']; ?>'s Information</span></h2>
	<div class="user-info white-box-01">
		<ul>
			<li><label>Firstname :</label><?php echo $user['user_first_name']; ?></li>
			<li><label>Lastname :</label><?php echo $user['user_last_name']; ?></li>
			<li><label>Email :</label><?php echo issetor($user['user_email'], '-'); ?></li><?php
			if(isset($user_data))
			{
				foreach($user_data as $key=>$field) { ?>
				<li><label><?php echo ucfirst($key); ?> :</label><?php 
					if (is_array($field)) {
						echo implode(', ', $field);
					} else {
						echo issetor(nl2br($field), '-');
					} ?>
				</li><?php
				} 
			}?>
		</ul>
	</div>
