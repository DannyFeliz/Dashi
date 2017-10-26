<?php

namespace App\Libraries\Slack;

class SlackAttachment
{
    private $fallback = 'Notification by Dashi ;)';
    private $color;
    private $pretext;
    private $author_name;
    private $author_link;
    private $author_icon;
    private $title;
    private $title_link;
    private $text;
    private $fields = [];
    private $footer;
    private $footer_icon;
    private $ts;
    private $mrkdwn_in;

    /**
     * GithubParser constructor.
     *
     * @param $request
     */
    public function __construct()
    {
        $this->ts = time();
        $this->mrkdwn_in = ['text', 'pretext', 'fields'];
    }

    public function setMrkdwnFields(array $mrkdwnFields)
    {
        $this->mrkdwn_in = $mrkdwnFields;
    }

    public function getMrkdwnFields(): array
    {
        return $this->mrkdwn_in;
    }

    public function addMrkdwnField(string $mrkdwnField)
    {
        $this->mrkdwn_in[] = $mrkdwnFields;
    }

    public function removeMrkdwnField(string $mrkdwnField)
    {
        if (false !== ($key = array_search($mrkdwnField, $this->mrkdwn_in))) {
            unset($this->mrkdwn_in[$key]);
        }
    }

    public function setFallback(string $fallback)
    {
        //the fallack message is mandatory
        if (!$fallback) {
            $this->fallback = $fallback;
        }

        return $this;
    }

    public function setColor(string $color)
    {
        $this->color = $color;

        return $this;
    }

    public function setPretext(string $pretext)
    {
        $this->pretext = $pretext;

        return $this;
    }

    public function setAuthorName(string $authorName)
    {
        $this->author_name = $authorName;

        return $this;
    }

    public function setAuthorLink(string $authorLink)
    {
        $this->author_link = $authorLink;

        return $this;
    }

    public function setAuthorIcon(string $authorIcon)
    {
        $this->author_icon = $authorIcon;

        return $this;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    public function setTitleLink(string $titleLink)
    {
        $this->title_link = $titleLink;

        return $this;
    }

    public function setText(string $text)
    {
        $this->text = $text;

        return $this;
    }

    public function setFields(array $fields)
    {
        $this->fields[] = $fields;

        return $this;
    }

    public function setFooter(string $footer)
    {
        $this->footer = $footer;

        return $this;
    }

    public function setFooterIcon(string $footerIcon)
    {
        $this->footer_icon = $footerIcon;

        return $this;
    }

    public function getFallback()
    {
        return $this->fallback;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getPretext()
    {
        return $this->pretext;
    }

    public function getAuthorName()
    {
        return $this->authorName;
    }

    public function getAuthorLink()
    {
        return $this->authorLink;
    }

    public function getAuthorIcon()
    {
        return $this->authorIcon;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getTitleLink()
    {
        return $this->titleLink;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getFooter()
    {
        return $this->footer;
    }

    public function getFooterIcon()
    {
        return $this->footerIcon;
    }

    public function getTs()
    {
        return $this->ts;
    }

    public function toArray()
    {
        $asArray = [];

        foreach ($this as $properties => $value) {
            $asArray[str_replace(get_class($this), '', $properties)] = $value;
        }

        return $asArray;
    }

    public function toJson()
    {
        return json_encode($this->toArray(), true);
    }
}
