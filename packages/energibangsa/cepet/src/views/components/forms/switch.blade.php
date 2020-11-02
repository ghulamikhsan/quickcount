<div class="form-group form-{{$id ?? $name}}">
    <label  for="{{ $name }}">{!! $label !!}</label>

    <div class="col-3">
        <span class="switch input-{{$id ?? $name}} {{$editDisabled ?? ""}}">
            <label>
                <input
                type="checkbox"
                {{ isset($value) && $value == 1 ? 'checked=checked' : '' }}
                name="{{ $name }}"/>
            <span></span>
            </label>
        </span>
        <span class="form-text text-muted">{{ $text_helper ?? '' }}</span>
    </div>
</div>