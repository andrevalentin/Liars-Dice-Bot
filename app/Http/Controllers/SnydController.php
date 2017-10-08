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
    protected $game;
    protected $calls;
    protected $first_round = false;
    protected $participants;
    protected $participant_count;
    protected $current_participant;
    protected $current_eligible_participant_order;
    protected $current_call;
    protected $next_eligible_participant_order;

    protected $emoji_numbers = [
        1 => ":one:",
        2 => ":two:",
        3 => ":three:",
        4 => ":four:",
        5 => ":five:",
        6 => ":six:",
    ];

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
        $current_participant_check = GameParticipant::where('game_id', $game->id)
            ->where('participant_id', $this->user->id)
            ->first();
        if(!empty($current_participant_check)) {
            $bot->reply("You are already in the game, stop trying to join! Enculé!");
            return;
        }

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
        $participant_count = count($participants);
        if($participant_count < 2) {
            $bot->reply("You are currently the only participant of this game, a bit lonely, no? Please wait for others to join before starting the game..");
            return;
        }

        // Set order of players
        $first_player = null;
        $shuffled_participants = $participants->shuffle();
        foreach ($shuffled_participants AS $key => $participant) {
            $player = User::find($participant->participant_id);
            if ($key == 0) {
                $first_player = $player;
            }

            GameParticipant::where('game_id', $game->id)
                ->where('participant_id', $participant->participant_id)
                ->update([
                    'participant_order' => $key
                ]);

            // Notify players about game starting
            $bot->say("Alright, let's play Snyd.. There are *$participant_count* players in the game.. Rolling the dice!", $player->slack_id);
            if ($key == 0) {
                $bot->say("You are the first player! You have the first call..", $player->slack_id);
            }else{
                $bot->say("<@" . $first_player->slack_id . "> is the first player!", $player->slack_id);
            }
        }

        $game->state = 'live';
        $game->save();

        $this->initGame($bot, $shuffled_participants, $game->id);

        $bot->reply("Game starting! Further instructions will be sent via DM..");
    }

    public function playRound(BotMan $bot)
    {
        // Getting the user
        $this->user = User::where('slack_id', $bot->getUser()->getId())->first();

        $this->current_participant = GameParticipant::where('participant_id', $this->user->id)
            ->orderBy('created_at', 'desc')
            ->first();
        if(empty($this->current_participant)) {
            $bot->reply("Sorry, you don't seem to be in any live games.. Perhaps join or start one?");
            return;
        }

        $this->game = Game::find($this->current_participant->game_id);
        if(!isset($this->game) || $this->game->state != 'live') {
            $bot->reply("Sorry, you don't seem to be in any live games.. Perhaps join or start one?");
            return;
        }

        $this->participants = GameParticipant::where('game_id', $this->game->id)
            ->orderBy('participant_order', 'asc')
            ->get();
        $this->participant_count = $this->participants->count();

        $this->current_call = $bot->getMessage()->getText();
        echo "User called: " . $this->current_call . "\n";

        $this->calls = Call::where('game_id', $this->game->id)
            ->orderBy('created_at', 'desc')
            ->get();
        if($this->calls->isEmpty()) {
            $this->first_round = true;
            $this->current_eligible_participant_order = 0;
            $this->next_eligible_participant_order = 1;
        }else{
            // Find current & next eligible participant order
            if($this->calls->first()->participant_order == ($this->participant_count-1)) {
                $this->current_eligible_participant_order = 0;
                $this->next_eligible_participant_order = 1;
            }else{
                $this->current_eligible_participant_order = $this->calls->first()->participant_order + 1;
                if($this->current_participant->participant_order == ($this->participant_count-1)) {
                    $this->next_eligible_participant_order = 0;
                }else{
                    $this->next_eligible_participant_order = $this->current_participant->participant_order + 1;
                }
            }
        }

        if($this->current_participant->participant_order !== $this->current_eligible_participant_order) {
            $bot->reply("It's not your turn yet, please wait!");
            return;
        }

        if($this->current_call == 'lift') {
            $this->endRound($bot);
        }else{
            $this->continueRound($bot);
        }
    }

    public function continueRound(BotMan $bot)
    {
        if($this->first_round) {
            $current_call = new Call;
            $current_call->call = $this->current_call;
            $current_call->game_id = $this->game->id;
            $current_call->participant_id = $this->user->id;
            $current_call->participant_order = $this->current_participant->participant_order;
            $current_call->save();
        }else{
            if(!$this->compareTwoCalls($this->current_call, $this->calls->first()->call)) {
                $bot->reply("You're call was lower than the person before you, please say something else..");
                return;
            }
            $current_call = new Call;
            $current_call->call = $this->current_call;
            $current_call->game_id = $this->game->id;
            $current_call->participant_id = $this->user->id;
            $current_call->participant_order = $this->current_participant->participant_order;
            $current_call->save();
        }

        $next_participant = $this->participants->where('participant_order', $this->next_eligible_participant_order)->first();
        $next_player = User::where('id', $next_participant->participant_id)->first();
        echo "Next participant: " . $next_participant->participant_id . "\n";

        foreach ($this->participants AS $participant) {
            $user = User::find($participant->participant_id);
            if($participant->participant_id == $next_participant->participant_id) {
                $bot->say("<@" . $this->user->slack_id . "> called $this->current_call", $user->slack_id);
                $bot->say("Now it's your turn! Call or lift!", $user->slack_id);
            }elseif($participant->participant_id == $this->current_participant->participant_id) {
                $bot->say("You called $this->current_call..", $user->slack_id);
                $bot->say("Now it's <@" . $next_player->slack_id . ">'s turn..", $user->slack_id);
            }else{
                $bot->say("<@" . $this->user->slack_id . "> called $this->current_call", $user->slack_id);
                $bot->say("Now it's <@" . $next_player->slack_id . ">'s turn..", $user->slack_id);
            }
        }

    }

    public function endRound(BotMan $bot)
    {
        $last_call = $this->calls->first();



        // get all dice from current round
        // get last call from last dude that the current dude didnt believe
        // compare last call to all dice
        // determine loser out of two players, all other players win and get one dice removed
    }

    public function abortGame(BotMan $bot)
    {
        // Getting the user
        $this->user = User::where('slack_id', $bot->getUser()->getId())->first();

        // get all dice from current round
        // get last call from last dude that the current dude didnt believe
        // compare last call to all dice
        // determine loser out of two players, all other players win and get one dice removed
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

    private function getNextTurnUser() {

    }

}
