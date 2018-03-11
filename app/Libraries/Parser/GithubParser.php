<?php

namespace App\Libraries\Parser;

use App\Color;
use App\Libraries\Slack\SlackAttachment;
use App\Libraries\Utils;

class GithubParser implements ParserInterface
{
    private $request;
    private $originalRequest;
    private $event;
    private $attachment;
    private $subscribers = [];
    private $supportedEvents = [
        'reviewRequested' => 'isReviewRequested',
        'changesRequested' => 'isChangesRequested',
        'mentionedInComment' => 'isMentionedInComment',
        'mentionedInPullRequest' => 'isMentionedInPullRequest',
        'pushOnOpenPullRequest' => 'isPushOnOpenPullRequest',
        'mergedPullRequest' => 'isMergedPullRequest',
        'closedPullRequest' => 'isClosedPullRequest',
    ];
    private $aliases = [
        'reviewRequested' => 'RR',
        'changesRequested' => 'CR',
        'mentionedInComment' => 'MIC',
        'mentionedInPullRequest' => 'MENPR',
        'pushOnOpenPullRequest' => 'POOPR',
        'mergedPullRequest' => 'MPR',
        'closedPullRequest' => 'CPR',
    ];
    private $supportedActionRequest = [
        'review_requested', 'submitted',
        'created', 'synchronize', 'closed',
    ];
    private $footer = 'Dashi';

