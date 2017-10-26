<?php

namespace App\Libraries;

use App\Notifications\RequestReview;
use App\SlackToken;
use App\User;
use App\Libraries\Parser\BitbucketParser;
use App\Libraries\Slack\SlackAttachment;
use App\Libraries\Slack\SlackNotifier;

class BitbucketNotifier
{
    private $parser;

    /**
     * BitbucketNotifier constructor.
     *
     * @param $request
     */
    public function __construct($request)
    {
        $this->parser = new BitbucketParser($request);
        $this->run();
    }

    public function run()
    {
        if (!$this->parser->isASupportedActionRequest()) {
            $actionsList = implode("', '", $this->parser->getSupportedActionRequest());

            echo "Only '{$actionsList}' actions are supported at this moment.\n";

            return;
        }

        if (!$this->parser->parse()) {
            echo 'We werent able to recognize this event :(';

            return;
        }

        $this->notify($this->parser->getSuscribers(), $this->parser->getAttachment());
    }

    /**
     * Dispatch the corresponding notification
     *
     * @param array $suscribers
     * @param mixed $attachment
     */
    public function notify(array $suscribers, SlackAttachment $attachment)
    {
        foreach ($suscribers as $suscriber) {
            $slackToken = SlackToken::where('bitbucket_username', $suscriber)->first();
            if ($slackToken) {
                $notifier = new SlackNotifier($slackToken);
                $notifier->send($attachment);
            }
        }
    }

}
