<ul>
	<?php foreach($activities as $activity) :?>
  <li>
	<div>
	  <p class="pic"><img src="<?php echo imgsize(issetor($page_activity['user_image']),'square');?>" alt=""></p>
	  <h2><?php echo $activity['user_name'];?></h2>
	  <p><?php echo $activity['activity_detail'];?></p>
	  <p><span><?php echo $activity['time_ago'];?> via <?php echo $activity['source'];?></span></p>
	</div>
	<?php if($activity['star_point']):?>
		<div>
		  <p class="star">x <?php echo $activity['star_point'];?></p>
		</div>
	<?php endif;?>
  </li>
  <?php endforeach;?>
</ul>