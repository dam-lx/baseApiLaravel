<?php
// Authentication Routes...
Route::get('/', 'LoginController@showLoginForm')->name('backend.login');
Route::post('login', 'LoginController@login')->name('backend.doLogin');
Route::post('logout', 'LoginController@logout')->name('backend.dologout');
// Password Reset Routes...
Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'ResetPasswordController@reset')->name('backend.password.doReset');

// Registration Routes...
Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'RegisterController@register')->name('register.create');

