@extends('layout')

@section('title', 'Account')

@section('hero')
    <div class="hero is-primary is-small">
        <div class="hero-body">
            <div class="container">
                <h1 class="subtitle">Account</h1>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <avatar-form current-avatar="{{ asset($user->avatar) }}"></avatar-form>
@endsection
