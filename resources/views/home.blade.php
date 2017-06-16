@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <h2>Info</h2>

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
                            <input type="text" required name="github_username" class="form-control" value="{{ $token->github_username}}">
                        </div>

                        <div class="form-group">
                            <label>Slack token</label>
                            <input type="text" required name="token" class="form-control" value="{{ $token->token }}">
                        </div>

                        <button type="submit" class="btn btn-default">Save</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
