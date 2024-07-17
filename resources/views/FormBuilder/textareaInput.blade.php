<div class="col-md-{{ $input['col'] }}">
    <div class="form-group">
        <label for="{{ $input['id'] }}">{{ $input['label'] }}</label>
        <textarea class="form-control" name="{{ $input['name'] }}" id="{{ $input['id'] }}" type="textarea"
            placeholder="{{ $input['placeholder'] }}" {{ $input['required'] ?? "required" }}>{{ $input['value'] }}</textarea>
    </div>
</div>