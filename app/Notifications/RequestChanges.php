<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;


class RequestChanges extends Notification
{
    public $notification;
    public $client;

    /**
     * Create a new notification instance.
     * @param array $notification
     */
    public function __construct($notification)
    {
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

    /**
     * Send the notification
     *
     * @return SlackMessage
     */
    public function toSlack()
    {
        $notification = $this->notification;
        $notification["fields"] = $this->generateFields();

        return (new SlackMessage)
            ->from(env("APP_NAME"))
            ->image(env("APP_URL") . "/img/dashi-danger.png")
            ->error()
            ->content(":hammer_and_wrench: *{$notification["username"]}* wants you to make some changes to this Pull Request.")
            ->attachment(function ($attachment) use ($notification) {
                $attachment->title($notification["title"], $notification["url"])
                    ->content(":crossed_swords: Make the changes and update the Pull Request.")
                    ->fields($notification["fields"]);
            });
    }

    private function generateFields()
    {
        return [
            "Repository" => $this->notification["repository"],
            "From" => $this->notification["from"],
            "Comment" => $this->notification["comment"],
        ];
    }

}