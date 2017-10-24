<?php

namespace App\Libraries\Parser;

use App\Libraries\Slack\SlackAttachment;

class BitbucketParser implements ParserInterface
{
    private $request;
    private $action;
    private $attachment;
    private $suscribers = [];
    private $supportedActions = [
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

    /**
     * GithubParser constructor.
     *
     * @param $request
     */
    public function __construct(array $request)
    {
        $this->request = $request;
        $this->run();
    }

    public function run()
    {
        $this->setAction();
        $this->setSuscribers();
        $this->buildSlackAttachment();
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getAttachment()
    {
        return $this->attachment;
    }

    public function getRawRequest()
    {
        return $this->request;
    }

    public function buildSlackAttachment()
    {
        $builderName = "buildSlackAttachmentFrom{$this->aliases[$this->action]}";

        if (method_exists($this, $builderName)) {
            return call_user_func([$this, $builderName]);
        }
    }

    public function getSuscribers()
    {
        return $this->suscribers;
    }

    private function setAction()
    {
        foreach ($this->supportedActions as $action => $isThisAction) {
            if ($this->{$isThisAction}()) {
                $this->action = $action;

                return;
            }
        }
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
        return 'synchronize' === $this->request['action'];
    }

    private function buildSlackAttachmentFromRR()
    {
        $slackAttachment = new SlackAttachment();

        $authorName = $this->request['pull_request']['user']['login'];
        $authorIcon = $this->request['pull_request']['user']['avatar_url'];
        $authorLink = $this->request['pull_request']['user']['url'];
        $title = $this->request['pull_request']['title'];
        $titleLink = $this->request['pull_request']['html_url'];
        $pretext = ":microscope: *{$authorName}* needs you to make a `Code Review` to these changes.";
        $text = ':sleuth_or_spy: Make sure everything is in order before approving this Pull Request.';

        $slackAttachment->setColor('#36a64f')
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
                            'short' => false,
                        ])
                        ->setFields([
                            'title' => 'From',
                            'value' => 'Github',
                            'short' => true,
                        ])
                        ->setFields([
                            'title' => 'File(s) changed',
                            'value' => $this->request['pull_request']['changed_files'],
                            'short' => true,
                        ])
                        ->setFooter('Dashi')
                        ->setFooterIcon('http://dashinotify.com/img/dashi-logo.png');

        $this->attachment = $slackAttachment;
    }

    private function setSuscribers()
    {
        $setterName = "setSuscribers{$this->aliases[$this->action]}";

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
    }

    private function setSuscribersMIC()
    {
    }

    private function setSuscribersPOOPR()
    {
    }

    private function buildSlackAttachmentFromCR()
    {
    }

    private function buildSlackAttachmentFromMIC()
    {
    }

    private function buildSlackAttachmentFromPOOPR()
    {
    }
}
