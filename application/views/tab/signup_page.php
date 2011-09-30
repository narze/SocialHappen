<div class="popup-fb-signup-page">
    <div>
		<div></div>
      <div id="signup-form">

<?php // Change the css classes to suit your needs    
		$attributes = array('class' => 'signup-form', 'id' => '');
		echo form_open("tab/signup_page/{$page_id}", $attributes); ?>
        <div class="profile">
          <p><?php echo $facebook_user['first_name'];?><span><?php echo $facebook_user['last_name'];?></span></p>
          <p class="thumb">
				<img src="<?php echo imgsize($user_profile_picture, 'square');?>" />
		  </p>
        </div>
		<br class="clear" />
        <div class="form">
          <h2>Page register</h2>
          <ul>
		  <label>Socialhappen User</label><input type="text" name="sh_name" disabled="true" value="<?php echo $user['user_first_name'].' '.$user['user_last_name'];?>"/>
		  <input type="hidden" name="empty" value="0" />
		  <?php foreach($page_user_fields as $user_fields) : ?>
			<li>
			<?php
				
				
				if($user_fields['required']) {
					echo ' REQUIRED ';
				}
				echo "<br />";
				echo "<label ".(form_error($user_fields['name'])? 'class="error" ':'').">".form_label($user_fields['label'])."</label>";
				switch($user_fields['type']){
					case 'radio':
						foreach($user_fields['items'] as $item){
							echo "{$item} :" . form_radio($user_fields['name'], $item, set_value($user_fields['name']));
						}
					break;
					case 'checkbox':
						$checked_list = array();
						while($checked = set_value($user_fields['name'])){
							$checked_list[] = $checked;
						}
						foreach($user_fields['items'] as $item){	
							echo "{$item} :" . form_checkbox($user_fields['name']."[]", $item, in_array($item, $checked_list));
							
						}
					break;
					case 'textarea':
						echo form_textarea(array(
							'name'        => $user_fields['name'],
							'id'          => $user_fields['name'],
							'value'		  => set_value($user_fields['name']),
							'size'        => '50',
							'rows'		  => '3',
							'cols'		  => '36'
						));
					break;					
					case 'text':
					default:
						echo form_input(array(
							'name'        => $user_fields['name'],
							'id'          => $user_fields['name'],
							'value'		  => set_value($user_fields['name']),
							'size'        => '50',
						));
				}
				?>
			</li>
          <?php endforeach; ?>
		  </ul>
          <p>
				<a class="bt-register-now"><span>Register now</span></a>
				
		  </p>
        </div>
<?php echo form_close(); ?>

		
      </div>
    </div>
</div>