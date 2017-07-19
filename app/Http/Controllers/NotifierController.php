<?php
/**
 * Created by PhpStorm.
 * User: Danny
 * Date: 15/06/2017
 * Time: 06:37 PM
 */

namespace App\Http\Controllers;


use App\Notifications\RequestChanges;
use App\Notifications\RequestReview;
use App\SlackToken;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;

class NotifierController
{
    use Notifiable;

    public $notification;

    /**
     * Parse the Github payload
     *
     * @param Request $request
     */
    public function index(Request $request)
    {

        $this->notification = json_decode($request->toArray()["payload"], true);

        // We need to check if the action key exists because Github send us a request to verify
        // if the given endpoint exists, otherwise is an event that has been triggered
        if (!array_key_exists("action", $this->notification)) return;

        $validActions = ["review_requested", "submitted"];

        $action = $this->notification["action"];
        if (!in_array($action, $validActions)) return;

        define("REVIEW_REQUESTED", $action == "review_requested");
        define("CHANGES_REQUESTED", $action == "submitted" && $this->notification["review"]['state'] == "changes_requested");

        if (REVIEW_REQUESTED) {
            $this->reviewRequested();
        } else if (CHANGES_REQUESTED) {
            $this->changesRequested();
        }
    }


    /**
     * Notify the reviewer
     */
    public function reviewRequested()
    {
        $username = $this->notification["requested_reviewer"]["login"];
        $this->notify($username);
    }


    /**
     * Notify the pull request owner
     */
    public function changesRequested()
    {
        $username = $this->notification["pull_request"]["user"]["login"];
        $this->notify($username);
    }


    /**
     * Trigger the notification
     *
     * @param string $username
     */
    public function notify($username)
    {
        $slackToken = SlackToken::where("github_username", $username)->first();
        if ($slackToken) {
            $user = User::where("id", $slackToken->user_id)->first();

            if (REVIEW_REQUESTED) {
                $user->notify(new RequestReview($this->notification));
            } else if (CHANGES_REQUESTED) {
                $user->notify(new RequestChanges($this->notification));
            }
        }
    }


    public function noAllowed()
    {
        return view("notifier.notallowed");
    }
}