@extends('layouts.angle_center')

@section('title', 'OzSpy')

@section('links')
    <link rel="stylesheet" href="{{mix('/css/app.css')}}">
@stop

@section('head_scripts')
    {{--<script src='https://www.google.com/recaptcha/api.js'></script>--}}
    <script>
        var token = "{!! isset($token) ? $token : null !!}";
        var email = "{!! isset($email) ? $email : null !!}";
    </script>
@stop

@section('body')
    <div id="ozspy" class="wrapper" v-cloak>
        <reset></reset>
    </div>
@stop

@section('body_scripts')
    <script src="{{mix('/js/auth/reset.js')}}"></script>
@stop