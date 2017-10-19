<?php

namespace App\Libraries;


use App\Notifications\PushOnOpenPullRequest;
use App\Notifications\RequestChanges;
use App\Notifications\RequestReview;
use App\Notifications\MentionInComment;
use App\SlackToken;
use App\User;

class GithubNotification
{
    public $notification;
    public $pushOnOpenPullRequestData = [];
    public $actions = [
        "reviewRequested" => false,
        "changesRequested" => false,
        "mentionedInComment" => false,
        "pushOnOpenPullRequest" => false,
    ];

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

        $this->notification = json_decode($this->notification->toArray()["payload"], true);

        // We need to check if the action key exists because Github send us a request to verify
        // if the given endpoint exists, otherwise is an event that has been triggered
        if (!array_key_exists("action", $this->notification)) return;

        $validActions = ["review_requested", "submitted", "created", "synchronize"];

        $action = $this->notification["action"];
        if (!in_array($action, $validActions)) {
            $actionsList = implode("', '", $validActions);
            echo "The action '{$action}' is not valid. Only '{$actionsList}' actions are supported at this moment.\n";
            return;
        }

        $this->actions["reviewRequested"] = $action === "review_requested";
        $this->actions["changesRequested"] = $action === "submitted" &&
                                             $this->notification["review"]['state'] === "changes_requested";
        $this->actions["mentionedInComment"] = $action === "created";
        $this->actions["pushOnOpenPullRequest"] = $action === "synchronize";

        if ($this->actions["reviewRequested"]) {
            $this->reviewRequested();
        } else if ($this->actions["changesRequested"]) {
            $this->changesRequested();
        } else if ($this->actions["mentionedInComment"]) {
            $this->mentionInComment();
        } else if ($this->actions["pushOnOpenPullRequest"]) {
            $this->pushOnOpenPullRequest();
        }
    }

    /**
     * Notify the reviewer
     */
    public function reviewRequested()
    {
        // Remember that the Webhook is triggered for each reviewer in the pull request
        $reviewer = $this->notification["requested_reviewer"]["login"];
        $this->notify($reviewer);
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
     * Notify to all mentioned users in a comment
     */
    public function mentionInComment()
    {
        $mentionedUsers = Utils::extractUsernames($this->notification["comment"]["body"]);
        if (count($mentionedUsers)) {
            foreach ($mentionedUsers as $user) {
                $this->notify($user);
            }
        } else {
            echo "There are no mentions in this comment.\n";
        }
    }

    /**
     * Notify to all reviewer in the open pull request
     */
    public function pushOnOpenPullRequest()
    {
        if (!$this->notification["pull_request"]["state"] == "open") {
            echo "This pull request is not open";
            return;
        }

        $reviewers = $this->notification["pull_request"]["requested_reviewers"];
        if (count($reviewers)) {
            foreach ($reviewers as $reviewer) {
                $this->pushOnOpenPullRequestData = $this->pushOnOpenPullRequestData($reviewer);

                $this->notify($reviewer["login"]);
            }
        } else {
            echo "There are no reviewers in this Pull Request.\n";
        }
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
                $notification = new RequestReview($this->requestReviewData());
            } else if ($this->actions["changesRequested"]) {
                $notification = new RequestChanges($this->requestChangesData());
            } else if ($this->actions["mentionedInComment"]) {
                $notification = new MentionInComment($this->mentionInCommentData());
            } else if ($this->actions["pushOnOpenPullRequest"]) {
                $notification = new PushOnOpenPullRequest($this->pushOnOpenPullRequestData);
            } else {
                return;
            }

            $user->notify($notification);
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
            "username" => $this->notification["sender"]["login"],
            "title" => $this->notification["pull_request"]["title"],
            "url" => $this->notification["pull_request"]["html_url"],
            "changed_files" => $this->notification["pull_request"]["changed_files"],
            "repository" => $this->notification["repository"]["name"],
            "from" => "Github"
        ];
    }

    /**
     * Constructs the data required for the request changes notification
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

    /**
     * Constructs the data required for the mention in comment notification
     *
     * @return array
     */
    public function mentionInCommentData()
    {
        return [
            "username" => $this->notification["comment"]["user"]["login"],
            "title" => $this->notification["pull_request"]["title"],
            "url" => $this->notification["comment"]["html_url"],
            "repository" => $this->notification["repository"]["name"],
            "comment" => $this->notification["comment"]["body"],
            "from" => "Github"
        ];
    }

    /**
     * Constructs the data required for the push on open pull request notification
     *
     * @param array $reviewer
     * @return array
     */
    public function pushOnOpenPullRequestData(array $reviewer)
    {
        return [
            "username" => $this->notification["pull_request"]["user"]["login"],
            "reviewer" => $reviewer["login"],
            "title" => $this->notification["pull_request"]["title"],
            "url" => $this->notification["pull_request"]["html_url"],
            "repository" => $this->notification["repository"]["name"],
            "from" => "Github"
        ];
    }
}
