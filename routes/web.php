<?php

Route::match(['get', 'post'], '/api/event', 'BotManController@handle');
Route::get('/api/oauth/redirect', 'BotManController@oauthRedirect');


Route::get('/', 'UIController@getIndexPage');
Route::get('/contact', 'UIController@getContactPage');
Route::get('/help', 'UIController@getHelpPage');
Route::get('/privacy', 'UIController@getPrivacyPage');
