<div id="signup-fields">
	<h2><span>SocialHappen Form</span></h2>
	<ul class="fields">
		<li>
			<div class="left">
				<label>First Name :</label>
				<div class="inputs"><input type="text"></div>
			</div>
		</li>
		<li>
			<div class="left">
				<label>Last Name :</label>
				<div class="inputs"><input type="text"></div>
			</div>
		</li>
		<li>
			<div class="left">
				<label>Email :</label>
				<div class="inputs"><input type="text"></div>
			</div>
		</li>
	</ul>
	<h2><span>Your Sign Up Form</span></h2>
	<?php echo form_open("configs/signup_fields/{$page['page_id']}", array('class' => 'signup-fields')); ?>
	
	<ul class="fields"><?php 
	if($fields)
	{
		foreach($fields as $key => $field)
		{ ?>
			<li class="<?php echo $key?>">
				<div class="left">
				<label><?php echo $field['label']; ?> :</label>
				<div class="inputs"><?php 

				if(isset($field['items'])) {
					foreach ($field['items'] as $item)
					{?>
						<input type="<?php echo $field['type'];?>" name="<?php echo $key; ?>"> <?php echo $item;?></input><br />
					<?php }
				} else {
				?>
					<input type="<?php echo $field['type'];?>"></input>
				<?php
				}
				?>
				</div>
				</div>
				<div class="right">
				<label><input type="checkbox" name="required" id="required-<?php echo $key?>" /> Required</label><span class="separator">|</span> 
				<a class="bt-remove-field">Remove</a>
				</div>
			</li> <?php
		}
	}
	else
	{ ?>
		<li class="no-field">to start adding more sign up field, click the button below</li><?php
	}?>
	</ul>
	
	<a href="#default-fields" id="bt-add-field-from-list" class="bt-add-field-from-list">Add field by choose from the list</a>
	<a class="bt-create-own-field">Create your own field</a>
	<?php 
	echo form_submit('submitForm', 'Submit', 'class="bt-update"');
	echo form_close(); ?>
	<div id="no-submit" style="display:none"><ul></ul></div>
	
</div>


<div id="fancy" style="display:none">
	<!-- Pop up default field-->
	<div id="default-fields">
		<h2>Add more field</h2>
		<p>Choose from the list below</p>
		<ul><?php
		foreach($fields as $field)
		{ ?>
			<li>
				<input type="checkbox" name="<?php echo $field['name']?>" id="<?php echo $field['name']?>" />
				<label for="<?php echo $field['name']?>"><?php echo $field['label']; ?></label>
			</li><?php
		} ?>
		</ul>
		<a class="bt-add-these-field">Add these fields to the sign up form</a>
	</div>
	
	<!-- Pop up custom field-->
	<div id="custom-fields">
		<h2>Create your own field</h2>
		<ul class="fields">
			<li>
				<div class="left">
				<label>Field title: </label><input type="text" name="new-field" />
				<label>Field type</label> 
					<select name="field-type">
						<option value='text' selected="selected">Text</option>
						<option value='textarea'>Paragraph Text</option>
						<option value='radio'>Radio Button</option>
						<option value='checkbox'>Checkbox</option>
					</select>
				</div>
				<div class="right">
				<input type="checkbox" name="new-field-required" /> <label>Required</label> | 
				<a class="bt-remove-field">Remove</a>
				<div class="options">
					<input type="text" value="Option 1" /><br />
					<input type="text" value="Click to add option" disabled="disabled" /> or add <a class="add-other">"Other"</a>
				</div>
				</div>
			</li>
		</ul>
		<a class="add-more-field">Add more field</a>
		
		<p style="text-align:center">
			<a class="bt-add-these-custom-field">Add this fields the sign up form</a>
		</p>
	</div>
</div>