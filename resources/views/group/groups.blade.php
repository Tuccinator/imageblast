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
            @foreach($groups as $group)
                <div class="group-row columns">
                    <div class="group-name column is-one-third">
                        <span>{{ $group->name }}</span>
                    </div>
                    <div class="column is-one-third"></div>
                    <div class="group-join column is-one-third">
                        <a href="/groups/{{ $group->id }}" class="button is-small is-info">View Group</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="column is-one-quarter">
            <create-group-form></create-group-form>
        </div>
    </div>
@endsection
