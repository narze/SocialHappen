<!-- Remove when integrate with getheader -->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script type="text/javascript">
        var base_url = "<?php echo base_url(); ?>";
        // Load the SDK Asynchronously
          (function(d){
             var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
             js = d.createElement('script'); js.id = id; js.async = true;
             js.src = "//connect.facebook.net/en_US/all.js";
             d.getElementsByTagName('head')[0].appendChild(js);
           }(document));

        window.fbAsyncInit = function() {
                FB.init({appId: '<?php echo $facebook_app_id; ?>', 
                        channelURL: '<?php echo $facebook_channel_url;?>', 
                        status: true, 
                        cookie: true,
                        xfbml: true,
                        oauth: true
                });
                
        }
</script>
<!-- end remove-->


<div id="fb-root"></div>
<script type="text/javascript">
        function fblogin() {
                FB.login(function(response) {
                        if (response.authResponse) {
                                var token = response.authResponse.accessToken;
                                FB.api('/me', function(response) {
                                        $.getJSON(base_url+"api/request_user_id?user_facebook_id=" + response.id , function(json){
                                                if(json.status != 'OK'){
                                                        window.location.replace(base_url+"player/signup?user_facebook_id="+response.id+'&token='+token);
                                                } else {
                                                        window.location.replace(base_url+"player/login");
                                                }
                                        });
                                });
                        } else {
                                
                        }
                }, {scope:'<?php echo $facebook_default_scope ; ?>'});
        }
</script>
<?php // Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => '');
echo form_open('player/signup', $attributes); ?>
<input type="hidden" name="user_facebook_id" value="<?php echo $this->session->userdata('user_facebook_id'); ?>">
<input type="hidden" name="token" value="<?php echo $this->session->userdata('token'); ?>">
<p>
        <?php if(isset($duplicated_email)) { echo '<div>This email is already a SocialHappen user.</div>'; } ?>
        <label for="email">Email <span class="required">*</span></label>
        <?php echo form_error('email'); ?>
        <br /><input id="email" type="text" name="email" maxlength="100" value="<?php echo set_value('email'); ?>"  />
</p>

<p>
        <?php if(isset($duplicated_phone)) { echo '<div>This phone number is already a SocialHappen user.</div>'; } ?>
        <label for="mobile_phone_number">Mobile Phone Number <span class="required">*</span></label>
        <?php echo form_error('mobile_phone_number'); ?>
        <br /><input id="mobile_phone_number" type="text" name="mobile_phone_number" maxlength="20" value="<?php echo set_value('mobile_phone_number'); ?>"  />
</p>

<p>     
        <?php if(isset($password_not_match)) { echo '<div>Password Not Match</div>'; } ?>
        <label for="password">Password <span class="required">*</span></label>
        <?php echo form_error('password'); ?>
        <br /><input id="password" type="password" name="password" maxlength="50" value="<?php echo set_value('password'); ?>"  />
</p>

<p>
        <label for="password_again">Password Again <span class="required">*</span></label>
        <?php echo form_error('password_again'); ?>
        <br /><input id="password_again" type="password" name="password_again" maxlength="50" value="<?php echo set_value('password_again'); ?>"  />
</p>


<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<p>Or <a onclick="fblogin();" href="#" id="fblogin">Connect with facebook</a></p>

<?php echo form_close(); ?>
