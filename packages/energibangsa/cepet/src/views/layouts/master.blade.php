{{-- Extends layout --}}
@extends('layout.default')

{{-- Content --}}
@section('content')
    <div class="row">
        <div class="col">
            {{-- Start Loading --}}
            <div class="card card-custom gutter-b card-loading" style="display: none">
                <div class="card-body">
                    <div class="spinner spinner-center spinner-lg"></div>
                </div>
            </div>
            {{-- End Loading --}}

            {{-- Start Card Form--}}
           @component('views::components.cards.card', [
               'title' => $title,
               'forms' => $forms,
               'header' => 0,
               'type' => 'form',
               'attr' => "style='display:none'",
               'class' => "card-form",
               'toolbar' => '<a href="javascript:closeForm()" class="btn btn-icon btn-sm btn-hover-light-primary" data-card-tool="remove" data-toggle="tooltip" data-placement="top" title="Close">
                        <i class="ki ki-close icon-nm"></i>
                    </a>'
           ])@endcomponent
           {{-- End Card Form --}}

           {{-- Start Card Table --}}
            @component('views::components.cards.card', [
                'header' => 1,
                'title' => "Data $title"
            ])
                @slot('toolbar')
                        @if (!Request::input('trash'))
                            @if ($trashBtn)
                                {{-- <div class="dropdown dropdown-inline mr-2"> --}}
                                    <a href="{{url()->current()}}?trash=true" class="btn btn-light-danger font-weight-bolder mr-2">
                                        <i class="fa fa-trash"></i>
                                        Sampah
                                    </a>
                                {{-- </div> --}}
                            @endif
                            <!--begin::Button-->
                            @if ($actionBtn)
                                <button onclick="openForm()" class="btn btn-primary font-weight-bolder btn-add">
                                    <span class="svg-icon svg-icon-md">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"/>
                                                <circle fill="#000000" cx="9" cy="15" r="6"/>
                                                <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3"/>
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>Tambah Baru
                                </button>
                            @endif
                            <!--end::Button-->
                        @else
                            <button onclick="javascript:history.back()" class="btn btn-light-warning font-weight-bolder">
                                <i class="fa fa-backward"></i>
                                Kembali
                            </button>
                        @endif
                @endslot

                <!--begin::Search Form-->
                <div class="mt-2 mb-5 mt-lg-5 mb-lg-10">
                    <div class="row align-items-center">
                        <div class="col-lg-9 col-xl-8">
                            <div class="row align-items-center">
                                <div class="col-md-4 my-2 my-md-0">
                                    <div class="input-icon">
                                        <input type="text" class="form-control" placeholder="Search..." id="kt_datatable_search_query"/>
                                        <span><i class="flaticon2-search-1 text-muted"></i></span>
                                    </div>
                                </div>
            
                                <div class="col-md-4 my-2 my-md-0">
                                    <div class="d-flex align-items-center">
                                        <a href="#" class="btn btn-light-primary px-6 font-weight-bold btn-search">
                                            Search
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Search Form-->
            
                {{-- Start Datatable --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="kt_datatable">
                        <thead>
                            @foreach ($cols as $col)
                                <th>{{$col['label']}}</th>
                            @endforeach
                        </thead>
                        <tfoot>
                            @foreach ($cols as $col)
                                <th>{{$col['label']}}</th>
                            @endforeach
                        </tfoot>
                    </table>
                </div>
                {{-- End Datatable --}}
            @endcomponent
            {{-- End Card Table --}}
        </div>
    </div>

@endsection

{{-- Styles Section --}}
@section('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endsection


{{-- Scripts Section --}}
@section('scripts')
    {{-- vendors --}}
    <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/custom/jqueryform/jquery.form.js') }}" type="text/javascript"></script>
    {{-- @include('views::assets/js/jqueryform') --}}

    {{-- page scripts --}}
    <script>
        "use strict";
        var KTDatatablesBasicBasic = function () {

            var initTable = function () {
                var table = $('#kt_datatable');

                // begin first table
                table.DataTable({
                    responsive: false,
                    order: [],

                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    ajax: "{{url()->current()}}/data{!! Request::input('trash') ? '?trash=true' : '' !!}",

                    // DOM Layout settings
                    dom: `<'row'<'col-sm-12'tr>>
                    <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                    pageLength: 10,
                    columns: [
                        @foreach($cols as $col)
                        {
                            name: "{{ $col['name'] }}",
                            data: "{{ $col['data'] }}",
                            @if(isset($col['additional']))
                                @foreach($col['additional'] as $key => $val)
                                    {{$key}} : {{$val}},
                                @endforeach
                            @endif
                        },
                        @endforeach
                    ]
                });
            };

            return {

                //main function to initiate the module
                init: function () {
                    initTable();
                }
            };
        }();

        jQuery(document).ready(function () {
            KTDatatablesBasicBasic.init();
        });
    </script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
    <script>
        var id;
        var title = '{{ $title }}';
        var myForm = $('#my-form');
        var editForm = false;

        // UI
        var myToast = function (type, title, msg = null) {
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            switch (type) {
                case 'success':
                    toastr.success(msg, title);
                    break;
                case 'info':
                    toastr.info(msg, title);
                    break;
                case 'warning':
                    toastr.warning(msg, title);
                    break;
                default:
                    toastr.error(msg, title);
                    break;
            }
        }

        function openForm(id = null) {
            this.clearErrorForm();
            $("#card-loading").show();
            $('.card-form-title').text( id == null ? 'Tambah '+title : 'Edit '+title)
            myForm.trigger('reset');
            $('.edit-disabled').attr('disabled', false);
            $(".btn-add").prop('disabled', false);
            window.scrollTo(0, 0);

            editForm = (id === null) ? false : true;
            this.id = id;

            if(editForm) {
                // script edit page
                {!!$scriptEditPage ?? '' !!}
                $('.edit-disabled').attr('disabled', true);
                setTimeout(function() {
                    $.ajax({
                        type : 'get',
                        url  : "{{url()->current()}}/data",
                        data : {id:id, _token: "{{ csrf_token() }}"},
                        dataType : 'json',
                        success : function(res) {
                            if(res.status==1){
                                var data = res.data;
                                @foreach($forms as $form)
                                    @if($form['input'] == "select")
                                        @if(isset($form['parent']))
                                            getParent("{{$form['parent']['id_parent']}}", 
                                                "{{$form['parent']['table']}}", 
                                                "{{$form['parent']['id_child']}}", 
                                                "{{$form['parent']['name_child']}}",
                                                "{{$form['name']}}",
                                                data.{{$form['name']}},
                                                true,
                                            );
                                        @else
                                            $("#input-{{$form['name']}}").val(data.{{$form['name']}}).change();
                                        @endif
                                    @elseif($form['input'] == 'file')
                                        $('#custom-file-label-{{$form['name']}}').text(data.{{$form['name']}});
                                    @else
                                        $("#input-{{$form['name']}}").val(data.{{$form['name']}})
                                    @endif
                                @endforeach
                                $('#card-loading').hide();
                                $('.card-form').fadeIn();
                            }else if(res.status==0){
                                $('#card-loading').hide();
                                myToast('error', 'Error', res.msg)
                                // showErrorMsg($('.table-responsive'), 'danger', res.message);
                            }
                        },
                        error : function(err){
                            // $("#card-loading").hide();
                            // showErrorMsg($('.table-responsive'), 'danger', 'Error: ' + err.statusText);
                        }
                    });
                }, 500);
            } else {
                // script add page
                {!!$scriptAddPage!!}
                $(".btn-add").prop('disabled', true);
                $('#card-loading').hide();
                $('.card-form').fadeIn();
            }
        }

        function closeForm() {
            $('.card-form').fadeOut();
            // script add page
            setTimeout(function() {
                $(".btn-add").prop('disabled', false);
            }, 500);
        }

        @foreach($forms as $form)
            @if(isset($form['parent']))
                $('#input-{{$form['parent']['id_parent']}}').change(function() {
                    getParent("{{$form['parent']['id_parent']}}", 
                                "{{$form['parent']['table']}}", 
                                "{{$form['parent']['id_child']}}", 
                                "{{$form['parent']['name_child']}}",
                                "{{$form['name']}}",
                                null,
                                true,
                            );
                });
            @endif
        @endforeach

        function getParent(id_parent, table, id_child, name, id_el, id_select = null, empty = null) {
            $('#'+id_el).html("<option value=''>Loading . . .</option>");
            $.get('{{url('master')}}/child', {
                'table': table,
                'id_child': id_child,
                'id': $('#'+id_parent).val(),
            }, function(data) {
                option = (empty === null) ? '' : "<option value=''>-- Tidak Ada --</option>";
                $.each(data, function(i, v) {
                    option += "<option value="+v[id_el]+">"+v[name]+"</option>";
                });

                $('#'+id_el).html(option);
                if(id_select !== null) {
                    $('#'+id_el).val(id_select);
                }

            });
        }

        $('.btn-submit').click(function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $(this).closest('form');

            btn.addClass('spinner spinner-right spinner-light').attr('disabled', true);
            
            additionalData = {
                _token: "{{ csrf_token() }}"
            };
            if(editForm == true) additionalData.id = id;

            form.ajaxSubmit({
                url : "{{url()->current()}}/"+ (editForm ? "edit" : "add"),
                data: additionalData,
                type: 'POST',
                success: function(response, status, xhr, $form) {
                    if(response.status == 1){
                        setTimeout(function() {
                            form.trigger('reset');

                            btn.removeClass('spinner spinner-right spinner-light').attr('disabled', false);
                            closeForm();
                            myToast('success', 'Data Berhasil di Update');
                            $('#kt_datatable').DataTable().ajax.reload();
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

        $('.btn-search').click(function() {
            var search = $('#kt_datatable_search_query').val();
            var table = $('#kt_datatable').DataTable();

            table.search(search).draw() ;
        })

        function doDelete(id) {
            Swal.fire({
                    title: "Apakah anda yakin?",
                    text: "Menghapus data ini?",
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
                                    $('#kt_datatable').DataTable().ajax.reload();
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

        function doPermanentDelete(id) {
        Swal.fire({
                title: "Apakah anda yakin?",
                text: "Menghapus data ini?",
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
                        url  : "{{url()->current()}}/permanent-delete/"+id,
                        data : {id:id, _token: "{{ csrf_token() }}"},
                        dataType : 'json',
                        success : function(res) {
                            Swal.close();
                            if(res.status==1){
                                myToast('success', 'Sukses', 'Berhasil dihapus');
                                $('#kt_datatable').DataTable().ajax.reload();
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

    function doRestore(id) {
        Swal.fire({
                title: "Apakah anda yakin?",
                text: "Mengembalikan data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, kembalikan!"
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
                        url  : "{{url()->current()}}/restore/"+id,
                        data : {id:id, _token: "{{ csrf_token() }}"},
                        dataType : 'json',
                        success : function(res) {
                            Swal.close();
                            if(res.status==1){
                                myToast('success', 'Sukses', 'Berhasil dikembalikan');
                                $('#kt_datatable').DataTable().ajax.reload();
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
