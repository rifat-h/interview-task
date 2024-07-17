@extends('dashboard.layouts.app')

@section('content')

<form method="POST" action="{{ route('module.store') }}">
    @csrf

    <div class="tile">
        <div class="tile-body">
            <div class="container-fluid mb-3">
                <div class="row">
                    <div class="col-md-12">
                        <button onclick="window.history.back()" type="button" class="btn btn-info float-left">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i> Go Back
                        </button>
                        <h3 class="text-center">Create New Module</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tile">
        <div class="tile-body">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Parent Name</label>

                            <select name="parent_id" class="form-control select2" id="parent_id">
                                <option value="">Select An Parent</option>
                                @foreach ($data->permissions as $permission)
                                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Module Name</label>
                            <input class="form-control" name="name" id="name" type="text" placeholder="Enter name">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Order</label>
                            <input class="form-control" name="order_no" id="order" type="number">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Url</label>
                            <input class="form-control" name="url" id="url" type="text">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="icon_color">Icon Color</label>
                            <input class="form-control" name="icon_color" id="icon_color" type="color" value="#ffffff">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="icon">Icon</label>
                            <input class="form-control" name="icon" id="icon" type="text" value="fa fa-dashboard">
                        </div>
                    </div>

                    <div class="col-md-4 mt-4 d-none" id="action_menu">
                        <label for="name">Action Menu</label>
                        <input name="action_menu" type="checkbox">
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="tile">
        <div class="tile-body">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-success mt-2 float-right">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>

</form>
@endsection

@section('custom-script')
<script>
    $(document).ready(function () {

        $('#action_menu').hide();

            $('#parent_id').change(function (e) {
                e.preventDefault();

                let parentValue = $(this).val();

                if(parentValue != ''){
                    $('#action_menu').show();
                }else{
                    $('#action_menu').hide();
                }

            });

        });
</script>
@endsection
