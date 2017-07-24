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
use App\VersionControlSystem;
use function explode;
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


        $site = $request->header('User-Agent');
        $site = explode("/", $site)[0];
        $vcs = VersionControlSystem::where("user_agent", "LIKE", $site)->first();
        if (!$vcs) return;
        if ($vcs->name == "Github") {
            $this->githubNotification($request);
        } else if ($vcs->name == "Bitbucket") {
            $this->bitbucketNotification($request);
        }
    }

    public function bitbucketNotification(Request $request)
    {
        $this->notification = json_decode($notification = '{
          "pullrequest": {
            "type": "pullrequest",
            "description": ".gitignore edited online with Bitbucket",
            "links": {
              "decline": {
                "href": "https://api.bitbucket.org/2.0/repositories/DannyFeliz/test-app/pullrequests/2/decline"
              },
              "commits": {
                "href": "https://api.bitbucket.org/2.0/repositories/DannyFeliz/test-app/pullrequests/2/commits"
              },
              "self": {
                "href": "https://api.bitbucket.org/2.0/repositories/DannyFeliz/test-app/pullrequests/2"
              },
              "comments": {
                "href": "https://api.bitbucket.org/2.0/repositories/DannyFeliz/test-app/pullrequests/2/comments"
              },
              "merge": {
                "href": "https://api.bitbucket.org/2.0/repositories/DannyFeliz/test-app/pullrequests/2/merge"
              },
              "html": {
                "href": "https://bitbucket.org/DannyFeliz/test-app/pull-requests/2"
              },
              "activity": {
                "href": "https://api.bitbucket.org/2.0/repositories/DannyFeliz/test-app/pullrequests/2/activity"
              },
              "diff": {
                "href": "https://api.bitbucket.org/2.0/repositories/DannyFeliz/test-app/pullrequests/2/diff"
              },
              "approve": {
                "href": "https://api.bitbucket.org/2.0/repositories/DannyFeliz/test-app/pullrequests/2/approve"
              },
              "statuses": {
                "href": "https://api.bitbucket.org/2.0/repositories/DannyFeliz/test-app/pullrequests/2/statuses"
              }
            },
            "title": ".gitignore edited online with Bitbucket",
            "close_source_branch": true,
            "reviewers": [
              {
                "username": "DannyFeliz",
                "type": "user",
                "display_name": "Danny Feliz",
                "uuid": "{714a8d9f-ac99-4aaa-9d40-f0ee638d2b17}",
                "links": {
                  "self": {
                    "href": "https://api.bitbucket.org/2.0/users/DannyFeliz"
                  },
                  "html": {
                    "href": "https://bitbucket.org/DannyFeliz/"
                  },
                  "avatar": {
                    "href": "https://bitbucket.org/account/DannyFeliz/avatar/32/"
                  }
                }
              }
            ],
            "destination": {
              "commit": {
                "hash": "42429e229a34",
                "links": {
                  "self": {
                    "href": "https://api.bitbucket.org/2.0/repositories/DannyFeliz/test-app/commit/42429e229a34"
                  }
                }
              },
              "branch": {
                "name": "master"
              },
              "repository": {
                "full_name": "DannyFeliz/test-app",
                "type": "repository",
                "name": "test-app",
                "links": {
                  "self": {
                    "href": "https://api.bitbucket.org/2.0/repositories/DannyFeliz/test-app"
                  },
                  "html": {
                    "href": "https://bitbucket.org/DannyFeliz/test-app"
                  },
                  "avatar": {
                    "href": "https://bitbucket.org/DannyFeliz/test-app/avatar/32/"
                  }
                },
                "uuid": "{bbdcccea-c2f6-4c04-81cb-50a7d44c8e50}"
              }
            },
            "comment_count": 0,
            "id": 2,
            "source": {
              "commit": {
                "hash": "9442ab173044",
                "links": {
                  "self": {
                    "href": "https://api.bitbucket.org/2.0/repositories/DannyFeliz/test-app/commit/9442ab173044"
                  }
                }
              },
              "branch": {
                "name": "TestOmarUser/gitignore-edited-online-with-bitbucket-1500872350024"
              },
              "repository": {
                "full_name": "DannyFeliz/test-app",
                "type": "repository",
                "name": "test-app",
                "links": {
                  "self": {
                    "href": "https://api.bitbucket.org/2.0/repositories/DannyFeliz/test-app"
                  },
                  "html": {
                    "href": "https://bitbucket.org/DannyFeliz/test-app"
                  },
                  "avatar": {
                    "href": "https://bitbucket.org/DannyFeliz/test-app/avatar/32/"
                  }
                },
                "uuid": "{bbdcccea-c2f6-4c04-81cb-50a7d44c8e50}"
              }
            },
            "state": "OPEN",
            "author": {
              "username": "TestOmarUser",
              "type": "user",
              "display_name": "TestOmarUser",
              "uuid": "{cc3513da-d128-414e-b214-ed04185680c0}",
              "links": {
                "self": {
                  "href": "https://api.bitbucket.org/2.0/users/TestOmarUser"
                },
                "html": {
                  "href": "https://bitbucket.org/TestOmarUser/"
                },
                "avatar": {
                  "href": "https://bitbucket.org/account/TestOmarUser/avatar/32/"
                }
              }
            },
            "created_on": "2017-07-24T04:59:21.212309+00:00",
            "participants": [
              {
                "type": "participant",
                "role": "REVIEWER",
                "user": {
                  "username": "DannyFeliz",
                  "type": "user",
                  "display_name": "Danny Feliz",
                  "uuid": "{714a8d9f-ac99-4aaa-9d40-f0ee638d2b17}",
                  "links": {
                    "self": {
                      "href": "https://api.bitbucket.org/2.0/users/DannyFeliz"
                    },
                    "html": {
                      "href": "https://bitbucket.org/DannyFeliz/"
                    },
                    "avatar": {
                      "href": "https://bitbucket.org/account/DannyFeliz/avatar/32/"
                    }
                  }
                },
                "approved": false
              }
            ],
            "reason": "",
            "updated_on": "2017-07-24T04:59:21.265651+00:00",
            "merge_commit": null,
            "closed_by": null,
            "task_count": 0
          },
          "actor": {
            "username": "TestOmarUser",
            "type": "user",
            "display_name": "TestOmarUser",
            "uuid": "{cc3513da-d128-414e-b214-ed04185680c0}",
            "links": {
              "self": {
                "href": "https://api.bitbucket.org/2.0/users/TestOmarUser"
              },
              "html": {
                "href": "https://bitbucket.org/TestOmarUser/"
              },
              "avatar": {
                "href": "https://bitbucket.org/account/TestOmarUser/avatar/32/"
              }
            }
          },
          "repository": {
            "scm": "git",
            "website": "",
            "name": "test-app",
            "links": {
              "self": {
                "href": "https://api.bitbucket.org/2.0/repositories/DannyFeliz/test-app"
              },
              "html": {
                "href": "https://bitbucket.org/DannyFeliz/test-app"
              },
              "avatar": {
                "href": "https://bitbucket.org/DannyFeliz/test-app/avatar/32/"
              }
            },
            "full_name": "DannyFeliz/test-app",
            "owner": {
              "username": "DannyFeliz",
              "type": "user",
              "display_name": "Danny Feliz",
              "uuid": "{714a8d9f-ac99-4aaa-9d40-f0ee638d2b17}",
              "links": {
                "self": {
                  "href": "https://api.bitbucket.org/2.0/users/DannyFeliz"
                },
                "html": {
                  "href": "https://bitbucket.org/DannyFeliz/"
                },
                "avatar": {
                  "href": "https://bitbucket.org/account/DannyFeliz/avatar/32/"
                }
              }
            },
            "type": "repository",
            "is_private": true,
            "uuid": "{bbdcccea-c2f6-4c04-81cb-50a7d44c8e50}"
          }
        }', true);


        print_r($this->bitbucketRequestReviewData());
        die("asda");
    }

    public function bitbucketRequestReviewData()
    {
        return [
            "username" => $this->notification["pullrequest"]["reviewers"]["username"],
            "title" => $this->notification["pull_request"]["title"],
            "url" =>  $this->notification["pull_request"]["links"]["html"]["href"],
            "repository" => $this->notification["pull_request"]["destination"]["repository"]["name"],
        ];
    }

    public function githubNotification(Request $request) {

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
                $user->notify(new RequestReview($this->githubRequestReviewData()));
            } else if (CHANGES_REQUESTED) {
                $user->notify(new RequestChanges($this->notification));
            }
        }
    }

    public function githubRequestReviewData()
    {
        return [
            "username" => $this->notification["sender"]["login"],
            "title" => $this->notification["pull_request"]["title"],
            "url" => $this->notification["pull_request"]["html_url"],
            "changed_files" => $this->notification["pull_request"]["changed_files"],
            "repository" => $this->notification["repository"]["name"],
        ];
    }

    public function noAllowed()
    {
        return view("notifier.notallowed");
    }
}