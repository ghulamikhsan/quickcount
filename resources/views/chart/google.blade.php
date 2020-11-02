{{-- Extends layout --}}
@extends('layout.default')

{{-- Content --}}
@section('content')
        </div>        
        <div class="container">
            {{-- s<div class="card card-custom bg-white card-stretch gutter-b"> --}}
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
    var data = google.visualization.arrayToDataTable(analytics);
    var options = {
     title : ''
    };
    var chart = new google.visualization.PieChart(document.getElementById('pie_chart'));
    chart.draw(data, options);
   }
    </script>
  </script>
//   chart end
@endsection
