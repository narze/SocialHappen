	<div class="container-fluid">
  	<div class="row-fluid">
      <div class="span12">
        <div id="user-data"></div>
      </div>
      <div class="span12">
        <div id="played-apps"></div>
      </div>
      <div class="span12">
        <div id="all-apps"></div>
      </div>
    </div>
  </div>

  <script type="text/template" id="user-data-template">
    <div class="well">
      <div class="row">
        <div class="span3">
          <img src="<%= picture %>" class="user-picture" />
        </div>
        <div class="span8">
          <h2 class="user-name">
            <%= name %>
          </h2>
          <p class="user-point">User Point: <%= point %></p>
        </div>
      </div>
    </div>
  </script>
  <script type="text/template" id="app-played-item-template">
    <div class="page-header">
      <h1>All apps you have played : </h1>
    </div>
    <div class="played-app-container">
      <ul class="played-apps-list">
        <% _.each(played_apps, function(played_app) { %>
          <li class="played-app">
            <div class="played-app">
              <a href="<%= played_app.app_url %>" title="Play" alt="Play">
                <img src="<%= played_app.picture %>" class="app-photo"/>
              </a>
              <h3 class="app-name"><%= played_app.app_name %></h3>
            </div>
          </li>
        <% }); %>
      </ul>
    </div>
  </script>
  <script type="text/template" id="app-item-template">
    <div class="page-header">
      <h1>Explore more SocialHappen apps
        <small>play apps and collect points</small>
      </h1>
      </div>
      <ul class="all-apps-list">
        <% _.each(available_apps, function(available_app) { %>
          <li class="app-item">
          <div class="app-item">
          <div class="app-photo">
            <a href="<%= available_app.app_url %>" title="Play" alt="Play"><img src="<%= available_app.app_image %>" class="app-photo"/></a>
          </div>
          <div class="app-detail">
            <h3 class="app-name"><%= available_app.app_name %></h3>
            <p class="description"><%= available_app.app_description %></p>
            <p>
              <a href="<%= available_app.app_url %>" title="Play" alt="Play" class="btn btn-success">Play</a>
            </p>
          </div>
        <div class="clear"></div>
        <% }); %>
        
      </div>
    </li>
    </ul>
  </script>