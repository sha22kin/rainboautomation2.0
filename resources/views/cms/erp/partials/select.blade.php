{{-- Pop-up form lists: replaces the browser's own list of the selects (it grew wider than the pop-up with long course names).
     The real <select> stays underneath: its value, required check, change events and the course filters keep working. --}}
<style>
.rs{position:relative}
.rs>select{position:absolute;left:0;top:0;width:100%;height:100%;opacity:0;pointer-events:none}
.rs-btn{width:100%;display:flex;align-items:center;justify-content:space-between;gap:8px;padding:11px;border-radius:9px;border:1px solid #dbe5ef;background:#fff;font:inherit;color:inherit;text-align:left;cursor:pointer;min-height:44px}
.rs-btn:focus-visible,.rs.open .rs-btn{outline:none;border-color:#287dcc;box-shadow:0 0 0 3px #287dcc22}
.rs-btn span{flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.rs-btn i{flex:0 0 auto;width:8px;height:8px;border-right:2px solid #6b7c92;border-bottom:2px solid #6b7c92;transform:rotate(45deg);margin:-4px 4px 0 0;transition:transform .15s}
.rs.open .rs-btn i{transform:rotate(-135deg);margin-top:4px}
/* The opened list floats above the page (the pop-up's scroll area would cut it), placed at its field */
.rs-list{display:none;position:fixed;z-index:1000;background:#fff;border:1px solid #dbe5ef;border-radius:10px;box-shadow:0 14px 34px #17284033;max-height:240px;overflow-y:auto;padding:4px;box-sizing:border-box}
.rs-list.show{display:block}
.rs-opt{padding:9px 10px;border-radius:7px;cursor:pointer;line-height:1.35;white-space:normal;overflow-wrap:anywhere}
.rs-opt:hover,.rs-opt.active{background:#eef5ff}
.rs-opt.selected{background:#287dcc;color:#fff}
.rs-opt.empty{color:#7a8aa0;cursor:default;background:none}
</style>
<script>
(function () {
    function visible(select) {
        return Array.prototype.filter.call(select.options, function (o) { return !o.hidden && !o.disabled; });
    }
    function enhance(select) {
        if (select.dataset.rs) return;
        select.dataset.rs = '1';
        var wrap = document.createElement('div'), button = document.createElement('button'), list = document.createElement('div');
        wrap.className = 'rs';
        button.type = 'button';
        button.className = 'rs-btn';
        button.setAttribute('aria-haspopup', 'listbox');
        button.innerHTML = '<span></span><i></i>';
        list.className = 'rs-list';
        list.setAttribute('role', 'listbox');
        select.parentNode.insertBefore(wrap, select);
        wrap.appendChild(select);
        wrap.appendChild(button);
        select.tabIndex = -1;
        var active = -1;

        function label() {
            var option = select.selectedOptions[0];
            button.firstChild.textContent = option ? option.text : '';
            button.title = option ? option.text : '';
        }
        function render() {
            list.innerHTML = '';
            var options = visible(select);
            if (!options.length) {
                var empty = document.createElement('div');
                var placeholder = Array.prototype.find.call(select.options, function (o) { return o.value === '' && !o.hidden; });
                empty.className = 'rs-opt empty';
                empty.textContent = placeholder ? placeholder.text : 'Nothing to choose';
                list.appendChild(empty);
            }
            options.forEach(function (option, i) {
                var item = document.createElement('div');
                item.className = 'rs-opt' + (option.value === select.value ? ' selected' : '');
                item.setAttribute('role', 'option');
                item.textContent = option.text;
                item.addEventListener('mousedown', function (e) { e.preventDefault(); choose(option); });
                list.appendChild(item);
                if (option.value === select.value) active = i;
            });
        }
        function choose(option) {
            if (select.value !== option.value) {
                select.value = option.value;
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
            label();
            close();
            button.focus();
        }
        // Below the field, or above it when the screen has more room there; never wider than the field
        function place() {
            var box = button.getBoundingClientRect(), gap = 4, edge = 8;
            var below = window.innerHeight - box.bottom - gap - edge, above = box.top - gap - edge;
            list.style.left = box.left + 'px';
            list.style.width = box.width + 'px';
            list.style.maxHeight = '240px';
            var height = Math.min(list.scrollHeight, 240);
            if (height <= below || below >= above) {
                list.style.maxHeight = Math.min(240, below) + 'px';
                list.style.top = (box.bottom + gap) + 'px';
            } else {
                height = Math.min(height, above);
                list.style.maxHeight = height + 'px';
                list.style.top = (box.top - gap - height) + 'px';
            }
        }
        function open() {
            if (wrap.classList.contains('open')) return;
            if (window.rsClose) window.rsClose();
            active = -1;
            render();
            document.body.appendChild(list);
            list.classList.add('show');
            wrap.classList.add('open');
            place();
            var current = list.querySelector('.selected');
            if (current) current.scrollIntoView({ block: 'nearest' });
            window.rsClose = close;
        }
        function close() {
            wrap.classList.remove('open');
            list.classList.remove('show');
            if (list.parentNode) list.parentNode.removeChild(list);
            if (window.rsClose === close) window.rsClose = null;
        }
        function move(step) {
            var items = list.querySelectorAll('.rs-opt:not(.empty)');
            if (!items.length) return;
            active = Math.max(0, Math.min(items.length - 1, active + step));
            items.forEach(function (item, i) { item.classList.toggle('active', i === active); });
            items[active].scrollIntoView({ block: 'nearest' });
        }
        button.addEventListener('click', function () { wrap.classList.contains('open') ? close() : open(); });
        button.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') { e.preventDefault(); open(); move(e.key === 'ArrowDown' ? 1 : -1); }
            else if ((e.key === 'Enter' || e.key === ' ') && wrap.classList.contains('open') && active >= 0) { e.preventDefault(); choose(visible(select)[active]); }
            else if (e.key === 'Escape' && wrap.classList.contains('open')) { e.preventDefault(); e.stopPropagation(); close(); }
        });
        button.addEventListener('blur', function () { setTimeout(close, 120); });
        select.addEventListener('change', label);
        // Values set by the page (Edit, course filters) without a change event
        select.rsLabel = label;
        label();
    }
    function enhanceAll() {
        var fields = document.getElementById('modalFields');
        if (!fields) return;
        fields.querySelectorAll('select').forEach(enhance);
        fields.querySelectorAll('select[data-rs]').forEach(function (s) { s.rsLabel(); });
    }
    // An open list closes when the page or the pop-up scrolls, the window resizes, or the pop-up closes
    ['scroll', 'resize'].forEach(function (type) {
        window.addEventListener(type, function (e) {
            if (window.rsClose && !(e.target && e.target.classList && e.target.classList.contains('rs-list'))) window.rsClose();
        }, true);
    });
    document.addEventListener('DOMContentLoaded', function () {
        var close = window.closeModal;
        if (typeof close === 'function') {
            window.closeModal = function () { if (window.rsClose) window.rsClose(); return close.apply(this, arguments); };
        }
        var fields = document.getElementById('modalFields');
        if (!fields) return;
        new MutationObserver(enhanceAll).observe(fields, { childList: true });
        // Edit fills the form and the course filters pick a value after the pop-up opens
        ['openCreate', 'openEdit', 'addStudentCourse'].forEach(function (name) {
            var original = window[name];
            if (typeof original !== 'function') return;
            window[name] = function () {
                var result = original.apply(this, arguments);
                enhanceAll();
                return result;
            };
        });
        document.addEventListener('change', function () { setTimeout(enhanceAll, 0); });
    });
})();
</script>
