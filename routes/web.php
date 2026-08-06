<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/guest', function () {
    return view('guest.index');
});

Route::get('/receptionist', function () {
    return view('receptionist.index');
});

Route::get('/customer-service', function () {
    return view('customer_service.index');
});

Route::get('/admin', function () {
    return view('admin.index');
});
