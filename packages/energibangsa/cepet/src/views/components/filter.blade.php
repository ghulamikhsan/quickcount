<div class="row">
    <div class="col-12 col-sm-4">{{$label}}</div>
    <div class="col-12 col-sm-4">
        <div class="form-group m-form__group">
            <select class="form-control m-input m-input--pill" name="diff_{{$name}}" id="diff_{{$name}}">
                @foreach ($diff as $d)
                    <option value="{{$d}}">{{$d}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        @if($type == 'select')
            <div class="form-group m-form__group">
                <select class="form-control m-input m-input--pill" name="filter_{{$name}}" id="filter_{{$name}}">
                    <option value="">-- Semua {{$label}} --</option>
                    @if (gettype($params['data']) == "array")
                        @foreach ($params['data'] as $val => $name)
                            <option value="{{$val}}">{{$name}}</option>
                        @endforeach
                    @else
                        @foreach ($params['data'] as $dt)
                            <option value="{{ $dt->{$params['name']} }}">{{ $dt->{$params['value']} }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        @else
            <div class="form-group m-form__group">
                <input name="filter_{{$name}}" id="filter_{{$name}}" type="{{$type}}" class="form-control m-input m-input--pill input_filter_{{$name}}" placeholder="{{$label}}">
            </div>
        @endif
    </div>
</div>