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
        <default-template v-cloak>
            <default-header slot="default-header"></default-header>
            <default-content slot="default-content">
                <content-header slot="content-header">
                    <span slot="content-header-title">Account Settings</span>
                </content-header>
                <content-body slot="content-body"></content-body>
            </default-content>
        </default-template>
    </div>
@stop

@section('scripts')
    <script src="{{mix('/js/app.js')}}"></script>
@stop