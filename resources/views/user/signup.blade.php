@extends('layout')

@section('title', 'Signup')

@section('hero')
    <div class="hero is-primary is-small">
        <div class="hero-body">
            <div class="container">
                <h1 class="subtitle">Signup</h1>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="padded-container">
        <div class="columns">
            <div class="column is-half is-offset-one-quarter">
                <signup-form></signup-form>
            </div>
        </div>
    </div>
@endsection
