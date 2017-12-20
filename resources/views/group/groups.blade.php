@extends('layout')

@section('title', 'Find Group')

@section('hero')
    <div class="hero is-primary is-small">
        <div class="hero-body">
            <div class="container">
                <h1 class="subtitle">Groups</h1>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="columns">
        <div class="column is-three-quarters">
            <groups-list :auth="{{ $auth ?: 0 }}" :auth-id="{{ $authId ?: 0 }}"></groups-list>
        </div>
        <div class="column is-one-quarter">
            <create-group-form></create-group-form>
        </div>
    </div>
@endsection
