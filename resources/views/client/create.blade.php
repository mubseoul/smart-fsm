{{Form::open(array('url'=>'client','method'=>'post'))}}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{Form::label('name',__('Name'),array('class'=>'form-label')) }} <span class="text-danger">*</span>
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('email',__('Email'),array('class'=>'form-label'))}} <span class="text-danger">*</span>
            {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter email'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('phone_number',__('Phone Number'),array('class'=>'form-label')) }} <span class="text-danger">*</span>
            {{Form::text('phone_number',null,array('class'=>'form-control','placeholder'=>__('Enter phone number'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('company',__('Company'),array('class'=>'form-label')) }}
            {{Form::text('company',null,array('class'=>'form-control','placeholder'=>__('Enter company')))}}
        </div>
    </div>
    <div class=" col-md-12 mb-20">
        <h5> {{__('Service Address')}}</h5>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            {{Form::label('service_city',__('City'),array('class'=>'form-label')) }} <span class="text-danger">*</span>
            {{Form::text('service_city',null,array('class'=>'form-control','placeholder'=>__('Enter service city'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('service_state',__('State'),array('class'=>'form-label'))}} <span class="text-danger">*</span>
            {{Form::text('service_state',null,array('class'=>'form-control','placeholder'=>__('Enter service state'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('service_country',__('Country'),array('class'=>'form-label'))}} <span class="text-danger">*</span>
            {{Form::text('service_country',null,array('class'=>'form-control','placeholder'=>__('Enter service country'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('service_zip_code',__('Zip Code'),array('class'=>'form-label'))}} <span class="text-danger">*</span>
            {{Form::text('service_zip_code',null,array('class'=>'form-control','placeholder'=>__('Enter service zip code'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-12">
            {{Form::label('service_address',__('Address'),array('class'=>'form-label'))}} <span class="text-danger">*</span>
            {{Form::textarea('service_address',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter service address'),'required'=>'required'))}}
        </div>
    </div>
    <div class=" col-md-12 mb-20">
        <div class="form-group">
            <div class="form-check custom-chek">
                <input class="form-check-input" type="checkbox" value="billing_info" id="billing_info" name="billing_info">
                <label class="form-check-label" for="billing_info"><h5> {{__('Billing Address')}}</h5></label>
            </div>
        </div>
    </div>
    <div class="row billing_info d-none">
        <div class="form-group col-md-6">
            {{Form::label('billing_city',__('City'),array('class'=>'form-label')) }}
            {{Form::text('billing_city',null,array('class'=>'form-control','placeholder'=>__('Enter billing city')))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('billing_state',__('State'),array('class'=>'form-label'))}}
            {{Form::text('billing_state',null,array('class'=>'form-control','placeholder'=>__('Enter billing state')))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('billing_country',__('Country'),array('class'=>'form-label'))}}
            {{Form::text('billing_country',null,array('class'=>'form-control','placeholder'=>__('Enter billing country')))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('billing_zip_code',__('Zip Code'),array('class'=>'form-label'))}}
            {{Form::text('billing_zip_code',null,array('class'=>'form-control','placeholder'=>__('Enter billing zip code')))}}
        </div>
        <div class="form-group col-md-12">
            {{Form::label('billing_address',__('Address'),array('class'=>'form-label'))}}
            {{Form::textarea('billing_address',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter billing address')))}}
        </div>
    </div>
</div>
<div class="modal-footer">
    {{Form::submit(__('Create'),array('class'=>'btn btn-secondary ml-10'))}}
</div>
{{Form::close()}}
<script>
    $('#billing_info').on('click',function() {
        if($(this).is(":checked")) {
           $('.billing_info').removeClass('d-none');
        }else{
            $('.billing_info').addClass('d-none');
        }

    });
</script>
