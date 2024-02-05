<?php

use App\Http\Controllers\Contacts\ContactAppController;
use App\Http\Controllers\Contacts\ContactController;
use App\Http\Controllers\Contacts\ContactEmailController;
use App\Http\Controllers\Contacts\ContactPhoneController;
use App\Http\Controllers\Contacts\GroupContactController;
use App\Http\Controllers\Contacts\GroupController;
use Illuminate\Support\Facades\Route;

Route::resource('groups', GroupController::class)->except('create', 'edit');
Route::resource('groups.contacts', GroupContactController::class)->only('index');
Route::resource('contacts', ContactController::class)->except('create', 'edit');
Route::resource('contacts.emails', ContactEmailController::class)->except('create', 'edit');
Route::resource('contacts.phones', ContactPhoneController::class)->except('create', 'edit');
Route::resource('contacts.apps', ContactAppController::class)->except('create', 'edit');