    /**
     * GithubParser constructor.
     *
     * @param $request
     */
    public function __construct($request)
    {
        $payload = $request->toArray()['payload'];
        $this->request = json_decode($payload, true);
        $this->originalRequest = $request;
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
        $fromBranch = $this->request['pull_request']['head']['ref'];
        $toBranch = $this->request['pull_request']['base']['ref'];

        $authorName = $this->request['pull_request']['user']['login'];
        $authorIcon = $this->request['pull_request']['user']['avatar_url'];
        $authorLink = $this->request['pull_request']['user']['html_url'];
        $title = $this->request['pull_request']['title'];
        $titleLink = $this->request['pull_request']['html_url'];
        $pretext = ':microscope: Hey! We need you to make a `Code Review` to these changes.';
        $text = ':sleuth_or_spy: Make sure everything is in order before approving this pull request.';

        $this->attachment->setColor(Color::GREEN)
            ->setIconUrl(env('APP_URL').'/img/dashi-success.png')
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
                'title' => 'Branch',
                'value' => "`{$fromBranch}` ⇢ `{$toBranch}`",
                'short' => true,
            ])
            ->setFields([
                'title' => 'From',
                'value' => 'Github',
                'short' => true,
            ])
            ->setFooter($this->footer)
            ->setFooterIcon(env('APP_URL').'/img/dashi-logo.png');
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
        return $this->originalRequest->header("x-github-event") === 'pull_request_review_comment' && 'created' === $this->request['action'];
    }

    private function isMentionedInPullRequest(): bool
    {
        return $this->originalRequest->header("x-github-event") === 'issue_comment' && 'created' === $this->request['action'];
    }

    private function isPushOnOpenPullRequest(): bool
    {
        return 'synchronize' === $this->request['action'] && $this->request['pull_request']['state'] === 'open';
    }

    private function isMergedPullRequest(): bool
    {
        return 'closed' === $this->request['action'] && $this->request['pull_request']['merged'];
    }

    /**
     * Check if request is a closed pull request
     *
     * @return bool
     **/
    private function isClosedPullRequest(): bool
    {
        return 'closed' === $this->request['action'] && !$this->request['pull_request']['merged'];
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

    private function setSubscribersMENPR()
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
     * Notifies the closed pull request to the user.
     **/
    private function setSubscribersCPR()
    {
        $this->subscribers[] = $this->request['pull_request']['user']['login'];
    }

    /**
     * Notifies to the owner of the pull request who merged it.
     **/
    private function setSubscribersMPR()
    {
        $this->subscribers[] = $this->request['pull_request']['user']['login'];
    }

    private function buildSlackAttachmentFromMPR()
    {
        $fromBranch = $this->request['pull_request']['head']['ref'];
        $toBranch = $this->request['pull_request']['base']['ref'];
        $mergedBy = $this->request['pull_request']['merged_by']['login'];
        $madeBy = $this->request['pull_request']['user']['login'];
        $merger = $mergedBy == $madeBy ? 'you' : $mergedBy;

        $authorName = $this->request['pull_request']['merged_by']['login'];
        $authorIcon = $this->request['pull_request']['merged_by']['avatar_url'];
        $authorLink = $this->request['pull_request']['merged_by']['html_url'];
        $title = $this->request['pull_request']['title'];
        $titleLink = $this->request['pull_request']['html_url'];
        $pretext = ":cyclone: Your pull request was merged by {$merger}.";
        $this->attachment->setColor(Color::PURPLE)
            ->setIconUrl(env('APP_URL').'/img/dashi-merged.png')
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
                'title' => 'Branch',
                'value' => "`{$fromBranch}` ⇢ `{$toBranch}`",
                'short' => true,
            ])
            ->setFields([
                'title' => 'From',
                'value' => 'Github',
                'short' => true,
            ])
            ->setFooter($this->footer)
            ->setFooterIcon(env('APP_URL').'/img/dashi-logo.png');
    }

    private function buildSlackAttachmentFromCR()
    {
        $authorName = $this->request['sender']['login'];
        $authorIcon = $this->request['pull_request']['user']['avatar_url'];
        $authorLink = $this->request['pull_request']['user']['html_url'];
        $title = $this->request['pull_request']['title'];
        $titleLink = $this->request['pull_request']['html_url'];
        $pretext = ':hammer_and_wrench: We want you to make some changes to this pull request.';
        $text = ':crossed_swords: Make the changes and update the pull request.';

        $this->attachment->setColor(Color::RED)
            ->setIconUrl(env('APP_URL').'/img/dashi-warning.png')
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
            ->setFooter($this->footer)
            ->setFooterIcon(env('APP_URL').'/img/dashi-logo.png');
    }

    private function buildSlackAttachmentFromMIC()
    {
        $authorName = $this->request['comment']['user']['login'];
        $authorIcon = $this->request['comment']['user']['avatar_url'];
        $authorLink = $this->request['comment']['user']['html_url'];
        $title = $this->request['pull_request']['title'];
        $titleLink = $this->request['comment']['html_url'];
        $pretext = ':loud_sound: Someone mentioned you in this pull request!';
        $text = ":left_speech_bubble: {$this->request['comment']['body']}";

        $this->attachment->setColor(Color::BLUE)
            ->setIconUrl(env('APP_URL').'/img/dashi-info.png')
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
            ->setFooter($this->footer)
            ->setFooterIcon(env('APP_URL').'/img/dashi-logo.png');
    }

    private function buildSlackAttachmentFromMENPR()
    {
        $authorName = $this->request['comment']['user']['login'];
        $authorIcon = $this->request['comment']['user']['avatar_url'];
        $authorLink = $this->request['comment']['user']['html_url'];
        $senderName = $this->request['sender']['login'];
        // Because comments in pull request are interpreted as issues comment we don't have the
        // pull request data, so we hardcoded the title
        $title = "Pull request";
        $titleLink = $this->request['comment']['html_url'];
        $pretext = ":loud_sound: ${senderName} mentioned you in this pull request!";
        $text = ":left_speech_bubble: {$this->request['comment']['body']}";
        $this->attachment->setColor(Color::BLUE)
            ->setIconUrl(env('APP_URL').'/img/dashi-info.png')
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
            ->setFooter($this->footer)
            ->setFooterIcon(env('APP_URL').'/img/dashi-logo.png');
    }

    private function buildSlackAttachmentFromPOOPR()
    {
        $authorName = $this->request['pull_request']['user']['login'];
        $authorIcon = $this->request['pull_request']['user']['avatar_url'];
        $authorLink = $this->request['pull_request']['user']['html_url'];
        $title = $this->request['pull_request']['title'];
        $titleLink = $this->request['pull_request']['html_url'];
        $pretext = ':arrow_up: New update in a pull request where you are a Reviewer.';

        $this->attachment->setColor(Color::BLUE)
            ->setIconUrl(env('APP_URL').'/img/dashi-info.png')
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
            ->setFooter($this->footer)
            ->setFooterIcon(env('APP_URL').'/img/dashi-logo.png');
    }

    /**
     * Prepare the data to notify the pull request creator.
     */
    private function buildSlackAttachmentFromCPR()
    {
        $fromBranch = $this->request['pull_request']['head']['ref'];
        $toBranch = $this->request['pull_request']['base']['ref'];
        $closedBy = $this->request['sender']['login'];
        $madeBy = $this->request['pull_request']['user']['login'];
        $closer = $closedBy == $madeBy ? 'you' : $closedBy;

        $authorName = $this->request['sender']['login'];
        $authorIcon = $this->request['sender']['avatar_url'];
        $authorLink = $this->request['sender']['html_url'];
        $title = $this->request['pull_request']['title'];
        $titleLink = $this->request['pull_request']['html_url'];
        $pretext = ":no_entry_sign: This pull request was closed by {$closer}.";
        $text = ':crossed_swords: Your pull request was closed for some reason, check it out!';

        if ($this->request['pull_request']['body']) {
            $this->attachment->setFields([
                'title' => 'Comment',
                'value' => $this->request['pull_request']['body'],
                'short' => false,
            ]);
        }

        $this->attachment->setColor(Color::RED)
            ->setIconUrl(env('APP_URL').'/img/dashi-danger.png')
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
                'title' => 'Branch',
                'value' => "`{$fromBranch}` ⇢ `{$toBranch}`",
                'short' => true,
            ])
            ->setFields([
                'title' => 'From',
                'value' => 'Github',
                'short' => true,
            ])
            ->setFooter($this->footer)
            ->setFooterIcon(env('APP_URL').'/img/dashi-logo.png');
    }
}
