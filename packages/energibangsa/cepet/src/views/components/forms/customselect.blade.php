<div class="form-group">
    @if(isset($label))
    <label for="{{ $name }}" class="col-md-2 col-form-label">{{ $label }}</label>
    @endif

    <div class="col-md-6">
        <select 
            class="custom-select form-control" 
            id="{{ $name }}">
            <option selected="selected">Open this select menu</option>
            <option value="1">{{ $val1 }}</option>
            <option value="2">{{ $val2 }}</option>
            <option value="3">{{ $val3 }}</option>
            <option value="4">{{ $val4 }}</option>
        </select>
        <span class="form-text text-muted">{{ isset($text) ? $text : '' }}</span>
    </div>
</div>
