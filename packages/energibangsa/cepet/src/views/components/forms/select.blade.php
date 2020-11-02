<div class="form-group form-{{$name}}">
    <label  for="{{ $name }}">{{ $label }}</label>
    <select 
        class="form-control input-{{$name}} {{$editDisabled ?? ""}}" 
            id="input-{{$name}}"
            name="{{ $name }}"
            {!! $attr ?? '' !!}
            >
        @foreach ($options as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
        @endforeach 
    </select>
    <span class="form-text text-muted">{{ $text_helper ?? '' }}</span>
</div>
