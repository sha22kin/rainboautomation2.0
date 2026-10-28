{{-- Add / remove / reorder items inside every [data-repeater] box (Industrial Solution, Solar Solution and Training Page Content admin) --}}
<script>
    document.querySelectorAll('[data-repeater]').forEach(function (box) {
        var list = box.querySelector('[data-repeat-list]');
        var empty = box.querySelector('[data-repeat-empty]');
        var template = box.querySelector('[data-repeat-template]').innerHTML;
        var counter = Date.now();

        function refresh() {
            var rows = list.querySelectorAll('[data-repeat-row]');
            empty.style.display = rows.length ? 'none' : '';
            rows.forEach(function (row, i) {
                var number = row.querySelector('[data-repeat-number]');
                number.textContent = number.hasAttribute('data-repeat-pad') ? String(i + 1).padStart(2, '0') : i + 1;
            });
        }

        box.querySelector('[data-repeat-add]').addEventListener('click', function () {
            list.insertAdjacentHTML('beforeend', template.replace(/__INDEX__/g, 'n' + (counter++)));
            var row = list.lastElementChild;
            row.classList.add('is-new');
            refresh();
            row.scrollIntoView({behavior: 'smooth', block: 'center'});
            var first = row.querySelector('input[type=text]:not(.nx-rep-icon), textarea');
            if (first) first.focus({preventScroll: true});
        });

        list.addEventListener('click', function (e) {
            var button = e.target.closest('[data-repeat-action]');
            if (!button) return;
            var row = button.closest('[data-repeat-row]');
            var action = button.getAttribute('data-repeat-action');
            if (action === 'remove' && confirm('Remove this item?')) row.remove();
            if (action === 'up' && row.previousElementSibling) list.insertBefore(row, row.previousElementSibling);
            if (action === 'down' && row.nextElementSibling) list.insertBefore(row.nextElementSibling, row);
            refresh();
        });

        // Preview the chosen colour on the item (top bar, number badge, icon bubble)
        list.addEventListener('input', function (e) {
            if (e.target.matches('[data-repeat-color]')) {
                var row = e.target.closest('[data-repeat-row]');
                row.style.setProperty('--rep-accent', e.target.value);
                var hex = row.querySelector('[data-repeat-hex]');
                if (hex) hex.textContent = e.target.value;
            }
        });

        refresh();
    });
</script>
