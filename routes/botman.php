<?php

$botman = resolve('botman');

// routes should exclusively work (listen) in channels
$botman->hears('help[ ]*', \App\Http\Controllers\LiarsDiceBotController::class.'@help');
$botman->hears('play liar.*', \App\Http\Controllers\LiarsDiceBotController::class.'@handle');
$botman->hears('close game[ ]*', \App\Http\Controllers\LiarsDiceBotController::class.'@close');
$botman->hears('me[ ]*', \App\Http\Controllers\LiarsDiceBotController::class.'@join');
$botman->hears('leave[ ]*', \App\Http\Controllers\LiarsDiceBotController::class.'@leave');
$botman->hears('start game[ ]*', \App\Http\Controllers\LiarsDiceBotController::class.'@handle');

// routes should exclusively work in the private message between the bot and the user
$botman->hears('([1-9][0-9]{0,3}(,|\.)[1-6])', \App\Http\Controllers\LiarsDiceBotController::class.'@handle');
$botman->hears('([1-9][0-9]{4,}(,|\.)[0-9]{1,9})', \App\Http\Controllers\LiarsDiceBotController::class.'@handleError');
$botman->hears('liar[ ]*', \App\Http\Controllers\LiarsDiceBotController::class.'@handle');
$botman->hears('abort game[ ]*', \App\Http\Controllers\LiarsDiceBotController::class.'@handle');
$botman->hears('say .+', \App\Http\Controllers\LiarsDiceBotController::class.'@handle');
