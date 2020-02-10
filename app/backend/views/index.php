<?php


/**
 * @var string $address
 */

?>
<script type='text/javascript'>
    <?php
    echo "var address = \"" . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http" . "://$_SERVER[HTTP_HOST]") . "\";";
    ?>
</script>
<link rel="stylesheet" type="text/css" href="/css/index.css"/>


<div id="book-container"></div>
<script defer src="/js/main.js" type="text/javascript"></script>






