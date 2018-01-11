<?php

namespace App\Libraries\Parser;

use App\Libraries\Slack\SlackAttachment;
use App\Libraries\Utils;

class GithubParser implements ParserInterface
{
    private $request;
    private $event;
    private $attachment;
    private $subscribers = [];
    private $supportedEvents = [
        'reviewRequested' => 'isReviewRequested',
        'changesRequested' => 'isChangesRequested',
        'mentionedInComment' => 'isMentionedInComment',
        'pushOnOpenPullRequest' => 'isPushOnOpenPullRequest',
        'closedPullRequest' => 'isClosedPullRequest',
    ];
    private $aliases = [
        'reviewRequested' => 'RR',
        'changesRequested' => 'CR',
        'mentionedInComment' => 'MIC',
        'pushOnOpenPullRequest' => 'POOPR',
        'closedPullRequest' => 'CPR',
    ];
    private $supportedActionRequest = [
        'review_requested', 'submitted',
        'created', 'synchronize', 'closed',
    ];

    /**
     * GithubParser constructor.
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
        return $this->request;
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
        return array_key_exists('action', $this->request);
    }

    public function getSupportedActionRequest(): array
    {
        return $this->supportedActionRequest;
    }

    public function isASupportedActionRequest(): bool
    {
        return in_array($this->request['action'], $this->getSupportedActionRequest());
    }

    private function buildSlackAttachmentFromRR()
    {
        $authorName = $this->request['pull_request']['user']['login'];
        $authorIcon = $this->request['pull_request']['user']['avatar_url'];
        $authorLink = $this->request['pull_request']['user']['html_url'];
        $title = $this->request['pull_request']['title'];
        $titleLink = $this->request['pull_request']['html_url'];
        $pretext = ':microscope: Hey! We need you to make a `Code Review` to these changes.';
        $text = ':sleuth_or_spy: Make sure everything is in order before approving this Pull Request.';

        $this->attachment->setColor('#36a64f')
            ->setIconUrl(env('APP_URL') . '/img/dashi-success.png')
            ->setPretext($pretext)
            ->setAuthorName($authorName)
            ->setAuthorLink($authorLink)
            ->setAuthorIcon($authorIcon)
            ->setTitle($title)
            ->setTitleLink($titleLink)
            ->setText($text)
            ->setFields([
                'title' => 'File(s) changed',
                'value' => $this->request['pull_request']['changed_files'],
                'short' => true,
            ])
            ->setFields([
                'title' => 'Repository',
                'value' => $this->request['repository']['name'],
                'short' => true,
            ])
            ->setFields([
                'title' => 'From',
                'value' => 'Github',
                'short' => true,
            ])
            ->setFooter('Dashi')
            ->setFooterIcon(env('APP_URL') . '/img/dashi-logo.png');
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
        return 'review_requested' === $this->request['action'];
    }

    private function isChangesRequested(): bool
    {
        return 'submitted' === $this->request['action'] && $this->request['review']['state'] === 'changes_requested';
    }

    private function isMentionedInComment(): bool
    {
        return 'created' === $this->request['action'];
    }

    private function isPushOnOpenPullRequest(): bool
    {
        return 'synchronize' === $this->request['action'] && $this->request['pull_request']['state'] == 'open';
    }
    /**
     * Check if request is a rejected (closed) pull request
     *
     * @return boolean
     **/
    private function isClosedPullRequest(): bool
    {
        return 'closed' === $this->request['action'];

    }

    private function setSubscribers()
    {
        $setterName = "setSubscribers{$this->aliases[$this->event]}";

        if (method_exists($this, $setterName)) {
            call_user_func([$this, $setterName]);
        }
    }

    private function setSubscribersRR()
    {
        $this->subscribers[] = $this->request['requested_reviewer']['login'];
    }

    private function setSubscribersCR()
    {
        $this->subscribers[] = $this->request['pull_request']['user']['login'];
    }

    private function setSubscribersMIC()
    {
        $this->subscribers = Utils::extractUsernames($this->request['comment']['body']);
    }

    private function setSubscribersPOOPR()
    {
        foreach ($this->request['pull_request']['requested_reviewers'] as $reviewers) {
            $this->subscribers[] = $reviewers['login'];
        }
    }
    /**
     * Notifies the rejected pull request to the user.
     * @return void()
     **/
    private function setSubscribersCPR()
    {
        $this->subscribers[] = $this->request['pull_request']['user']['login'];
    }

