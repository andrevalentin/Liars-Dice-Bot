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
            return response()->json([
                "status" => "error",
                "message" => "Something went wrong.."
            ], 500);
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

        // Notify Bot Island somehow..

        return response()->json([
            "status" => "success",
            "message" => "Nice, now go play Liar's dice!"
        ], 200);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }
}
