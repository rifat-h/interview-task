<div class="col-md-{{ $input['col'] }}">
    <label for="{{ $input['id'] }}">{{ $input['label'] }}</label>
    <div class="custom-file">
        <input type="file" class="custom-file-input" name="{{ $input['name'] }}" id="{{ $input['id'] }}" {{
            $input['required'] ?? "required" }}>
        <label class="custom-file-label" for="{{ $input['id'] }}">Choose file</label>
    </div>
</div>