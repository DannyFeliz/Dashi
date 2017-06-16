<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SlackToken extends Model
{
    protected $table = "slack_tokens";

    protected $fillable = ["github_username", "token", "user_id"];

    public function user ()
    {
        return $this->belongsTo('App\User');
    }

}
