@if ($type == "hidden")
    <input type="hidden" name="{{$name}}" id="{{$name}}" value="{{$value ?? ""}}" />
@elseif($type == "button")
    <div class="form-group m-form__group form_{{$name}} {{$class_form ?? ""}}">
        <button id="{{$name}}" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btn-sm input_{{$name}}" {{$attr ?? ""}}>
            <span>
                @if (isset($icon))
                    <i class="{{$icon}}"></i>
                @endif
                <span>
                    {{$label}}
                </span>
            </span>
        </button>
    </div>
@elseif($type == 'select')
    <div class="form-group m-form__group form_{{$name}}">
        <label>
            {{$label}}
        </label>
        <select class="form-control m-input m-input--pill {{$editDisabled ?? ""}}" name="{{$name}}" id="{{$name}}" $add>
            @foreach ($options as $val => $name)
                <option value="{{$val}}">{{$name}}</option>
            @endforeach
        </select>
    </div>
@else
    <div class="form-group m-form__group form_{{$name}}">
        <label>
            {{ $label }}
        </label>
        <input name="{{$name}}" id="{{$name}}" type="{{$type}}" value="{{$value ?? "" }}" class="form-control m-input m-input--pill input_{{$name}} {{$editDisabled ?? ""}}" placeholder="{{ $placeholder ?? $label}}" {{$attr ?? ""}}>
    </div>
@endif