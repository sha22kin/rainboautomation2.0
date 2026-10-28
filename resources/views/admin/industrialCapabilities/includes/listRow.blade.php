{{-- One repeatable item: Industrial / Solar hero feature chip ($list = 'features'), workflow step ('steps') or Training hero course chip ('chips') --}}
@php
    $field = function ($key, $default = '') use ($row) { return $row[$key] ?? $default; };
    $isChip = in_array($list, ['features', 'chips']);
    $color = $list == 'features' ? $field('color', '#287dcc') : '#287dcc';
@endphp
<div class="nx-rep-item" data-repeat-row style="--rep-accent:{{$color}}">
    <div class="nx-rep-head">
        <span class="nx-rep-num">{{$isChip ? '#' : ''}}<span data-repeat-number @if(!$isChip) data-repeat-pad @endif></span></span>
        <span class="nx-rep-kind">{{$isChip ? 'Chip' : 'Step'}}</span>
        <div class="nx-rep-actions">
            <button type="button" class="nx-rep-btn" data-repeat-action="up" title="Move up"><i class="fa-solid fa-arrow-up"></i></button>
            <button type="button" class="nx-rep-btn" data-repeat-action="down" title="Move down"><i class="fa-solid fa-arrow-down"></i></button>
            <button type="button" class="nx-rep-btn is-danger" data-repeat-action="remove" title="Remove"><i class="fa-solid fa-trash-can"></i></button>
        </div>
    </div>
    <div class="nx-rep-body">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" class="form-control nx-rep-title" name="{{$list}}[{{$index}}][title]" value="{{$field('title')}}" maxlength="100" />
        </div>
        <div class="form-group">
            <label>Text</label>
            @if($isChip)
            <input type="text" class="form-control" name="{{$list}}[{{$index}}][text]" value="{{$field('text')}}" maxlength="191" />
            @else
            <textarea class="form-control" name="{{$list}}[{{$index}}][text]" rows="3" maxlength="500">{{$field('text')}}</textarea>
            @endif
        </div>
        @if($list == 'features')
        <div class="nx-rep-color">
            <input type="color" name="{{$list}}[{{$index}}][color]" value="{{$color}}" data-repeat-color title="Dot color" />
            <label>Dot color</label>
            <span class="nx-rep-hex" data-repeat-hex>{{$color}}</span>
        </div>
        @endif
    </div>
</div>
