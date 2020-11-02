<div class="form-group">
    @if(isset($label))
    <label for="{{ $name }}" class="col-md-2 col-form-label">{{ $label }}</label>
    @endif

    <div class="col-md-6">
        <select multiple="multiple"class="form-control" id="{{ $name }}">
            <option>{{ $value1 }}</option>
            <option>{{ $value2 }}</option>
            <option>{{ $value3 }}</option>
            <option>{{ $value4 }}</option>
        </select>
        <span class="form-text text-muted">{{isset($text) ? $text : ''}}</span>
    </div>
</div>
