<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Dashi</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">

        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/pages.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    </head>
    <body>
    <!--     <div class="bg-pic">
      <img class="auth-bg" src="" class="lazy">
    </div> -->
        <div class="intro flex-center position-ref full-height">
    <!--         @if (Route::has('login'))
        <div class="top-right links">
            @if (Auth::check())
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ url('/login') }}">Login</a>
                <a href="{{ url('/register') }}">Register</a>
            @endif
        </div>
    @endif -->

            <div class="content">
                <div class="">
                    <figure class="logo">
                        <img src="{{ asset('img/dashi-logo.png') }}"/>
                    </figure>
                    <!-- <p class="title" style="margin: 0;">Welcome to Dashi<p> -->
                    <p class="m-b-20">Get a notification in Slack every time someone asks you to check his code on Github.</p>
                    <div class="row">
                        <div class="col text-right">
                            <a class="btn btn-complete btn-cons" href="{{ url('/login') }}">Login</a>
                        </div>
                        <div class="col text-left">
                            <a class="btn btn-complete btn-cons" href="{{ url('/register') }}">Register</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="intro-arrow">
                <a href="#examples" class="text-complete">
                    <i class="fa fa-angle-down animated infinite bounce" aria-hidden="true"></i>
                    <p>See more</p>
                </a>
            </div>
        </div>
        <div class="section examples" id="examples">
            <div class="container">
                <h2 class="section-title">Screenshots</h2>
                <div class="row">
                    <div class="col-12 col-xl-6">
                        <h6>Request Review</h6>
                        <img src="img/screenshot/request-review-pr-example.png" class="img-fluid" alt="Request review">
                    </div>
                    <div class="col-12 col-xl-6">
                        <h6>Request Changes</h6>
                        <img src="img/screenshot/request-changes-example.png" class="img-fluid" alt="Request review">
                    </div>
                    <div class="col-12 col-xl-6">
                        <h6>Mention in comment</h6>
                        <img src="img/screenshot/mention-in-comment-example.png" class="img-fluid" alt="Request review">
                    </div>
                </div>
            </div>
        </div>
        <div class="section support">
            <div class="container">
                <h2 class="section-title">Support / Webhook needed</h2>
                <div class="row">
                    <div class="col-12 col-xl-5">
                        <h6>Support</h6>
                        <table class="table table-responsive table-striped table-hover dataTable no-footer">
                            <thead>
                                <tr>
                                    <th style="width:90%">Feature</th>
                                    <th style="width:5%">Github</th>
                                    <th style="width:5%">Bitbucket</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><p>Request Review (pull request)</p></td>
                                    <td><g-emoji alias="heavy_check_mark" fallback-src="https://assets-cdn.github.com/images/icons/emoji/unicode/2714.png" ios-version="6.0">✔️</g-emoji></td>
                                    <td><g-emoji alias="heavy_check_mark" fallback-src="https://assets-cdn.github.com/images/icons/emoji/unicode/2714.png" ios-version="6.0">✔️</g-emoji></td>
                                </tr>
                                <tr>
                                    <td><p>Request Changes (pull request)</p></td>
                                    <td><g-emoji alias="heavy_check_mark" fallback-src="https://assets-cdn.github.com/images/icons/emoji/unicode/2714.png" ios-version="6.0">✔️</g-emoji></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><p>Mentions in comments (pull request diff code)</p></td>
                                    <td><g-emoji alias="heavy_check_mark" fallback-src="https://assets-cdn.github.com/images/icons/emoji/unicode/2714.png" ios-version="6.0">✔️</g-emoji></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 col-xl-7">
                        <h6>Webhook</h6>
                        <table class="table table-responsive table-striped table-hover dataTable no-footer">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Github Webhook</th>
                                    <th>Bitbucket Webhook</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><p>Request Review in a pull request</p></td>
                                    <td><p>Pull request</p></td>
                                    <td><p>Created</p></td>
                                </tr>
                                <tr>
                                    <td><p>Request Changes in a pull request</p></td>
                                    <td><p>Pull request</p></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><p>Mentions in comments</p></td>
                                    <td><p>Pull request review comment</p></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="section setup">
            <div class="container">
                <h2 class="section-title">Setup</h2>
                <div class="row">
                    <div class="col">
                        <div class="card card-borderless m-t-20">
                          <ul class="nav nav-tabs nav-tabs-simple" role="tablist" data-init-reponsive-tabs="dropdownfx">
                            <li class="nav-item">
                              <a class="active" data-toggle="tab" role="tab" data-target="#slack" href="#">Slack incoming webhooks setup</a>
                            </li>
                            <li class="nav-item">
                              <a href="#" data-toggle="tab" role="tab" data-target="#dashi">Dashi setup</a>
                            </li>
                            <li class="nav-item">
                              <a href="#" data-toggle="tab" role="tab" data-target="#repository">Repository Setup</a>
                            </li>
                          </ul>
                          <div class="tab-content">
                            <div class="tab-pane active" id="slack">
                              <div class="row column-seperation">
                                <div class="col text-center">
                                  <img src="img/setup/slack/1.png" class="img-fluid m-b-10">
                                  <img src="img/setup/slack/2.png" class="img-fluid m-b-10">
                                  <img src="img/setup/slack/3.png" class="img-fluid m-b-10">
                                </div>
                              </div>
                            </div>
                            <div class="tab-pane" id="dashi">
                              <div class="row">
                                <div class="col">
                                  <ol class="m-t-20">
                                      <li>Go to <a href="http://dashinotify.com/login" target="_blank">Dashi</a> and login or signup</li>
                                      <li>Type your Github or Bitbucket username</li>
                                      <li>Paste the copied Slack Webook URL</li>
                                      <li>Save :)</li>
                                  </ol>
                                </div>
                              </div>
                            </div>
                            <div class="tab-pane" id="repository">
                              <div class="row">
                                <div class="col m-l-20">
                                    <h4 class="bold">Use <a href="javascript:void(0)">http://dashinotify.com/notifier</a> as the URL of the WebHook.</h4>
                                    <h5 class="bold">Github</h5>
                                    <img src="img/setup/repository/github.png" class="img-fluid m-b-10">
                                    <h5 class="bold">Bitbucket</h5>
                                    <img src="img/setup/repository/bitbucket.png" class="img-fluid m-b-10">
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('js/jquery-3.2.1.slim.min.js') }}"></script>
        <script src="{{ asset('js/tether.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/smooth-scroll.min.js') }}"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
    </body>
</html>
