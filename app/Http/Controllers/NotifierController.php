<?php

namespace App\Http\Controllers;

use App\Libraries\BitbucketNotifier;
use App\Libraries\GithubNotifier;
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
        $site = current(explode('/', $site));

        $vcs = VersionControlSystem::where('user_agent', 'LIKE', $site)->first();
        if (!$vcs) {
            return;
        }

        if ('Github' == $vcs->name) {
            new GithubNotifier($request);
        } elseif ('Bitbucket' == $vcs->name) {
            new BitbucketNotifier($request);
        }

        echo 'Everything went well with '.$vcs->name.'.';
    }

    public function noAllowed()
    {
        return view('notifier.notallowed');
    }
}
