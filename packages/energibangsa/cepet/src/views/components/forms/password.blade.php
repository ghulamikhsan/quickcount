<div class="form-group form-{{$name}}">
    <label  for="{{ $name }}">{{ $label }}</label>
    <input 
        type="password" 
        class="form-control input-{{$name}}" 
        id="input-{{$name}}"
        name="{{ $name }}"
        placeholder="{{ $placeholder ?? '' }}" />
    <span class="form-text text-muted">{{ $text_helper ?? '' }}</span>
</div>