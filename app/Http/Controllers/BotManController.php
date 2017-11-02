<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Models\Installation;
use Log;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');
        $botman->listen();
    }

    public function oauthRedirect(Request $request)
    {
        Log::info("OAuth Redirect hit.");

        $result = json_decode(file_get_contents("https://slack.com/api/oauth.access"
            . "?code=" . $request->get('code')
            . "&client_id=" . env('SLACK_CLIENT_ID')
            . "&client_secret=" . env('SLACK_CLIENT_SECRET')
        ));

        if(!$result->ok) {
            Log::error("The slack app installation failed.");
            $alert = [
                'status' => 'error',
                'message' => 'The installation of the App failed! If you didn\'t initiate this, then please try again..'
            ];
            return view('main', $alert);
        }

        Installation::updateOrCreate(
            [
                'team_id' => $result->team_id
            ],
            [
                'team_name' => $result->team_name,
                'slack_bot_access_token' => $result->bot->bot_access_token
            ]
        );

        Log::info("New app installation complete! team_id=$result->team_id - team_name=$result->team_name");

        // Notify Bot Island of new installation..
        $webhook_data = json_encode([
            "username"  => "App Manager",
            "text"      => "*$result->team_name* just installed Liar's Dice!",
        ]);

        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/json',
                'content' => $webhook_data
            ]
        ];

        $context  = stream_context_create($opts);
        file_get_contents(env('BOTISLAND_INCOMING_WEBHOOK_URL'), false, $context);

        $alert = [
            'status' => 'success',
            'message' => 'The installation of the App was successful! You are now ready to play Lair\'s Dice!'
        ];
        return view('main', $alert);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }
}
