<?php echo link_tag('css/main.css'); ?>
<?php foreach($challenges as $challenge) : ?>
	<div class="challenge">
		<p><?php echo $challenge['detail']['name'];?></p>
		<p><?php echo anchor('player/challenge/'.$challenge['_id'], 'View');?></p>
	</div>
<?php endforeach; ?>

<?php
echo '<pre>';
var_dump($challenges);
echo '</pre>';