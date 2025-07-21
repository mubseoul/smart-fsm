@extends('layouts.app')
@section('page-title')
    {{ __('Assets') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ __('Assets') }}
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
                            <h5>{{ __('Asset List') }}</h5>
                        </div>
                        <div class="col-auto">
                            @if (Gate::check('create asset'))
                                <a class="btn btn-secondary customModal" href="#" data-size="lg"
                                    data-url="{{ route('asset.create') }}" data-title="{{ __('Create Asset') }}"> <i
                                        class="ti ti-circle-plus align-text-bottom"></i>
                                    {{ __('Create Asset') }}
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
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Asset Number') }}</th>
                                    <th>{{ __('Part') }}</th>
                                    <th>{{ __('Parent Asset') }}</th>
                                    <th>{{ __('GIAI') }}</th>
                                    <th>{{ __('Order') }}</th>
                                    <th>{{ __('Purchase') }}</th>
                                    <th>{{ __('Installation') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assets as $asset)
                                    <tr>
                                        <td>{{ $asset->name }} </td>
                                        <td>{{ $asset->asset_number }} </td>
                                        <td>{{ !empty($asset->parts) ? $asset->parts->title : '-' }} </td>
                                        <td>{{ !empty($asset->parents) ? $asset->parents->name : '-' }} </td>
                                        <td>{{ $asset->giai }} </td>
                                        <td>{{ dateFormat($asset->order_date) }} </td>
                                        <td>{{ dateFormat($asset->purchase_date) }} </td>
                                        <td>{{ dateFormat($asset->installation_date) }} </td>
                                        <td>
                                            <div class="cart-action">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['asset.destroy', $asset->id]]) !!}
                                                @can('show asset')
                                                    <a class="avtar avtar-xs btn-link-warning text-warning customModal"
                                                        data-bs-toggle="tooltip" data-size="lg"
                                                        data-bs-original-title="{{ __('Details') }}" href="#"
                                                        data-url="{{ route('asset.show', $asset->id) }}"
                                                        data-title="{{ __('Asset Detail') }}"> <i data-feather="eye"></i></a>
                                                @endcan
                                                @can('edit asset')
                                                    <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                        data-bs-toggle="tooltip" data-size="lg"
                                                        data-bs-original-title="{{ __('Edit') }}" href="#"
                                                        data-url="{{ route('asset.edit', $asset->id) }}"
                                                        data-title="{{ __('Edit Asset') }}"> <i data-feather="edit"></i></a>
                                                @endcan
                                                @can('delete asset')
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
