<?php

namespace App\Libraries\Parser;

use App\Colors;
use App\Libraries\Slack\SlackAttachment;

class BitbucketParser implements ParserInterface
{
    private $request;
    private $event;
    private $attachment;
    private $subscribers = [];
    private $supportedEvents = [
        'reviewRequested' => 'isReviewRequested',
    ];
    private $aliases = [
        'reviewRequested' => 'RR',
    ];
    private $supportedActionRequest = [
        'pullrequest:created',
    ];
    private $footer = 'Dashi';

    /**
     * BitbucketParser constructor.
     *
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;
        $this->attachment = new SlackAttachment();
    }

    public function parse(): bool
    {
        if ($this->setEvent()) {
            $this->setSubscribers();
            $this->buildSlackAttachment();

            return true;
        }

        return false;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getAttachment(): SlackAttachment
    {
        return $this->attachment;
    }

    public function getRawRequest(): array
    {
        return $this->request->toArray();
    }

    public function buildSlackAttachment()
    {
        $builderName = "buildSlackAttachmentFrom{$this->aliases[$this->event]}";

        if (method_exists($this, $builderName)) {
            return call_user_func([$this, $builderName]);
        }
    }

    public function getSubscribers(): array
    {
        return $this->subscribers;
    }

    public function isAnActionRequest(): bool
    {
        return true;
    }

    public function getSupportedActionRequest(): array
    {
        return $this->supportedActionRequest;
    }

    public function isASupportedActionRequest(): bool
    {
        return in_array($this->request->header('X-Event-Key'), $this->getSupportedActionRequest());
    }

    private function buildSlackAttachmentFromRR()
    {
        $authorName = $this->request['pullrequest']['author']['username'];
        $authorDisplayName = $this->request['pullrequest']['author']['display_name'];
        $authorIcon = $this->request['pullrequest']['author']['links']['avatar']['href'];
        $authorLink = $this->request['pullrequest']['author']['links']['html']['href'];
        $title = $this->request['pullrequest']['title'];
        $titleLink = $this->request['pullrequest']['links']['html']['href'];
        $pretext = ":microscope: Hey! {$authorDisplayName} needs you to make a `Code Review` to these changes.";
        $text = ':sleuth_or_spy: Make sure everything is in order before approving this Pull Request.';

        $this->attachment->setColor(Colors::GREEN)
                         ->setPretext($pretext)
                         ->setIconUrl(env('APP_URL').'/img/dashi-success.png')
                         ->setAuthorName($authorName)
                         ->setAuthorLink($authorLink)
                         ->setAuthorIcon($authorIcon)
                         ->setTitle($title)
                         ->setTitleLink($titleLink)
                         ->setText($text)
                         ->setFields([
                            'title' => 'Repository',
                            'value' => $this->request['pullrequest']['destination']['repository']['name'],
                            'short' => true,
                         ])
                         ->setFields([
                            'title' => 'From',
                            'value' => 'Bitbucket',
                            'short' => true,
                         ])
                         ->setFooter($this->footer)
                         ->setFooterIcon(env('APP_URL').'/img/dashi-logo.png');
    }

    private function setSubscribersRR()
    {
        foreach ($this->request['pullrequest']['reviewers'] as $reviewers) {
            $this->subscribers[] = $reviewers['username'];
        }
    }

    private function setEvent(): bool
    {
        foreach ($this->supportedEvents as $event => $isThisEvent) {
            if ($this->{$isThisEvent}()) {
                $this->event = $event;

                return true;
            }
        }

        return false;
    }

    private function isReviewRequested(): bool
    {
        return true;
    }

    private function setSubscribers()
    {
        $setterName = "setSubscribers{$this->aliases[$this->event]}";

        if (method_exists($this, $setterName)) {
            call_user_func([$this, $setterName]);
        }
    }
}
