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

Route::get('/customer-service', function () {
    return view('customer_service');
});

Route::get('/admin', function () {
    return view('admin');
});

