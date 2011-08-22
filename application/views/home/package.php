{header}
{breadcrumb}
<div class="wrapper-content">

	<div class="wrapper-details">
		<?php if($facebook_user) echo $facebook_user['first_name']; ?>
		<h1>Package</h1>
		
		<div class="packages">
			<ul>
			<?php foreach($packages as $package) { ?>  
				<!-- package -->
				<li class="package-<?php echo $package['package_id']; ?>">
					<h3><?php echo $package['package_name']; ?></h3>
					<h4><?php echo $package['package_price'] == 0 ? 'Free!': $package['package_price']; ?></h4>
					<p><?php echo $package['package_detail']; ?></p>
					<a href="#" class="bt-select-package">Add to cart</a>
				</li>
				<!-- /package -->
			<?php } ?>
			</ul>
			<br class="clear" />
		</div>
	</div>
	
	<div class="bottom"><!--bottom--></div>
</div>
{footer}