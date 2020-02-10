<?php

use app\common\core\Routing\RouteCollection;


RouteCollection::get([
    "/" => "HomeController@index"
]);

RouteCollection::get([
    "/home/books/all/{offset?}" => "HomeController@getBooks"
]);

RouteCollection::get([
    "/home/books/edit/{id}" => "HomeController@getEditBookParameters"
]);

RouteCollection::get([
    "/home/books/create" => "HomeController@getCreateBookParameters"
]);

RouteCollection::post([
    "/home/delete/{id}" => "HomeController@delete"
]);

RouteCollection::post([
    "/home/edit/{id}" => "HomeController@edit"
]);

RouteCollection::post([
    "/home/create" => "HomeController@create"
]);


