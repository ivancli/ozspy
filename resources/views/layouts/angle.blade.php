<!doctype html>
<html lang="en">
<head>
    {{--redirect if js not available--}}
    <noscript>
        <meta http-equiv="refresh" content="0; url={{ route('errors.javascript-disabled') }}"/>
    </noscript>

    {{--redirect if cookie not available, unable to store login session anyway without cookie--}}
    <script type="text/javascript">
        if (navigator.cookieEnabled === false) {
            window.location = "{{ route('errors.cookie-disabled') }}";
        }
    </script>

    @component('components.csrf_token_meta')
    @endcomponent

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', 'OzSpy')</title>

    @yield('links')

    @yield('head_scripts')
</head>
<body class="layout-h">

@yield('body')

@yield('body_scripts')

</body>
</html>