{{-- Extends layout --}}
@extends('layout.default')

{{-- Content --}}
@section('content')
    {{-- Start Card --}}
    @component('views::components.cards.card', [
        'title' => $title,
        'header' => 0,
        'card_toolbar' => '<button class="btn btn-warning" onclick="window.history.back();">Kembali</button>'
    ])
        <form method="POST" action="{{ url()->current() }}" id="add-form">
            @csrf
            @component("views::components.forms.text", [
                'label' => 'Nama',
                'name' => "name",
                'attr' => 'required',
            ])@endcomponent
            @component("views::components.forms.text", [
                'label' => 'Value',
                'name' => "value",
                'attr' => 'required',
            ])@endcomponent
        </form>
            @slot('footer')
                @component('views::components.forms.button', [
                    'type' => 'submit' ,
                    'class' => 'btn-primary btn-submit',
                    'name' => 'Simpan',
                    'attr' => 'form="add-form"'
                ])@endcomponent
            @endslot
    @endcomponent
    {{-- End Card --}}

@endsection
