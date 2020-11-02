<div class="form-group">
    @if(isset($label))
    <label class="col-2 col-form-label">{{ $label }}</label>
    @endif

    <div class="col-md-6">
        <input type="radio"> 
            <td>{{isset($value) ? $value: ''}}</td>
        <span class="form-text text-muted">{{isset($text) ? $text : ''}}</span>
    </div>
</div>