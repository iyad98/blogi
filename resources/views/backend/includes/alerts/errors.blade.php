@if ($errors->any())
    @foreach ($errors->all() as $error)
    <div class="alert alert-danger text-right ">
        {{ $error }}
    </div>
    @endforeach
@endif
