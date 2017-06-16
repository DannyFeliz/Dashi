<?php

namespace App\Notifications;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use function json_decode;


class ReviewerNotifier extends Notification
{
    use Queueable;

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
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the pull request commits list in markdown format
     *
     * @return array
     */
    public function getCommits()
    {
        $commitsUrl = $this->notification["pull_request"]["commits_url"];
        $commits = json_decode($this->client->get($commitsUrl)
            ->getBody()
            ->getContents(),
            true);

        $commitsList = [];
        foreach ($commits as $commit) {
            $commitsList[] = '- [' . $commit['commit']['message'] . '](' . $commit['html_url'] . ')';
        }

        return $commitsList;
    }

    public function toSlack($notifiable)
    {

        $notification = $this->notification;


        $user = json_decode($this->client->get($notification["sender"]["url"])
            ->getBody()
            ->getContents(),
            true);

        $name = $user['name'] ? $user['name'] : $notification["sender"]["login"];

        return (new SlackMessage)
            ->from("Review Notifier")
            ->image("http://icons.iconarchive.com/icons/thehoth/seo/256/seo-web-code-icon.png")
            ->success()
            ->content(":point_right: ¡{$name} necesita le hagas un Code Review a sus cambios!")
            ->attachment(function ($attachment) use ($notification) {
                $attachment->title("Pull Request: " . $notification["pull_request"]["title"], $notification["pull_request"]["html_url"])
                    ->content("¡Asegúrate de que todo esté en orden antes de aprobar el Pull Request!")
                    ->markdown(["Commits", "text"])
                    ->fields([
                        "Usuario" => $notification["sender"]["login"],
                        "Archivos modificados" => $notification["pull_request"]["changed_files"]
                    ]);
            });
    }

}
