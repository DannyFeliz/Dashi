<?php

namespace App\Libraries;


use App\Notifications\RequestReview;
use App\SlackToken;
use App\User;

class BitbucketNotification
{
    public $notification;


    /**
     * GithubNotification constructor.
     * @param $notification
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
        $this->run();
    }


    public function run()
    {
        $action = $this->notification->header("X-Event-Key");
        $validActions = ["pullrequest:created"];

        if (!in_array($action, $validActions)) {
            echo "The action '{$action}' is not valid. Only 'pullrequest:created' action is supported at this moment.\n";
            return;
        }

        $reviewers = $this->notification["pullrequest"]["reviewers"];
        foreach ($reviewers as $reviewer) {
            $this->notify($reviewer["username"]);
        }
    }


    /**
     * Trigger the notification
     *
     * @param string $username
     */
    public function notify($username)
    {
        $slackToken = SlackToken::where("bitbucket_username", $username)->first();
        if ($slackToken) {
            $user = User::where("id", $slackToken->user_id)->first();
            $user->notify(new RequestReview($this->requestReviewData()));
        }
    }

    /**
     * Constructs the data required for the request review notification
     *
     * @return array
     */
    public function requestReviewData()
    {
        return [
            "username" => $this->notification["pullrequest"]["author"]["username"],
            "title" => $this->notification["pullrequest"]["title"],
            "url" =>  $this->notification["pullrequest"]["links"]["html"]["href"],
            "repository" => $this->notification["pullrequest"]["destination"]["repository"]["name"],
            "from" => "Bitbucket"
        ];
    }
}