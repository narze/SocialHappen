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
              window.location.replace(base_url+"player/connect_facebook?user_facebook_id="+response.id+'&token='+token);
            } else {
                alert('This facebook user has already been used');
            }
          });
        });
      } else {
        
      }
    }, {scope:'<?php echo $facebook_default_scope ; ?>'});
  }
</script>

<?php if($facebook_connected) : ?>
        You are connected to facebook <a href="<?php echo base_url().'player';?>">Back</a>
<?php else : ?>
        <a href="#" onclick="fblogin();" id="fblogin">Click to connect to Facebook</a>
<?php endif; ?>