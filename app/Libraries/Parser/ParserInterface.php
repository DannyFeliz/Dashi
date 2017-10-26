<?php

namespace App\Libraries\Parser;

use App\Libraries\Slack\SlackAttachment;

interface ParserInterface
{
    public function __construct(array $request);

    public function parse();

    public function getEvent(): string;

    public function getAttachment(): SlackAttachment;

    public function getRawRequest(): array;

    public function buildSlackAttachment();

    public function getSuscribers(): array;
}
