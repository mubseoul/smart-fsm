@extends('layouts.app')
@section('page-title')
    {{ __('Categories') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item" aria-current="page"> {{ __('Categories') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Category List') }}</h5>
                        </div>
                        @if (\Auth::user()->type == 'super admin')
                            <div class="col-auto">
                                <a href="#" class="btn btn-secondary customModal" data-size="md"
                                    data-url="{{ route('categories.create') }}" data-title="{{ __('Create Category') }}">
                                    <i class="ti ti-circle-plus align-text-bottom"></i> {{ __('Create Category') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped m-0" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Slug') }}</th>
                                    <th>{{ __('Parent Category') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories->where('parent_id', 0) as $mainCategory)
                                    {{-- Main Category Row --}}
                                    <tr>
                                        <td>
                                            <strong>{{ $mainCategory->name }}</strong>
                                            @if($mainCategory->children->count() > 0)
                                                <span class="badge bg-info ms-2">{{ $mainCategory->children->count() }} {{ __('subcategories') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $mainCategory->slug }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ __('Main Category') }}</span>
                                        </td>
                                        <td>
                                            {{ Str::limit($mainCategory->description, 50) }}
                                        </td>
                                        <td>
                                            @if($mainCategory->active)
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @can('edit categories')
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal" data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                    data-url="{{ route('categories.edit', $mainCategory->id) }}"
                                                    data-title="{{ __('Edit Category') }}"> <i data-feather="edit"></i></a>
                                            @endcan
                                            @if ($mainCategory->is_deletable && $mainCategory->children->count() == 0)
                                                @can('delete categories')
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['categories.destroy', $mainCategory->id], 'style' => 'display:inline']) !!}
                                                    <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog" data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{ __('Delete') }}" href="#"><i
                                                            data-feather="trash-2"></i></a>
                                                    {!! Form::close() !!}
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                    
                                    {{-- Subcategories Rows --}}
                                    @foreach ($mainCategory->children as $subcategory)
                                        <tr>
                                            <td>
                                                <span class="text-muted ms-3">└─</span> 
                                                {{ $subcategory->name }}
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $subcategory->slug }}</span>
                                            </td>
                                            <td>
                                                {{ $mainCategory->name }}
                                            </td>
                                            <td>
                                                {{ Str::limit($subcategory->description, 50) }}
                                            </td>
                                            <td>
                                                @if($subcategory->active)
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @can('edit categories')
                                                    <a class="avtar avtar-xs btn-link-secondary text-secondary customModal" data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{ __('Edit') }}" href="#"
                                                        data-url="{{ route('categories.edit', $subcategory->id) }}"
                                                        data-title="{{ __('Edit Category') }}"> <i data-feather="edit"></i></a>
                                                @endcan
                                                @if ($subcategory->is_deletable)
                                                    @can('delete categories')
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['categories.destroy', $subcategory->id], 'style' => 'display:inline']) !!}
                                                        <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog" data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Delete') }}" href="#"><i
                                                                data-feather="trash-2"></i></a>
                                                        {!! Form::close() !!}
                                                    @endcan
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 