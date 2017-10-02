<!doctype html>
<html lang="en">
<head>
    @component('components.javascript_detection')
    @endcomponent

    @component('components.cookie_detection')
    @endcomponent

    @component('components.facebook_hash_removal')
    @endcomponent

    @component('components.csrf_token_meta')
    @endcomponent

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', 'OzSpy')</title>

    @component('components.variables_setter')
    @endcomponent

    @yield('links')

    @yield('head_scripts')
</head>
<body>

@yield('body')

@yield('body_scripts')

</body>
</html>