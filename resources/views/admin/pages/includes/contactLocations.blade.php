{{-- Contact Us page: office / training locations shown as cards (with map) on the website --}}
@php
    $locationRows = old('locations', $page->contactLocations->map(function ($location) {
        return array_merge($location->location(), [
            'label' => $location->name,
            'phones' => implode("\n", $location->location('phones')),
        ]);
    })->all());
@endphp
<div class="card" id="contactLocationsCard">
    <div class="card-header" style="border-bottom: 1px solid #e3ebf3; display:flex; justify-content:space-between; align-items:center;">
        <h4 class="card-title">Contact Locations <small style="color:#999;">(Contact Us page address cards)</small></h4>
        <button type="button" class="btn btn-sm btn-primary" data-location-add><i class="fa fa-plus"></i> Add Location</button>
    </div>
    <div class="card-content">
        <div class="card-body">
            <input type="hidden" name="locations_form" value="1" />
            <div data-location-list>
                @foreach($locationRows as $i => $row)
                    @include(adminTheme().'pages.includes.contactLocationRow', ['index' => $i, 'row' => $row])
                @endforeach
            </div>
            <p data-location-empty style="color:#999; margin:0; {{count($locationRows) ? 'display:none;' : ''}}">No location added. Click "Add Location".</p>
        </div>
    </div>
</div>

<template id="contactLocationTemplate">
    @include(adminTheme().'pages.includes.contactLocationRow', ['index' => '__INDEX__', 'row' => []])
</template>

@push('js')
<script>
    (function () {
        var card = document.getElementById('contactLocationsCard');
        if (!card) return;
        var list = card.querySelector('[data-location-list]');
        var empty = card.querySelector('[data-location-empty]');
        var template = document.getElementById('contactLocationTemplate').innerHTML;
        var counter = Date.now();

        function refresh() {
            var rows = list.querySelectorAll('[data-location-row]');
            empty.style.display = rows.length ? 'none' : '';
            rows.forEach(function (row, i) {
                row.querySelector('[data-location-number]').textContent = i + 1;
            });
        }

        card.querySelector('[data-location-add]').addEventListener('click', function () {
            list.insertAdjacentHTML('beforeend', template.replace(/__INDEX__/g, 'n' + (counter++)));
            refresh();
        });

        list.addEventListener('click', function (e) {
            var button = e.target.closest('[data-location-action]');
            if (!button) return;
            var row = button.closest('[data-location-row]');
            var action = button.getAttribute('data-location-action');
            if (action === 'remove' && confirm('Remove this location?')) row.remove();
            if (action === 'up' && row.previousElementSibling) list.insertBefore(row, row.previousElementSibling);
            if (action === 'down' && row.nextElementSibling) list.insertBefore(row.nextElementSibling, row);
            refresh();
        });

        refresh();
    })();
</script>
@endpush
