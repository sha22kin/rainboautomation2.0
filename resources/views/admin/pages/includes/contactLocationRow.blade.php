@php $field = function ($key) use ($row) { return $row[$key] ?? ''; }; @endphp
<div data-location-row style="border:1px solid #e3ebf3; border-radius:6px; padding:15px; margin-bottom:15px; background:#fafbfd;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <b>Location #<span data-location-number></span></b>
        <div>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-location-action="up" title="Move up"><i class="fa fa-arrow-up"></i></button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-location-action="down" title="Move down"><i class="fa fa-arrow-down"></i></button>
            <button type="button" class="btn btn-sm btn-outline-danger" data-location-action="remove" title="Remove"><i class="fa fa-trash"></i></button>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label>Label <small style="color:#999;">(e.g. CORPORATE OFFICE)</small></label>
            <input type="text" class="form-control" name="locations[{{$index}}][label]" value="{{$field('label')}}" maxlength="100" />
        </div>
        <div class="form-group col-md-6">
            <label>Title *</label>
            <input type="text" class="form-control" name="locations[{{$index}}][title]" value="{{$field('title')}}" maxlength="191" />
        </div>
        <div class="form-group col-md-12">
            <label>Address *</label>
            <textarea class="form-control" name="locations[{{$index}}][address]" rows="2" maxlength="500">{{$field('address')}}</textarea>
        </div>
        <div class="form-group col-md-6">
            <label>Phone Numbers <small style="color:#999;">(one per line)</small></label>
            <textarea class="form-control" name="locations[{{$index}}][phones]" rows="2">{{$field('phones')}}</textarea>
        </div>
        <div class="form-group col-md-6">
            <label>Email</label>
            <input type="email" class="form-control" name="locations[{{$index}}][email]" value="{{$field('email')}}" maxlength="191" />
            <input type="text" class="form-control" style="margin-top:6px;" name="locations[{{$index}}][email_note]" value="{{$field('email_note')}}" placeholder="Text before email (optional), e.g. General enquiries:" maxlength="100" />
        </div>
        <div class="form-group col-md-12" style="margin-bottom:0;">
            <label>Map Search Address <small style="color:#999;">(optional – leave empty to use the address above)</small></label>
            <input type="text" class="form-control" name="locations[{{$index}}][map]" value="{{$field('map')}}" maxlength="500" />
        </div>
    </div>
</div>
