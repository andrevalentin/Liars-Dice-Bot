<?php

$botman = resolve('botman');

// routes should exclusively work (listen) in channels
$botman->hears('help[ ]*', \App\Http\Controllers\SnydController::class.'@help');
$botman->hears('play liar.*', \App\Http\Controllers\SnydController::class.'@host');
$botman->hears('close game', \App\Http\Controllers\SnydController::class.'@close');
$botman->hears('me', \App\Http\Controllers\SnydController::class.'@join');
$botman->hears('leave', \App\Http\Controllers\SnydController::class.'@leave');
$botman->hears('start game', \App\Http\Controllers\SnydController::class.'@start');

// routes should exclusively work in the private message between the bot and the user
$botman->hears('([1-9]{0,1}[0-9]+,[0-6])', \App\Http\Controllers\SnydController::class.'@playRound');
$botman->hears('liar[ ]*', \App\Http\Controllers\SnydController::class.'@playRound');
$botman->hears('abort game', \App\Http\Controllers\SnydController::class.'@abortGame');
$botman->hears('say .+', \App\Http\Controllers\SnydController::class.'@say');
