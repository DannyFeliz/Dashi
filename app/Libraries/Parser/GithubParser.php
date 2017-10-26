<?php

namespace App\Libraries\Parser;

use App\Libraries\Slack\SlackAttachment;
use App\Libraries\Utils;

class GithubParser implements ParserInterface
{
    private $request;
    private $event;
    private $attachment;
    private $suscribers = [];
    private $supportedEvents = [
        'reviewRequested' => 'isReviewRequested',
        'changesRequested' => 'isChangesRequested',
        'mentionedInComment' => 'isMentionedInComment',
        'pushOnOpenPullRequest' => 'isPushOnOpenPullRequest',
    ];
    private $aliases = [
        'reviewRequested' => 'RR',
        'changesRequested' => 'CR',
        'mentionedInComment' => 'MIC',
        'pushOnOpenPullRequest' => 'POOPR',
    ];
    private $supportedActionRequest = [
        'review_requested', 'submitted',
        'created', 'synchronize',
    ];

    /**
     * GithubParser constructor.
     *
     * @param $request
     */
    public function __construct(array $request)
    {
        $this->request = $request;
        $this->attachment = new SlackAttachment();
    }

    public function parse()
    {
        if ($this->setEvent()) {
            $this->setSuscribers();
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

    public function getSuscribers(): array
    {
        return $this->suscribers;
    }

    public function isAnActionRequest()
    {
        return array_key_exists('action', $this->request);
    }

    public function getSupportedActionRequest()
    {
        return $this->supportedActionRequest;
    }

    public function isASupportedActionRequest()
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
                         ->setFooterIcon(env('APP_URL').'/img/dashi-logo.png');
    }

    private function setEvent()
    {
        foreach ($this->supportedEvents as $event => $isThisEvent) {
            if ($this->{$isThisEvent}()) {
                $this->event = $event;

                return true;
            }
        }

        return false;
    }

    private function isReviewRequested()
    {
        return 'review_requested' === $this->request['action'];
    }

    private function isChangesRequested()
    {
        return 'submitted' === $this->request['action'] && $this->request['review']['state'] === 'changes_requested';
    }

    private function isMentionedInComment()
    {
        return 'created' === $this->request['action'];
    }

    private function isPushOnOpenPullRequest()
    {
        return 'synchronize' === $this->request['action'] && $this->request['pull_request']['state'] == 'open';
    }

    private function setSuscribers()
    {
        $setterName = "setSuscribers{$this->aliases[$this->event]}";

        if (method_exists($this, $setterName)) {
            call_user_func([$this, $setterName]);
        }
    }

    private function setSuscribersRR()
    {
        $this->suscribers[] = $this->request['sender']['login'];
    }

    private function setSuscribersCR()
    {
        $this->suscribers[] = $this->request['pull_request']['user']['login'];
    }

    private function setSuscribersMIC()
    {
        $this->suscribers = Utils::extractUsernames($this->request['comment']['body']);
    }

    private function setSuscribersPOOPR()
    {
        foreach ($this->request['pull_request']['requested_reviewers'] as $reviewers) {
            $this->suscribers[] = $reviewers['login'];
        }
    }

    private function buildSlackAttachmentFromCR()
    {
        $authorName = $this->request['sender']['login'];
        $authorIcon = $this->request['pull_request']['user']['avatar_url'];
        $authorLink = $this->request['pull_request']['user']['html_url'];
        $title = $this->request['pull_request']['title'];
        $titleLink = $this->request['pull_request']['html_url'];
        $pretext = ':hammer_and_wrench: We wants you to make some changes to this Pull Request.';
        $text = ':crossed_swords: Make the changes and update the Pull Request.';

        $this->attachment->setColor('warning')
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
                         ->setFooterIcon(env('APP_URL').'/img/dashi-logo.png');
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
                         ->setFooterIcon(env('APP_URL').'/img/dashi-logo.png');
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
                         ->setFooterIcon(env('APP_URL').'/img/dashi-logo.png');
    }
}
