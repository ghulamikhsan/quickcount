{{-- Start Card Form--}}
<div class="card card-custom gutter-b {!! $class ?? '' !!}" {!! $attr ?? '' !!}>
    <div class="card-header">
        <h3 class="card-title card-form-title">
            {!! $title !!}
        </h3>
        <div class="card-toolbar">
            {!! $toolbar ?? '' !!}
        </div>
    </div>
    @if (isset($type))
        @if ($type == 'form')
            <!--begin::Form-->
            <form id="my-form" class="my-form">
                <div class="card-body">
                    @foreach ($forms as $form)
                        @component("views::components.forms.$form[input]", $form)@endcomponent
                    @endforeach
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary mr-2 btn-submit">Submit</button>
                    <button type="reset" class="btn btn-secondary" onclick="closeForm()">Cancel</button>
                </div>
            </form>
            <!--end::Form-->
        @endif
    @else
        <div class="card-body">
            {{ $slot }}
        </div>
        <div class="card-footer">
            {{ $footer ?? "" }}
        </div>
    @endif
</div>
{{-- End Card Form --}}