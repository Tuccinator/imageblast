@extends('layout')

@section('title', 'Feed')

@section('hero')
    <div class="hero is-primary is-small">
        <div class="hero-body">
            <div class="container">
                <h1 class="subtitle">Feed</h1>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="columns">
        <div class="column is-half is-offset-one-quarter">
            <div class="feed-section">
                <upload-form></upload-form>
            </div>
            <feed></feed>
        </div>
    </div>
@endsection
