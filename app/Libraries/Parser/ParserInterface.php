<?php

namespace App\Libraries\Parser;

interface ParserInterface
{
    public function __construct(array $request);

    public function parse();

    public function getEvent();

    public function getAttachment();

    public function getRawRequest();

    public function buildSlackAttachment();

    public function getSuscribers();
}
