<?php

$botman = resolve('botman');

$botman->hears('.*', \App\Http\Controllers\SnydController::class.'@init');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});

// route should exclusively work in channels
$botman->hears('Who wants to play Snyd?', \App\Http\Controllers\SnydController::class.'@host');
$botman->hears('close game', \App\Http\Controllers\SnydController::class.'@close');
$botman->hears('me', \App\Http\Controllers\SnydController::class.'@join');
$botman->hears('start game', \App\Http\Controllers\SnydController::class.'@start');

// routes should exclusively work in the private message between the bot and the player
$botman->hears('([1-9]{0,1}[0-9]+,[0-6])', \App\Http\Controllers\SnydController::class.'@playRound');
$botman->hears('lift', \App\Http\Controllers\SnydController::class.'@playRound');
$botman->hears('abort game', \App\Http\Controllers\SnydController::class.'@abortGame');

$botman->hears('Show me a list of ongoing games', function($bot) {
    $games = Games::all();
    $bot->reply('Here you go..');
    $bot->reply(json_encode($games));
});