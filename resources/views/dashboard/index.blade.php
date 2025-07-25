@extends('layouts.app')
@section('page-title')
    {{ __('Dashboard') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page">{{ __('Dashboard') }}</li>
@endsection
@push('script-page')

<script>
    var options = {
        chart: {
            type: 'area',
            height: 450,
            toolbar: {
                show: false
            }
        },
        colors: ['#2ca58d'],
        dataLabels: {
            enabled: false
        },
        legend: {
            show: true,
            position: 'top'
        },
        markers: {
            size: 1,
            colors: ['#fff'],
            strokeColors: ['#2ca58d'],
            strokeWidth: 1,
            shape: 'circle',
            hover: {
                size: 4
            }
        },
        stroke: {
            width: 2,
            curve: 'smooth'
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                type: 'vertical',
                inverseColors: false,
                opacityFrom: 0.5,
                opacityTo: 0
            }
        },
        grid: {
            show: false
        },
        series: [{
            name: "{{ __('Income') }}",
            data: {!! json_encode(@$result['incomeByMonth']['income']) !!}
        }],
        xaxis: {
            categories: {!! json_encode(@$result['incomeByMonth']['label']) !!},
            tooltip: {
                enabled: false
            },
            labels: {
                hideOverlappingLabels: true
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            }
        }
    };

    var chart = new ApexCharts(document.querySelector('#incomeExpenseByMonth'), options);
    chart.render();
</script>




@endpush
@section('content')
    <div class="row">

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar bg-light-secondary">
                                <i class="ti ti-users f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-1">{{ __('Total Client') }}</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="mb-0">{{ $result['totalClient'] }}</h4>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar bg-light-warning">
                                <i class="ti ti-package f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-1">{{ __('Total WO Request') }}</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="mb-0">{{ $result['totalWORequest'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar bg-light-primary">
                                <i class="ti ti-history f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-1">{{ __('Total Workorder') }}</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="mb-0">{{ $result['totalWorkorder'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar bg-light-danger">
                                <i class="ti ti-credit-card f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-1">{{ __('Total Invoice') }}</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="mb-0">{{ $result['totalInvoice'] }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h5 class="mb-1">{{ __('Analysis Report') }}</h5>
                            <p class="text-muted mb-2">{{ __('Income Overview') }}</p>
                        </div>

                    </div>
                    <div id="incomeExpenseByMonth"></div>
                </div>
            </div>
        </div>

    </div>
@endsection
