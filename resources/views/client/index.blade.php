@extends('layouts.app')
@section('page-title')
    {{ __('Client') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ __('Client') }}
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
                            <h5>{{ __('Client List') }}</h5>
                        </div>
                        <div class="col-auto">
                            @if (Gate::check('create client'))
                                <a class="btn btn-secondary customModal" href="#" data-size="lg"
                                    data-url="{{ route('client.create') }}" data-title="{{ __('Create Client') }}"> <i
                                        class="ti ti-circle-plus align-text-bottom"></i>
                                    {{ __('Create Client') }}
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
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone Number') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $client)
                                    <tr>
                                        <td>{{ clientPrefix() }}{{ !empty($client->clients) ? $client->clients->client_id : '' }}
                                        </td>
                                        <td>{{ $client->name }} </td>
                                        <td>{{ $client->email }} </td>
                                        <td>{{ !empty($client->phone_number) ? $client->phone_number : '-' }} </td>
                                        <td>{{ !empty($client->clients) && !empty($client->clients->company) ? $client->clients->company : '-' }}
                                        </td>
                                        <td>
                                            <div class="cart-action">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['client.destroy', $client->id]]) !!}
                                                @can('show client')
                                                    <a class="avtar avtar-xs btn-link-warning text-warning"
                                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Details') }}"
                                                        href="{{ route('client.show', \Illuminate\Support\Facades\Crypt::encrypt($client->id)) }}">
                                                        <i data-feather="eye"></i></a>
                                                @endcan
                                                @can('edit client')
                                                    <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                        data-bs-toggle="tooltip" data-size="lg"
                                                        data-bs-original-title="{{ __('Edit') }}" href="#"
                                                        data-url="{{ route('client.edit', $client->id) }}"
                                                        data-title="{{ __('Edit Client') }}"> <i data-feather="edit"></i></a>
                                                @endcan
                                                @can('delete client')
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
