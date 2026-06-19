<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/guest', function () {
    return view('guest');
});

Route::get('/receptionist', function () {
    return view('receptionist');
});

Route::get('/admin', function () {
    return view('admin');
});

Route::get('/sekjen', function () {
    return view('sekjen');
});

Route::get('/sekretariat-jenderal', function () {
    return view('sekjen');
});

