@if(isset($errors))
    <script>
        var errors = {!! $errors->toJson() !!};
    </script>
@endif