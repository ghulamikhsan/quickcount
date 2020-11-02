<div class="form-group form-{{$id ?? $name}}">
    <label  for="{{ $name }}">{!! $label !!}</label>
    <textarea 
        class="form-control input-{{$id ?? $name}} {{$editDisabled ?? ""}}" 
        id="input-{{$id ?? $name}}" 
        name="{{ $name }}"
        rows="{{$rows ?? 3}}"
        {!! $attr ?? "" !!}>{{ $value ?? ""}}</textarea>
    <span class="form-text text-muted">{{ $text_helper ?? '' }}</span>
</div>