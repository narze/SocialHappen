	<div class="wrapper-details">

		<h1>Package</h1>
		
		<div class="packages">
			<ul>
			<?php foreach($packages as $package) { ?>  
				<!-- package -->
				<li class="package-<?php echo $package['package_id']; ?>">
					<h3><?php echo $package['package_name']; ?></h3>
					<h4><?php echo $package['package_price'] == 0 ? 'Free!': $package['package_price']; ?></h4>
					<p><?php echo $package['package_detail']; ?></p>
					<a rel="<?php echo $package['package_id']; ?>" class="bt-select-package">Add to cart</a>
				</li>
				<!-- /package -->
			<?php } ?>
			</ul>
			<br class="clear" />
		</div>
	</div>
	
	<div class="bottom"><!--bottom--></div>