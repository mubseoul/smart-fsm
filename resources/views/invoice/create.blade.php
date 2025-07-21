{{ Form::open(['url' => 'invoice', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('invoice_id', __('Invoice Number'), ['class' => 'form-label']) }}
            <span class="text-danger">*</span>
            <div class="input-group">
                <span class="input-group-text ">
                    {{ invoicePrefix() }}
                </span>
                {{ Form::text('invoice_id', $invoiceNumber, ['class' => 'form-control', 'placeholder' => __('Enter Invoice Number')]) }}
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('invoice_date', __('Invoice Date'), ['class' => 'form-label']) }} <span
                class="text-danger">*</span>
            {{ Form::date('invoice_date', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('client', __('Client'), ['class' => 'form-label']) }} <span class="text-danger">*</span>
            {!! Form::select('client', $clients, null, ['class' => 'form-control hidesearch', 'required' => 'required']) !!}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('workorder', __('Work Order'), ['class' => 'form-label']) }} <span
                class="text-danger">*</span>
            <div class="workorder_div">
                <select class="form-control workorder" id="workorder" name="workorder">
                    <option value="">{{ __('Select Workorder') }}</option>
                </select>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('total', __('Total Amount'), ['class' => 'form-label']) }} <span
                class="text-danger">*</span>
            {{ Form::number('total', null, ['class' => 'form-control', 'placeholder' => __('Enter total amount'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('discount', __('Discount'), ['class' => 'form-label']) }}
            {{ Form::number('discount', null, ['class' => 'form-control', 'placeholder' => __('Enter discount')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('due_date', __('Due Date'), ['class' => 'form-label']) }} <span
                class="text-danger">*</span>
            {{ Form::date('due_date', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }} <span class="text-danger">*</span>
            {!! Form::select('status', $status, null, ['class' => 'form-control hidesearch', 'required' => 'required']) !!}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
            {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 2]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary ml-10']) }}
</div>
{{ Form::close() }}
<script>
    // Document ready function
    $(document).ready(function() {
        // Initialize select2
        $(".hidesearch").each(function() {
            var basic_select = new Choices(this, {
                searchEnabled: false,
                removeItemButton: true,
            });
        });

        // Event handler for client change
        $(document).on('change','#client', function() {
            var client = $(this).val();
            var url = '{{ route('client.workorder') }}';
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    client: client,
                },
                type: 'GET',
                success: function(data) {
                    console.log(data);

                    $('.workorder').empty().append(
                        '<option value="">{{ __('Select Workorder') }}</option>');
                    $.each(data, function(key, value) {
                        $('.workorder').append('<option value="' + key + '">' +
                            value + '</option>');
                    });

                },
            });
        });

        // Event delegation for workorder change
        $(document).on('change', '.workorder', function() {
            var workorder = $(this).val();
            var url = '{{ route('workorder.details') }}';
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    workorder: workorder,
                },
                type: 'GET',
                success: function(data) {
                    $('#total').val(data);
                },
            });
        });
    });
</script>
