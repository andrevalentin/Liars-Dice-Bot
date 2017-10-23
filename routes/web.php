<?php

Route::match(['get', 'post'], '/api/event', 'BotManController@handle');
Route::get('/api/oauth/redirect', 'BotManController@oauthRedirect');
