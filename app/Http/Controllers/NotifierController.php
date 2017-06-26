<?php
/**
* Created by PhpStorm.
* User: Danny
* Date: 15/06/2017
* Time: 06:37 PM
*/

namespace App\Http\Controllers;


use App\Notifications\ReviewerNotifier;
use App\SlackToken;
use App\User;
use function dd;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use function json_decode;

class NotifierController
{
    use Notifiable;

    /**
    * Trigger the notification
    *
    * @param Request $request
    */
    public function index(Request $request)
    {
        $notification = json_decode($request->toArray()["payload"], true);

        if (!$notification) return;
        if ($notification["action"] !== "review_requested") return;

        $username = $notification["requested_reviewer"]["login"];
        $slackToken = SlackToken::where("github_username", $username)->first();

        if ($slackToken) {
            $user = User::where("id", $slackToken->user_id)->first();
            $user->notify(new ReviewerNotifier($notification));
        }
    }


    public function noAllowed()
    {
        return view("notifier.notallowed");
    }
}