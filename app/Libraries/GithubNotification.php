<?php

namespace App\Libraries;


use App\Notifications\RequestChanges;
use App\Notifications\RequestReview;
use App\SlackToken;
use App\User;

class GithubNotification
{
    public $notification;
    public $actions = [
        "reviewRequested" => false,
        "changesRequested" => false,
    ];


    /**
     * GithubNotification constructor.
     * @param $notification
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }


    public function run()
    {
        $this->notification = json_decode($this->notification->toArray()["payload"], true);

        // We need to check if the action key exists because Github send us a request to verify
        // if the given endpoint exists, otherwise is an event that has been triggered
        if (!array_key_exists("action", $this->notification)) return;

        $validActions = ["review_requested", "submitted"];

        $action = $this->notification["action"];
        if (!in_array($action, $validActions)) return;

        $this->actions["reviewRequested"] = $action == "review_requested";
        $this->actions["changesRequested"] = $action == "submitted" &&
                                             $this->notification["review"]['state'] == "changes_requested";

        if ($this->actions["reviewRequested"]) {
            $this->reviewRequested();
        } else if ($this->actions["changesRequested"]) {
            $this->changesRequested();
        }
    }


    /**
     * Notify each reviewer in the list
     */
    public function reviewRequested()
    {
        $reviewers = $this->notification["pull_request"]["requested_reviewers"];

        foreach ($reviewers as $reviewer) {
            $this->notify($reviewer["login"]);
        }
    }


    /**
     * Notify to the pull request owner about the changes request
     */
    public function changesRequested()
    {
        $username = $this->notification["pull_request"]["user"]["login"];
        $this->notify($username);
    }


    /**
     * Dispatch the corresponding notification
     *
     * @param string $username
     */
    public function notify($username)
    {
        $slackToken = SlackToken::where("github_username", $username)->first();
        if ($slackToken) {
            $user = User::where("id", $slackToken->user_id)->first();
            if ($this->actions["reviewRequested"]) {
                $user->notify(new RequestReview($this->requestReviewData()));
            } else if ($this->actions["changesRequested"]) {
                $user->notify(new RequestChanges($this->requestChangesData()));
            }
        }
    }

    public function requestReviewData()
    {
        return [
            "username" => $this->notification["sender"]["login"],
            "title" => $this->notification["pull_request"]["title"],
            "url" => $this->notification["pull_request"]["html_url"],
            "changed_files" => $this->notification["pull_request"]["changed_files"],
            "repository" => $this->notification["repository"]["name"],
            "from" => "Github"
        ];
    }

    /**
     * Array
     *
     * @return array
     */
    public function requestChangesData()
    {
        return [
            "username" => $this->notification["sender"]["login"],
            "title" => $this->notification["pull_request"]["title"],
            "url" => $this->notification["pull_request"]["html_url"],
            "repository" => $this->notification["repository"]["name"],
            "comment" => $this->notification["review"]["body"],
            "from" => "Github"
        ];
    }
}