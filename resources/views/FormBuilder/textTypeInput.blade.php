<div class="col-md-{{ $input['col'] }}">
    <div class="form-group">
        <label for="{{ $input['id'] }}">{{ $input['label'] }}</label>
        <input class="form-control" name="{{ $input['name'] }}" id="{{ $input['id'] }}" type="{{ $input['type'] }}"
            placeholder="{{ $input['placeholder'] }}" value="{{ $input['value'] }}" {{ $input['required'] ?? "required" }}>
    </div>
</div>