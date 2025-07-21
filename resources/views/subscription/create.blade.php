{{ Form::open(array('url' => 'subscriptions')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{Form::label('title',__('Title'),array('class'=>'form-label'))}}
            {{Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter subscription title'),'required'=>'required'))}}
        </div>
        <div class="form-group">
            {{ Form::label('interval', __('Interval'),array('class'=>'form-label')) }}
            {!! Form::select('interval', $intervals, null,array('class' => 'form-control basic-select','required'=>'required')) !!}
        </div>
        <div class="form-group">
            {{Form::label('package_amount',__('Package Amount'),array('class'=>'form-label'))}}
            {{Form::number('package_amount',null,array('class'=>'form-control','placeholder'=>__('Enter package amount'),'step'=>'0.01'))}}
        </div>
        <div class="form-group">
            {{Form::label('user_limit',__('User Limit'),array('class'=>'form-label'))}}
            {{Form::number('user_limit',null,array('class'=>'form-control','placeholder'=>__('Enter user limit'),'required'=>'required'))}}
        </div>
        <div class="form-group">
            {{Form::label('client_limit',__('Client Limit'),array('class'=>'form-label'))}}
            {{Form::number('client_limit',null,array('class'=>'form-control','placeholder'=>__('Enter client limit'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            <div class="form-check form-switch custom-switch-v1 mb-2">
                <input type="checkbox" class="form-check-input input-secondary" name="enabled_logged_history" id="enabled_logged_history" >
                {{Form::label('enabled_logged_history',__('Show User Logged History'),array('class'=>'form-label'))}}
              </div>
        </div>
        
        <!-- Trial Settings -->
        <div class="col-12">
            <hr>
            <h5>{{ __('Trial Settings') }}</h5>
        </div>
        <div class="form-group col-md-6">
            <div class="form-check form-switch custom-switch-v1 mb-2">
                <input type="checkbox" class="form-check-input input-secondary" name="trial_enabled" id="trial_enabled" checked>
                {{Form::label('trial_enabled',__('Enable Trial Period'),array('class'=>'form-label'))}}
              </div>
        </div>
        <div class="form-group col-md-6">
            {{Form::label('trial_days',__('Trial Days'),array('class'=>'form-label'))}}
            {{Form::number('trial_days',30,array('class'=>'form-control','placeholder'=>__('Enter trial days'),'min'=>'1','max'=>'365'))}}
            <small class="text-muted">{{ __('Number of days for free trial (1-365 days)') }}</small>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

<script>
document.addEventListener('DOMContentLoaded', function() {
    const trialEnabledCheckbox = document.getElementById('trial_enabled');
    const trialDaysInput = document.querySelector('input[name="trial_days"]');
    
    function toggleTrialDays() {
        if (trialEnabledCheckbox.checked) {
            trialDaysInput.disabled = false;
            trialDaysInput.required = true;
        } else {
            trialDaysInput.disabled = true;
            trialDaysInput.required = false;
            trialDaysInput.value = 0;
        }
    }
    
    trialEnabledCheckbox.addEventListener('change', toggleTrialDays);
    toggleTrialDays(); // Initial state
});
</script>

