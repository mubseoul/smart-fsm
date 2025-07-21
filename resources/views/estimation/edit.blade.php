@extends('layouts.app')
@section('page-title')
    {{ __('Estimation') }}
@endsection
@push('script-page')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        var serviceSelector = "body .services";
        if ($(serviceSelector + " .repeater").length) {
            var $serviceRepeater = $(serviceSelector + ' .repeater').repeater({
                initEmpty: true,
                defaultValues: {
                    'status': 1
                },
                show: function() {
                    $(".hidesearch").each(function() {
                        var basic_select = new Choices(this, {
                            searchEnabled: false,
                            removeItemButton: true,
                        });
                    });
                    $(this).slideDown();
                },
                hide: function(deleteEstimation) {
                    if (confirm('Are you sure you want to delete this record?')) {
                        var id = $(this).find('.id').val();

                        $.ajax({
                            url: '{{ route('estimation.service.part.destroy') }}',
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                'id': id
                            },
                            cache: false,
                            success: function(result) {
                                $(this).slideUp(deleteEstimation);
                                $(this).remove();
                            },
                        });
                    }
                },
                isFirstItemUndeletable: true
            });
            var serviceValue = $(serviceSelector + " .repeater").attr('data-value');
            if (typeof serviceValue != 'undefined' && serviceValue.length != 0) {
                serviceValue = JSON.parse(serviceValue);
                $serviceRepeater.setList(serviceValue);
            }
        }

        var partSelector = "body .parts";
        if ($(partSelector + " .repeater").length) {
            var $partRepeater = $(partSelector + ' .repeater').repeater({
                initEmpty: true,
                defaultValues: {
                    'status': 1
                },
                show: function() {
                    $(".hidesearch_part").each(function() {
                        var basic_select = new Choices(this, {
                            searchEnabled: false,
                            removeItemButton: true,
                        });
                    });

                    $(this).slideDown();
                },
                hide: function(deleteEstimation) {
                    if (confirm('Are you sure you want to delete this record?')) {
                        var id = $(this).find('.id').val();

                        $.ajax({
                            url: '{{ route('estimation.service.part.destroy') }}',
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                'id': id
                            },
                            cache: false,
                            success: function(result) {
                                $(this).slideUp(deleteEstimation);
                                $(this).remove();
                            },
                        });
                    }
                },
                isFirstItemUndeletable: true
            });
            var partValue = $(partSelector + " .repeater").attr('data-value');
            if (typeof partValue != 'undefined' && partValue.length != 0) {
                partValue = JSON.parse(partValue);
                $partRepeater.setList(partValue);
            }
        }
    </script>
    <script>
        $(document).on('change', '.service_part_id', function() {
            var currentElement = $(this).closest('tr');
            var service_part_id = $(this).val();
            var url = '{{ route('estimation.service.part') }}';
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: service_part_id,
                },
                contentType: false,
                type: 'GET',
                success: function(data) {
                    currentElement.find('.quantity').val(1);
                    currentElement.find('.amount').val(data.price);
                    currentElement.find('.unit').val(data.unit);
                    currentElement.find('.description').val(data.description);
                },
            });
        });
    </script>
@endpush
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('estimation.index') }}">{{ __('Estimation') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">{{ __('Edit') }}</a>
        </li>
    </ul>
@endsection

