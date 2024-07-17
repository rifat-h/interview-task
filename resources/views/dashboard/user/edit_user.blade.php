@extends('dashboard.layouts.app')

@section('content')
@include('dashboard.layouts.partials.error')

<form action="{{ route('user.update', $data->user) }}" method="POST">
    @csrf
    @method('patch')

    <div class="tile mb-3">
        <div class="tile-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <button onclick="window.history.back()" type="button" type="button" class="btn btn-info float-left">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i> Go Back
                        </button>
                        <h3 class="text-center">Edit User</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tile">
        <div class="tile-body">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-12">

                        <div class="row">
                            {!! $data->form !!}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tile">
        <div class="tile-body">
            <div class="row">
                <div class="col-md-4 offset-md-8">
                    <button class="btn btn-success float-right">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>

</form>

@endsection
