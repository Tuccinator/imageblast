@if(!Auth::check())
    @include('landing-page')
@else
    @include('feed')
@endif
