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
  <script>var baseUrl = '<?php echo $base_url ?>';</script>
</head>
<?php flush(); ?>
<body>
  <!-- Application container. -->

  <main role="main" id="main"></main>

  <!-- Application source. -->

  <!--(if target dummy)><!-->
  <script data-main="<?php echo $base_url ?>assets/backend/app/config" src="<?php echo $base_url ?>assets/backend/vendor/js/libs/require.js"></script>
  <!--<!(endif)-->

  <!--(if target release)>
  <script src="require.js"></script>
  <!(endif)-->

  <!--(if target debug)>
  <script src="require.js"></script>
  <!(endif)-->
</body>
</html>
