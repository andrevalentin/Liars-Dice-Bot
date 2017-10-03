<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use BotMan\BotMan\BotMan;

class SnydController extends Controller
{

    protected $user;

    public function __construct(BotMan $bot)
    {
        //echo "Test: " . json_encode("");
    }

    public function init(BotMan $bot)
    {
        echo "User " . $bot->getUser()->getUsername() . " sent a message!\n";
        $this->user = User::updateOrCreate(
            [
                "slack_id" => $bot->getUser()->getId()
            ],
            [
                "username" => $bot->getUser()->getUsername()
            ]
        );
    }

    public function start(BotMan $bot)
    {
        // Getting the user
        $this->user = User::where('slack_id', $bot->getUser()->getId())->first();

        $open_game_check = Game::where('state', 'open')->first();
        if(!empty($open_game_check)) {
            $bot->reply("Another open game is currently recruiting players.. It has to start before you can start another! Type \"me\" to join that game!");
            return;
        }

        $game = new Game;
        $game->host_id = $this->user->id;
        $game->save();

        echo "New game of Snyd starting! ID: $game->id Host: $game->host_id \n";

        $bot->reply("Let's play Snyd! <@" . $this->user->slack_id . "> is hosting.. This is game number #$game->id! Type \"me\" to join!");
        return;
    }

    public function close(BotMan $bot)
    {
        // Getting the user
        $this->user = User::where('slack_id', $bot->getUser()->getId())->first();

        // Check if a game is OPEN where the current user is HOST
        $game_check = Game::where('state', 'open')
            ->where('host_id', $this->user->id)
            ->first();

        if(empty($game_check)) {
            $bot->reply("You are not currently hosting any open games, thus you cannot close any! :thinking_face:");
        }else{
            $bot->reply(":scream: Okay, I'll close that game for you..");
            $game_check->state = 'aborted';
            $game_check->save();
        }
    }

}
