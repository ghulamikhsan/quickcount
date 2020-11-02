{{-- Extends layout --}}
@extends('layout.default')

{{-- Content --}}
@section('content')
    {{-- Start Card --}}
    @component('views::components.cards.card', [
        'title' => $title,
        'header' => 0,
        'card_toolbar' => "<a href='".url()->current()."/add' class='btn btn-primary'>Tambah</a>"
    ])
        <form method="POST" action="{{ url()->current() }}/save" class="settings-form">
            @csrf
            @foreach ($forms as $form)
                @component("views::components.forms.text", [
                    'label' => $form->name . " <div class='btn btn-sm btn-danger' onclick='doDelete(".$form->id.")'>x</div> ",
                    'name' => "name[$form->id]",
                    'id' => "input_$form->id",
                    'value' => $form->value,
                ])@endcomponent
            @endforeach
            @slot('footer')
                @component('views::components.forms.button', [
                    'type' => 'submit' ,
                    'class' => 'btn-primary btn-submit',
                    'name' => 'Simpan'
                ])@endcomponent
            @endslot
        </form>
    @endcomponent
    {{-- End Card --}}

@endsection


{{-- Scripts Section --}}
@section('scripts')
    {{-- vendors --}}
    <script src="{{ asset('plugins/custom/jqueryform/jquery.form.js') }}" type="text/javascript"></script>

    {{-- page scripts --}}
    <script>
        function clearErrorForm() {
            $('.invalid-feedback').remove();
            $('.is-invalid').removeClass('is-invalid')
        }

        function formError(err) {
            this.clearErrorForm();
            $.each(err, function(i, v) {
                $('#input-'+i).addClass('is-invalid');
                $('#input-'+i).after('<div class="invalid-feedback">'+v[0]+'</div>');
            })
        }
        
        $('.btn-submit').click(function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('.settings-form');

            btn.addClass('spinner spinner-right spinner-light').attr('disabled', true);
            
            additionalData = {
                _token: "{{ csrf_token() }}"
            };

            form.ajaxSubmit({
                url : "{{ url()->current() }}/save",
                data: additionalData,
                type: 'POST',
                success: function(response, status, xhr, $form) {
                    if(response.status == 1){
                        setTimeout(function() {
                            btn.removeClass('spinner spinner-right spinner-light').attr('disabled', false);
                            myToast('success', 'Data Berhasil di Update');
                        }, 1000);
                    }else{
                        btn.removeClass('spinner spinner-right spinner-light').attr('disabled', false);
                        // showErrorMsg(form, 'danger', response.message);
                        myToast('error', 'Terjadi Kesalahan');
                        formError(response.errors);
                    }
                    
                },
                error: function(err){
                    btn.removeClass('spinner spinner-right spinner-light').attr('disabled', false);
                },
            });
        });

        function doDelete(id) {
            Swal.fire({
                    title: "Apakah anda yakin?",
                    text: "Menghapus setting ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, hapus!"
                }).then(function(result) {
                    if (result.value) {
                        Swal.fire({
                            title: 'Mohon Tunggu',
                            html: '<div class="spinner spinner-center mb-1" style="display: inline-block;"></div>',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                        });

                        $.ajax({
                            type : 'POST',
                            url  : "{{url()->current()}}/delete",
                            data : {id:id, _token: "{{ csrf_token() }}"},
                            dataType : 'json',
                            success : function(res) {
                                Swal.close();
                                if(res.status==1){
                                    myToast('success', 'Sukses', 'Berhasil dihapus');
                                    location.reload();
                                }else if(res.status==0){
                                    myToast('error', 'Gagal', res.message);
                                }
                            },
                            error : function(err){
                                Swal.close();
                                myToast('error', 'Error', err.statusText);
                            }
                        });
                    }
                });
        }
    </script>
@endsection
