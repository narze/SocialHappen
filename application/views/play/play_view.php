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
      <div class="played-apps-list">
      <% _.each(played_apps, function(played_app) { %>
        <a href="<%= played_app.app_url %>" title="Play" alt="Play">
          <div class="played-app-item well">
            <div class="app-photo">
                <img src="<%= played_app.picture %>" class="app-photo"/>
            </div>
            <div class="played-app-detail">
              <h3 class="app-name"><%= played_app.app_name %></h3>
            </div>
            <div class="play-button played" style="display: none;">
              <div class="btn btn-primary btn-large">Play</div>
            </div>
          </div>
        </a>
      <% }); %>
    </div>
  </script>

  <script type="text/template" id="app-item-template">
    <div class="page-header">
      <h1>Explore more SocialHappen apps
        <small>play apps and collect points</small>
      </h1>
      </div>
      <div class="all-apps-list">
        <% _.each(available_apps, function(available_app) { %>
          <a href="<%= available_app.app_url %>" alt="Play">
            <div class="app-item well">
              <div class="app-photo">
                <img src="<%= available_app.app_banner %>" class="app-photo"/>
              </div>
              <div class="app-detail">
                <h3 class="app-name"><%= available_app.app_name %></h3>
                <p class="description"><%= available_app.app_description %></p>
              </div>
              <div class="play-button" style="display: none;">
                <div class="btn btn-primary btn-large">Play</div>
              </div>
            </div>

              <!--<div>Who played this</div>
              <div>[Player list]</div>-->
          </a>
        <% }); %>

      </div>

    </div>
  </script>