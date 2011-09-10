<div class="popup_payment-confirm">
	<?php $attributes = array('class' => 'payment-form', 'id' => '');
	echo form_open('payment/confirm_paypal/'.$order['order_id'], $attributes); ?>
    <h2>Payment Confirm</h2>
    <p><b>Billing Information</b> <?php echo $order['billing_info'];?></p>
    <p><b>Package</b> <?php echo $order['package']; ?> (<?php echo $order['order_net_price']? '<span class="price">'.$order['order_net_price'].' USD</span>' : 'FREE' ; ?>)</p>
    <p><b>Payment Method</b> <?php echo $order['payment_method']; ?></p>
	<input type="hidden" name="token" value="<?php echo $this->input->get('token');?>" />
	<input type="hidden" name="PayerID" value="<?php echo $this->input->get('PayerID');?>" />
	<a href="#">cancel</a> <a class="bt-continue"><span>Continue</span></a>
	<?php echo form_submit( 'submit-form', 'Submit', 'style="display:none"'); ?> 
	<?php echo form_close(); ?>
</div>