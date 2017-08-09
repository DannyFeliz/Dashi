<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;


class RequestReview extends Notification
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
     *  Send the notification
     *
     * @return SlackMessage
     */
    public function toSlack()
    {
        $notification = $this->notification;
        $notification["fields"] = $this->generateFields();

        return (new SlackMessage)
            ->from(env("APP_NAME"))
            ->image(env("APP_URL") . "/img/dashi-success.png")
            ->success()
            ->content(":microscope: *{$notification['username']}* needs you to make a `Code Review` to these changes.")
            ->attachment(function ($attachment) use ($notification) {
                $attachment->title($notification["title"], $notification["url"])
                           ->content(":sleuth_or_spy: Make sure everything is in order before approving this Pull Request.")
                           ->fields($notification["fields"]);
            });
    }

    /**
     * Generates the field array that will appear in the slack message
     *
     * @return array
     */
    public function generateFields()
    {
        $fields = [
            "Repository" => $this->notification["repository"],
            "From" => $this->notification["from"]
        ];

        if (array_key_exists("changed_files", $this->notification)) {
            $fields["File(s) changed"] = $this->notification["changed_files"];
        }

        return $fields;
    }

}
