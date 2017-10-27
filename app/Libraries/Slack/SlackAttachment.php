<?php

namespace App\Libraries\Slack;

class SlackAttachment
{
    private $username = '';
    private $iconUrl = '';
    private $fallback = 'Notification by Dashi ;)';
    private $color = '';
    private $pretext = '';
    private $authorName = '';
    private $authorLink = '';
    private $authorIcon = '';
    private $title = '';
    private $titleLink = '';
    private $fields = [];
    private $mrkdwnIn = [];
    private $footer = '';
    private $footerIcon = '';
    private $ts = 0;

    /**
     * GithubParser constructor.
     *
     * @param $request
     */
    public function __construct()
    {
        $this->username = env('APP_NAME');
        $this->ts = time();
        $this->mrkdwnIn = ['text', 'pretext', 'fields'];
    }

    public function setMrkdwnFields(array $mrkdwnFields): SlackAttachment
    {
        $this->mrkdwnIn = $mrkdwnFields;

        return $this;
    }

    public function getMrkdwnFields(): array
    {
        return $this->mrkdwnIn;
    }

    public function addMrkdwnField(string $mrkdwnField)
    {
        $this->mrkdwnIn = $mrkdwnFields;

        return $this;
    }

    public function removeMrkdwnField(string $mrkdwnField)
    {
        if (false !== ($key = array_search($mrkdwnField, $this->mrkdwnIn))) {
            unset($this->mrkdwnIn[$key]);
        }

        return $this;
    }

    public function setFallback(string $fallback): SlackAttachment
    {
        //the fallack attachment is mandatory
        if (!$fallback) {
            $this->fallback = $fallback;
        }

        return $this;
    }

    public function setColor(string $color): SlackAttachment
    {
        $this->color = $color;

        return $this;
    }

    public function setUsername(string $username): SlackAttachment
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setIconUrl(string $iconUrl): SlackAttachment
    {
        $this->iconUrl = $iconUrl;

        return $this;
    }

    public function getIconUrl(): string
    {
        return $this->iconUrl;
    }

    public function setPretext(string $pretext): SlackAttachment
    {
        $this->pretext = $pretext;
        //in order to use meaningfull attachment in the notification
        $this->fallback = $pretext;

        return $this;
    }

    public function setAuthorName(string $authorName): SlackAttachment
    {
        $this->authorName = $authorName;

        return $this;
    }

    public function setAuthorLink(string $authorLink): SlackAttachment
    {
        $this->authorLink = $authorLink;

        return $this;
    }

    public function setAuthorIcon(string $authorIcon): SlackAttachment
    {
        $this->authorIcon = $authorIcon;

        return $this;
    }

    public function setTitle(string $title): SlackAttachment
    {
        $this->title = $title;

        return $this;
    }

    public function setTitleLink(string $titleLink): SlackAttachment
    {
        $this->titleLink = $titleLink;

        return $this;
    }

    public function setText(string $text): SlackAttachment
    {
        $this->text = $text;

        return $this;
    }

    public function setFields(array $fields): SlackAttachment
    {
        $this->fields[] = $fields;

        return $this;
    }

    public function setFooter(string $footer): SlackAttachment
    {
        $this->footer = $footer;

        return $this;
    }

    public function setFooterIcon(string $footerIcon): SlackAttachment
    {
        $this->footerIcon = $footerIcon;

        return $this;
    }

    public function getFallback(): string
    {
        return $this->fallback;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getPretext(): string
    {
        return $this->pretext;
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function getAuthorLink(): string
    {
        return $this->authorLink;
    }

    public function getAuthorIcon(): string
    {
        return $this->authorIcon;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTitleLink(): string
    {
        return $this->titleLink;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getFooter(): string
    {
        return $this->footer;
    }

    public function getFooterIcon(): string
    {
        return $this->footerIcon;
    }

    public function getTs(): int
    {
        return $this->ts;
    }

    public function getMessage(): array
    {
        return [
            'username' => $this->username,
            'icon_url' => $this->iconUrl,
            'attachments' => [[
                'fallback' => $this->fallback,
                'color' => $this->color,
                'pretext' => $this->pretext,
                'author_name' => $this->authorName,
                'author_link' => $this->authorLink,
                'author_icon' => $this->authorIcon,
                'title' => $this->title,
                'title_link' => $this->titleLink,
                'fields' => $this->fields,
                'mrkdwn_in' => $this->mrkdwnIn,
                'footer' => $this->footer,
                'footer_icon' => $this->footerIcon,
                'ts' => $this->ts,
            ]],
        ];
    }
}
