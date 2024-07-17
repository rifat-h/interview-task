@foreach ($errors->all() as $error)
<div class="bs-component">
    <div class="alert alert-dismissible alert-danger">
        <button class="close" type="button" data-dismiss="alert">×</button>
        {{ $error }}
    </div>
</div>
@endforeach

@if( Session('message'))
<div class="alert alert-{{Session('class_name')}} alert-dismissible fade show" role="alert">
    <strong>{{ Session('message') }}</strong>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif