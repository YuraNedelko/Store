<?php

return array(
    'appName' => 'Book Store',
    'host' => '127.0.0.1:3306',
    'user' => 'root',
    'pass' => 'root',
    'db' => 'book_store',
    'charset' => 'utf8',
    'timezone' => 'Europe/Kiev',
    'startSunday' => false,
    'configurators' => [
        '\app\common\core\AppDependencyBinders\AppRouteDependencyBinder',
        '\app\common\core\AppDependencyBinders\AppRequestDependencyBinder',
        '\app\common\core\AppDependencyBinders\AppMailerDependencyBinder'
    ]
);