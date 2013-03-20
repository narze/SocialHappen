<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <!-- <meta name="viewport" content="width=1024" /> -->
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <title>SocialHappen - How to play</title>
    <link rel="stylesheet" href="../assets/css/video/video.css">
</head>

<body class="impress-not-supported">
    <div class="fallback-message">
        <p>Your browser <b>doesn't support the features required</b>, For the best experience please use the latest <b>Chrome</b>, <b>Safari</b> or <b>Firefox</b> browser.</p>
    </div>

    <div id="impress">
        <div id="open" class="step slide" data-x="0" data-y="0">
            <img src="../assets/images/howto/sh-visual1_en.png" alt="">
        </div>

        <div id="scan" class="step slide" data-x="2500" data-y="0" data-z="-2000" data-rotate="90">
            <img src="../assets/images/howto/sh-visual2_en.png" alt="">
        </div>

        <div id="underlying-magic" class="step slide" data-x="0" data-y="2000" data-z="-3000" data-rotate="180">
            <img src="../assets/images/howto/sh-visual3_en.png" alt="">
        </div>

        <div id="overview" class="step" data-x="0" data-y="1600" data-scale="6">
        </div>
    </div>

    <div class="control prev"></div>
    <div class="control next"></div>

    <script src="../assets/js/common/jquery.min.js"></script>
    <script src="../assets/js/common/impress.js"></script>
    <script>
    $(function() {
        impress().init();
        $('.prev').click(impress().prev);
        $('.next').click(impress().next);
    });
    </script>
</body>
</html>