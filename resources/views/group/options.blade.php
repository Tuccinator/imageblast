@extends('layout')

@section('title', $group->name)

@section('hero')
    <div class="hero is-primary is-small">
        <div class="hero-body">
            <div class="container">
                <h1 class="subtitle">Group Options</h1>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="columns">
        <div class="column is-half is-offset-one-quarter">
            <group-options :group-id="{{ $group->id }}"></group-options>
        </div>
    </div>
@endsection
