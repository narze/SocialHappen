<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container-fluid">
      <span class="bar-icon pull-left"><a href="#"><strong>SocialHappen</strong></a></span>
      <span class="bar-text"><a class="brand" href="#">SocialHappen</a></span>
      <ul class="nav bar-menu"></ul>
      <ul class="nav pull-right">
        <li class="bar-company-list"></li>
        <li class="bar-user"></li>
        <li class="bar-notification"></li>
      </ul>
    </div>
  </div>
</div>

<div class="modal hide fade" id="bar-login-modal">
  <div class="modal-header">
    <button class="close" data-dismiss="modal">×</button>
    <h3>Login SocialHappen</h3>
  </div>

  <form method="POST" action="<?php echo base_url('login');?>">
    <div class="modal-body">
      <div id="login">
        <div class="row-fluid text-center">
          <div class="well">
            <button id="facebook-connect"
              class="btn btn-info" data-toggle="button"
              onclick="fbLogin(fbLoginResult)"
              data-redirect="">
                Connect with facebook
            </button>
          </div>
        </div>
        <div class="row-fluid"><center class="center-or">or<center></div>
        <div class="row-fluid">
          <div class="well">

            <div class="control-group">
              <label for="email">Email</label>
              <input id="email" type="text" name="email" maxlength="100" value=""  />
            </div>

            <div class="control-group">
              <label for="password">Password</label>
              <input id="password" type="password" name="password" maxlength="50" value=""  />
            </div>

          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Login</button><span> or </span><a href="<?php echo base_url('signup');?>" class="btn">Signup SocialHappen</a>
    </div>
  </form>
</div>

<script type="text/template" id="bar-login-template">
  <li class="login">
    <a class="btn-login" href="<%= baseUrl %>login">Login SocialHappen</a>
  </li>
</script>

<script type="text/template" id="bar-menu-template">
  <li class="play">
    <a href="<%= baseUrl %>play"><i class="icon-play icon-large"></i><span class="bar-text">Play</span></a>
  </li>
  <li class="passport">
    <a href="<%= baseUrl %>passport"><i class="icon-book icon-large"></i><span class="bar-text">Passport</span></a>
  </li>
  <li class="world">
    <a href="<%= baseUrl %>world"><i class="icon-globe icon-large"></i><span class="bar-text">World</span></a>
  </li>
</script>

<script type="text/template" id="bar-company-list-template">
  <% if(companies && companies.length) { %>
    <li class="bar-company-list dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="icon-group icon-large"></i><span class="bar-text-right">Your Companies<b class="caret"></b></span>
      </a>
      <ul class="dropdown-menu mega-dropdown-menu user">
        <% _.each(companies, function(company) {  %>
          <li class="separator">
            <a href="<%= base_url + 'assets/company/#/company/' + company.company_id %>">
              <img src="<%= company.company_image %>" />
              <p class="company-name"><%= company.company_name %></p>
            </a>
          </li>
        <% }); %>
      </ul>
    </li>
  <% } else { %>
    <li><a href="<%= base_url + 'company/create'%>">Create Your Company</a></li>
  <% } %>
</script>

<script type="text/template" id="bar-user-template">
  <li class="divider-vertical"></li>
  <li class="bar-user dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <img class="user-image" src="<%= user.user_image %>?type=square" alt=""/ >
      <span class="bar-text-right"> <%= user.user_first_name %> <%= user.user_last_name %> <b class="caret"></b></span>
    </a>
    <ul class="dropdown-menu mega-dropdown-menu user">
      <li>
        <a class="btn-settings" href="<%= baseUrl %>player/settings">» Settings</a>
      </li>
      <li>
        <a class="btn-logout" href="<%= baseUrl %>logout">» Logout</a>
      </li>
    </ul>
  </li>
</script>

<script type="text/template" id="bar-notification-template">
  <li class="divider-vertical"></li>
  <li class="bar-notification dropdown notification">
    <a href="#" class="dropdown-toggle amount" data-toggle="dropdown">
    <i class="icon-info-sign icon-large"></i>
    <span></span>
    </a>
    <ul class="dropdown-menu mega-dropdown-menu notification_list_bar">
      <li class="no-notification">
        <p>
          No notification.
        </p>
      </li>
      <% _.each(notifications.list, function(notification) {  %>
        <li>
          <a href="<%= notification.link %>">
            <img src="<%= notification.image %>" />
            <p class="message"><%= notification.message %></p>
            <p class="time"><%= moment().from(moment.unix(notification.timestamp)) %></p>
          </a>
        </li>
      <% }); %>
      <li class="divider"></li>
      <li class="all-notification">
        <a class="a-notification" href="#">See all Notifications</a>
      </li>
    </ul>
  </li>
</script>