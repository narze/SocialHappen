var user_facebook_id = 0;
var fb_loaded = false;

var facebook_image = '';
var facebook_name = '';
var facebook_email = '';

function allow_facebook_login(){
  fb_loaded = true;
}

function check_user(){
  $('#box-overlay').show();
  $('#progress_bar').show();
  console.log(user_facebook_id);
  if(!user_facebook_id){
    self.location.href=base_url+'player/static_signup?app_data='+app_data;
    return;
  }
  jQuery.ajax({
    url: base_url+'player/static_user_check',
    type: "POST",
    data: {
      user_facebook_id: user_facebook_id
    },
    dataType: "json",
    success:function(data){
      console.log(data);
      $('#progress_bar').hide();

      if(data.result=='ok'){
        if(true_app_data) {
          self.location.href=base_url+'player/play?app_data='+app_data+'&dashboard=true';
        }else{
          self.location.href=base_url+'player/static_play_app_trigger?app_data='+app_data;
        } 
      }else{
        self.location.href=base_url+'player/static_signup?app_data='+app_data+'';
      }

      
    }
  });
}

checkFBConnected(function(id) {
  
  console.log('test');
  if(id){
    user_facebook_id = id;
  }
  check_user();
});