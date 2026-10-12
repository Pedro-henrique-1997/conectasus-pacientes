<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/patients/create', function () {
    return view('patients.create');
});

Route::get('/patients', function () {
    return view('patients.index');
});

Route::get('/patients/{id}/edit', function ($id) {
    return view('patients.edit', ['id' => $id]);
});
