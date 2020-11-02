<div class="form-group form-{{$id ?? $name}}">
    <label  for="{{ $name }}">{!! $label !!}</label>
    <input 
        type="text" 
        class="form-control input-{{$id ?? $name}} {{$editDisabled ?? ""}}" 
        id="input-{{$id ?? $name}}"
        name="{{ $name }}"
        placeholder="{{ $placeholder ?? '' }}"
        value="{{ $value ?? '' }}"
        {!! $attr ?? '' !!}/>
    <span class="form-text text-muted">{{ $text_helper ?? '' }}</span>
</div>