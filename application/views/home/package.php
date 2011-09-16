<div class="wrapper-package">
	
	<div class="wrapper-details">
		<div class="packages-intro-text">
			<?php if($user_current_package_id) { ?><a class="bt-package-setting fr" href="<?php echo base_url().'settings?s=package&id='.$user_id;?>">Package Setting</a><?php } ?>
			<h2>Choose the package that's best for your needs</h2>
			<p>You can updrade at any time.</p>
		</div>
	
		<ul id="package_key">				
			<li class="package_price"><p>Feature</p></li>
			<li class="package_companies feature"><p>Companies</p></li>
			<li class="package_pages feature"><p>Pages</p></li>
			<li class="package_users feature"><p>Members</p></li>
			<li class="package_apps feature"><p>Applications</p></li>
			<li class="package_stat feature"><p>Members' Stat</p></li>
			<li class="package_detail feature"><p>Package Details</p></li>
		</ul>
		<div class="packages">
			<?php $i=1; $last_package = count($packages);
			foreach($packages as $package) 
			{
				
				if($user_current_package_id == $package['package_id']) $selected = true;
				else $selected = false;
				
				?>  
				<ul id="package-<?php echo $package['package_id']; ?>" class="package<?php if($selected) echo ' selected'; if($i == $last_package) echo ' last-child'; ?>">
					<li class="package_name">
						<p>
							<?php echo $package['package_name']; ?>
							<?php if(!$selected && $package['package_price'] >= $user_current_package_price) { ?><a class="bt-pick payment-pop" href="<?php echo base_url().'home/package?package_id='.$package['package_id']; ?>" >Pick</a><?php } ?>
						</p>
					</li>
					<li class="package_price">
						<p <?php echo $package['package_image'] ? 'style="background-image:url('.$package['package_image'].');" ' : '' ; ?>>
						<span><?php echo $package['package_price'] == 0 ? 'Free': number_format($package['package_price']).'<small>USD/Month</small>'; ?></span>
						</p>
					</li>
					<li class="package_companies feature"><p><?php echo number_format($package['package_max_companies']); ?> <?php echo $package['package_max_companies'] > 1 ? 'Companies' : 'Company'; ?></p></li>
					<li class="package_pages feature"><p><?php echo number_format($package['package_max_pages']); ?> <?php echo $package['package_max_pages'] > 1 ? 'Pages' : 'Page'; ?></p></li>
					<li class="package_users feature"><p><?php echo number_format($package['package_max_users']); ?> <?php echo $package['package_max_users'] > 1 ? 'Members' : 'Member'; ?></p></li>
					<li class="package_apps feature"><p>3 Applications</p></li>
					<li class="package_stat feature"><p>Full<span class="asterisk">*</span></p></li>
					<li class="package_detail feature"><p><br /><?php echo $package['package_detail']; ?><br /><a class="more">For more detail</a></p></li>
					<li class="buy">
						<p><br /><?php 
						if($selected)
						{	?>
							Now you are using this package. <?php 
							if($package['package_price'] > 0) 
							{ ?>
								<a class="unsubscribe" href="<?php echo base_url().'payment/cancel_package/'.$user_id; ?>">Unsubscribe this package</a><?php 
							}
						}
						else
						{
							if($user_current_package_id)
							{
								if($package['package_price'] > $user_current_package_price )
								{ ?>
									<a href="<?php echo base_url().'home/package?package_id='.$package['package_id']; ?>" class="bt-upgrade-package payment-pop">Upgrade to this package</a>
									<?php 
								}
							}
							else
							{ ?>
								<a href="<?php echo base_url().'home/package?package_id='.$package['package_id']; ?>" class="bt-choose-package payment-pop">Choose this package</a>
								<?php 
							}
						} ?>
						</p>
					</li>
				</ul><?php $i++;
			} ?>
			
			
		</div>
		
	</div>
</div>
<div class="remark">
	<p><b>Remark</b></p>
    <p><span class="asterisk">*</span> Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.</p>
    <p><span class="asterisk">**</span> Totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt.</p>
	<div class="more-info">
		<b>For more information</b>
		<a class="bt-read-faqs">Read FAQs</a>
	</div>
</div>