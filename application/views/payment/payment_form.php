<body>
<div class="popup_payment">
<?php if($is_upgradable == false) { ?>
	<p>You are already use a maximum package</p>
<? } else { ?>
	<div id="payment-form">
	<?php $attributes = array('class' => 'payment-form', 'id' => '');
		echo form_open('payment/payment_form', $attributes); ?>
		<div class="form">
            <h2>Your Package</h2>
			<ul class="form">
              <li>
				<input type="hidden" name="free_package_id" value="<?php echo $free_package_id; ?>" />
				<?php echo form_dropdown('package_id', $options, $selected_package['package_id']); ?>
			  </li>
              <li>
				<div id="package_detail"><?php 
						foreach ($packages as $package)
						{
							$display = $package['package_id'] == $selected_package['package_id'] ? 'display:block' : 'display:none'; ?>
							<p style="<?php echo $display; ?>"><?php echo $package['package_detail']; ?></p><?
						}
					?>
				</div>
			  </li>
            </ul>
			
			<div id="select-payment" <?php if($selected_package && $selected_package['package_price'] == 0) { echo 'style="display:none"'; } ?>>
			<h2>Select payment method</h2>
			<ul class="form">
				<li><input type="radio" name="payment_method" value="bank_transfer" /> Bank Transfer</li>
				<li><input type="radio" name="payment_method" value="credit_card" /> Credit card <br />
					<div>
						Credit Card Number: <input type="text" name="credit_card_number">
						Expiration Date <input name="credit_card_expire_month" type="text">/<input name="credit_card_expire_year" type="text"> CSC: <input name="credit_card_csc" type="text">
					</div>
				</li>
				<li><input type="radio" name="payment_method" value="paypal" checked /> Paypal</li>
				<li><input type="radio" name="payment_method" value="counter_service" /> Counter Service</li>
            </ul>
			</div>

			<p>
				<a href="#">cancel</a> <a class="bt-continue"><span>Continue</span></a>
				<?php echo form_submit( 'submit-form', 'Submit', 'style="display:none"'); ?> 
			</p>
			<br class="clear" />
		</div>
	<?php echo form_close(); ?>
	</div>
<?php } ?>
</div>
</body>