<?php

use app\common\core\App;
use app\common\core\DIContainer\Container;

//spl_autoload_register(function ($class_name) {
////    $class_name = str_replace("\\","/", $class_name);
////    if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/../' . $class_name . '.php')){
////        require_once $_SERVER['DOCUMENT_ROOT'] . '/../' . $class_name . '.php';
////    }
////});

$config = (object)array_merge(
    include_once __DIR__ . '/../../common/config/config.php',
    include_once __DIR__ . '/../../backend/config/config.php'
);

include_once __DIR__ . '/../routes/routes.php';

$app = new App();
$app->init($config, new Container())->parseRequest();
