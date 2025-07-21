@extends('layouts.app')
@section('page-title')
    {{ __('Estimation') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('estimation.index') }}">{{ __('Estimation') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">{{ estimationPrefix() . $estimation->estimation_id }}</a>
        </li>
    </ul>
@endsection
@php
    $admin_logo = getSettingsValByName('company_logo');
    $settings = settings();
@endphp
@push('script-page')
    <script>
        $(document).on('click', '.print', function() {
            $('.action').addClass('d-none');
            var printContents = document.getElementById('invoice-print').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            $('.action').removeClass('d-none');
        });

        $(document).on('click', '.estimationStatusChange', function() {
            var estimationStatus = this.value;
            var estimationUrl = $(this).data('url');
            $.ajax({
                url: estimationUrl + '?status=' + estimationStatus,
                type: 'GET',
                cache: false,
                success: function(data) {
                    location.reload();
                },
            });
        });
    </script>
@endpush
@section('content')

    <div class="row" id="invoice-print">
        <div class="col-sm-12">
            <div class="d-print-none card mb-3">
                <div class="card-body p-3">
                    <ul class="list-inline ms-auto mb-0 d-flex justify-content-end flex-wrap">

                        <li class="list-inline-item align-bottom me-2">
                            <a href="javascript:void(0);" class="avtar avtar-s btn-link-secondary print"
                                data-bs-toggle="tooltip" data-bs-original-title="{{ __('Download') }}">
                                <i class="ph-duotone ph-printer f-22"></i>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
            <div class="card">

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 ">
                            <div class="row align-items-center g-3">
                                <div class="col-sm-6 ">
                                    <div class="d-flex align-items-center mb-2 navbar-brand img-fluid invoice-logo">
                                        <img src="{{ asset(Storage::url('upload/logo/')) . '/' . (isset($admin_logo) && !empty($admin_logo) ? $admin_logo : 'logo.png') }}"
                                            class="img-fluid brand-logo" alt="images" />
                                    </div>
                                    <p class="mb-0">{{ estimationPrefix() . $estimation->estimation_id }}</p>
                                </div>
                                <div class="col-sm-6 text-sm-end">

                                    <h6>
                                        {{ __('Asset') }} :
                                        <span
                                            class="text-muted f-w-400">{{ !empty($estimation->assets) ? $estimation->assets->name : '-' }}</span>
                                    </h6>
                                    <h6>
                                        {{ __('Due Date') }} :
                                        <span class="text-muted f-w-400">{{ dateFormat($estimation->due_date) }}</span>
                                    </h6>
                                    <h6>
                                        {{ __('Status') }} :
                                        <span class="text-muted f-w-400">
                                            @if ($estimation->status == 'pending')
                                                <span
                                                    class="badge text-bg-warning">{{ \App\Models\Estimation::$status[$estimation->status] }}</span>
                                            @elseif($estimation->status == 'on_hold')
                                                <span
                                                    class="badge text-bg-primary">{{ \App\Models\Estimation::$status[$estimation->status] }}</span>
                                            @elseif($estimation->status == 'approved' || $estimation->status == 'completed')
                                                <span
                                                    class="badge text-bg-success">{{ \App\Models\Estimation::$status[$estimation->status] }}</span>
                                            @else
                                                <span
                                                    class="badge text-bg-danger">{{ \App\Models\Estimation::$status[$estimation->status] }}</span>
                                            @endif
                                        </span>
                                    </h6>

                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="border rounded p-3">
                                <h6 class="mb-0">{{ __('From') }} :</h6>
                                <h5>{{ $settings['company_name'] }}</h5>
                                <p class="mb-0">{{ $settings['company_phone'] }}</p>
                                <p class="mb-0">{{ $settings['company_email'] }}</p>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md {{ !empty($estimation->clients->clients->billing_address) ? '4' : '6' }}">
                            <div class="border rounded p-3">
                                <h6 class="mb-0">{{ __('To') }} :</h6>
                                <h5>{{ !empty($invoice) && !empty($invoice->Customer) ? $invoice->Customer->name : '' }}
                                </h5>
                                <p class="mb-0">
                                    {{ !empty($estimation->clients) ? $estimation->clients->name : '' }}
                                    ({{ !empty($estimation->clients) && !empty($estimation->clients->clients) ? $estimation->clients->clients->company : '' }})
                                </p>

                                <p class="mb-0">
                                    {{ !empty($estimation->clients) ? $estimation->clients->phone_number : '' }}
                                </p>

                                <p class="mb-0">
                                <h6 class="mt-10 text-primary">{{ __('Service Address') }}:</h6>

                                {{ !empty($estimation->clients) && !empty($estimation->clients->clients) ? $estimation->clients->clients->service_address : '' }}
                                @if (
                                    !empty($estimation->clients) &&
                                        !empty($estimation->clients->clients) &&
                                        !empty($estimation->clients->clients->service_city))
                                    <br> {{ $estimation->clients->clients->service_city }}
                                    , {{ $estimation->clients->clients->service_state }}
                                    , {{ $estimation->clients->clients->service_country }},
                                    {{ $estimation->clients->clients->service_zip_code }}
                                @endif
                                </p>
                            </div>
                        </div>

                        @if(!empty($estimation->clients->clients->billing_address))
                        <div class="col-sm-12 col-md-4">
                            <div class="border rounded p-3">
                                <h6 class="mt-10 text-primary">{{ __('Billing Address') }}:</h6>

                                {{ !empty($estimation->clients) && !empty($estimation->clients->clients) ? $estimation->clients->clients->billing_address : '' }}
                                @if (
                                    !empty($estimation->clients) &&
                                        !empty($estimation->clients->clients) &&
                                        !empty($estimation->clients->clients->billing_city))
                                    <br> {{ $estimation->clients->clients->billing_city }}
                                    , {{ $estimation->clients->clients->billing_state }}
                                    , {{ $estimation->clients->clients->billing_country }},
                                    {{ $estimation->clients->clients->billing_zip_code }}
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Service') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Description') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($estimation->services as $service)
                                            <tr>
                                                <td>{{ !empty($service->serviceParts) ? $service->serviceParts->title : '-' }}
                                                </td>
                                                <td>{{ $service->quantity }}
                                                    {{ !empty($service->serviceParts) ? $service->serviceParts->unit : '' }}
                                                </td>
                                                <td>{{ !empty($service->description) ? $service->description : '-' }}
                                                </td>
                                                <td>{{ priceFormat($service->amount) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-start">
                                <hr class="mb-2 mt-1 border-secondary border-opacity-50" />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Part') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Description') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($estimation->parts as $part)
                                            <tr>
                                                <td>
                                                    {{ !empty($part->serviceParts) ? $part->serviceParts->title : '-' }}
                                                </td>
                                                <td>{{ $part->quantity }}
                                                    {{ !empty($part->serviceParts) ? $part->serviceParts->unit : '' }}
                                                </td>
                                                <td>{{ !empty($part->description) ? $part->description : '-' }}</td>
                                                <td>{{ priceFormat($part->amount) }}</td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-start">
                                <hr class="mb-2 mt-1 border-secondary border-opacity-50" />
                            </div>
                        </div>

                        <div class="card-body p-3">
                            <div class="rounded p-3 bg-light-secondary">
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-end mb-0">
                                                <tbody>

                                                    <tr>
                                                        <td class="pe-0 pt-0">

                                                            <h4 class="text-primary m-r-10 ">{{ __('Grand Total') }} :</h4>
                                                        </td>
                                                        <td class="ps-0 pt-0">

                                                            <h4 class="text-primary">
                                                                {{ priceFormat($estimation->getEstimationSubTotalAmount()) }}
                                                            </h4>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 text-dark">
                                    @if (!empty($estimation->notes))
                                        {{ __('Notes') }} : <p>{{ $estimation->notes }}</p>
                                    @endif
                                </div>
                                @if (\Auth::user()->type == 'owner')
                                    <div class="row mb-2">
                                        @foreach ($status as $k => $val)
                                            <div class="col-md-3 col-xxl-2">
                                                <div class="card border p-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input estimationStatusChange"
                                                            type="radio" value="{{ $k }}"
                                                            {{ $estimation->status == $k ? 'checked' : '' }}
                                                            id="{{ $val }}"
                                                            data-url="{{ route('estimation.status', $estimation->id) }}"
                                                            name="status"></span>


                                                        <label class="form-check-label d-block" for="{{ $val }}">
                                                            <span class="h5 mb-0 d-block mt-1">{{ $val }}</span>

                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
