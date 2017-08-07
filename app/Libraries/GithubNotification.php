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


    /**
     * @return mixed
     */
    public function githubJson()
    {
        return $this->notification = json_decode('{"payload": {
          "action": "review_requested",
          "number": 8,
          "pull_request": {
            "url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/8",
            "id": 131249324,
            "html_url": "https://github.com/DannyFeliz/Dashi/pull/8",
            "diff_url": "https://github.com/DannyFeliz/Dashi/pull/8.diff",
            "patch_url": "https://github.com/DannyFeliz/Dashi/pull/8.patch",
            "issue_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/8",
            "number": 8,
            "state": "open",
            "locked": false,
            "title": "Create file3.txt",
            "user": {
              "login": "DannyFeliz",
              "id": 5460365,
              "avatar_url": "https://avatars1.githubusercontent.com/u/5460365?v=4",
              "gravatar_id": "",
              "url": "https://api.github.com/users/DannyFeliz",
              "html_url": "https://github.com/DannyFeliz",
              "followers_url": "https://api.github.com/users/DannyFeliz/followers",
              "following_url": "https://api.github.com/users/DannyFeliz/following{/other_user}",
              "gists_url": "https://api.github.com/users/DannyFeliz/gists{/gist_id}",
              "starred_url": "https://api.github.com/users/DannyFeliz/starred{/owner}{/repo}",
              "subscriptions_url": "https://api.github.com/users/DannyFeliz/subscriptions",
              "organizations_url": "https://api.github.com/users/DannyFeliz/orgs",
              "repos_url": "https://api.github.com/users/DannyFeliz/repos",
              "events_url": "https://api.github.com/users/DannyFeliz/events{/privacy}",
              "received_events_url": "https://api.github.com/users/DannyFeliz/received_events",
              "type": "User",
              "site_admin": false
            },
            "body": "",
            "created_at": "2017-07-19T06:57:09Z",
            "updated_at": "2017-08-01T00:13:48Z",
            "closed_at": null,
            "merged_at": null,
            "merge_commit_sha": "eb4ee0d3161ea4fffc0769cdd83200a09505c5ad",
            "assignee": null,
            "assignees": [
        
            ],
            "requested_reviewers": [
              {
                "login": "MrFather",
                "id": 18308892,
                "avatar_url": "https://avatars3.githubusercontent.com/u/18308892?v=4",
                "gravatar_id": "",
                "url": "https://api.github.com/users/MrFather",
                "html_url": "https://github.com/MrFather",
                "followers_url": "https://api.github.com/users/MrFather/followers",
                "following_url": "https://api.github.com/users/MrFather/following{/other_user}",
                "gists_url": "https://api.github.com/users/MrFather/gists{/gist_id}",
                "starred_url": "https://api.github.com/users/MrFather/starred{/owner}{/repo}",
                "subscriptions_url": "https://api.github.com/users/MrFather/subscriptions",
                "organizations_url": "https://api.github.com/users/MrFather/orgs",
                "repos_url": "https://api.github.com/users/MrFather/repos",
                "events_url": "https://api.github.com/users/MrFather/events{/privacy}",
                "received_events_url": "https://api.github.com/users/MrFather/received_events",
                "type": "User",
                "site_admin": false
              },
              {
                "login": "DannyFeliz",
                "id": 18308892,
                "avatar_url": "https://avatars3.githubusercontent.com/u/18308892?v=4",
                "gravatar_id": "",
                "url": "https://api.github.com/users/DannyFeliz",
                "html_url": "https://github.com/DannyFeliz",
                "followers_url": "https://api.github.com/users/DannyFeliz/followers",
                "following_url": "https://api.github.com/users/DannyFeliz/following{/other_user}",
                "gists_url": "https://api.github.com/users/DannyFeliz/gists{/gist_id}",
                "starred_url": "https://api.github.com/users/DannyFeliz/starred{/owner}{/repo}",
                "subscriptions_url": "https://api.github.com/users/DannyFeliz/subscriptions",
                "organizations_url": "https://api.github.com/users/DannyFeliz/orgs",
                "repos_url": "https://api.github.com/users/DannyFeliz/repos",
                "events_url": "https://api.github.com/users/DannyFeliz/events{/privacy}",
                "received_events_url": "https://api.github.com/users/DannyFeliz/received_events",
                "type": "User",
                "site_admin": false
              }
            ],
            "milestone": null,
            "commits_url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/8/commits",
            "review_comments_url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/8/comments",
            "review_comment_url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/comments{/number}",
            "comments_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/8/comments",
            "statuses_url": "https://api.github.com/repos/DannyFeliz/Dashi/statuses/50231cdd38bb5bf20ce93ce331ce4b5d43d4df51",
            "head": {
              "label": "DannyFeliz:test2",
              "ref": "test2",
              "sha": "50231cdd38bb5bf20ce93ce331ce4b5d43d4df51",
              "user": {
                "login": "DannyFeliz",
                "id": 5460365,
                "avatar_url": "https://avatars1.githubusercontent.com/u/5460365?v=4",
                "gravatar_id": "",
                "url": "https://api.github.com/users/DannyFeliz",
                "html_url": "https://github.com/DannyFeliz",
                "followers_url": "https://api.github.com/users/DannyFeliz/followers",
                "following_url": "https://api.github.com/users/DannyFeliz/following{/other_user}",
                "gists_url": "https://api.github.com/users/DannyFeliz/gists{/gist_id}",
                "starred_url": "https://api.github.com/users/DannyFeliz/starred{/owner}{/repo}",
                "subscriptions_url": "https://api.github.com/users/DannyFeliz/subscriptions",
                "organizations_url": "https://api.github.com/users/DannyFeliz/orgs",
                "repos_url": "https://api.github.com/users/DannyFeliz/repos",
                "events_url": "https://api.github.com/users/DannyFeliz/events{/privacy}",
                "received_events_url": "https://api.github.com/users/DannyFeliz/received_events",
                "type": "User",
                "site_admin": false
              },
              "repo": {
                "id": 94479088,
                "name": "Dashi",
                "full_name": "DannyFeliz/Dashi",
                "owner": {
                  "login": "DannyFeliz",
                  "id": 5460365,
                  "avatar_url": "https://avatars1.githubusercontent.com/u/5460365?v=4",
                  "gravatar_id": "",
                  "url": "https://api.github.com/users/DannyFeliz",
                  "html_url": "https://github.com/DannyFeliz",
                  "followers_url": "https://api.github.com/users/DannyFeliz/followers",
                  "following_url": "https://api.github.com/users/DannyFeliz/following{/other_user}",
                  "gists_url": "https://api.github.com/users/DannyFeliz/gists{/gist_id}",
                  "starred_url": "https://api.github.com/users/DannyFeliz/starred{/owner}{/repo}",
                  "subscriptions_url": "https://api.github.com/users/DannyFeliz/subscriptions",
                  "organizations_url": "https://api.github.com/users/DannyFeliz/orgs",
                  "repos_url": "https://api.github.com/users/DannyFeliz/repos",
                  "events_url": "https://api.github.com/users/DannyFeliz/events{/privacy}",
                  "received_events_url": "https://api.github.com/users/DannyFeliz/received_events",
                  "type": "User",
                  "site_admin": false
                },
                "private": false,
                "html_url": "https://github.com/DannyFeliz/Dashi",
                "description": null,
                "fork": false,
                "url": "https://api.github.com/repos/DannyFeliz/Dashi",
                "forks_url": "https://api.github.com/repos/DannyFeliz/Dashi/forks",
                "keys_url": "https://api.github.com/repos/DannyFeliz/Dashi/keys{/key_id}",
                "collaborators_url": "https://api.github.com/repos/DannyFeliz/Dashi/collaborators{/collaborator}",
                "teams_url": "https://api.github.com/repos/DannyFeliz/Dashi/teams",
                "hooks_url": "https://api.github.com/repos/DannyFeliz/Dashi/hooks",
                "issue_events_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/events{/number}",
                "events_url": "https://api.github.com/repos/DannyFeliz/Dashi/events",
                "assignees_url": "https://api.github.com/repos/DannyFeliz/Dashi/assignees{/user}",
                "branches_url": "https://api.github.com/repos/DannyFeliz/Dashi/branches{/branch}",
                "tags_url": "https://api.github.com/repos/DannyFeliz/Dashi/tags",
                "blobs_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/blobs{/sha}",
                "git_tags_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/tags{/sha}",
                "git_refs_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/refs{/sha}",
                "trees_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/trees{/sha}",
                "statuses_url": "https://api.github.com/repos/DannyFeliz/Dashi/statuses/{sha}",
                "languages_url": "https://api.github.com/repos/DannyFeliz/Dashi/languages",
                "stargazers_url": "https://api.github.com/repos/DannyFeliz/Dashi/stargazers",
                "contributors_url": "https://api.github.com/repos/DannyFeliz/Dashi/contributors",
                "subscribers_url": "https://api.github.com/repos/DannyFeliz/Dashi/subscribers",
                "subscription_url": "https://api.github.com/repos/DannyFeliz/Dashi/subscription",
                "commits_url": "https://api.github.com/repos/DannyFeliz/Dashi/commits{/sha}",
                "git_commits_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/commits{/sha}",
                "comments_url": "https://api.github.com/repos/DannyFeliz/Dashi/comments{/number}",
                "issue_comment_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/comments{/number}",
                "contents_url": "https://api.github.com/repos/DannyFeliz/Dashi/contents/{+path}",
                "compare_url": "https://api.github.com/repos/DannyFeliz/Dashi/compare/{base}...{head}",
                "merges_url": "https://api.github.com/repos/DannyFeliz/Dashi/merges",
                "archive_url": "https://api.github.com/repos/DannyFeliz/Dashi/{archive_format}{/ref}",
                "downloads_url": "https://api.github.com/repos/DannyFeliz/Dashi/downloads",
                "issues_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues{/number}",
                "pulls_url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls{/number}",
                "milestones_url": "https://api.github.com/repos/DannyFeliz/Dashi/milestones{/number}",
                "notifications_url": "https://api.github.com/repos/DannyFeliz/Dashi/notifications{?since,all,participating}",
                "labels_url": "https://api.github.com/repos/DannyFeliz/Dashi/labels{/name}",
                "releases_url": "https://api.github.com/repos/DannyFeliz/Dashi/releases{/id}",
                "deployments_url": "https://api.github.com/repos/DannyFeliz/Dashi/deployments",
                "created_at": "2017-06-15T21:09:33Z",
                "updated_at": "2017-07-23T05:09:17Z",
                "pushed_at": "2017-07-24T08:57:15Z",
                "git_url": "git://github.com/DannyFeliz/Dashi.git",
                "ssh_url": "git@github.com:DannyFeliz/Dashi.git",
                "clone_url": "https://github.com/DannyFeliz/Dashi.git",
                "svn_url": "https://github.com/DannyFeliz/Dashi",
                "homepage": null,
                "size": 254,
                "stargazers_count": 1,
                "watchers_count": 1,
                "language": "PHP",
                "has_issues": true,
                "has_projects": true,
                "has_downloads": true,
                "has_wiki": true,
                "has_pages": false,
                "forks_count": 1,
                "mirror_url": null,
                "open_issues_count": 3,
                "forks": 1,
                "open_issues": 3,
                "watchers": 1,
                "default_branch": "master"
              }
            },
            "base": {
              "label": "DannyFeliz:master",
              "ref": "master",
              "sha": "cc0839630f85742e46cd39eca1d1afd892c9af4f",
              "user": {
                "login": "DannyFeliz",
                "id": 5460365,
                "avatar_url": "https://avatars1.githubusercontent.com/u/5460365?v=4",
                "gravatar_id": "",
                "url": "https://api.github.com/users/DannyFeliz",
                "html_url": "https://github.com/DannyFeliz",
                "followers_url": "https://api.github.com/users/DannyFeliz/followers",
                "following_url": "https://api.github.com/users/DannyFeliz/following{/other_user}",
                "gists_url": "https://api.github.com/users/DannyFeliz/gists{/gist_id}",
                "starred_url": "https://api.github.com/users/DannyFeliz/starred{/owner}{/repo}",
                "subscriptions_url": "https://api.github.com/users/DannyFeliz/subscriptions",
                "organizations_url": "https://api.github.com/users/DannyFeliz/orgs",
                "repos_url": "https://api.github.com/users/DannyFeliz/repos",
                "events_url": "https://api.github.com/users/DannyFeliz/events{/privacy}",
                "received_events_url": "https://api.github.com/users/DannyFeliz/received_events",
                "type": "User",
                "site_admin": false
              },
              "repo": {
                "id": 94479088,
                "name": "Dashi",
                "full_name": "DannyFeliz/Dashi",
                "owner": {
                  "login": "DannyFeliz",
                  "id": 5460365,
                  "avatar_url": "https://avatars1.githubusercontent.com/u/5460365?v=4",
                  "gravatar_id": "",
                  "url": "https://api.github.com/users/DannyFeliz",
                  "html_url": "https://github.com/DannyFeliz",
                  "followers_url": "https://api.github.com/users/DannyFeliz/followers",
                  "following_url": "https://api.github.com/users/DannyFeliz/following{/other_user}",
                  "gists_url": "https://api.github.com/users/DannyFeliz/gists{/gist_id}",
                  "starred_url": "https://api.github.com/users/DannyFeliz/starred{/owner}{/repo}",
                  "subscriptions_url": "https://api.github.com/users/DannyFeliz/subscriptions",
                  "organizations_url": "https://api.github.com/users/DannyFeliz/orgs",
                  "repos_url": "https://api.github.com/users/DannyFeliz/repos",
                  "events_url": "https://api.github.com/users/DannyFeliz/events{/privacy}",
                  "received_events_url": "https://api.github.com/users/DannyFeliz/received_events",
                  "type": "User",
                  "site_admin": false
                },
                "private": false,
                "html_url": "https://github.com/DannyFeliz/Dashi",
                "description": null,
                "fork": false,
                "url": "https://api.github.com/repos/DannyFeliz/Dashi",
                "forks_url": "https://api.github.com/repos/DannyFeliz/Dashi/forks",
                "keys_url": "https://api.github.com/repos/DannyFeliz/Dashi/keys{/key_id}",
                "collaborators_url": "https://api.github.com/repos/DannyFeliz/Dashi/collaborators{/collaborator}",
                "teams_url": "https://api.github.com/repos/DannyFeliz/Dashi/teams",
                "hooks_url": "https://api.github.com/repos/DannyFeliz/Dashi/hooks",
                "issue_events_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/events{/number}",
                "events_url": "https://api.github.com/repos/DannyFeliz/Dashi/events",
                "assignees_url": "https://api.github.com/repos/DannyFeliz/Dashi/assignees{/user}",
                "branches_url": "https://api.github.com/repos/DannyFeliz/Dashi/branches{/branch}",
                "tags_url": "https://api.github.com/repos/DannyFeliz/Dashi/tags",
                "blobs_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/blobs{/sha}",
                "git_tags_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/tags{/sha}",
                "git_refs_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/refs{/sha}",
                "trees_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/trees{/sha}",
                "statuses_url": "https://api.github.com/repos/DannyFeliz/Dashi/statuses/{sha}",
                "languages_url": "https://api.github.com/repos/DannyFeliz/Dashi/languages",
                "stargazers_url": "https://api.github.com/repos/DannyFeliz/Dashi/stargazers",
                "contributors_url": "https://api.github.com/repos/DannyFeliz/Dashi/contributors",
                "subscribers_url": "https://api.github.com/repos/DannyFeliz/Dashi/subscribers",
                "subscription_url": "https://api.github.com/repos/DannyFeliz/Dashi/subscription",
                "commits_url": "https://api.github.com/repos/DannyFeliz/Dashi/commits{/sha}",
                "git_commits_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/commits{/sha}",
                "comments_url": "https://api.github.com/repos/DannyFeliz/Dashi/comments{/number}",
                "issue_comment_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/comments{/number}",
                "contents_url": "https://api.github.com/repos/DannyFeliz/Dashi/contents/{+path}",
                "compare_url": "https://api.github.com/repos/DannyFeliz/Dashi/compare/{base}...{head}",
                "merges_url": "https://api.github.com/repos/DannyFeliz/Dashi/merges",
                "archive_url": "https://api.github.com/repos/DannyFeliz/Dashi/{archive_format}{/ref}",
                "downloads_url": "https://api.github.com/repos/DannyFeliz/Dashi/downloads",
                "issues_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues{/number}",
                "pulls_url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls{/number}",
                "milestones_url": "https://api.github.com/repos/DannyFeliz/Dashi/milestones{/number}",
                "notifications_url": "https://api.github.com/repos/DannyFeliz/Dashi/notifications{?since,all,participating}",
                "labels_url": "https://api.github.com/repos/DannyFeliz/Dashi/labels{/name}",
                "releases_url": "https://api.github.com/repos/DannyFeliz/Dashi/releases{/id}",
                "deployments_url": "https://api.github.com/repos/DannyFeliz/Dashi/deployments",
                "created_at": "2017-06-15T21:09:33Z",
                "updated_at": "2017-07-23T05:09:17Z",
                "pushed_at": "2017-07-24T08:57:15Z",
                "git_url": "git://github.com/DannyFeliz/Dashi.git",
                "ssh_url": "git@github.com:DannyFeliz/Dashi.git",
                "clone_url": "https://github.com/DannyFeliz/Dashi.git",
                "svn_url": "https://github.com/DannyFeliz/Dashi",
                "homepage": null,
                "size": 254,
                "stargazers_count": 1,
                "watchers_count": 1,
                "language": "PHP",
                "has_issues": true,
                "has_projects": true,
                "has_downloads": true,
                "has_wiki": true,
                "has_pages": false,
                "forks_count": 1,
                "mirror_url": null,
                "open_issues_count": 3,
                "forks": 1,
                "open_issues": 3,
                "watchers": 1,
                "default_branch": "master"
              }
            },
            "_links": {
              "self": {
                "href": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/8"
              },
              "html": {
                "href": "https://github.com/DannyFeliz/Dashi/pull/8"
              },
              "issue": {
                "href": "https://api.github.com/repos/DannyFeliz/Dashi/issues/8"
              },
              "comments": {
                "href": "https://api.github.com/repos/DannyFeliz/Dashi/issues/8/comments"
              },
              "review_comments": {
                "href": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/8/comments"
              },
              "review_comment": {
                "href": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/comments{/number}"
              },
              "commits": {
                "href": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/8/commits"
              },
              "statuses": {
                "href": "https://api.github.com/repos/DannyFeliz/Dashi/statuses/50231cdd38bb5bf20ce93ce331ce4b5d43d4df51"
              }
            },
            "merged": false,
            "mergeable": true,
            "rebaseable": true,
            "mergeable_state": "clean",
            "merged_by": null,
            "comments": 1,
            "review_comments": 0,
            "maintainer_can_modify": false,
            "commits": 1,
            "additions": 1,
            "deletions": 0,
            "changed_files": 1
          },
          "requested_reviewer": {
            "login": "MrFather",
            "id": 18308892,
            "avatar_url": "https://avatars3.githubusercontent.com/u/18308892?v=4",
            "gravatar_id": "",
            "url": "https://api.github.com/users/MrFather",
            "html_url": "https://github.com/MrFather",
            "followers_url": "https://api.github.com/users/MrFather/followers",
            "following_url": "https://api.github.com/users/MrFather/following{/other_user}",
            "gists_url": "https://api.github.com/users/MrFather/gists{/gist_id}",
            "starred_url": "https://api.github.com/users/MrFather/starred{/owner}{/repo}",
            "subscriptions_url": "https://api.github.com/users/MrFather/subscriptions",
            "organizations_url": "https://api.github.com/users/MrFather/orgs",
            "repos_url": "https://api.github.com/users/MrFather/repos",
            "events_url": "https://api.github.com/users/MrFather/events{/privacy}",
            "received_events_url": "https://api.github.com/users/MrFather/received_events",
            "type": "User",
            "site_admin": false
          },
          "repository": {
            "id": 94479088,
            "name": "Dashi",
            "full_name": "DannyFeliz/Dashi",
            "owner": {
              "login": "DannyFeliz",
              "id": 5460365,
              "avatar_url": "https://avatars1.githubusercontent.com/u/5460365?v=4",
              "gravatar_id": "",
              "url": "https://api.github.com/users/DannyFeliz",
              "html_url": "https://github.com/DannyFeliz",
              "followers_url": "https://api.github.com/users/DannyFeliz/followers",
              "following_url": "https://api.github.com/users/DannyFeliz/following{/other_user}",
              "gists_url": "https://api.github.com/users/DannyFeliz/gists{/gist_id}",
              "starred_url": "https://api.github.com/users/DannyFeliz/starred{/owner}{/repo}",
              "subscriptions_url": "https://api.github.com/users/DannyFeliz/subscriptions",
              "organizations_url": "https://api.github.com/users/DannyFeliz/orgs",
              "repos_url": "https://api.github.com/users/DannyFeliz/repos",
              "events_url": "https://api.github.com/users/DannyFeliz/events{/privacy}",
              "received_events_url": "https://api.github.com/users/DannyFeliz/received_events",
              "type": "User",
              "site_admin": false
            },
            "private": false,
            "html_url": "https://github.com/DannyFeliz/Dashi",
            "description": null,
            "fork": false,
            "url": "https://api.github.com/repos/DannyFeliz/Dashi",
            "forks_url": "https://api.github.com/repos/DannyFeliz/Dashi/forks",
            "keys_url": "https://api.github.com/repos/DannyFeliz/Dashi/keys{/key_id}",
            "collaborators_url": "https://api.github.com/repos/DannyFeliz/Dashi/collaborators{/collaborator}",
            "teams_url": "https://api.github.com/repos/DannyFeliz/Dashi/teams",
            "hooks_url": "https://api.github.com/repos/DannyFeliz/Dashi/hooks",
            "issue_events_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/events{/number}",
            "events_url": "https://api.github.com/repos/DannyFeliz/Dashi/events",
            "assignees_url": "https://api.github.com/repos/DannyFeliz/Dashi/assignees{/user}",
            "branches_url": "https://api.github.com/repos/DannyFeliz/Dashi/branches{/branch}",
            "tags_url": "https://api.github.com/repos/DannyFeliz/Dashi/tags",
            "blobs_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/blobs{/sha}",
            "git_tags_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/tags{/sha}",
            "git_refs_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/refs{/sha}",
            "trees_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/trees{/sha}",
            "statuses_url": "https://api.github.com/repos/DannyFeliz/Dashi/statuses/{sha}",
            "languages_url": "https://api.github.com/repos/DannyFeliz/Dashi/languages",
            "stargazers_url": "https://api.github.com/repos/DannyFeliz/Dashi/stargazers",
            "contributors_url": "https://api.github.com/repos/DannyFeliz/Dashi/contributors",
            "subscribers_url": "https://api.github.com/repos/DannyFeliz/Dashi/subscribers",
            "subscription_url": "https://api.github.com/repos/DannyFeliz/Dashi/subscription",
            "commits_url": "https://api.github.com/repos/DannyFeliz/Dashi/commits{/sha}",
            "git_commits_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/commits{/sha}",
            "comments_url": "https://api.github.com/repos/DannyFeliz/Dashi/comments{/number}",
            "issue_comment_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/comments{/number}",
            "contents_url": "https://api.github.com/repos/DannyFeliz/Dashi/contents/{+path}",
            "compare_url": "https://api.github.com/repos/DannyFeliz/Dashi/compare/{base}...{head}",
            "merges_url": "https://api.github.com/repos/DannyFeliz/Dashi/merges",
            "archive_url": "https://api.github.com/repos/DannyFeliz/Dashi/{archive_format}{/ref}",
            "downloads_url": "https://api.github.com/repos/DannyFeliz/Dashi/downloads",
            "issues_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues{/number}",
            "pulls_url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls{/number}",
            "milestones_url": "https://api.github.com/repos/DannyFeliz/Dashi/milestones{/number}",
            "notifications_url": "https://api.github.com/repos/DannyFeliz/Dashi/notifications{?since,all,participating}",
            "labels_url": "https://api.github.com/repos/DannyFeliz/Dashi/labels{/name}",
            "releases_url": "https://api.github.com/repos/DannyFeliz/Dashi/releases{/id}",
            "deployments_url": "https://api.github.com/repos/DannyFeliz/Dashi/deployments",
            "created_at": "2017-06-15T21:09:33Z",
            "updated_at": "2017-07-23T05:09:17Z",
            "pushed_at": "2017-07-24T08:57:15Z",
            "git_url": "git://github.com/DannyFeliz/Dashi.git",
            "ssh_url": "git@github.com:DannyFeliz/Dashi.git",
            "clone_url": "https://github.com/DannyFeliz/Dashi.git",
            "svn_url": "https://github.com/DannyFeliz/Dashi",
            "homepage": null,
            "size": 254,
            "stargazers_count": 1,
            "watchers_count": 1,
            "language": "PHP",
            "has_issues": true,
            "has_projects": true,
            "has_downloads": true,
            "has_wiki": true,
            "has_pages": false,
            "forks_count": 1,
            "mirror_url": null,
            "open_issues_count": 3,
            "forks": 1,
            "open_issues": 3,
            "watchers": 1,
            "default_branch": "master"
          },
          "sender": {
            "login": "DannyFeliz",
            "id": 5460365,
            "avatar_url": "https://avatars1.githubusercontent.com/u/5460365?v=4",
            "gravatar_id": "",
            "url": "https://api.github.com/users/DannyFeliz",
            "html_url": "https://github.com/DannyFeliz",
            "followers_url": "https://api.github.com/users/DannyFeliz/followers",
            "following_url": "https://api.github.com/users/DannyFeliz/following{/other_user}",
            "gists_url": "https://api.github.com/users/DannyFeliz/gists{/gist_id}",
            "starred_url": "https://api.github.com/users/DannyFeliz/starred{/owner}{/repo}",
            "subscriptions_url": "https://api.github.com/users/DannyFeliz/subscriptions",
            "organizations_url": "https://api.github.com/users/DannyFeliz/orgs",
            "repos_url": "https://api.github.com/users/DannyFeliz/repos",
            "events_url": "https://api.github.com/users/DannyFeliz/events{/privacy}",
            "received_events_url": "https://api.github.com/users/DannyFeliz/received_events",
            "type": "User",
            "site_admin": false
          }
        }
    }', true);
    }


    public function changesRequestedJson()
    {
        return $this->notification = json_decode('{"payload": {
              "action": "submitted",
              "review": {
                "id": 54555476,
                "user": {
                  "login": "MrFather",
                  "id": 18308892,
                  "avatar_url": "https://avatars3.githubusercontent.com/u/18308892?v=4",
                  "gravatar_id": "",
                  "url": "https://api.github.com/users/MrFather",
                  "html_url": "https://github.com/MrFather",
                  "followers_url": "https://api.github.com/users/MrFather/followers",
                  "following_url": "https://api.github.com/users/MrFather/following{/other_user}",
                  "gists_url": "https://api.github.com/users/MrFather/gists{/gist_id}",
                  "starred_url": "https://api.github.com/users/MrFather/starred{/owner}{/repo}",
                  "subscriptions_url": "https://api.github.com/users/MrFather/subscriptions",
                  "organizations_url": "https://api.github.com/users/MrFather/orgs",
                  "repos_url": "https://api.github.com/users/MrFather/repos",
                  "events_url": "https://api.github.com/users/MrFather/events{/privacy}",
                  "received_events_url": "https://api.github.com/users/MrFather/received_events",
                  "type": "User",
                  "site_admin": false
                },
                "body": "Also fix the validation issue",
                "commit_id": "50231cdd38bb5bf20ce93ce331ce4b5d43d4df51",
                "submitted_at": "2017-08-07T02:37:18Z",
                "state": "changes_requested",
                "html_url": "https://github.com/DannyFeliz/Dashi/pull/8#pullrequestreview-54555476",
                "pull_request_url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/8",
                "_links": {
                  "html": {
                    "href": "https://github.com/DannyFeliz/Dashi/pull/8#pullrequestreview-54555476"
                  },
                  "pull_request": {
                    "href": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/8"
                  }
                }
              },
              "pull_request": {
                "url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/8",
                "id": 131249324,
                "html_url": "https://github.com/DannyFeliz/Dashi/pull/8",
                "diff_url": "https://github.com/DannyFeliz/Dashi/pull/8.diff",
                "patch_url": "https://github.com/DannyFeliz/Dashi/pull/8.patch",
                "issue_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/8",
                "number": 8,
                "state": "open",
                "locked": false,
                "title": "Create file3.txt",
                "user": {
                  "login": "DannyFeliz",
                  "id": 5460365,
                  "avatar_url": "https://avatars1.githubusercontent.com/u/5460365?v=4",
                  "gravatar_id": "",
                  "url": "https://api.github.com/users/DannyFeliz",
                  "html_url": "https://github.com/DannyFeliz",
                  "followers_url": "https://api.github.com/users/DannyFeliz/followers",
                  "following_url": "https://api.github.com/users/DannyFeliz/following{/other_user}",
                  "gists_url": "https://api.github.com/users/DannyFeliz/gists{/gist_id}",
                  "starred_url": "https://api.github.com/users/DannyFeliz/starred{/owner}{/repo}",
                  "subscriptions_url": "https://api.github.com/users/DannyFeliz/subscriptions",
                  "organizations_url": "https://api.github.com/users/DannyFeliz/orgs",
                  "repos_url": "https://api.github.com/users/DannyFeliz/repos",
                  "events_url": "https://api.github.com/users/DannyFeliz/events{/privacy}",
                  "received_events_url": "https://api.github.com/users/DannyFeliz/received_events",
                  "type": "User",
                  "site_admin": false
                },
                "body": "",
                "created_at": "2017-07-19T06:57:09Z",
                "updated_at": "2017-08-07T02:37:18Z",
                "closed_at": null,
                "merged_at": null,
                "merge_commit_sha": "ea1a8155c834d3c5c3b24e08364f318d1651a280",
                "assignee": null,
                "assignees": [
            
                ],
                "requested_reviewers": [
            
                ],
                "milestone": null,
                "commits_url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/8/commits",
                "review_comments_url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/8/comments",
                "review_comment_url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/comments{/number}",
                "comments_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/8/comments",
                "statuses_url": "https://api.github.com/repos/DannyFeliz/Dashi/statuses/50231cdd38bb5bf20ce93ce331ce4b5d43d4df51",
                "head": {
                  "label": "DannyFeliz:test2",
                  "ref": "test2",
                  "sha": "50231cdd38bb5bf20ce93ce331ce4b5d43d4df51",
                  "user": {
                    "login": "DannyFeliz",
                    "id": 5460365,
                    "avatar_url": "https://avatars1.githubusercontent.com/u/5460365?v=4",
                    "gravatar_id": "",
                    "url": "https://api.github.com/users/DannyFeliz",
                    "html_url": "https://github.com/DannyFeliz",
                    "followers_url": "https://api.github.com/users/DannyFeliz/followers",
                    "following_url": "https://api.github.com/users/DannyFeliz/following{/other_user}",
                    "gists_url": "https://api.github.com/users/DannyFeliz/gists{/gist_id}",
                    "starred_url": "https://api.github.com/users/DannyFeliz/starred{/owner}{/repo}",
                    "subscriptions_url": "https://api.github.com/users/DannyFeliz/subscriptions",
                    "organizations_url": "https://api.github.com/users/DannyFeliz/orgs",
                    "repos_url": "https://api.github.com/users/DannyFeliz/repos",
                    "events_url": "https://api.github.com/users/DannyFeliz/events{/privacy}",
                    "received_events_url": "https://api.github.com/users/DannyFeliz/received_events",
                    "type": "User",
                    "site_admin": false
                  },
                  "repo": {
                    "id": 94479088,
                    "name": "Dashi",
                    "full_name": "DannyFeliz/Dashi",
                    "owner": {
                      "login": "DannyFeliz",
                      "id": 5460365,
                      "avatar_url": "https://avatars1.githubusercontent.com/u/5460365?v=4",
                      "gravatar_id": "",
                      "url": "https://api.github.com/users/DannyFeliz",
                      "html_url": "https://github.com/DannyFeliz",
                      "followers_url": "https://api.github.com/users/DannyFeliz/followers",
                      "following_url": "https://api.github.com/users/DannyFeliz/following{/other_user}",
                      "gists_url": "https://api.github.com/users/DannyFeliz/gists{/gist_id}",
                      "starred_url": "https://api.github.com/users/DannyFeliz/starred{/owner}{/repo}",
                      "subscriptions_url": "https://api.github.com/users/DannyFeliz/subscriptions",
                      "organizations_url": "https://api.github.com/users/DannyFeliz/orgs",
                      "repos_url": "https://api.github.com/users/DannyFeliz/repos",
                      "events_url": "https://api.github.com/users/DannyFeliz/events{/privacy}",
                      "received_events_url": "https://api.github.com/users/DannyFeliz/received_events",
                      "type": "User",
                      "site_admin": false
                    },
                    "private": false,
                    "html_url": "https://github.com/DannyFeliz/Dashi",
                    "description": "Get a notification in Slack every time someone asks you to check his code on Github.",
                    "fork": false,
                    "url": "https://api.github.com/repos/DannyFeliz/Dashi",
                    "forks_url": "https://api.github.com/repos/DannyFeliz/Dashi/forks",
                    "keys_url": "https://api.github.com/repos/DannyFeliz/Dashi/keys{/key_id}",
                    "collaborators_url": "https://api.github.com/repos/DannyFeliz/Dashi/collaborators{/collaborator}",
                    "teams_url": "https://api.github.com/repos/DannyFeliz/Dashi/teams",
                    "hooks_url": "https://api.github.com/repos/DannyFeliz/Dashi/hooks",
                    "issue_events_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/events{/number}",
                    "events_url": "https://api.github.com/repos/DannyFeliz/Dashi/events",
                    "assignees_url": "https://api.github.com/repos/DannyFeliz/Dashi/assignees{/user}",
                    "branches_url": "https://api.github.com/repos/DannyFeliz/Dashi/branches{/branch}",
                    "tags_url": "https://api.github.com/repos/DannyFeliz/Dashi/tags",
                    "blobs_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/blobs{/sha}",
                    "git_tags_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/tags{/sha}",
                    "git_refs_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/refs{/sha}",
                    "trees_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/trees{/sha}",
                    "statuses_url": "https://api.github.com/repos/DannyFeliz/Dashi/statuses/{sha}",
                    "languages_url": "https://api.github.com/repos/DannyFeliz/Dashi/languages",
                    "stargazers_url": "https://api.github.com/repos/DannyFeliz/Dashi/stargazers",
                    "contributors_url": "https://api.github.com/repos/DannyFeliz/Dashi/contributors",
                    "subscribers_url": "https://api.github.com/repos/DannyFeliz/Dashi/subscribers",
                    "subscription_url": "https://api.github.com/repos/DannyFeliz/Dashi/subscription",
                    "commits_url": "https://api.github.com/repos/DannyFeliz/Dashi/commits{/sha}",
                    "git_commits_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/commits{/sha}",
                    "comments_url": "https://api.github.com/repos/DannyFeliz/Dashi/comments{/number}",
                    "issue_comment_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/comments{/number}",
                    "contents_url": "https://api.github.com/repos/DannyFeliz/Dashi/contents/{+path}",
                    "compare_url": "https://api.github.com/repos/DannyFeliz/Dashi/compare/{base}...{head}",
                    "merges_url": "https://api.github.com/repos/DannyFeliz/Dashi/merges",
                    "archive_url": "https://api.github.com/repos/DannyFeliz/Dashi/{archive_format}{/ref}",
                    "downloads_url": "https://api.github.com/repos/DannyFeliz/Dashi/downloads",
                    "issues_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues{/number}",
                    "pulls_url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls{/number}",
                    "milestones_url": "https://api.github.com/repos/DannyFeliz/Dashi/milestones{/number}",
                    "notifications_url": "https://api.github.com/repos/DannyFeliz/Dashi/notifications{?since,all,participating}",
                    "labels_url": "https://api.github.com/repos/DannyFeliz/Dashi/labels{/name}",
                    "releases_url": "https://api.github.com/repos/DannyFeliz/Dashi/releases{/id}",
                    "deployments_url": "https://api.github.com/repos/DannyFeliz/Dashi/deployments",
                    "created_at": "2017-06-15T21:09:33Z",
                    "updated_at": "2017-08-07T02:34:30Z",
                    "pushed_at": "2017-08-03T18:26:25Z",
                    "git_url": "git://github.com/DannyFeliz/Dashi.git",
                    "ssh_url": "git@github.com:DannyFeliz/Dashi.git",
                    "clone_url": "https://github.com/DannyFeliz/Dashi.git",
                    "svn_url": "https://github.com/DannyFeliz/Dashi",
                    "homepage": "http://dashinotify.com",
                    "size": 308,
                    "stargazers_count": 2,
                    "watchers_count": 2,
                    "language": "PHP",
                    "has_issues": true,
                    "has_projects": true,
                    "has_downloads": true,
                    "has_wiki": true,
                    "has_pages": false,
                    "forks_count": 2,
                    "mirror_url": null,
                    "open_issues_count": 7,
                    "forks": 2,
                    "open_issues": 7,
                    "watchers": 2,
                    "default_branch": "master"
                  }
                },
                "base": {
                  "label": "DannyFeliz:master",
                  "ref": "master",
                  "sha": "cc0839630f85742e46cd39eca1d1afd892c9af4f",
                  "user": {
                    "login": "DannyFeliz",
                    "id": 5460365,
                    "avatar_url": "https://avatars1.githubusercontent.com/u/5460365?v=4",
                    "gravatar_id": "",
                    "url": "https://api.github.com/users/DannyFeliz",
                    "html_url": "https://github.com/DannyFeliz",
                    "followers_url": "https://api.github.com/users/DannyFeliz/followers",
                    "following_url": "https://api.github.com/users/DannyFeliz/following{/other_user}",
                    "gists_url": "https://api.github.com/users/DannyFeliz/gists{/gist_id}",
                    "starred_url": "https://api.github.com/users/DannyFeliz/starred{/owner}{/repo}",
                    "subscriptions_url": "https://api.github.com/users/DannyFeliz/subscriptions",
                    "organizations_url": "https://api.github.com/users/DannyFeliz/orgs",
                    "repos_url": "https://api.github.com/users/DannyFeliz/repos",
                    "events_url": "https://api.github.com/users/DannyFeliz/events{/privacy}",
                    "received_events_url": "https://api.github.com/users/DannyFeliz/received_events",
                    "type": "User",
                    "site_admin": false
                  },
                  "repo": {
                    "id": 94479088,
                    "name": "Dashi",
                    "full_name": "DannyFeliz/Dashi",
                    "owner": {
                      "login": "DannyFeliz",
                      "id": 5460365,
                      "avatar_url": "https://avatars1.githubusercontent.com/u/5460365?v=4",
                      "gravatar_id": "",
                      "url": "https://api.github.com/users/DannyFeliz",
                      "html_url": "https://github.com/DannyFeliz",
                      "followers_url": "https://api.github.com/users/DannyFeliz/followers",
                      "following_url": "https://api.github.com/users/DannyFeliz/following{/other_user}",
                      "gists_url": "https://api.github.com/users/DannyFeliz/gists{/gist_id}",
                      "starred_url": "https://api.github.com/users/DannyFeliz/starred{/owner}{/repo}",
                      "subscriptions_url": "https://api.github.com/users/DannyFeliz/subscriptions",
                      "organizations_url": "https://api.github.com/users/DannyFeliz/orgs",
                      "repos_url": "https://api.github.com/users/DannyFeliz/repos",
                      "events_url": "https://api.github.com/users/DannyFeliz/events{/privacy}",
                      "received_events_url": "https://api.github.com/users/DannyFeliz/received_events",
                      "type": "User",
                      "site_admin": false
                    },
                    "private": false,
                    "html_url": "https://github.com/DannyFeliz/Dashi",
                    "description": "Get a notification in Slack every time someone asks you to check his code on Github.",
                    "fork": false,
                    "url": "https://api.github.com/repos/DannyFeliz/Dashi",
                    "forks_url": "https://api.github.com/repos/DannyFeliz/Dashi/forks",
                    "keys_url": "https://api.github.com/repos/DannyFeliz/Dashi/keys{/key_id}",
                    "collaborators_url": "https://api.github.com/repos/DannyFeliz/Dashi/collaborators{/collaborator}",
                    "teams_url": "https://api.github.com/repos/DannyFeliz/Dashi/teams",
                    "hooks_url": "https://api.github.com/repos/DannyFeliz/Dashi/hooks",
                    "issue_events_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/events{/number}",
                    "events_url": "https://api.github.com/repos/DannyFeliz/Dashi/events",
                    "assignees_url": "https://api.github.com/repos/DannyFeliz/Dashi/assignees{/user}",
                    "branches_url": "https://api.github.com/repos/DannyFeliz/Dashi/branches{/branch}",
                    "tags_url": "https://api.github.com/repos/DannyFeliz/Dashi/tags",
                    "blobs_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/blobs{/sha}",
                    "git_tags_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/tags{/sha}",
                    "git_refs_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/refs{/sha}",
                    "trees_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/trees{/sha}",
                    "statuses_url": "https://api.github.com/repos/DannyFeliz/Dashi/statuses/{sha}",
                    "languages_url": "https://api.github.com/repos/DannyFeliz/Dashi/languages",
                    "stargazers_url": "https://api.github.com/repos/DannyFeliz/Dashi/stargazers",
                    "contributors_url": "https://api.github.com/repos/DannyFeliz/Dashi/contributors",
                    "subscribers_url": "https://api.github.com/repos/DannyFeliz/Dashi/subscribers",
                    "subscription_url": "https://api.github.com/repos/DannyFeliz/Dashi/subscription",
                    "commits_url": "https://api.github.com/repos/DannyFeliz/Dashi/commits{/sha}",
                    "git_commits_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/commits{/sha}",
                    "comments_url": "https://api.github.com/repos/DannyFeliz/Dashi/comments{/number}",
                    "issue_comment_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/comments{/number}",
                    "contents_url": "https://api.github.com/repos/DannyFeliz/Dashi/contents/{+path}",
                    "compare_url": "https://api.github.com/repos/DannyFeliz/Dashi/compare/{base}...{head}",
                    "merges_url": "https://api.github.com/repos/DannyFeliz/Dashi/merges",
                    "archive_url": "https://api.github.com/repos/DannyFeliz/Dashi/{archive_format}{/ref}",
                    "downloads_url": "https://api.github.com/repos/DannyFeliz/Dashi/downloads",
                    "issues_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues{/number}",
                    "pulls_url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls{/number}",
                    "milestones_url": "https://api.github.com/repos/DannyFeliz/Dashi/milestones{/number}",
                    "notifications_url": "https://api.github.com/repos/DannyFeliz/Dashi/notifications{?since,all,participating}",
                    "labels_url": "https://api.github.com/repos/DannyFeliz/Dashi/labels{/name}",
                    "releases_url": "https://api.github.com/repos/DannyFeliz/Dashi/releases{/id}",
                    "deployments_url": "https://api.github.com/repos/DannyFeliz/Dashi/deployments",
                    "created_at": "2017-06-15T21:09:33Z",
                    "updated_at": "2017-08-07T02:34:30Z",
                    "pushed_at": "2017-08-03T18:26:25Z",
                    "git_url": "git://github.com/DannyFeliz/Dashi.git",
                    "ssh_url": "git@github.com:DannyFeliz/Dashi.git",
                    "clone_url": "https://github.com/DannyFeliz/Dashi.git",
                    "svn_url": "https://github.com/DannyFeliz/Dashi",
                    "homepage": "http://dashinotify.com",
                    "size": 308,
                    "stargazers_count": 2,
                    "watchers_count": 2,
                    "language": "PHP",
                    "has_issues": true,
                    "has_projects": true,
                    "has_downloads": true,
                    "has_wiki": true,
                    "has_pages": false,
                    "forks_count": 2,
                    "mirror_url": null,
                    "open_issues_count": 7,
                    "forks": 2,
                    "open_issues": 7,
                    "watchers": 2,
                    "default_branch": "master"
                  }
                },
                "_links": {
                  "self": {
                    "href": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/8"
                  },
                  "html": {
                    "href": "https://github.com/DannyFeliz/Dashi/pull/8"
                  },
                  "issue": {
                    "href": "https://api.github.com/repos/DannyFeliz/Dashi/issues/8"
                  },
                  "comments": {
                    "href": "https://api.github.com/repos/DannyFeliz/Dashi/issues/8/comments"
                  },
                  "review_comments": {
                    "href": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/8/comments"
                  },
                  "review_comment": {
                    "href": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/comments{/number}"
                  },
                  "commits": {
                    "href": "https://api.github.com/repos/DannyFeliz/Dashi/pulls/8/commits"
                  },
                  "statuses": {
                    "href": "https://api.github.com/repos/DannyFeliz/Dashi/statuses/50231cdd38bb5bf20ce93ce331ce4b5d43d4df51"
                  }
                }
              },
              "repository": {
                "id": 94479088,
                "name": "Dashi",
                "full_name": "DannyFeliz/Dashi",
                "owner": {
                  "login": "DannyFeliz",
                  "id": 5460365,
                  "avatar_url": "https://avatars1.githubusercontent.com/u/5460365?v=4",
                  "gravatar_id": "",
                  "url": "https://api.github.com/users/DannyFeliz",
                  "html_url": "https://github.com/DannyFeliz",
                  "followers_url": "https://api.github.com/users/DannyFeliz/followers",
                  "following_url": "https://api.github.com/users/DannyFeliz/following{/other_user}",
                  "gists_url": "https://api.github.com/users/DannyFeliz/gists{/gist_id}",
                  "starred_url": "https://api.github.com/users/DannyFeliz/starred{/owner}{/repo}",
                  "subscriptions_url": "https://api.github.com/users/DannyFeliz/subscriptions",
                  "organizations_url": "https://api.github.com/users/DannyFeliz/orgs",
                  "repos_url": "https://api.github.com/users/DannyFeliz/repos",
                  "events_url": "https://api.github.com/users/DannyFeliz/events{/privacy}",
                  "received_events_url": "https://api.github.com/users/DannyFeliz/received_events",
                  "type": "User",
                  "site_admin": false
                },
                "private": false,
                "html_url": "https://github.com/DannyFeliz/Dashi",
                "description": "Get a notification in Slack every time someone asks you to check his code on Github.",
                "fork": false,
                "url": "https://api.github.com/repos/DannyFeliz/Dashi",
                "forks_url": "https://api.github.com/repos/DannyFeliz/Dashi/forks",
                "keys_url": "https://api.github.com/repos/DannyFeliz/Dashi/keys{/key_id}",
                "collaborators_url": "https://api.github.com/repos/DannyFeliz/Dashi/collaborators{/collaborator}",
                "teams_url": "https://api.github.com/repos/DannyFeliz/Dashi/teams",
                "hooks_url": "https://api.github.com/repos/DannyFeliz/Dashi/hooks",
                "issue_events_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/events{/number}",
                "events_url": "https://api.github.com/repos/DannyFeliz/Dashi/events",
                "assignees_url": "https://api.github.com/repos/DannyFeliz/Dashi/assignees{/user}",
                "branches_url": "https://api.github.com/repos/DannyFeliz/Dashi/branches{/branch}",
                "tags_url": "https://api.github.com/repos/DannyFeliz/Dashi/tags",
                "blobs_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/blobs{/sha}",
                "git_tags_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/tags{/sha}",
                "git_refs_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/refs{/sha}",
                "trees_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/trees{/sha}",
                "statuses_url": "https://api.github.com/repos/DannyFeliz/Dashi/statuses/{sha}",
                "languages_url": "https://api.github.com/repos/DannyFeliz/Dashi/languages",
                "stargazers_url": "https://api.github.com/repos/DannyFeliz/Dashi/stargazers",
                "contributors_url": "https://api.github.com/repos/DannyFeliz/Dashi/contributors",
                "subscribers_url": "https://api.github.com/repos/DannyFeliz/Dashi/subscribers",
                "subscription_url": "https://api.github.com/repos/DannyFeliz/Dashi/subscription",
                "commits_url": "https://api.github.com/repos/DannyFeliz/Dashi/commits{/sha}",
                "git_commits_url": "https://api.github.com/repos/DannyFeliz/Dashi/git/commits{/sha}",
                "comments_url": "https://api.github.com/repos/DannyFeliz/Dashi/comments{/number}",
                "issue_comment_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues/comments{/number}",
                "contents_url": "https://api.github.com/repos/DannyFeliz/Dashi/contents/{+path}",
                "compare_url": "https://api.github.com/repos/DannyFeliz/Dashi/compare/{base}...{head}",
                "merges_url": "https://api.github.com/repos/DannyFeliz/Dashi/merges",
                "archive_url": "https://api.github.com/repos/DannyFeliz/Dashi/{archive_format}{/ref}",
                "downloads_url": "https://api.github.com/repos/DannyFeliz/Dashi/downloads",
                "issues_url": "https://api.github.com/repos/DannyFeliz/Dashi/issues{/number}",
                "pulls_url": "https://api.github.com/repos/DannyFeliz/Dashi/pulls{/number}",
                "milestones_url": "https://api.github.com/repos/DannyFeliz/Dashi/milestones{/number}",
                "notifications_url": "https://api.github.com/repos/DannyFeliz/Dashi/notifications{?since,all,participating}",
                "labels_url": "https://api.github.com/repos/DannyFeliz/Dashi/labels{/name}",
                "releases_url": "https://api.github.com/repos/DannyFeliz/Dashi/releases{/id}",
                "deployments_url": "https://api.github.com/repos/DannyFeliz/Dashi/deployments",
                "created_at": "2017-06-15T21:09:33Z",
                "updated_at": "2017-08-07T02:34:30Z",
                "pushed_at": "2017-08-03T18:26:25Z",
                "git_url": "git://github.com/DannyFeliz/Dashi.git",
                "ssh_url": "git@github.com:DannyFeliz/Dashi.git",
                "clone_url": "https://github.com/DannyFeliz/Dashi.git",
                "svn_url": "https://github.com/DannyFeliz/Dashi",
                "homepage": "http://dashinotify.com",
                "size": 308,
                "stargazers_count": 2,
                "watchers_count": 2,
                "language": "PHP",
                "has_issues": true,
                "has_projects": true,
                "has_downloads": true,
                "has_wiki": true,
                "has_pages": false,
                "forks_count": 2,
                "mirror_url": null,
                "open_issues_count": 7,
                "forks": 2,
                "open_issues": 7,
                "watchers": 2,
                "default_branch": "master"
              },
              "sender": {
                "login": "MrFather",
                "id": 18308892,
                "avatar_url": "https://avatars3.githubusercontent.com/u/18308892?v=4",
                "gravatar_id": "",
                "url": "https://api.github.com/users/MrFather",
                "html_url": "https://github.com/MrFather",
                "followers_url": "https://api.github.com/users/MrFather/followers",
                "following_url": "https://api.github.com/users/MrFather/following{/other_user}",
                "gists_url": "https://api.github.com/users/MrFather/gists{/gist_id}",
                "starred_url": "https://api.github.com/users/MrFather/starred{/owner}{/repo}",
                "subscriptions_url": "https://api.github.com/users/MrFather/subscriptions",
                "organizations_url": "https://api.github.com/users/MrFather/orgs",
                "repos_url": "https://api.github.com/users/MrFather/repos",
                "events_url": "https://api.github.com/users/MrFather/events{/privacy}",
                "received_events_url": "https://api.github.com/users/MrFather/received_events",
                "type": "User",
                "site_admin": false
              }
            }
            }', true);

    }

}