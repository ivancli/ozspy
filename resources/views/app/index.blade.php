@extends('layouts.angle')

@section('title', 'Account Settings - SpotLite')

@section('links')
    <link rel="stylesheet" href="{{mix('/css/app.css')}}">
@stop

@section('head_scripts')
    {{--<script src='https://www.google.com/recaptcha/api.js'></script>--}}
@stop

@section('body')
    <div id="ozspy">
        <angle v-cloak>
            <header slot="header"></header>
            <sidebar slot="sidebar"></sidebar>
            <index slot="content"></index>
        </angle>
    </div>
@stop

@section('scripts')
    <script src="{{mix('/js/app.js')}}"></script>
@stop