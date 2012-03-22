<?php if($facebook_connected) : ?>
	<p><a href="<?php echo base_url('player/disconnect_facebook');?>">Disconnect Facebook</a></p>
<?php else : ?>
	<p><a href="<?php echo base_url('player/connect_facebook');?>">Connect Facebook</a></p>
<?php endif; ?>