@extends('layouts.app')
@section('page-title')
    {{ __('Estimation') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ __('Estimation') }}
            </a>
        </li>
    </ul>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Estimation List') }}</h5>
                        </div>
                        <div class="col-auto">
                            @if (Gate::check('create estimation'))
                                <a class="btn btn-secondary" href="{{ route('estimation.create') }}">
                                    <i class="ti ti-circle-plus align-text-bottom"></i>
                                    {{ __('Create Estimation') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover advance-datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Client') }}</th>
                                    <th>{{ __('Asset') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Due Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($estimations as $estimation)
                                    <tr>
                                        <td>{{ estimationPrefix() . $estimation->estimation_id }} </td>
                                        <td>{{ $estimation->title }} </td>
                                        <td>{{ !empty($estimation->clients) ? $estimation->clients->name : '-' }} </td>
                                        <td>{{ !empty($estimation->assets) ? $estimation->assets->name : '-' }} </td>
                                        <td>{{ priceFormat($estimation->getEstimationSubTotalAmount()) }} </td>
                                        <td>{{ dateFormat($estimation->due_date) }} </td>
                                        <td>
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

                                        </td>

                                        <td>
                                            <div class="cart-action">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['estimation.destroy', $estimation->id]]) !!}
                                                @can('show estimation')
                                                    <a class="avtar avtar-xs btn-link-warning text-warning"
                                                        data-bs-toggle="tooltip"
                                                        href="{{ route('estimation.show', \Illuminate\Support\Facades\Crypt::encrypt($estimation->id)) }}"
                                                        data-title="{{ __('Estimation Detail') }}"> <i
                                                            data-feather="eye"></i></a>
                                                @endcan
                                                @can('edit estimation')
                                                    <a class="avtar avtar-xs btn-link-secondary text-secondary"
                                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Edit') }}"
                                                        href="{{ route('estimation.edit', \Illuminate\Support\Facades\Crypt::encrypt($estimation->id)) }}">
                                                        <i data-feather="edit"></i></a>
                                                @endcan
                                                @can('delete estimation')
                                                    <a class=" avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Detete') }}"
                                                        href="#"> <i data-feather="trash-2"></i></a>
                                                @endcan
                                                {!! Form::close() !!}
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
