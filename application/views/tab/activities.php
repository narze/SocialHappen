<ul>
	<?php foreach($activities as $activity) :?>
  <li>
	<div>
	  <p class="pic"><img src="<?php echo imgsize(issetor($activity['user_image']),'square');?>" alt=""></p>
	  <p><?php echo $activity['message'];?></p>
	  <?php if(isset($activity['time_ago']) && isset($activity['source'])) : ?> 
	  <p><span><?php echo $activity['time_ago'];?> via <?php echo $activity['source'];?></span></p>
	  <?php endif; ?>
	</div>
	<?php if(isset($activity['star_point'])):?>
		<div>
		  <p class="star">x <?php echo $activity['star_point'];?></p>
		</div>
	<?php endif;?>
  </li>
  <?php endforeach;?>
</ul>