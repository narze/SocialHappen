<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container-fluid">
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a class="brand" href="#">SocialHappen</a>
      <div class="nav-collapse collapse">
        <ul class="nav bar-menu"></ul>
        <ul class="nav pull-right">
          <li class="bar-user"></li>
          <li class="bar-notification"></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<script type="text/template" id="bar-login-template">
  <li class="login">
    <a class="btn-login" href="<%= baseUrl %>login">Login SocialHappen</a>
  </li>
</script>

<script type="text/template" id="bar-menu-template">
  <li class="play">
    <a href="<%= baseUrl %>player/play">Play</a>
  </li>
  <li class="passport">
    <a href="<%= baseUrl %>assets/passport/#/profile/<%= user.id %>">Passport</a>
  </li>
  <li class="world">
    <a href="<%= baseUrl %>world">World</a>
  </li>
</script>

<script type="text/template" id="bar-user-template">
  <li class="divider-vertical"></li>
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <img class="user-image" src="<%= user.user_image %>?type=square" alt=""/ >  <%= user.user_first_name %> <%= user.user_last_name %> <b class="caret"></b> </a>
    <ul class="dropdown-menu mega-dropdown-menu user">
      <li>
        <a class="btn-logout" href="<%= baseUrl %>logout">» Logout</a>
      </li>
    </ul>
  </li>
</script>

<script type="text/template" id="bar-notification-template">
  <li class="divider-vertical"></li>
  <li class="dropdown notification">
    <a href="#" class="dropdown-toggle amount" data-toggle="dropdown"> </a>
    <ul class="dropdown-menu mega-dropdown-menu notification_list_bar">
      <li class="no-notification">
        <p>
          No notification.
        </p>
      </li>
      <li class="divider"></li>
      <li class="all-notification">
        <% _.each(notifications.list, function(notification) {  %>
          <li class="separator">
            <a href="<%= notification.link %>">
              <img src="<%= notification.image %>" />
              <p class="message"><%= notification.message %></p>
              <p class="time"><%= $.timeago(new Date(parseInt(notification.timestamp, 10) * 1000)) %></p>
            </a>
          </li>
        <% }); %>
        <a class="a-notification" href="#">See all Notifications</a>
      </li>
    </ul>
  </li>
  <li class="divider-vertical"></li>
</script>