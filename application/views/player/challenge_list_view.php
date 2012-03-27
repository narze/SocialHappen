<?php echo link_tag('css/main.css'); ?>
<?php foreach($challenges as $challenge) : ?>
	<div class="challenge">
		<p><?php echo $challenge['detail']['name'];?></p>
		<p><?php echo anchor('player/challenge/'.$challenge['hash'], 'View');?></p>
		<p><?php echo anchor('r/c?hash='.$challenge['hash']);?></p>
	</div>
<?php endforeach; ?>