<?php

use app\common\core\App;

/**
 * @var $content_view string
 */
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= App::getConfig()->appName ?></title>
    <link rel="shortcut icon" type="image/jpg" href="/images/favicon.png"/>
    <link rel="icon" type="image/jpg" href="/images/favicon.png"/>
    <link rel="stylesheet" type="text/css" href="/css/layout.css"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>


<?php include __DIR__ . "/$content_view.php"; ?>

<div id="ass"></div>
<footer class="footer">
    <div class="footer-content">
        <div class="footer-text">
            Boook store 2020
        </div>
    </div>
</footer>
</body>
</html>