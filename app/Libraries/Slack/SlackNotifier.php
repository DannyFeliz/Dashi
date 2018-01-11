<?php

namespace App\Libraries\Slack;

use App\SlackToken;
use GuzzleHttp\Client;

class SlackNotifier
{
    private $token;

    /**
     * GithubParser constructor.
     *
     * @param $request
     */
    public function __construct(SlackToken $token)
    {
        $this->token = $token->token;
    }

    public function send(SlackAttachment $attachment)
    {
        $client = new Client();
        $result = $client->post($this->token, [
            'json' => $attachment->getMessage(),
        ]);
    }
}
