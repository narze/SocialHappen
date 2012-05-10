	<div class="container-fluid">
  	<div class="row-fluid">
      <div class="span12">
        <div class="user-data well">
          
        </div>
      </div>
      <div class="span12">
        <div class="page-header">
          <h1>All apps you've played</h1>
        </div>
        <div class="played-app-container">
          <ul class="played-apps-list">
            
          </ul>
        </div>
        
      </div>
      <div class="span12">
        <div class="page-header">
          <h1>Explore more SocialHappen apps
            <small>play apps and collect points</small>
          </h1>
        </div>
        <ul class="all-apps-list">
        
        </ul>
      </div>
    </div>
  </div>

  <script type="text/template" id="user-data-template">
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
  </script>
  <script type="text/template" id="app-played-item-template">
    <li class="played-app">
      <div class="played-app">
        <a href="<%= url %>" title="Play" alt="Play"><img src="<%= picture %>" class="app-photo"/></a>
        <h3 class="app-name"><%= name %></h3>
      </div>
    </li>
  </script>
  <script type="text/template" id="app-item-template">
    <li class="app-item">
      <div class="app-item">
        <div class="app-photo">
          <a href="<%= url %>" title="Play" alt="Play"><img src="<%= picture %>" class="app-photo"/></a>
        </div>
        <div class="app-detail">
          <h3 class="app-name"><%= name %></h3>
          <p class="description"><%= description %></p>
          <p>
            <a href="<%= url %>" title="Play" alt="Play" class="btn btn-success">Play</a>
          </p>
        </div>
        <div class="clear"></div>
      </div>
    </li>
  </script>