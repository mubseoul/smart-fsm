{{ Form::model($category, array('route' => array('categories.update', $category->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'form-label'))}}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter category name'),'required'=>'required'))}}
        </div>
        <div class="form-group">
            {{ Form::label('parent_id', __('Parent Category'),array('class'=>'form-label')) }}
            <select name="parent_id" class="form-control basic-select">
                <option value="">{{ __('None (Main Category)') }}</option>
                @foreach($parentCategories as $parent)
                    <option value="{{ $parent->id }}" {{ $category->parent_id == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            {{Form::label('slug',__('Slug'),array('class'=>'form-label'))}}
            {{Form::text('slug',null,array('class'=>'form-control','placeholder'=>__('Enter category slug'),'required'=>'required'))}}
        </div>
        <div class="form-group">
            {{Form::label('description',__('Description'),array('class'=>'form-label'))}}
            {{Form::textarea('description',null,array('class'=>'form-control','placeholder'=>__('Enter category description'),'rows'=>'3'))}}
        </div>
        <div class="form-group col-md-6">
            <div class="form-check form-switch custom-switch-v1 mb-2">
                <input type="checkbox" class="form-check-input input-secondary" name="active" id="active" {{ $category->active ? 'checked' : '' }}>
                {{Form::label('active',__('Active'),array('class'=>'form-label'))}}
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-check form-switch custom-switch-v1 mb-2">
                <input type="checkbox" class="form-check-input input-secondary" name="is_deletable" id="is_deletable" {{ $category->is_deletable ? 'checked' : '' }}>
                {{Form::label('is_deletable',__('Is Deletable'),array('class'=>'form-label'))}}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
    {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
</div>
{{ Form::close() }}

<script>
    // Auto-generate slug from name
    $('input[name="name"]').on('keyup', function() {
        var name = $(this).val();
        var slug = name.toLowerCase()
            .replace(/[^a-z0-9 -]/g, '') // remove invalid chars
            .replace(/\s+/g, '-') // collapse whitespace and replace by -
            .replace(/-+/g, '-'); // collapse dashes
        $('input[name="slug"]').val(slug);
    });
</script> 