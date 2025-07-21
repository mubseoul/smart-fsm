@extends('layouts.app')
@section('page-title')
    {{clientPrefix()}}{{!empty($client->clients)?$client->clients->client_id:''}} {{__('Details')}}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{route('dashboard')}}">{{__('Dashboard')}}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{route('client.index')}}">{{__('Client')}}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{clientPrefix()}}{{!empty($client->clients)?$client->clients->client_id:''}} {{__('Details')}}
            </a>
        </li>
    </ul>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>  {{clientPrefix()}}{{!empty($client->clients)?$client->clients->client_id:''}} {{__('Details')}}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-lg-4">
                            <div class="detail-group">
                                <b>{{__('Name')}}</b>
                                <p class="mb-20">{{$client->name}}</p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="detail-group">
                                <b>{{__('Email')}}</b>
                                <p class="mb-20">{{$client->email}}</p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="detail-group">
                                <b>{{__('Phone Number')}}</b>
                                <p class="mb-20">{{$client->phone_number}}</p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="detail-group">
                                <b>{{__('Company')}}</b>
                                <p class="mb-20">{{!empty($client->clients)?$client->clients->company:'-'}} </p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12 mb-20">
                        <h4> <b>{{__('Service Address')}}</b></h4>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4 col-lg-4">
                            <div class="detail-group">
                                <b>{{__('Country')}}</b>
                                <p class="mb-20">{{!empty($client->clients)?$client->clients->service_country:'-'}} </p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="detail-group">
                                <b>{{__('State')}}</b>
                                <p class="mb-20">{{!empty($client->clients)?$client->clients->service_state:'-'}} </p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="detail-group">
                                <b>{{__('City')}}</b>
                                <p class="mb-20">{{!empty($client->clients)?$client->clients->service_city:'-'}} </p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="detail-group">
                                <b>{{__('Zip Code')}}</b>
                                <p class="mb-20">{{!empty($client->clients)?$client->clients->service_zip_code:'-'}} </p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="detail-group">
                                <b>{{__('Address')}}</b>
                                <p class="mb-20">{{!empty($client->clients)?$client->clients->service_address:'-'}} </p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class=" col-md-12 mb-20">
                        <h4> <b>{{__('Billing Address')}}</b></h4>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4 col-lg-4">
                            <div class="detail-group">
                                <b>{{__('Billing Country')}}</b>
                                <p class="mb-20">{{!empty($client->clients)?$client->clients->billing_country:'-'}} </p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="detail-group">
                                <b>{{__('Billing State')}}</b>
                                <p class="mb-20">{{!empty($client->clients)?$client->clients->billing_state:'-'}} </p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="detail-group">
                                <b>{{__('Billing City')}}</b>
                                <p class="mb-20">{{!empty($client->clients)?$client->clients->billing_city:'-'}} </p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="detail-group">
                                <b>{{__('Billing Zip Code')}}</b>
                                <p class="mb-20">{{!empty($client->clients)?$client->clients->billing_zip_code:'-'}} </p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="detail-group">
                                <b>{{__('Billing Address')}}</b>
                                <p class="mb-20">{{!empty($client->clients)?$client->clients->billing_address:'-'}} </p>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
