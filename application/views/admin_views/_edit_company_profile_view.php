<!DOCTYPE html>
<html lang="en">
<head> 
	<meta charset="utf-8">
	<title>Edit company proile</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Edit company proile</h1>
<?php
echo form_open('company/edit_company_profile');
?>

<p>
<?php
echo form_label('Company name: ', 'company_name');
$data = array(
              'name'        => 'company_name',
              'id'          => 'company_name',
              'value'       => $form['company_name'],
              'maxlength'   => '250',
              'size'        => '100',
            );

echo form_input($data); ?>
</p>

<p>
<?php
echo form_label('Address: ', 'company_address');
$data = array(
              'name'        => 'company_address',
              'id'          => 'company_address',
              'value'       => $form['company_address'],
              'rows'		=>	'5',
              'cols'		=>	'100',
            );

echo form_textarea($data); ?>
</p>

<p>
<?php
echo form_label('Email: ', 'company_email');
$data = array(
              'name'        => 'company_email',
              'id'          => 'company_email',
              'value'       => $form['company_email'],
              'maxlength'   => '250',
              'size'        => '100',
            );

echo form_input($data); ?>
</p>

<p>
<?php
echo form_label('Telephone: ', 'company_telephone');
$data = array(
              'name'        => 'company_telephone',
              'id'          => 'company_telephone',
              'value'       => $form['company_telephone'],
              'maxlength'   => '250',
              'size'        => '100',
            );

echo form_input($data); ?>
</p>

<p>
<?php
echo form_submit('submit', 'Save');
?> or <?php echo anchor('/company/dashboard', 'Cancel', 'title="cancel editing company"');?>
</p>

<?php echo form_close(); ?>
</body>
</html>