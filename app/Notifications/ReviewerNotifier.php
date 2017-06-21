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
             ->from("Dashi")                                                                                                             
             ->image("http://icons.iconarchive.com/icons/thehoth/seo/256/seo-web-code-icon.png")                                         
             ->success()                                                                                                                 
             ->content(":point_right: ¡{$name} needs you to make him a Code Review to his changes!")                                     
             ->attachment(function ($attachment) use ($notification) {                                                                   
                 $attachment->title("Pull Request: " . $notification["pull_request"]["title"], $notification["pull_request"]["html_url"])
                     ->content("¡Make sure everything is in order before approving the Pull Request!")                                   
                     ->markdown(["Commits", "text"])                                                                                     
                     ->fields([                                                                                                          
                         "User" => $notification["sender"]["login"],                                                                     
                         "File changed" => $notification["pull_request"]["changed_files"]                                                
                     ]);                                                                                                                 
             });                                                                                                                         
     }                                                                                                                                   

}
