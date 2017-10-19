@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Settings</div>

                    <div class="panel-body">
                        <p>Configure your <a target="_blank" href="https://github.com">Github</a> and
                            <a target="_blank" href="https://bitbucket.com">Bitbucket</a> username also create a
                            <a target="_blank" href="https://mctekk.slack.com/apps/A0F7XDUAZ-incoming-webhooks">Slack
                                Incoming Webhook</a></p>


                        @if (session('message'))
                            <div class="alert alert-success">
                                {{ session('message') }}
                            </div>
                        @endif
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="/save" method="post">
                            {{ csrf_field() }}

                            <input type="hidden" name="id" value="{{ $token->id }}">
                            <div class="form-group">
                                <label>Github username</label>
                                <input type="text"
                                       name="github_username"
                                       class="form-control"
                                       value="{{ $token->github_username}}"
                                       placeholder="JhonDoe">
                            </div>
                            <div class="form-group">
                                <label>Bitbucket username</label>
                                <input type="text"
                                       name="bitbucket_username"
                                       class="form-control"
                                       value="{{ $token->bitbucket_username}}"
                                       placeholder="JhonDoe">
                            </div>

                            <div class="form-group">
                                <label>Slack Webhook URL</label>
                                <input type="url"
                                       required
                                       name="token"
                                       class="form-control"
                                       value="{{ $token->token }}"
                                       minlength="69"
                                       placeholder="https://hooks.slack.com/services/xxxxxxxxx/xxxxxxxxx/xxxxxxxxxxxxxxxxxxxxxxxx">
                            </div>

                            <button type="submit" class="btn btn-default">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
