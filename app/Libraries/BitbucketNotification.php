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