@section('content')
<div class="row">
    {{ Form::model($estimation, ['route' => ['estimation.update', \Illuminate\Support\Facades\Crypt::encrypt($estimation->id)], 'method' => 'PUT']) }}
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Estimate Details') }}</h4>
                </div>
                <div class="card-body">
                    <div class="info-group row">
                        <div class="form-group col-md-6">
                            <div class="form-group">
                                {{ Form::label('estimation_id', __('Estimation Number'), ['class' => 'form-label']) }}
                                <span class="text-danger">*</span>
                                <div class="input-group">
                                    <span class="input-group-text ">
                                        {{ estimationPrefix() }}
                                    </span>
                                    {{ Form::text('estimation_id', $estimation->estimation_id, ['class' => 'form-control', 'placeholder' => __('Enter Estimation Number')]) }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('title', __('Title'), ['class' => 'form-label']) }} <span
                                class="text-danger">*</span>
                            {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Enter title'), 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('client', __('Client'), ['class' => 'form-label']) }} <span
                                class="text-danger">*</span>
                            {!! Form::select('client', $clients, null, ['class' => 'form-control hidesearch', 'required' => 'required']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('asset', __('Asset'), ['class' => 'form-label']) }} <span
                                class="text-danger">*</span>
                            {!! Form::select('asset', $assets, null, ['class' => 'form-control hidesearch', 'required' => 'required']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('due_date', __('Due Date'), ['class' => 'form-label']) }} <span
                                class="text-danger">*</span>
                            {{ Form::date('due_date', null, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
                            {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 2]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="">
            <div class="col-lg-12 col-md-12">
                <div class="services">
                    <div class="card repeater" data-value='{!! json_encode($estimationServices) !!}'>
                        <div class="card-header">
                            <div class="row align-items-center g-2">
                                <div class="col">
                                    <h5>{{ __('Services') }}</h5>
                                </div>
                                <div class="col-auto">
                                    <a class="btn btn-secondary btn-sm" href="#" data-repeater-create=""> <i
                                            class="ti ti-circle-plus align-text-bottom"></i>{{ __('Add Service') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="display dataTable cell-border" data-repeater-list="services">
                                <thead>
                                    <tr>
                                        <th>{{ __('Service') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Unit') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody data-repeater-item>
                                    <tr>
                                        {{ Form::hidden('id', null, ['class' => 'form-control id']) }}
                                        <td width="30%">
                                            {{ Form::select('service_part_id', $services, null, ['class' => 'form-control hidesearch service_part_id']) }}
                                        </td>
                                        <td>
                                            {{ Form::number('quantity', null, ['class' => 'form-control quantity']) }}
                                        </td>
                                        <td>

                                            {{ Form::text('unit', null, ['class' => 'form-control unit', 'disabled']) }}

                                        </td>
                                        <td>
                                            {{ Form::number('amount', null, ['class' => 'form-control amount']) }}
                                        </td>
                                        <td>
                                            {{ Form::textarea('description', null, ['class' => 'form-control description', 'rows' => 1]) }}
                                        </td>
                                        <td>
                                            <a class="text-danger" data-repeater-delete href="#"> <i
                                                    data-feather="trash-2"></i></a>
                                        </td>
                                    </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
                <div class="parts">
                    <div class="card repeater" data-value='{!! json_encode($estimationParts) !!}'>
                        <div class="card-header">
                            <div class="row align-items-center g-2">
                                <div class="col">
                                    <h5>{{ __('Parts') }}</h5>
                                </div>
                                <div class="col-auto">
                                    <a class="btn btn-secondary btn-sm" href="#" data-repeater-create=""> <i
                                            class="ti ti-circle-plus align-text-bottom"></i>{{ __('Add Part') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="display dataTable cell-border" data-repeater-list="parts">
                                <thead>
                                    <tr>
                                        <th>{{ __('Part') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Unit') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody data-repeater-item>
                                    <tr>
                                        {{ Form::hidden('id', null, ['class' => 'form-control id']) }}
                                        <td width="30%">
                                            {{ Form::select('service_part_id', $parts, null, ['class' => 'form-control hidesearch_part service_part_id']) }}
                                        </td>
                                        <td>
                                            {{ Form::number('quantity', null, ['class' => 'form-control quantity']) }}
                                        </td>
                                        <td>

                                            {{ Form::text('unit', null, ['class' => 'form-control unit', 'disabled']) }}

                                        </td>
                                        <td>
                                            {{ Form::number('amount', null, ['class' => 'form-control amount']) }}
                                        </td>
                                        <td>
                                            {{ Form::textarea('description', null, ['class' => 'form-control description', 'rows' => 1]) }}
                                        </td>
                                        <td>
                                            <a class="text-danger" data-repeater-delete href="#"> <i
                                                    data-feather="trash-2"></i></a>
                                        </td>
                                    </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class=" text-end">
                    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary btn-rounded', 'id' => 'estimation-submit']) }}
                </div>
            </div>
        </div>

        {{ Form::close() }}
    </div>
@endsection
