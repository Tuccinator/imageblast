@extends('layout')

@section('title', 'View Group')

@section('content')
    <group-view :group-id="{{ $group->id }}" :auth="{{ $isAllowed ?: 0 }}"></group-view>
@endsection
