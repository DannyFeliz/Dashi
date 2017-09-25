<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;


class MentionInComment extends Notification
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
            // TODO: Change this icon with other
            ->image(env("APP_URL") . "/img/dashi-warning.png")
            ->warning()
            ->content(":loud_sound: *{$notification['username']}* mentioned you in this Pull Request.")
            ->attachment(function ($attachment) use ($notification) {
                $attachment->title($notification["title"], $notification["url"])
                    ->content(":left_speech_bubble: {$notification['comment']}")
                    ->fields($notification["fields"]);
            });
    }

    private function generateFields()
    {
        return [
            "Repository" => $this->notification["repository"],
            "From" => $this->notification["from"],
        ];
    }



}
