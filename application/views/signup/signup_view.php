<div class="container-fluid">
  <div id="login" style="display:none"><div class="row-fluid text-center">
    <a id="facebook-connect" onclick="fbLogin(fbLoginResult)" data-redirect="<?php echo base_url('signup/facebook'.$next);?>">Connect with facebook</a>
  </div>
  <div class="row-fluid text-center">
    Or
  </div>
  <div class="row-fluid text-center">
    <a href="<?php echo base_url('signup/form'.$next);?>">Signup with email</a>
  </div>
  </div>
</div>



