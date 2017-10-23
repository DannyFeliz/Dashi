@extends('layouts.app')

@section('content')
<div class="container main-content">
    <div class="row">
        <div class="ml-auto col-md-8 mr-auto">
            <h2 class="section-title">Settings</h2>
            <div class="card card-default">
                <div class="card-block">
                    <p>Configure your <a target="_blank" href="https://github.com">Github</a> and
                        <a target="_blank" href="https://bitbucket.com">Bitbucket</a> username also create a
                        <a target="_blank" href="https://mctekk.slack.com/apps/A0F7XDUAZ-incoming-webhooks">Slack Incoming Webhook</a></p>

                        @if (session('message'))
                            <div class="alert alert-success">
                                {{ session('message') }}
                            </div>
                        @endif

                        <form action="/save" method="post">
                            {{ csrf_field() }}

                        <input type="hidden" name="id" value="{{ $token->id }}" >
                        <div class="form-group">
                            <label>Github username</label>
                            <input type="text"
                                   name="github_username"
                                   class="form-control"
                                   value="{{ $token->github_username}}"
                                   placeholder="JhonDoe"
                            >

                        </div>

                            <div class="form-group">
                                <label>Bitbucket username</label>
                                <input type="text"
                                       name="bitbucket_username"
                                       class="form-control"
                                       value="{{ $token->bitbucket_username}}"
                                       placeholder="JhonDoe"
                                >
                            </div>

                        <div class="form-group">
                            <label>Slack Webhook URL</label>
                            <input type="text"
                                   required
                                   name="token"
                                   class="form-control"
                                   value="{{ $token->token }}"
                                   minlength="69"
                                   placeholder="https://hooks.slack.com/services/XXXXX/XXXXXX/XXXXXX"
                            >
                        </div>

                        <button type="submit" class="btn btn-complete">Save</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
