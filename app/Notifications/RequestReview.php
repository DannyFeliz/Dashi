<?php

namespace App\Notifications;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;


class RequestReview extends Notification
{

    public $notification;
    public $client;

    /**
    * Create a new notification instance.
    * @param Request $notification
    */
    public function __construct($notification)
    {
        $this->client = new Client();
        $this->notification = $notification;
    }

    /**
    * Get the notification's delivery channels.
    *
    * @return array
    */
    public function via()
    {
        return ['slack'];
    }

    public function toSlack()
    {
        $notification = $this->notification;

        $username = $notification["sender"]["login"];

        return (new SlackMessage)
        ->from("Dashi")
        ->image("http://icons.iconarchive.com/icons/thehoth/seo/256/seo-web-code-icon.png")
        ->success()
        ->content(":microscope: {$username} needs you to make a Code Review to this changes")
        ->attachment(function ($attachment) use ($notification) {
            $attachment->title($notification["pull_request"]["title"], $notification["pull_request"]["html_url"])
            ->content(":sleuth_or_spy: Make sure everything is in order before approve the Pull Request")
            ->fields([
            "User" => $notification["sender"]["login"],
            "Repository" => $notification["repository"]["name"],
            "File(s) changed" => $notification["pull_request"]["changed_files"]
            ]);
        });
    }

}