    private function buildSlackAttachmentFromCR()
    {
        $authorName = $this->request['sender']['login'];
        $authorIcon = $this->request['pull_request']['user']['avatar_url'];
        $authorLink = $this->request['pull_request']['user']['html_url'];
        $title = $this->request['pull_request']['title'];
        $titleLink = $this->request['pull_request']['html_url'];
        $pretext = ':hammer_and_wrench: We want you to make some changes to this Pull Request.';
        $text = ':crossed_swords: Make the changes and update the Pull Request.';

        $this->attachment->setColor('#ce0502')
            ->setIconUrl(env('APP_URL') . '/img/dashi-danger.png')
            ->setPretext($pretext)
            ->setAuthorName($authorName)
            ->setAuthorLink($authorLink)
            ->setAuthorIcon($authorIcon)
            ->setTitle($title)
            ->setTitleLink($titleLink)
            ->setText($text)
            ->setFields([
                'title' => 'Comment',
                'value' => $this->request['review']['body'],
                'short' => false,
            ])
            ->setFields([
                'title' => 'Repository',
                'value' => $this->request['repository']['name'],
                'short' => true,
            ])
            ->setFields([
                'title' => 'From',
                'value' => 'Github',
                'short' => true,
            ])
            ->setFooter('Dashi')
            ->setFooterIcon(env('APP_URL') . '/img/dashi-logo.png');
    }

    private function buildSlackAttachmentFromMIC()
    {
        $authorName = $this->request['comment']['user']['login'];
        $authorIcon = $this->request['comment']['user']['avatar_url'];
        $authorLink = $this->request['comment']['user']['html_url'];
        $title = $this->request['pull_request']['title'];
        $titleLink = $this->request['comment']['html_url'];
        $pretext = ':loud_sound: Someone mentioned you in this Pull Request!';
        $text = ":left_speech_bubble: {$this->request['comment']['body']}";

        $this->attachment->setColor('#047bff')
            ->setIconUrl(env('APP_URL') . '/img/dashi-info.png')
            ->setPretext($pretext)
            ->setAuthorName($authorName)
            ->setAuthorLink($authorLink)
            ->setAuthorIcon($authorIcon)
            ->setTitle($title)
            ->setTitleLink($titleLink)
            ->setText($text)
            ->setFields([
                'title' => 'Repository',
                'value' => $this->request['repository']['name'],
                'short' => true,
            ])
            ->setFields([
                'title' => 'From',
                'value' => 'Github',
                'short' => true,
            ])
            ->setFooter('Dashi')
            ->setFooterIcon(env('APP_URL') . '/img/dashi-logo.png');
    }

    private function buildSlackAttachmentFromPOOPR()
    {
        $authorName = $this->request['pull_request']['user']['login'];
        $authorIcon = $this->request['pull_request']['user']['avatar_url'];
        $authorLink = $this->request['pull_request']['user']['html_url'];
        $title = $this->request['pull_request']['title'];
        $titleLink = $this->request['pull_request']['html_url'];
        $pretext = ':arrow_up: New update in a Pull Request where you are a Reviewer.';

        $this->attachment->setColor('#047bff')
            ->setIconUrl(env('APP_URL') . '/img/dashi-info.png')
            ->setPretext($pretext)
            ->setAuthorName($authorName)
            ->setAuthorLink($authorLink)
            ->setAuthorIcon($authorIcon)
            ->setTitle($title)
            ->setTitleLink($titleLink)
            ->setFields([
                'title' => 'Repository',
                'value' => $this->request['repository']['name'],
                'short' => true,
            ])
            ->setFields([
                'title' => 'From',
                'value' => 'Github',
                'short' => true,
            ])
            ->setFooter('Dashi')
            ->setFooterIcon(env('APP_URL') . '/img/dashi-logo.png');
    }
    /**
     * Prepare the data to notify the pull request creator.
     *
     * @return void
     */

    private function buildSlackAttachmentFromCPR()
    {
        $authorName = $this->request['pull_request']['user']['login'];
        $authorIcon = $this->request['pull_request']['user']['avatar_url'];
        $authorLink = $this->request['pull_request']['user']['html_url'];
        $title = $this->request['pull_request']['title'];
        $titleLink = $this->request['pull_request']['html_url'];
        $pretext = '::no_entry_sign:: This Pull Request was rejected.';
        $text = ':crossed_swords: Your Pull Request was rejected for some reason, check it out!';

        $this->attachment->setColor('warning')
            ->setIconUrl(env('APP_URL') . '/img/dashi-warning.png')
            ->setPretext($pretext)
            ->setAuthorName($authorName)
            ->setAuthorLink($authorLink)
            ->setAuthorIcon($authorIcon)
            ->setTitle($title)
            ->setTitleLink($titleLink)
            ->setText($text)
            ->setFields([
                'title' => 'Comment',
                'value' => $this->request['pull_request']['body'],
                'short' => false,
            ])
            ->setFields([
                'title' => 'Repository',
                'value' => $this->request['repository']['name'],
                'short' => true,
            ])
            ->setFields([
                'title' => 'From',
                'value' => 'Github',
                'short' => true,
            ])
            ->setFooter('Dashi')
            ->setFooterIcon(env('APP_URL') . '/img/dashi-logo.png');
    }
}
