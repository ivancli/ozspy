@extends('layouts.angle_center')

@section('title', 'OzSpy')

@section('links')
    <link rel="stylesheet" href="{{mix('/css/app.css')}}">
@stop

@section('head_scripts')
    {{--<script src='https://www.google.com/recaptcha/api.js'></script>--}}
@stop

@section('body')
    <div id="ozspy" class="wrapper" v-cloak>
        <register></register>
    </div>
@stop

@section('body_scripts')
    <script src="{{mix('/js/auth/register.js')}}"></script>
@stop