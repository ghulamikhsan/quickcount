<div class="form-group">
    @if(isset($label))
    <label for="{{ $name }}" class="col-2 col-form-label">{{ $label }}</label>
    @endif
    <div class="col-md-6">
        <input 
            type="tel"
            class="form-control"
            id="{{$name}}"
            value="{{isset($value) ? $value : ''}}"
            placeholder="{{isset($placeholder) ? $placeholder : ''}}"
            name="{{ $name }}">
        <span class="form-text text-muted">{{ isset($text) ? $text : '' }}</span>    
    </div>
</div>