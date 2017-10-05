<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\Game;
use App\Models\GameParticipant;
use App\Models\Roll;
use App\Models\User;
use BotMan\BotMan\BotMan;

class SnydController extends Controller
{

    protected $user;

    protected $emoji_numbers = [
        1 => ":one:",
        2 => ":two:",
        3 => ":three:",
        4 => ":four:",
        5 => ":five:",
        6 => ":six:",
    ];

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

    public function host(BotMan $bot)
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

    // Method for the host of an game with an open game state, to start a game.
    public function start(BotMan $bot)
    {
        // Getting the user
        $this->user = User::where('slack_id', $bot->getUser()->getId())->first();

        // Getting the currently open game
        $game = Game::where('state', 'open')
            ->first();
        if(empty($game)) {
            $bot->reply("There doesn't seem to be any games you can start right now.. :thinking_face: You could host one by asking if anyone wants to play?");
            return;
        }

        if($game->host_id !== $this->user->id) {
            $bot->reply("You are trying to start a game which you are not the host of, for helved..");
            return;
        }

        $participants = GameParticipant::where('game_id', $game->id)
            ->get();
        if(count($participants) < 2) {
            $bot->reply("You are currently the only participant of this game, a bit lonely, no? Please wait for others to join before starting the game..");
            return;
        }

        // Set order of players
        $shuffled_participants = $participants->shuffle();
        foreach ($shuffled_participants AS $key => $participant) {
            GameParticipant::where('game_id', $game->id)
                ->where('participant_id', $participant->participant_id)
                ->update([
                    'participant_order' => $key
                ]);

            // Notify players about game starting
            $player = User::find($participant->participant_id);
            $bot->say("Alright, let's play Snyd.. Rolling the dice!", $player->slack_id);
            if($key == 0) {
                $bot->say("You are the first player! You have the first call..", $player->slack_id);
            }
        }

        $game->state = 'live';
        $game->save();

        $this->initGame($bot, $shuffled_participants, $game->id);

        $bot->reply("Game starting! Further instructions will be sent via DM..");
    }

    public function continueGame(BotMan $bot)
    {
        // Getting the user
        $this->user = User::where('slack_id', $bot->getUser()->getId())->first();

        $current_participant = GameParticipant::where('participant_id', $this->user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $game = Game::find($current_participant->game_id);
        if(!isset($game) || $game->state != 'live') {
            $bot->reply("Sorry, you don't seem to be in any live games.. Perhaps join or start one?");
        }

        $call = $bot->getMessage()->getText();
        echo "User called: $call \n";

        $calls = Call::where('game_id', $game->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $participants = GameParticipant::where('game_id', $game->id)
            ->orderBy('participant_order', 'asc')
            ->get();
        $participant_count = $participants->count();

        if($calls->isEmpty()) {
            if($current_participant->participant_order != 0) {
                $bot->reply("It's not your turn yet, please wait!");
                return;
            }
            $current_call = new Call;
            $current_call->call = $call;
            $current_call->game_id = $game->id;
            $current_call->participant_id = $this->user->id;
            $current_call->participant_order = $current_participant->participant_order;
            $current_call->save();
        }else{
            echo "Last recorded call: " . json_encode($calls->first()->call) . "\n";
            $my_turn = false;

            // Check order
            if($current_participant->participant_order == ($calls->first()->participant_order + 1)) {
                $my_turn = true;
            }
            if($current_participant->participant_order == 0 && $calls->first()->participant_order == ($participant_count-1)) {
                $my_turn = true;
            }

            if(!$my_turn) {
                $bot->reply("It's not your turn.. hold your horses!");
                return;
            }

            if(!$this->compareTwoCalls($call, $calls->first()->call)) {
                $bot->reply("You're call was lower than the person before you, please say something else..");
                return;
            }
            $current_call = new Call;
            $current_call->call = $call;
            $current_call->game_id = $game->id;
            $current_call->participant_id = $this->user->id;
            $current_call->participant_order = $current_participant->participant_order;
            $current_call->save();
        }

        $turn_check = 0;
        foreach ($participants AS $participant) {
            if($participant->participant_id === $this->user->id) {
                $turn_check = 1;
                continue;
            }
            $player = User::find($participant->participant_id);
            $bot->say("<@" . $this->user->slack_id . "> called $call", $player->slack_id);
            if($turn_check) {
                $bot->say("Now it's your turn! Call or lift!", $participant->participant_id);
                $turn_check = 0;
            }
        }


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

    private function initGame(BotMan $bot, $participants, $game_id) {
        foreach ($participants AS $participant) {
            $player = User::find($participant->participant_id);
            $dice = $this->rollDice();

            $roll = new Roll;
            $roll->roll = json_encode($dice);
            $roll->game_id = $game_id;
            $roll->participant_id = $participant->participant_id;
            $roll->save();

            $emoji_dice = "";
            foreach ($dice AS $die) {
                $emoji_dice .= $this->emoji_numbers[$die] . " ";
            }
            $bot->say("Your roll: " . $emoji_dice, $player->slack_id);
        }
    }

    private function rollDice($no_of_dice = 4) {
        $rolls = [];
        for ($c = 0; $c != $no_of_dice; $c++) {
            $roll = rand(1, 6);
            $rolls[] = $roll;
        }
        return $rolls;
    }

    private function compareTwoCalls($current_call, $previous_call) {
        $exp_current_call = explode(",", $current_call);
        $exp_previous_call = explode(",", $previous_call);

        if($exp_current_call[1] == 1) {
            $exp_current_call[1] = 7;
        }
        if($exp_previous_call[1] == 1) {
            $exp_previous_call[1] = 7;
        }

        if($exp_current_call[0] > $exp_previous_call[0]) {
            return true;
        }elseif($exp_current_call[0] == $exp_previous_call[0]) {
            if($exp_current_call[1] > $exp_previous_call[1]) {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}
