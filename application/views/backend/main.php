<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <title>SocialHappen<?php  echo " - $title";?></title>

  <?php
    $base_url = base_url();
    foreach($styles as $one) {
      if($one){
        if(strrchr($one, '.') === '.css') {
          echo '<link rel="stylesheet" type="text/css"  href="'.$one.'" />'."\n";
        } else {
          echo '<link rel="stylesheet" type="text/css"  href="'.$base_url.'assets/css/'.$one.'.css" />'."\n";
        }
      }
    }
  ?>

  <link rel="stylesheet" href="<?php echo $base_url ?>assets/backend/app/styles/main.css">
  <script src="<?php echo $base_url ?>assets/backend/app/scripts/vendor/modernizr.min.js"></script>

  <script>var baseUrl = '<?php echo $base_url ?>';</script>
</head>
<?php flush(); ?>
<body>
  <!-- Application container. -->

  <div id="app"></div>
  <div id="overlay">
    <ul>
      <li class="li1"></li>
      <li class="li2"></li>
      <li class="li3"></li>
      <li class="li4"></li>
      <li class="li5"></li>
      <li class="li6"></li>
    </ul>
  </div>

  <!-- Application source. -->

  <!--(if target dummy)><!-->
  <script data-main="<?php echo $base_url ?>assets/backend/app/scripts/main" src="<?php echo $base_url ?>assets/backend/app/scripts/vendor/require.js"></script>
  <!--<!(endif)-->

  <!--(if target release)>
  <script src="require.js"></script>
  <!(endif)-->

  <!--(if target debug)>
  <script src="require.js"></script>
  <!(endif)-->
</body>
</html>
