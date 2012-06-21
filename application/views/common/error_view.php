<div class="container-fluid">
  <div class="row-fluid">
    <div class="span2">&nbsp;</div>
    <div class="span8">
      <div class="hero-unit">
        <h1>Error!</h1>
        <?php foreach($error_messages as $error) {
          echo '<p>'.$error.'</p>';
        }?>
      </div>
    </div>
  </div>
</div>