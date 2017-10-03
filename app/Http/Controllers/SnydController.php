<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameParticipant;
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

        $participant = new GameParticipant;
        $participant->game_id = $game->id;
        $participant->participant_id = $this->user->id;
        $participant->save();

        echo "New game of Snyd starting! ID: $game->id Host: $game->host_id \n";

        $bot->reply("Let's play Snyd! <@" . $this->user->slack_id . "> is hosting.. This is game number #$game->id! Type \"me\" to join!");
        return;
    }

    public function join(BotMan $bot)
    {
        // Getting the user
        $this->user = User::where('slack_id', $bot->getUser()->getId())->first();

        // Getting the currently open game
        $game = Game::where('state', 'open')
            ->first();
        if(empty($game)) {
            $bot->reply("There doesn't seem to be any open games right now.. :thinking_face: You could host one by asking if anyone wants to play?");
            return;
        }

        if($game->host_id === $this->user->id) {
            $bot->reply("You are trying to join your own game.. FeelsBadMan..");
            return;
        }

        // At this point we KNOW that the user who is trying to join CAN join
        $participant = new GameParticipant;
        $participant->game_id = $game->id;
        $participant->participant_id = $this->user->id;
        $participant->save();

        $bot->reply("You have successfully joined the game.. please wait for the host to start it!");
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
