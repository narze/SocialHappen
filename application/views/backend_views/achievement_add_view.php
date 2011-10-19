<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Add new achievement</title>
	<?php echo link_tag('css/style.css'); ?>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
	<script>
		var criteriaStringNo = 0;
		var criteriaNo = 0;
		
		$(function(){
			appendCriteriaString();
			bindAddCriteriaString();
			
			bindAddCriteria();
			appendCriteria();
		});
		
		function bindAddCriteriaString(){
			$('input.addCriteriaString')
				.unbind('click')
				.click(function(){
					appendCriteriaString();
				});
		}
		
		function appendCriteriaString(){
			$('ol.criteriaString')
				.append('<li class="criteriaString '+criteriaStringNo+'"><input size="100" type="text" name="criteria_string['+criteriaStringNo+']" />'
				+'<input type="button" value="-" class="removeCriteriaString '+criteriaStringNo+'"/>'
				+'</li>');
			$('input.removeCriteriaString.'+criteriaStringNo)
				.click(function(){
					$('li.criteriaString.'+$(this).attr('class').split(' ')[1]).remove();
					return false;
				});
			criteriaStringNo++;
		}
		
		function bindAddCriteria(){
			$('input.addCriteria')
				.unbind('click')
				.click(function(){
					appendCriteria();
				});
		}
		
		function appendCriteria(){
			$('ol.criteria')
				.append('<li class="criteria '+criteriaNo+'">key: <input size="50" type="text" name="criteria_key['+criteriaNo+']" />'
				+' >= <input size="20" type="text" name="criteria_value['+criteriaNo+']" />'
				+'<input type="button" value="-" class="removeCriteria '+criteriaNo+'"/>'
				+'</li>');
			$('input.removeCriteria.'+criteriaNo)
				.click(function(){
					$('li.criteria.'+$(this).attr('class').split(' ')[1]).remove();
					return false;
				});
			criteriaNo++;
		}
	</script>
</head>
<body>
<h1>Add new achievement</h1>
<?php
echo form_open('backend/new_achievement_info');
?>
		<ul>
			<li>
				<?=form_label('Name*', 'name', array(
				'class' => 'name',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'name',
						  'id'          => 'name',
						  'value'       => set_value('name'),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('name')) ? 'error' : '',
				))?>
				<?php echo form_error('name'); ?>
			</li>
			
			<li>
				<?=form_label('Description*', 'description', array(
				'class' => 'description',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'description',
						  'id'          => 'description',
						  'value'       => set_value('description'),
						  'maxlength'   => '400',
						  'size'        => '200',
						  'class'		=> (form_error('description')) ? 'error' : '',
				))?>
				<?php echo form_error('description'); ?>
			</li>
			
			<li>
				<?=form_label('app_id*', 'app_id', array(
				'class' => 'app_id',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'app_id',
						  'id'          => 'app_id',
						  'value'       => set_value('app_id'),
						  'maxlength'   => '300',
						  'size'        => '50',
						  'class'		=> (form_error('app_id')) ? 'app_id' : '',
				))?>
				<?php echo form_error('app_id'); ?>
			</li>
			
			<li>
				<?=form_label('app_install_id', 'app_install_id', array(
				'class' => 'app_install_id',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'app_install_id',
						  'id'          => 'app_install_id',
						  'value'       => set_value('app_install_id'),
						  'maxlength'   => '300',
						  'size'        => '50',
						  'class'		=> (form_error('app_install_id')) ? 'app_install_id' : '',
				))?>
				<?php echo form_error('app_install_id'); ?>
			</li>
				
			<li>
				<?=form_label('page_id', 'page_id', array(
				'class' => 'page_id',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'page_id',
						  'id'          => 'page_id',
						  'value'       => set_value('page_id'),
						  'maxlength'   => '300',
						  'size'        => '50',
						  'class'		=> (form_error('page_id')) ? 'page_id' : '',
				))?>
				<?php echo form_error('page_id'); ?>
			</li>
			
			<li>
				<?=form_label('campaign_id', 'campaign_id', array(
				'class' => 'campaign_id',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'campaign_id',
						  'id'          => 'campaign_id',
						  'value'       => set_value('campaign_id'),
						  'maxlength'   => '300',
						  'size'        => '50',
						  'class'		=> (form_error('campaign_id')) ? 'campaign_id' : '',
				))?>
				<?php echo form_error('campaign_id'); ?>
			</li>
			
			<li>
				Criteria String: <input type="button" value="+" class="addCriteriaString"/>
				<ol class="criteriaString">
				</ol>
				<?php echo form_error('criteria_string[]'); ?>
			</li>
			
			<li>
				Criteria: <input type="button" value="+" class="addCriteria"/>
				<ol class="criteria">
				</ol>
				<?php echo form_error('criteria_value[]'); ?>
			</li>
		</ul>
		
		<div class="button-style">
		
		<p>
		<?php echo form_submit(array(
							'name' => 'submit', 
							'value' => 'Add',
							'class' => 'bt-create-now'
							)); ?> or <?php echo anchor('backend/achievements', 'Cancel');?>	
		</p>
		</div>
		<?=form_close()?>
</body>
</html>