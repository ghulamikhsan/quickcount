<div class="form-group form-{{$id ?? $name}}">
    <label  for="{{ $name }}">{!! $label !!}</label>

    <div class="col-12">
        <input 
            type="file"
            class="custom-file-input form-control input-{{$id ?? $name}} {{$editDisabled ?? ""}}" 
            id="input-{{$id ?? $name}}"
            name="{{ $name }}"
            {!! $attr ?? '' !!}>
            
        <label 
            style="overflow:hidden;text-overflow:ellipsis;"
            class="custom-file-label"
            id = "custom-file-label-{{$id ?? $name}}";
            for="customFile"> {{ $placeholder ?? 'Choose File' }}</label>
        <span class="form-text text-muted">{{ $text_helper ?? '' }}</span>
    </div>
</div>