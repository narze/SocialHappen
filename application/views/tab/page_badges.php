<?php if($header) { ?>
<div class="page-badges-header">
	<a href="<?php echo $page['facebook_tab_url']; ?>" class="page-image" style="background-image:url(<?php echo $page['page_image']; ?>);"></a>
	<p class="mt25"><?php 
	if($page['achieved_badges']>0) 
	{
		echo 'You collected <b>'.($page['achieved_badges']>1 ? $page['achieved_badges'].' badges' : $page['achieved_badges'].' badge').'</b> on';
	
	} else {
		echo 'You got no badges on';
	} ?>
	<a href="<?php echo $page['facebook_tab_url']; ?>" class="page-name tc-blue3 bold"><?php echo $page['page_name']; ?></a>
	<a class="back-to-user-badges underline fr mr10">Back</a>
	</p>
</div>
<?php } ?>
<div class="page-badges-list"><?php //echo '<pre>'; print_r($pages); echo '</pre>'
	if(count($page['badges']) > 0) 
	{ 
		foreach ($page['badges'] as $badge) {
			if($badge['info']['enable']) 
			{ ?>
				<div class="badge">
					<div class="left-col mt15">
						<img class="badge-icon <?php echo $badge['info']['achieved']? 'achieved' : ''; ?>" src="<?php echo $badge['info']['badge_image']; ?>" title="<?php echo $badge['info']['name']; ?>" />
						<p class="badge-name bold fs14 mt10"><?php echo $badge['info']['name']; ?></p>
						<p class="badge-point">30 Points</p>
						<p class="badge-awarded">2,500 Awarded</p>
					</div>
					<div class="right-col mt15 ml10">
						<p class="bold mb5">Goal check list</p>
						<p><?php echo $badge['info']['description']; ?></p>
					</div>
				</div><?php
			}
		}
	}
	else 
	{ ?>
		<p class="ta-center mt15">This page has no badges</p><?php 
	} ?>
</div>