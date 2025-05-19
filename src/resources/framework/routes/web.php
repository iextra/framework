<?php

use Extro\HttpRouter\Facade\Route;
use Laminas\Diactoros\Response\HtmlResponse;

return [
    Route::get('/', function () {
        return new HtmlResponse('Hello World!');
    })
];
