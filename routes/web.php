<?php

Route::match(['get', 'post'], '/api/event', 'BotManController@handle');
Route::get('/api/oauth/redirect', 'BotManController@oauthRedirect');


Route::get('/', 'UIController@getIndexPage');
Route::get('/about', 'UIController@getAboutPage');
Route::get('/help', 'UIController@getHelpPage');
