<?php if($player_logged_in) : ?>
You are already logged in
<?php else : ?>
You are not logged in
<?php endif; ?>
<?php if($facebook_connected) : ?>
You are connected to facebook
<?php else : ?>
You are not connected to facebook
<?php endif; ?>
<?php 
echo '<p>Player status :</p> <pre>';
var_dump($user);
echo '</pre>';