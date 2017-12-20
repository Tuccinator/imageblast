@extends('layout')

@section('title', $group->name)

@section('content')
    <group-view :group-id="{{ $group->id }}" :auth="{{ $isAllowed ?: 0 }}"></group-view>
@endsection
