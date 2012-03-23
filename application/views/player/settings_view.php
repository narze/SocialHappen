Settings page<br />
<?php if($facebook_connected) : ?>
	<p>You are connected to facebook</p>
	<p><a href="<?php echo base_url('player/disconnect_facebook');?>">Disconnect Facebook</a></p>
<?php else : ?>
	<p>You are not connected to facebook</p>
	<p><a href="<?php echo base_url('player/connect_facebook');?>">Connect Facebook</a></p>
<?php endif; ?>