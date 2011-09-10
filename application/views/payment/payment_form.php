<body>
<div class="popup_payment">
	<div id="payment-form">
	<?php $attributes = array('class' => 'payment-form', 'id' => '');
		echo form_open('payment/payment_form', $attributes); ?>
		<div class="form">
            <h2>Your Package</h2>
			<ul class="form">
              <li>
				<input type="hidden" name="package_id" value="<?php echo $selected_package['package_id']; ?>" />
				<?php echo $selected_package['package_name'];
					echo $selected_package['package_price'] ? ' ('.$selected_package['package_price'].'USD' : ' (FREE)';
					echo $selected_package['package_duration'] ? '/'.$selected_package['package_duration'].')' : ''; 
				?>
			  </li>
              <li>
				<p id="package_detail"><?php echo $selected_package['package_detail']; ?></p>
			  </li>
            </ul>
			<?php if($selected_package['package_price'] > 0) { ?>
			<div id="select-payment">
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
			<?php } ?>
			<p>
				<a href="#">cancel</a> <a class="bt-continue"><span>Continue</span></a>
				<?php echo form_submit( 'submit-form', 'Submit', 'style="display:none"'); ?> 
			</p>
			<br class="clear" />
		</div>
	<?php echo form_close(); ?>
	</div>
</div>
</body>