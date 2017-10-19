<?php

namespace App\Http\Controllers;

use App\SlackToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $token = SlackToken::where("user_id", Auth()->user()->id)->first();
        $token = $token ?: new SlackToken();
        return view('home', compact("token"));
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            "token" => [
                "required",
                "regex:/https:\/\/hooks\.slack\.com\/services\/\w{9}\/\w{9}\/\w{24}/"
            ]
        ],
        [
            "required" => "The Slack Webhook URL is required",
            "regex" => "The Slack Webhook URL format is invalid",
        ]);

        if ($request->id) {
            $token = SlackToken::where("id", $request->id)->first();
        } else {
            $token = new SlackToken();
            $token->user_id = Auth::user()->id;
        }

        $token->github_username = $request->github_username;
        $token->bitbucket_username = $request->bitbucket_username;
        $token->token = $request->token;
        $token->save();

        return redirect("/home")->with("message", "Information saved successfully");

    }
}
