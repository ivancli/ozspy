{{--redirect if cookie not available, unable to store login session anyway without cookie--}}
<script type="text/javascript">
    if (navigator.cookieEnabled === false) {
        window.location = "{{ route('errors.cookie-disabled') }}";
    }
</script>