<?php

namespace App\Libraries\Parser;

use App\Libraries\Slack\SlackAttachment;

interface ParserInterface
{
    public function __construct($request);

    public function parse(): bool;

    public function getEvent(): string;

    public function getAttachment(): SlackAttachment;

    public function getRawRequest(): array;

    public function buildSlackAttachment();

    public function getSuscribers(): array;

    public function isAnActionRequest(): bool;

    public function getSupportedActionRequest(): array;

    public function isASupportedActionRequest(): bool;
    
}
