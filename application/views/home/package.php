	<div class="wrapper-details">

		<h1>Package</h1>
		
		<div class="packages">
			<ul>
			<?php foreach($packages as $package) { ?>  
				<!-- package -->
				<li class="package-<?php echo $package['package_id']; ?>">
					<h3><?php echo $package['package_name']; ?></h3>
					<h4><?php echo $package['package_price'] == 0 ? 'Free!': $package['package_price'].'USD'; ?></h4>
					<p><?php echo $package['package_detail']; ?></p>
					<?php if(!$user_current_package || $package['package_price'] > $user_current_package['package_price'] ) { ?>
					<a href="<?php echo base_url().'home/package?package_id='.$package['package_id']; ?>" class="bt-select-package">Add to cart</a>
					<?php } ?>
				</li>
				<!-- /package -->
			<?php } ?>
			</ul>
			<br class="clear" />
		</div>
	</div>
	
	<div class="bottom"><!--bottom--></div>