<div class="wrapper-content">
    <div class="account-data">
      <div class="pic"><img src="<?php echo imgsize($page['page_image'],'normal');?>" alt="" /><span></span></div>
      <div class="data">
        <h1><?php echo $page['page_name'];?></h1>
        <p><?php echo $page['page_detail'];?></p>
        <ul>
          <li class="fav"><a href="#">Add Favorite</a></li>
          <li class="member"><a href="#"><?php echo issetor($page_user_count,'-');?></a></li>
          <li class="badges"><a href="#"><?php echo issetor($page_badge_count,'-');?></a></li>
          <li class="point"><a href="#"><?php echo issetor($page_point_count,'-');?></a></li>
          <li class="setting"><a href="#"><span>setting</span></a></li>
        </ul>
      </div>
    </div>
    <div>
      <div class="tab-head">
        <h2>Application and Campaign</h2>
        <div>
          <p>Display:</p>
          <ul>
            <li><a class="active a-app-campaign">All</a></li>
            <li><a class="a-app">Applicaton</a></li>
            <li><a class="a-campaign">Campaign</a></li>
          </ul>
        </div>
      </div>
      <div class="list_app-camp"></div>
      <div class="pagination-app-campaign strip"></div>
    </div>
    <div>
      <div class="tab-head">
        <h2>Recent Activity</h2>
        <div>
          <p>Display:</p>
          <ul>
            <li><a class="active a-activity-app-campaign">All</a></li>
            <li><a class="a-activity-app">Applicaton</a></li>
            <li><a class="a-activity-campaign">Campaign</a></li>
            <li><a class="a-activity-me">Me</a></li>
          </ul>
        </div>
      </div>
      <div class="list_resent-activity"></div>
      <div class="pagination-activity strip"></div>
    </div>
  </div>