<div class="col-md-{{ $input['col'] }}">
    <div class="form-group">
        <label for="{{ $input['id'] }}">{{ $input['label'] }}</label>
        <select class="form-control {{ $input['smart'] ? " select2" : "" }}" name="{{ $input['name'] }}"
            id="{{ $input['id'] }}" type="select" {{ $input['required'] ? "required" : ""}}>

            @if ($input['show_blank'])
            <option>Select an Option</option>
            @endif

            @foreach ($input['options'] as $option)
            <option value="{{ $option['value'] }}" @if ($option['value'] == $input['value'])
                selected
            @endif>{{ $option['label'] }}</option>
            @endforeach
        </select>
    </div>
</div>