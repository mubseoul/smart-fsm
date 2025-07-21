<div class="modal-body wrapper">
    <div class="row">
        <div class="col-md-6 col-lg-6">
            <div class="detail-group">
                <b>{{__('Name')}}</b>
                <p class="mb-20">{{ $asset->name }} </p>
            </div>
        </div>
        <div class="col-md-6 col-lg-6">
            <div class="detail-group">
                <b>{{__('Asset Number')}}</b>
                <p class="mb-20">{{ $asset->asset_number }} </p>
            </div>
        </div>
        <div class="col-md-6 col-lg-6">
            <div class="detail-group">
                <b>{{__('Part')}}</b>
                <p class="mb-20">{{ !empty($asset->parts)?$asset->parts->title:'-' }}</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-6">
            <div class="detail-group">
                <b>{{__('Parent Asset')}}</b>
                <p class="mb-20">{{ !empty($asset->parents)?$asset->parents->name:'-' }} </p>
            </div>
        </div>
        <div class="col-md-6 col-lg-6">
            <div class="detail-group">
                <b>{{__('GIAI')}}</b>
                <p class="mb-20">{{ $asset->giai }} </p>
            </div>
        </div>

        <div class="col-md-6 col-lg-6">
            <div class="detail-group">
                <b>{{__('Order Date')}}</b>
                <p class="mb-20"> {{ dateFormat($asset->order_date) }}</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-6">
            <div class="detail-group">
                <b>{{__('Purchase Date')}}</b>
                <p class="mb-20"> {{ dateFormat($asset->purchase_date) }}</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-6">
            <div class="detail-group">
                <b>{{__('Installation Date')}}</b>
                <p class="mb-20"> {{ dateFormat($asset->installation_date) }}</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-6">
            <div class="detail-group">
                <b>{{__('Warranty Expiration')}}</b>
                <p class="mb-20"> {{ !empty($asset->warranty_expiration)?dateFormat($asset->warranty_expiration) :'-'}}</p>
            </div>
        </div>

        <div class="col-md-6 col-lg-6">
            <div class="detail-group">
                <b>{{__('Warranty Notes')}}</b>
                <p class="mb-20">{{ !empty($asset->warranty_notes)?$asset->warranty_notes:"-" }}</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-12">
            <div class="detail-group">
                <b>{{__('Description')}}</b>
                <p class="mb-20">{{ !empty($asset->description)?$asset->description:"-" }}</p>
            </div>
        </div>
    </div>

</div>

