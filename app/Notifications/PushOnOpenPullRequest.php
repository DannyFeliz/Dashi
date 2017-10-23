<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class PushOnOpenPullRequest extends Notification
{
    public $notification;

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
            ->image(env("APP_URL") . "/img/dashi-info.png")
            ->content(":arrow_up: *{$notification['username']}* updated a Pull Request where you are a Reviewer.")
            ->attachment(function ($attachment) use ($notification) {
                $attachment->title($notification["title"], $notification["url"])
                    ->fields($notification["fields"]);
            });
    }

    private function generateFields()
    {
        return [
            "Repository" => $this->notification["repository"],
            "From" => $this->notification["from"]
        ];
    }
}
