@extends('layout.default')

@section('title')
    Chart    
@endsection

@section('content')
    <h1>Grafik Perolehan Suara</h1>
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <div class="card card-custom card-stretch gutter-b">
                <!--begin::Body-->
                <div class="card-body d-flex align-items-center py-0 mt-8">
                    <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                        <div id="suaraChart"></div>
                    </div>
                    {{-- <img src="{{url('media/svg/avatars/014-girl-7.svg')}}" alt="" class="align-self-end h-100px" /> --}}
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script> 
    Highcharts.chart('suaraChart', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Perolehan suara Pilkada 2020'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    accessibility: {
        point: {
            valueSuffix: '%'
        }
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: false
            },
            showInLegend: true
        }
    },
    series: [{
        name: 'Suara',
        colorByPoint: true,
        data: {!! json_encode($poling_suara) !!},
        // name: {!! json_encode($poling_detail) !!}
        // data: [{
        //     name: 'Chrome',
        //     y: 61.41,
        //     sliced: true,
        //     selected: true
        // }, {
        //     name: 'Internet Explorer',
        //     y: 11.84
        // }, {
        //     name: 'Firefox',
        //     y: 10.85
        // }, {
        //     name: 'Edge',
        //     y: 4.67
        // }, {
        //     name: 'Safari',
        //     y: 4.18
        // }, {
        //     name: 'Other',
        //     y: 7.05
        // }]
    }]
});
</script>
@endsection