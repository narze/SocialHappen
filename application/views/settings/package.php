
  <div id="current-package">
	<h2><span>Current Package</span></h2>
	<div class="box-current_package">
	  <div class="detail">
		<h2><?php echo $current_package['package_name']; ?></h2>
		<p><?php echo $current_package['package_detail']; ?></p>
		<p class="thumb" style="background-image:url('<?php echo $current_package['package_image'] ? $current_package['package_image'] : base_url().'images/enterprise-deluxe.png' ; ?>');">
		</p>
	  </div>
	  <ul class="detail">
		<li><b><?php echo number_format($user_companies).'/'.number_format($current_package['package_max_companies']); ?></b><span>Companies</span></li>
		<li><b><?php echo number_format($user_pages).'/'.number_format($current_package['package_max_pages']); ?></b><span>Pages</span></li>
		<li><b><abbr title="<?php echo number_format($members).'/'.number_format($current_package['package_max_users']); ?>"><?php echo $members; ?></abbr></b><span>Members</span></li>
		<li class="package-apps"><img src="<?php echo str_replace('_icon_', '_app_', $current_package['package_image']);?>" alt="" />
			<?php if($apps) { ?>
			<div class="package-overlay" style="display:none;">
				<ul>
				  <?php foreach($apps as $app) { ?>
				  <li>
					<p><img class="app-image" style="width:64px;height:64px;" src="<?php echo $app['app_image']; ?>" alt="<?php echo $app['app_name']; ?>" /></p>
					<p><?php echo $app['app_name']; ?></p>
				  </li>
				  <?php } ?>
				</ul>
			</div>
			<?php } ?>
		</li>
	  </ul>
	  <?php if($is_upgradable) { ?>
		<p><a class="bt-upgrade_package" href="<?php echo base_url().'home/package'; ?>"><span>Upgrade</span></a></p>
	  <?php } ?>
	  
	</div>
  </div>
  
  <div id="billing-information" class="border-none">
	<h2><span>Billing information</span></h2>
	<div class="box-billing_info">
	  <table cellpadding="0" cellspacing="0">
		<tr>
		  <th>Date</th>
		  <th>Invoice ID</th>
		  <th>Package name</th>
		  <th></th>
		</tr>
		<?php if($orders) 
		{
			foreach($orders as $order) { ?>
			<tr>
			  <td><?php echo $order['order_date']; ?></td>
			  <td class="billing-popup"><a href="<?php echo base_url().'payment/invoice/'.$order['order_id']; ?>"><?php echo $order['order_id']; ?></a></td>
			  <td><?php echo $order['package_name']; ?></td>
			  <td class="right"><?php echo number_format($order['order_net_price']); ?> USD</td>
			</tr>
			<?php } 
		} else { ?>
			<tr>
			  <td colspan="4" align="center">No invoice found.</td>
			</tr>
		<?php } ?>
	  </table>
	  <?php if($current_package['package_price'] > 0) { ?>
	  <p style="text-align:right"><br /><a href="<?php echo base_url().'payment/cancel_package/'.$user_id; ?>">Cancel package</a></p>
	  <?php } ?>
	</div>
  </div>