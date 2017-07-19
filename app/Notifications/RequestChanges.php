<?php

namespace App\Notifications;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;


class RequestChanges extends Notification
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

        $user = $notification["review"]["user"]["login"];

        return (new SlackMessage)
            ->from("Dashi")
            ->image("http://icons.iconarchive.com/icons/thehoth/seo/256/seo-web-code-icon.png")
            ->error()
            ->content(":hammer_and_wrench: {$user} wants you to make some changes to this Pull Request.")
            ->attachment(function ($attachment) use ($notification) {
                $attachment->title($notification["pull_request"]["title"], $notification["review"]["_links"]["html"]["href"])
                    ->content(":crossed_swords: Make the changes and update the Pull Request.")
                    ->fields([
                        "Repository" => $notification["repository"]["name"],
                        "Comment(s)" => $this->getComments()
                    ]);
            });
    }

    /**
     * Get the pull request comments
     *
     * @return array
     */
    public function getComments()
    {
        $commentsUrl = $this->notification["pull_request"]["_links"]["comments"]["href"];
        $comments = json_decode($this->client->get($commentsUrl)
            ->getBody()
            ->getContents(),
            true);

        return count($comments);
    }

}