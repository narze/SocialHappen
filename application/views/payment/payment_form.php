<body>
<div class="popup_payment">
<?php if(count($packages) == 0) { ?>
	<p>You are already use a maximum package</p>
<?php } else { ?>
	<div id="payment-form">
	<?php $attributes = array('class' => 'payment-form', 'id' => '');
		echo form_open('payment/payment_form', $attributes); ?>
		<div class="form">
            <h2 class="in-popup"><?php echo $selected_package['package_name']; ?></h2>
			
				<div id="package-image"><?php
					foreach ($packages as $package)
					{
						$display = $package['package_id'] == $selected_package['package_id'] ? 'display:block' : 'display:none'; ?>
						<img style="<?php echo $display; ?>" src="<?php echo $package['package_image'] ? $package['package_image'] : base_url().'images/package_icon_default.png' ; ?>" /><?
					} ?>
				</div>

				<div id="package-detail">
					<input type="hidden" name="free_package_id" value="<?php echo $free_package_id; ?>" />
					<div class="selected-package"><?php echo form_dropdown('package_id', $options, $selected_package['package_id']); ?></div><?php
					foreach ($packages as $package)
					{
						$display = $package['package_id'] == $selected_package['package_id'] ? 'display:block' : 'display:none'; ?>
						<p style="<?php echo $display; ?>">
							<span><b>Companies</b> <?php echo number_format($package['package_max_companies']); ?></span>
							<span><b>Pages</b> <?php echo number_format($package['package_max_pages']); ?></span>
							<span><b>Users</b> <?php echo number_format($package['package_max_users']); ?></span>
						</p><?
					} ?>
				</div>


			
			<div class="clear" id="select-payment" <?php if($selected_package && $selected_package['package_price'] == 0) { echo 'style="display:none"'; } ?>>
			<h3>Select payment method</h3>
			<ul class="form">
				<li><input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer" /> <label for="bank_transfer">Bank Transfer</label></li>
				<li class="credit_card"><input type="radio" id="credit_card" name="payment_method" value="credit_card" /> <label for="credit_card">Credit Card</label>
					<img src="<?php echo base_url(); ?>images/credit_card.gif" /><br />
					<?php /*
					<p><span class="card_number">Credit Card Number:</span><input type="text" name="credit_card_number" maxlength="19" /><br />
					<span class="exp_date">Expiration Date:</span><input name="credit_card_expire_month" type="text" maxlength="2" /><span class="slash">/</span><input name="credit_card_expire_year" type="text" maxlength="4" /> <span class="csc">CSC:</span><input name="credit_card_csc" type="text" maxlength="3" /></p>
					*/ ?>
				</li>
				<li><input type="radio" id="paypal" name="payment_method" value="paypal" checked /> <label for="paypal">Paypal</label></li>
				<li><input type="radio" id="counter_service" name="payment_method" value="counter_service" /> <label for="counter_service">Counter Service</label></li>
            </ul>
			</div>

			<p class="clear ta-center">
				<a class="bt-continue"><span>Continue</span></a>
				<?php echo form_submit( 'submit-form', 'Submit', 'style="display:none"'); ?> 
			</p>
			<br class="clear" />
		</div>
	<?php echo form_close(); ?>
	</div>
<?php } ?>
</div>
</body>