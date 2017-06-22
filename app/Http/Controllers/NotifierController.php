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
     * @param Request $request
     */
    public function index(Request $request)
    {
        // Convert to array the request payload
        $notification = json_decode($request->toArray()["payload"], true);

        // We only want to trigger the notification if a review is requested
        if ($notification["action"] !== "review_requested") return;

        $reviewers = $notification["pull_request"]["requested_reviewers"];

        // Notify each user marked in the reviewer list
        foreach ($reviewers as $reviewer) {
            $currentUser = SlackToken::where("github_username", $reviewer["login"])->first();
            if ($currentUser) {
                $user = User::where("id", $currentUser->user_id)->first();
                $user->notify(new ReviewerNotifier($notification));
            }
        }
    }

    public function noAllowed()
    {
        return view("notifier.notallowed");
    }
}