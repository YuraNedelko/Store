<?php

use app\common\core\Routing\RouteCollection;


RouteCollection::get([
    "/" => "HomeController@index"
]);

RouteCollection::get([
    "/home/books/all/{offset?}" => "HomeController@getBooks"
]);

RouteCollection::get([
    "/home/books/view/{id}" => "HomeController@view"
]);

RouteCollection::post([
    "/home/order/{id}" => "HomeController@makeOrder"
]);


