<div class="user-badges-list">
<?php //echo '<pre>'; print_r($pages); echo '</pre>'
	if($pages) 
	{
		foreach ($pages as $page) 
		{ ?>
			<div class="page">
				<a href="<?php echo $page['facebook_tab_url']; ?>" class="page-image" style="background-image:url(<?php echo $page['page_image']; ?>);"></a>
				<a href="<?php echo $page['facebook_tab_url']; ?>" class="page-name tc-blue3 bold inline-block"><?php echo $page['page_name']; ?></a><?php 
				if(count($page['badges']) > 0) 
				{ ?>
					<p><?php
						if($page['achieved_badges']>0) 
						{ 
							echo 'You collected <a class="next tc-green6 bold underline" data-page-id="'. $page['page_id']. '" >'.($page['achieved_badges']>1 ? $page['achieved_badges'].' badges' : $page['achieved_badges'].' badge').'</a> on this page';
						} else {
							echo 'You got no badges';
						} ?>
					</p>
					<?php foreach ($page['badges'] as $badge) {
						if($badge['info']['enable']) 
						{ ?>
							<div class="inline-block mt5">
								<img class="badge-icon <?php echo $badge['info']['achieved']? 'achieved' : ''; ?>" src="<?php echo $badge['info']['badge_image']; ?>" title="<?php echo $badge['info']['name']; ?>" />
							</div><?php
						}
					}?>
					<a class="icon next abs-r" data-page-id="<?php echo $page['page_id']; ?>"></a><?php
				} 
				else 
				{ ?>
					<p>This page has no badges</p><?php 
				} ?>
			</div><?php 
		}
	} ?>
</div>