{{ Form::model($invoice, array('route' => array('invoice.update', $invoice->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{Form::label('invoice_id',__('Invoice Number'),array('class'=>'form-label'))}}
            <span class="text-danger">*</span>
            <div class="input-group">
                <span class="input-group-text ">
                  {{invoicePrefix()}}
                </span>
                {{Form::text('invoice_id',$invoice->invoice_id,array('class'=>'form-control','placeholder'=>__('Enter Invoice Number')))}}
            </div>
        </div>
        <div class="form-group col-md-6">
            {{Form::label('invoice_date',__('Invoice Date'),array('class'=>'form-label')) }} <span class="text-danger">*</span>
            {{Form::date('invoice_date',null,array('class'=>'form-control','required'=>'required'))}}
        </div>

        <div class="form-group col-md-6">
            {{Form::label('total',__('Total Amount'),array('class'=>'form-label'))}} <span class="text-danger">*</span>
            {{Form::number('total',null,array('class'=>'form-control','placeholder'=>__('Enter total amount'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('discount',__('Discount'),array('class'=>'form-label'))}}
            {{Form::number('discount',null,array('class'=>'form-control','placeholder'=>__('Enter discount')))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('due_date',__('Due Date'),array('class'=>'form-label')) }} <span class="text-danger">*</span>
            {{Form::date('due_date',null,array('class'=>'form-control','required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Status'),['class'=>'form-label']) }} <span class="text-danger">*</span>
            {!! Form::select('status', $status, null,array('class' => 'form-control hidesearch','required'=>'required')) !!}
        </div>
        <div class="form-group col-md-12">
            {{Form::label('notes',__('Notes'),array('class'=>'form-label')) }}
            {{Form::textarea('notes',null,array('class'=>'form-control','rows'=>2))}}
        </div>
    </div>
</div>
<div class="modal-footer">
    {{Form::submit(__('Update'),array('class'=>'btn btn-secondary ml-10'))}}
</div>
{{Form::close()}}



