<ul><?php 
	foreach($activities as $activity) 
	{
		if(isset($activity['user_id'])) 
		{ ?>
		  <li>
			<div>
			  <p class="pic"><img src="<?php echo imgsize(issetor($activity['user_image'], base_url().'assets/images/default/user.png'),'square');?>" alt=""></p>
			  <h2><?php echo issetor($activity['user_name'], 'Unknown');?></h2>
			  <p class="desc"><?php echo $activity['message'];?></p>
			  <p>
				<span class="timeago"><?php echo issetor($activity['timestamp']) ;?></span>
				<?php echo isset($activity['source']) ? 'via '.$activity['source'] : ''; ?>
			  </p>
			</div>
			<?php if(isset($activity['star_point'])):?>
				<div>
				  <p class="star">x <?php echo $activity['star_point'];?></p>
				</div>
			<?php endif;?>
		  </li><?php 
		} 
	} ?>
</ul>