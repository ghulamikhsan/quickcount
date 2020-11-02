<center><div id="loading" class="m-loader m-loader--brand" style="width: 30px; display: inline-block; margin-bottom:20px"></div></center>

@if ($actionBtn)
{{-- start form page --}}
<div class="m-portlet" id="page-form" style="display:none">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <h3 class="m-portlet__head-text">
                   <span class="page-title">Tambah/Edit</span> {{$title}}
                </h3>
            </div>
        </div>
        <div class="m-portlet__head-tools">
            <ul class="m-portlet__nav">
                <li class="m-portlet__nav-item">
                    <a style="cursor:pointer" onclick="pageForm(false)" class="m-portlet__nav-link m-portlet__nav-link--icon">
                        <i class="la la-close"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="m-portlet__body">
        <form id="form-input" class="m-form m-form--fit m-form--label-align-right">
            @foreach ($forms as $form)
            @component('components.form', $form)@endcomponent
            @endforeach

            <div class="form-group m-form__group text-right">
                <a style="cursor:pointer" id="do-cancel" class="btn btn-secondary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btn-sm input_do-cancel" onclick="pageForm(false)">
                    <span>
                        <i class="la la-close"></i>
                        <span>
                            Cancel
                        </span>
                    </span>
                </a>
                <button id="do-simpan" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btn-sm input_do-simpan">
                    <span>
                        <i class="la la-save"></i>
                        <span>
                            Simpan
                        </span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
{{-- end form page --}}
@endif

<!--begin::Modal-->
@if ($filters)
<div class="modal fade" id="modal-filter" tabindex="-1" role="dialog" aria-labelledby="modalFilterLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFilterLabel">
                    Filter {{$title}}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_filter">
                    @foreach ($filters as $filter)
                        @component('components.filter', $filter)
                            
                        @endcomponent
                    @endforeach
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary do-filter">
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>
@endif
<!--end::Modal-->

