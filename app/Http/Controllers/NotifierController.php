<?php
/**
 * Created by PhpStorm.
 * User: Danny
 * Date: 15/06/2017
 * Time: 06:37 PM
 */

namespace App\Http\Controllers;


use App\Libraries\BitbucketNotification;
use App\Libraries\GithubNotification;
use App\VersionControlSystem;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;

class NotifierController
{
    use Notifiable;

    public $notification;

    /**
     * Determine which VCS use to send the notification
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
            (new GithubNotification($request))->run();
        } else if ($vcs->name == "Bitbucket") {
            (new BitbucketNotification($request))->run();
        }

        echo "Done With " . $vcs->name;
    }

    public function noAllowed()
    {
        return view("notifier.notallowed");
    }

}