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
    protected $current_call;
    protected $participant_count;
    protected $current_round_rolls;
    protected $current_participant;
    protected $current_round_participants;
    protected $current_round_participant_count;
    protected $current_eligible_participant_order;
    protected $next_eligible_participant_order;
    protected $next_participant;
    protected $next_user;

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
        $game->dice = 4;
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
        $this->game = Game::where('state', 'open')
            ->first();
        if(empty($this->game)) {
            $bot->reply("There doesn't seem to be any games you can start right now.. :thinking_face: You could host one by asking if anyone wants to play?");
            return;
        }

        if($this->game->host_id !== $this->user->id) {
            $bot->reply("You are trying to start a game which you are not the host of, for helved..");
            return;
        }

        $participants = GameParticipant::where('game_id', $this->game->id)
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

            GameParticipant::where('game_id', $this->game->id)
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

        $this->game->state = 'live';
        $this->game->save();

        $this->initRound($bot, $shuffled_participants, $this->game->dice, 0);

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
        $this->setCurrentRoundParticipants();
        $this->participant_count = $this->participants->count();
        $this->current_round_participant_count = $this->current_round_participants->count();
        echo "Current round participant count: " . $this->current_round_participant_count . "\n";

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
            $last_call = $this->calls->first();
            if($last_call->call == 'snyd') {
                // Figure out who lost, that person becomes current eligible participant
                $this->current_eligible_participant_order = $this->current_round_participants
                    ->where('participant_id', $last_call->loser_id)
                    ->first()
                    ->participant_order;
                $current_eligible_participant_key = key($this->current_round_participants
                    ->where('participant_id', $last_call->loser_id)
                    ->all());
                if($current_eligible_participant_key == ($this->current_round_participant_count-1)) {
                    $this->next_eligible_participant_order = $this->current_round_participants
                        ->first()
                        ->participant_order;
                }else{
                    $this->next_eligible_participant_order = $this->current_round_participants[$current_eligible_participant_key+1]
                        ->participant_order;
                }
            }else{
                $last_participant_key = key($this->current_round_participants
                    ->where('participant_id', $last_call->participant_id)
                    ->all());
                if($last_participant_key == ($this->current_round_participant_count-1)) {
                    $this->current_eligible_participant_order = $this->current_round_participants
                        ->first()
                        ->participant_order;
                }else{
                    $this->current_eligible_participant_order = $this->current_round_participants[$last_participant_key+1]
                        ->participant_order;
                }
                $current_eligible_participant_key = key($this->current_round_participants
                    ->where('participant_order', $this->current_eligible_participant_order)
                    ->all());
                if($current_eligible_participant_key == ($this->current_round_participant_count-1)) {
                    $this->next_eligible_participant_order = $this->current_round_participants
                        ->first()
                        ->participant_order;
                }else{
                    $this->next_eligible_participant_order = $this->current_round_participants[$current_eligible_participant_key+1]
                        ->participant_order;
                }
            }

            /*if($this->calls->first()->participant_order == ($this->participant_count-1)) {
                $this->current_eligible_participant_order = 0;
                $this->next_eligible_participant_order = 1;
            }else{
                $this->current_eligible_participant_order = $this->calls->first()->participant_order + 1;
                if($this->current_participant->participant_order == ($this->participant_count-1)) {
                    $this->next_eligible_participant_order = 0;
                }else{
                    $this->next_eligible_participant_order = $this->current_participant->participant_order + 1;
                }
            }*/

        }

        $this->next_participant = $this->current_round_participants->where('participant_order', $this->next_eligible_participant_order)->first();
        $this->next_user = User::find($this->next_participant->participant_id);

        if($this->current_participant->participant_order !== $this->current_eligible_participant_order) {
            $bot->reply("It's not your turn yet, please wait!");
            return;
        }

        if(strtolower($this->current_call) == 'lift') {
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
            if($this->calls->first()->call != 'snyd' && !$this->compareTwoCalls($this->current_call, $this->calls->first()->call)) {
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

        foreach ($this->participants AS $participant) {
            $user = User::find($participant->participant_id);
            if($participant->participant_id == $this->next_participant->participant_id) {
                $bot->say("<@" . $this->user->slack_id . "> called $this->current_call", $user->slack_id);
                $bot->say("Now it's your turn! Call or lift!", $user->slack_id);
            }elseif($participant->participant_id == $this->current_participant->participant_id) {
                $bot->say("You called $this->current_call..", $user->slack_id);
                $bot->say("Now it's <@" . $this->next_user->slack_id . ">'s turn..", $user->slack_id);
            }else{
                $bot->say("<@" . $this->user->slack_id . "> called $this->current_call", $user->slack_id);
                $bot->say("Now it's <@" . $this->next_user->slack_id . ">'s turn..", $user->slack_id);
            }
        }

    }

    public function endRound(BotMan $bot)
    {
        $last_call = $this->calls->first();
        $exp_last_call = explode(",", $last_call->call);
        $dice_amount_to_look_for = $exp_last_call[0];
        $dice_face_to_look_for = $exp_last_call[1];

        $rolls = Roll::where('game_id', $this->game->id)
            ->orderBy('round', 'desc')
            ->get();
        $this->current_round_rolls = $rolls->where('round', $rolls->first()->round)->flatten();
        echo "Current round rolls: " . json_encode($this->current_round_rolls) . "\n";

        echo "Dice amount to look for: $dice_amount_to_look_for \n";
        echo "Dice face to look for: $dice_face_to_look_for \n";

        $hits = 0;
        foreach ($this->current_round_rolls AS $rolls) {

            // Checking for "Trappen" (ladder).
            /*$ladder_counter = 1;
            $dice_count = count(json_decode($rolls->roll));
            for ($c=0; $c<$dice_count; $c++) {
                if(in_array($ladder_counter, json_decode($rolls->roll))) {
                    $ladder_counter++;
                }
            }
            echo "Roll: $rolls->roll \n";
            echo "Ladder counter: $ladder_counter \n";
            echo "Dice counter: $dice_count \n";
            if($ladder_counter == $dice_count) {
                $hits = $hits + $dice_count + 1;
                continue;
            }*/

            foreach (json_decode($rolls->roll) AS $roll) {
                if($roll == 1) {
                    $hits++;
                }elseif($roll == $dice_face_to_look_for) {
                    $hits++;
                }
            }
        }

        echo "Hits: $hits \n";

        $loser_id = $last_call->participant_id;
        if($hits >= $dice_amount_to_look_for) {
            echo "Less hits, looser id is current user! \n";
            $loser_id = $this->user->id;
        }

        $current_call = new Call;
        $current_call->call = 'snyd';
        $current_call->game_id = $this->game->id;
        $current_call->participant_id = $this->user->id;
        $current_call->participant_order = $this->current_participant->participant_order;
        $current_call->loser_id = $loser_id;
        $current_call->save();

        $this->initRound($bot, $this->current_round_participants, null, $this->current_round_rolls->first()->round + 1, $loser_id);

        if($this->game->state == 'concluded') {
            return;
        }

        $this->next_participant = $this->current_round_participants->where('participant_id', $loser_id)->first();
        $this->next_user = User::find($loser_id);

        foreach ($this->current_round_participants as $participant) {
            $user = User::find($participant->participant_id);
            if($participant->participant_id == $this->next_participant->participant_id) {
                $bot->say("<@" . $this->user->slack_id . "> called snyd and *" . ($loser_id == $this->user->id ? 'LOST' : 'WON') . "*!", $user->slack_id);
                $bot->say("Now it's your turn! Call or lift!", $user->slack_id);
            }elseif($participant->participant_id == $this->current_participant->participant_id) {
                $bot->say("You called snyd and *" . ($loser_id == $this->user->id ? 'LOST' : 'WON') . "*!", $user->slack_id);
                $bot->say("Now it's <@" . $this->next_user->slack_id . ">'s turn..", $user->slack_id);
            }else{
                $bot->say("<@" . $this->user->slack_id . "> called snyd and *" . ($loser_id == $this->user->id ? 'LOST' : 'WON') . "*!", $user->slack_id);
                $bot->say("Now it's <@" . $this->next_user->slack_id . ">'s turn..", $user->slack_id);
            }
        }
    }

    private function endGame(BotMan $bot, $looser_id) {
        $looser = User::find($looser_id);
        foreach ($this->participants as $participant) {
            $user = User::find($participant->participant_id);
            if($looser_id == $participant->participant_id) {
                $bot->say("You lost, better luck next time.. FeelsBadMan..", $user->slack_id);
            }else{
                $bot->say("The game is over! <@" . $looser->slack_id . "> lost! Up for another game?", $user->slack_id);
            }
        }
        // Setting the game state to be over.
        $this->game->state = 'concluded';
        $this->game->save();
    }

    public function abortGame(BotMan $bot)
    {
        // Getting the user
        $this->user = User::where('slack_id', $bot->getUser()->getId())->first();

        // Check if a game is LIVE where the current user is HOST
        $game_check = Game::where('state', 'live')
            ->where('host_id', $this->user->id)
            ->first();

        if(empty($game_check)) {
            $bot->reply("You are not currently hosting any open games, thus you cannot abort any! :thinking_face:");
        }else{
            $bot->reply(":scream: Okay, I'll abort that game for you..");
            $game_check->state = 'aborted';
            $game_check->save();

            $participants = GameParticipant::where('game_id', $game_check)->get();
            foreach ($participants AS $participant) {
                if($participant->participant_id == $this->user->id) {
                    continue;
                }
                $user = User::find($participant->participant_id);
                $bot->say("Your current game was aborted by the host! Please start a new game to continue playing..", $user->slack_id);
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
            return;
        }else{
            $bot->reply(":scream: Okay, I'll cancel that game for you..");
            $game_check->state = 'cancelled';
            $game_check->save();
        }
    }

    public function say(BotMan $bot) {
        // Getting the user
        $this->user = User::where('slack_id', $bot->getUser()->getId())->first();

        $this->game = Game::where('state', 'live')
            ->first();
        if(empty($this->game)) {
            $bot->reply("You are not currently participating in any games.");
            return;
        }

        $this->current_participant = GameParticipant::where('game_id', $this->game->id)
            ->where('participant_id', $this->user->id)
            ->first();
        if(empty($this->current_participant)) {
            $bot->reply("You are not currently participating in any games.");
            return;
        }

        $message = substr($bot->getMessage()->getText(), 4);
        $this->participants = GameParticipant::where('game_id', $this->game->id)->get();
        foreach ($this->participants as $participant) {
            if($participant->participant_id != $this->user->id) {
                $user = User::find($participant->participant_id);
                $bot->say("<@" . $this->user->slack_id . "> says " . $message, $user->slack_id);
            }
        }
    }

    private function initRound(BotMan $bot, $participants, $no_of_dice, $round, $loser_id = null) {
        foreach ($participants AS $participant) {
            $player = User::find($participant->participant_id);

            if($round > 0) {
                $last_roll = Roll::where('game_id', $this->game->id)
                    ->where('participant_id', $participant->participant_id)
                    ->orderBy('round', 'desc')
                    ->first();
                $current_dice_count = count(json_decode($last_roll->roll));
                if($current_dice_count == 1 && $loser_id != $participant->participant_id) {
                    // Participant currently being looped over won and will be removed from the game..
                    $bot->say("Hi, you won the game! Congrats! :meat_on_bone:", $player->slack_id);
                    foreach ($this->current_round_participants as $crp) {
                        if($crp == $participant) {
                            continue;
                        }
                        $user = User::find($crp->participant_id);
                        $bot->say("<@" . $player->slack_id . "> won and has been removed from the game!", $user->slack_id);
                        if($this->current_round_participants->count() - 1 == 1) {
                            $this->endGame($bot, $loser_id);
                            return;
                        }
                    }
                    continue;
                }else{
                    if($loser_id == $participant->participant_id) {
                        $dice_to_roll = $current_dice_count;
                    }else{
                        $dice_to_roll = $current_dice_count - 1;
                    }
                }
            }else{
                $dice_to_roll = $no_of_dice;
            }

            $dice = $this->rollDice($dice_to_roll);

            $roll = new Roll;
            $roll->roll = json_encode($dice);
            $roll->game_id = $this->game->id;
            $roll->participant_id = $participant->participant_id;
            $roll->round = $round;
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

    private function setCurrentRoundParticipants() {
        if(!isset($this->participants)) {
            throw new \Exception("You cannot call setCurrentRoundParticipants without having set participants first!");
        }
        $rolls = Roll::where('game_id', $this->game->id)
            ->orderBy('round', 'desc')
            ->get();
        $this->current_round_rolls = $rolls->where('round', $rolls->first()->round)->flatten();
        $this->current_round_participants = collect();
        foreach ($this->current_round_rolls AS $current_round_participant) {
            $this->current_round_participants->push($this->participants->where('participant_id', $current_round_participant->participant_id)->first());
        }
        $this->current_round_participants = $this->current_round_participants->sortBy('participant_order');
    }

    private function announceMessageToParticipants($bot, $participants, $message) {
        foreach ($participants as $participant) {
            $user = User::find($participant->participant_id);
            $bot->say($message, $user->slack_id);
        }
    }

    private function determineParticipantOrder() {
        // If no calls were made yet, take 0 and 1
        // If a call was made
    }

}
