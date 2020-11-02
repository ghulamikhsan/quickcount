<div class="form-group">
    @if(isset($label))
    <label for="{{ $name }}" class="col-2 col-form-label">{{ $label }}</label>
    @endif

    <div class="col-md-6">
        <input 
            class="form-control" 
            type="search" 
            value="{{ isset($value) ? $value : '' }}" 
            id="{{ $name }}" />
        <span class="form-text text-muted">{{isset($text) ? $text : ''}}</span>
    </div>
</div>
