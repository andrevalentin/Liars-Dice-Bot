<?php

Route::match(['get', 'post'], '/api/event', 'BotManController@handle');
