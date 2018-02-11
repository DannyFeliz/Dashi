<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    public const RED = "danger";
    public const WARNING = "#daa038";
    public const MERGED = "#6f42c1";
    public const PR_CLOSED = "#ce0502";
    public const PR_UPDATED = "#047bff";
    public const MENTION_IN_COMMENT = "#047bff";
    public const REVIEW_REQUEST = "#36a64f";
}
