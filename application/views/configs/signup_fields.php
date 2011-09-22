<div id="signup-fields">
	<h2><span>Sign Up Form</span></h2>
	<?php echo form_open("configs/signup_fields/{$page['page_id']}", array('class' => 'signup-fields')); ?>
	
	<ul><?php 
	if($fields)
	{
		foreach($fields as $field)
		{ ?>
			<li id="field-<?php echo $field['field_id']?>">
				<label><?php echo $field['label']; ?>: </label><?php 
				foreach ($field['inputs'] as $input)
				{
					echo $input;
				}?>
				<input type="checkbox" name="required" id="required-<?php echo $field['field_id']?>" /> <label for="required-<?php echo $field['field_id']?>">Required</label> | 
				<a class="bt-remove-field">Remove</a>
			</li> <?php
		} 
	} 
	else
	{ ?>
		<li class="no-field">to start adding more sign up field, click the button below</li><?php
	}?>
	</ul>
	
	<a class="bt-add-field-from-list">Add field by choose from the list</a>
	<a class="bt-create-own-field">Create your own field</a>
	<?php 
	echo form_submit('submitForm', 'Submit', 'class="bt-update"');
	echo form_close(); ?>
	
</div>

<!-- Pop up default field-->
<div id="default-fields" style="display:none">
	<h2>Add more field</h2>
	<p>Choose from the list below</p>
	<ul><?php
	foreach($default_fields as $field)
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
<div id="custom-fields" style="display:none">
	<h2>Create your own field</h2>
	<ul>
		<li>
			<label>Field title: </label><input type="text" name="new-field" />
			<label>Field type</label> 
				<select name="field-type">
					<option selected="selected">Text</option>
					<option>Paragraph Text</option>
					<option>Multiple choices</option>
					<option>Check box</option>
					<option>Dropdown</option>
				</select>
			<input type="checkbox" name="new-field-required" /> <label>Required</label> | 
			<a class="bt-remove-field">Remove</a>
			<div class="options">
				<input type="text" value="Option 1" /><br />
				<input type="text" value="Click to add option" disabled="disabled" /> or add <a class="add-other">"Other"</a>
			</div>
		</li>
	</ul>
	<a class="add-more-field">Add more field</a>
	
	<p style="text-align:center">
		<a class="bt-add-these-field">Add this fields the sign up form</a>
	</p>
</div>