{{-- page list --}}
<div class="m-portlet" id="page-list">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <h3 class="m-portlet__head-text">
                    {{$title}}
                </h3>
            </div>
        </div>
    </div>
    <div class="m-portlet__body">
        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
            <div class="row align-items-center">
                {{-- <div class="col-xl-8 order-2 order-xl-1">
                    <div class="form-group m-form__group row align-items-center">
                        <div class="col-md-4">
                            
                        </div>
                    </div>
                </div> --}}
                <div class="col-xl-12 m--align-right">
                    @if ($backBtn)                        
                    <a href="javascript:window.history.back()" id="button-back" class="btn btn-secondary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill m-1">
                        <span>
                            <i class="la la-arrow-left"></i>
                            <span>
                                Kembali
                            </span>
                        </span>
                    </a>
                    @endif
                    @if ($filters)                    
                    <button id="button-filter" class="btn m-btn btn-outline-primary m-btn--custom m-btn--icon m-btn--air m-btn--pill m-1" data-toggle="modal" data-target="#modal-filter">
                        <span>
                            <i class="la la-filter"></i>
                            <span>
                                Saring
                            </span>
                        </span>
                    </button>
                    @endif
                    @if ($trashBtn)                        
                    <a href="{{url()->current()."?trash=true"}}" id="button-trash" class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill m-1">
                        <span>
                            <i class="la la-trash"></i>
                            <span>
                                Sampah
                            </span>
                        </span>
                    </a>
                    @endif
                    @if ($addBtn)                        
                    <button id="button-tambah" onclick="pageForm(true)" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill m-1">
                        <span>
                            <i class="la la-plus"></i>
                            <span>
                                Tambah
                            </span>
                        </span>
                    </button>
                    @endif
                    <div class="m-separator m-separator--dashed d-xl-none"></div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="datatable table-hover table-striped" id="html_table" width="100%">
                <thead>
                    <tr>
                        @foreach ($cols as $col)
                            <th>{{$col['label']}}</th>
                        @endforeach
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
    var current_url = '{{ url()->current() }}';
    var base_url = '{{url('/')}}'
    $(document).ready(function(){
        $("#loading").hide();
        $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url()->current().($trash ? '?trash=true' : '') }}',
                type: 'get',
                @if($filters)
                    data: function(data) {
                        @foreach($filters as $filter)
                            data.filter_{{$filter['name']}} = $('#filter_{{$filter['name']}}').val();
                            data.diff_{{$filter['name']}} = $('#diff_{{$filter['name']}}').val();
                        @endforeach
                    }
                @endif
            },
            columns: [
                @foreach($cols as $col)
                {
                    name: "{{$col['name']}}",
                    data: "{{$col['name']}}",
                    @if(isset($col['additional']))
                        @foreach($col['additional'] as $key => $val)
                            {{$key}} : {{$val}},
                        @endforeach
                    @endif
                },
                @endforeach
            ]
        });
    })
    
    // UI
    var editForm = false;
    var id;
    
    function pageForm(show, id = null){
        window.scrollTo(0, 0);
        {!!$scriptClosePage!!}
        $('.edit-disabled').attr('disabled', false);
        $("#page-form").hide();
        $("#button-tambah").prop('disabled', false);
        $("#form-input").trigger('reset');

        $('.page-title').text( id == null ? 'Tambah' : 'Edit')

        if(show){
            editForm = (id === null) ? false : true;
            this.id = id;

            $("#loading").show();
            if(editForm) {
                // script edit page
                {!!$scriptEditPage!!}
                $('.edit-disabled').attr('disabled', true);
                setTimeout(function() {
                    $.ajax({
                        type : 'get',
                        url  : "{{url()->current()}}",
                        data : {id:id, _token: "{{ csrf_token() }}"},
                        dataType : 'json',
                        success : function(res) {
                            $("#loading").hide();
                            if(res.api_status==1){
                                var data = res.data;
                                @foreach($forms as $form)
                                    @if($form['type'] == "select")
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
                                            $("#{{$form['name']}}").val(data.{{$form['name']}}).change();
                                        @endif
                                    @else
                                        $("#{{$form['name']}}").val(data.{{$form['name']}})
                                    @endif
                                @endforeach
                                $("#page-form").show();
                                $("#button-tambah").prop('disabled', false);
                            }else if(res.api_status==0){
                                showErrorMsg($('.table-responsive'), 'danger', res.api_message);
                            }
                        },
                        error : function(err){
                            $("#loading").hide();
                            showErrorMsg($('.table-responsive'), 'danger', 'Error: ' + err.statusText);
                        }
                    });
                }, 500);
            } else {
                // script add page
                {!!$scriptAddPage!!}
                setTimeout(function() {
                    $("#page-form").show();
                    $("#button-tambah").prop('disabled', true);
                    $("#loading").hide();
                }, 500);
            }
        }
    }

    $('.do-filter').click(function(e) {
        $(this).closest('.modal').modal('hide');
        $('.datatable').DataTable().ajax.reload();
    })

    $('#do-simpan').click(function(e) {
        e.preventDefault();
        var btn = $(this);
        var form = $(this).closest('form');

        form.validate({
            rules: {
                @foreach($forms as $form)
                {{$form['name']}}: {!!$form['rules']!!},
                @endforeach
            }
        });
        if (!form.valid()) {
            return;
        }

        btn.addClass('m-loader m-loader--right m-loader--light').attr('disabled', true);
        
        additionalData = {
            _token: "{{ csrf_token() }}"
        };
        if(editForm == true) additionalData.id = id;

        form.ajaxSubmit({
            url : "{{url()->current()}}/"+ (editForm ? "edit" : "add"),
            data: additionalData,
            type: 'POST',
            success: function(response, status, xhr, $form) {
                if(response.api_status == 1){
                    showErrorMsg(form, 'success', response.api_message);
                    setTimeout(function() {
                        // location.reload();
                        form.trigger('reset');
                        btn.removeClass('m-loader m-loader--right m-loader--light').attr('disabled', false);
                        pageForm(false);
                        $("#button-tambah").prop('disabled', false);
                        $('.alert-dismissible').remove();
                        $('.datatable').DataTable().ajax.reload();
                    }, 1000);
                }else{
                    btn.removeClass('m-loader m-loader--right m-loader--light').attr('disabled', false);
                    showErrorMsg(form, 'danger', response.api_message);
                }
                
            },
            error: function(err){
                btn.removeClass('m-loader m-loader--right m-loader--light').attr('disabled', false);
                showErrorMsg(form, 'danger', 'Error: ' + err.statusText);
            },
        });
    });

    function doDelete(id){
        swal({
            title: "Apakah Anda yakin?",
            text: "Menghapus data ini?",
            type: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#39BF4D",
            confirmButtonText: "Ya, hapus!"
        })
        .then((result) => {
            if (result.value) {
                swal({
                    title: 'Wait',
                    html: '<div class="m-loader m-loader--lg m-loader--brand" style="width: 30px; display: inline-block;"></div>',
                    showConfirmButton: false,
                    customClass: 'sweetalert-xs',
                    allowOutsideClick: false,
                });
                $.ajax({
                    type : 'POST',
                    url  : "{{url()->current()}}/delete",
                    data : {id:id, _token: "{{ csrf_token() }}"},
                    dataType : 'json',
                    success : function(res) {
                        swal.close();
                        if(res.api_status==1){
                            showErrorMsg($('.table-responsive'), 'success', res.api_message);
                            setTimeout(function() {
                                // location.reload();
                                $('.alert-dismissible').remove();
                                $('.datatable').DataTable().ajax.reload();
                            }, 1000);
                        }else if(res.api_status==0){
                            showErrorMsg($('.table-responsive'), 'danger', res.api_message);
                        }
                    },
                    error : function(err){
                        swal.close();
                        showErrorMsg($('.datatable'), 'danger', 'Error: ' + err.statusText);
                    }
                });
            }
        });
    }

    function doPermanentDelete(id){
        swal({
            title: "Apakah Anda yakin?",
            text: "Menghapus permanen data ini?",
            type: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#39BF4D",
            confirmButtonText: "Ya, hapus!"
        })
        .then((result) => {
            if (result.value) {
                swal({
                    title: 'Wait',
                    html: '<div class="m-loader m-loader--lg m-loader--brand" style="width: 30px; display: inline-block;"></div>',
                    showConfirmButton: false,
                    customClass: 'sweetalert-xs',
                    allowOutsideClick: false,
                });
                $.ajax({
                    type : 'POST',
                    url  : "{{url()->current()}}/permanent-delete",
                    data : {id:id, _token: "{{ csrf_token() }}"},
                    dataType : 'json',
                    success : function(res) {
                        swal.close();
                        if(res.api_status==1){
                            showErrorMsg($('.table-responsive'), 'success', res.api_message);
                            setTimeout(function() {
                                // location.reload();
                                $('.alert-dismissible').remove();
                                $('.datatable').DataTable().ajax.reload();
                            }, 1000);
                        }else if(res.api_status==0){
                            showErrorMsg($('.table-responsive'), 'danger', res.api_message);
                        }
                    },
                    error : function(err){
                        swal.close();
                        showErrorMsg($('.datatable'), 'danger', 'Error: ' + err.statusText);
                    }
                });
            }
        });
    }

    function doRestore(id){
        swal({
            title: "Apakah Anda yakin?",
            text: "Mengembalikan data ini?",
            type: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#39BF4D",
            confirmButtonText: "Ya, kembalikan!"
        })
        .then((result) => {
            if (result.value) {
                swal({
                    title: 'Wait',
                    html: '<div class="m-loader m-loader--lg m-loader--brand" style="width: 30px; display: inline-block;"></div>',
                    showConfirmButton: false,
                    customClass: 'sweetalert-xs',
                    allowOutsideClick: false,
                });
                $.ajax({
                    type : 'POST',
                    url  : "{{url()->current()}}/restore",
                    data : {id:id, _token: "{{ csrf_token() }}"},
                    dataType : 'json',
                    success : function(res) {
                        swal.close();
                        if(res.api_status==1){
                            showErrorMsg($('.table-responsive'), 'success', res.api_message);
                            setTimeout(function() {
                                // location.reload();
                                $('.alert-dismissible').remove();
                                $('.datatable').DataTable().ajax.reload();
                            }, 1000);
                        }else if(res.api_status==0){
                            showErrorMsg($('.table-responsive'), 'danger', res.api_message);
                        }
                    },
                    error : function(err){
                        swal.close();
                        showErrorMsg($('.datatable'), 'danger', 'Error: ' + err.statusText);
                    }
                });
            }
        });
    }

    @foreach($forms as $form)
        @if(isset($form['parent']))
            $('#{{$form['parent']['id_parent']}}').change(function() {
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
</script>