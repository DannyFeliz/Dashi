<?php

namespace App\Http\Controllers;

use App\SlackToken;
use App\User;
use function compact;
use function dd;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function redirect;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
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
        $token = $token ? $token : new SlackToken();
        return view('home', compact("token"));
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            "github_username" => "required",
            "token"           => "required"
        ]);

        if ($request->id) {
            $token = SlackToken::where("id", $request->id)->first();
            $token->github_username = $request->github_username;
            $token->token = $request->token;

        } else {

            $token = new SlackToken();
            $token->user_id = Auth::user()->id;
            $token->github_username = $request->github_username;
            $token->token = $request->token;
        }

        $token->save();

        return redirect("/home")->with("message", "Information saved successfully");

    }
}
