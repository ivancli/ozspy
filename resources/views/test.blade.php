@extends('layouts.angle')

@section('title', 'OzSpy')

@section('links')
    <link rel="stylesheet" href="{{mix('/css/app.css')}}">
@stop

@section('head_scripts')
    {{--<script src='https://www.google.com/recaptcha/api.js'></script>--}}
@stop

@section('body')
    <div id="ozspy" class="wrapper" v-cloak>
        <passport-clients></passport-clients>
        <passport-authorized-clients></passport-authorized-clients>
        <passport-personal-access-tokens></passport-personal-access-tokens>
    </div>
@stop

@section('body_scripts')
    <script src="{{mix('/js/test.js')}}"></script>
@stop