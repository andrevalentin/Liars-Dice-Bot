<?php

$botman = resolve('botman');

$botman->hears('.*', \App\Http\Controllers\SnydController::class.'@init');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});

$botman->hears('Who wants to play Snyd?', \App\Http\Controllers\SnydController::class.'@host');
$botman->hears('close game', \App\Http\Controllers\SnydController::class.'@close');
$botman->hears('me', \App\Http\Controllers\SnydController::class.'@join');
$botman->hears('start game', \App\Http\Controllers\SnydController::class.'@start');

$botman->hears('([1-9]{0,1}[0-9]+,[0-6])', \App\Http\Controllers\SnydController::class.'@continueGame');

$botman->hears('Show me a list of ongoing games', function($bot) {
    $games = Games::all();
    $bot->reply('Here you go..');
    $bot->reply(json_encode($games));
});