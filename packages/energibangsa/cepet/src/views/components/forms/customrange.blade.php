<div class="form-group">
    @if(isset($label))
    <label for="{{$name}}" class="col-2 col-form-label">{{ $label }}</label>
    @endif

    <div class="col-md-6">
        <input 
            type="range" 
            class="custom-range"
            id="{{ $name }}"
            min="0" max="5"
            placeholder="{{ isset($placeholder) ? $placeholder : '2011-08-19T13:45:00'}}">
        <span class="form-text text-muted">{{ isset($text) ? $text : '' }}</span>    
    </div>
</div>