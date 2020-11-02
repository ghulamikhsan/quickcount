{{-- Extends layout --}}
@extends('layout.default')

{{-- Content --}}
@section('content')
    <h1>Dashboard</h1>
    <div class="row">
        <div class="col-sm-4 col-xs-12">
            <div class="card card-custom bg-white card-stretch gutter-b">
                <!--begin::Body-->
                <div class="card-body">
                    <span class="svg-icon svg-icon-2x svg-icon-success">
                        {{ Metronic::getSVG("media/svg/icons/Communication/Group.svg", "svg-icon-xl svg-icon-primary") }}
                        <!--end::Svg Icon-->
                    </span>
                    <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">{{$users}} Pengguna</span>
                    <span class="font-weight-bold text-muted font-size-sm">Total Pengguna</span>
                </div>
                <!--end::Body-->
            </div>
        </div>
        <div class="col-sm-4 col-xs-12">
            <div class="card card-custom bg-white card-stretch gutter-b">
                <!--begin::Body-->
                <div class="card-body">
                    <span class="svg-icon svg-icon-2x svg-icon-danger">
                        {{ Metronic::getSVG("media/svg/icons/General/User.svg", "svg-icon-xl svg-icon-primary") }}
                        <!--end::Svg Icon-->
                    </span>
                    <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">{{$polings}} TPS</span>
                    <span class="font-weight-bold text-muted font-size-sm">Total TPS</span>
                </div>
                <!--end::Body-->
            </div>
        </div>
        <div class="col-sm-4 col-xs-12">
            <div class="card card-custom bg-white card-stretch gutter-b">
                <!--begin::Body-->
                <div class="card-body">
                    <span class="svg-icon svg-icon-2x svg-icon-secondary">
                        {{ Metronic::getSVG("media/svg/icons/Home/Box.svg", "svg-icon-xl svg-icon-primary") }}
                        <!--end::Svg Icon-->
                    </span>
                    
                    <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">{{$details}} Suara</span>
                    <span class="font-weight-bold text-muted font-size-sm">Total Suara</span>
                </div>
                <!--end::Body-->
            </div>
        </div>        
        <div class="container">
            <div class="card card-custom bg-white card-stretch gutter-b">
                <!--begin::Body-->
                <div class="card-body">                
                    <span class="card-title font-weight-bolder text-center text-dark-75 font-size-h2 mb-0 mt-6 d-block">Tabel Perolehan Suara Tiap TPS Pilkada 2020</span>
                    <table class="table table-bordered data-table">
                        <thead>
                            <tr>
                                {{-- <th>No</th> --}}
                                <th>Nama</th>
                                <th>Nama TPS</th>
                                <th>Jumlah Suara</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!--end::Body-->
            </div>
        </div>        
        <div class="container">
            <div class="card card-custom bg-white card-stretch gutter-b">
                <!--begin::Body-->
                <div class="card-body" style="width: auto; height: auto;">
                    <span class="card-title font-weight-bolder text-center text-dark-75 font-size-h2 mb-0 mt-6 d-block">Perolehan Suara Pilkada 2020</span>
                    <div id="pie_chart"></div>
                </div>
            </div> 
        </div>
    

@endsection

{{-- Scripts Section --}}
@section('scripts')
    
    {{-- chart start --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  
  {{-- <script type="text/javascript"> --}}
    <script type="text/javascript">
   var analytics = <?php echo $nama; ?>

   google.charts.load('current', {'packages':['corechart']});

   google.charts.setOnLoadCallback(drawChart);

   function drawChart()
   {
    var data = google.visualization.arrayToDataTable([
        ['name', 'count'],
            @php
                foreach($charts as $d) {
                    echo "['".$d->name."', ".$d->count."],";
                }
            @endphp
    ]);
    var options = {
     title : ''
    };
    var chart = new google.visualization.PieChart(document.getElementById('pie_chart'));
    chart.draw(data, options);
   }
    </script>
  </script>
//   chart end

//data table start 
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
    $(function () {
      
      var table = $('.data-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('table.index') }}",
          columns: [
            //   {data: 'DT_RowIndex', name: 'DT_RowIndex'},
              {data: 'name', name: 'calons.name'},
              {data: 'tps_name', name: 'counts.tps_name'},
              {data: 'count', name: 'counts.count'},
          ]
      });
      
    });

  </script>

@endsection
