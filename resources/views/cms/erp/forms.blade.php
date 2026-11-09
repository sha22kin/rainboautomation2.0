@php
    // Design field(): label + input, or label + select from [value, text] options
    $field = function($name, $label, $type = 'text', $options = null, $required = true){
        $req = $required ? 'required' : '';
        if($options !== null){
            $control = '<select name="'.e($name).'" '.$req.'>'.collect($options)->map(fn ($o) => '<option value="'.e($o[0]).'">'.e($o[1]).'</option>')->implode('').'</select>';
        }else{
            $control = '<input name="'.e($name).'" type="'.e($type).'" '.$req.($type == 'number' ? ' step="any" min="0"' : '').'>';
        }
        return '<div class="field"><label>'.e($label).'</label>'.$control.'</div>';
    };
    $opts = fn (...$values) => array_map(fn ($v) => [$v, $v], $values);
    $notAssigned = fn ($list) => array_merge([['', 'Not assigned']], $list);
    $forms = [
        'inquiry' => $field('name', 'Full Name').$field('phone', 'Mobile').$field('course', 'Interested Course', 'text', $courseOpts).$field('status', 'Status', 'text', $opts(...App\Support\Erp::INQUIRY_STAGES)),
        'student' => $field('name', 'Student Name').$field('phone', 'Mobile Number').$field('course', 'Course', 'text', $courseOpts).$field('batch', 'Batch', 'text', $notAssigned($batchOpts), false),
        'batch' => $field('course', 'Course', 'text', $courseOpts).$field('trainer', 'Trainer', 'text', $notAssigned($trainerOpts)).$field('start', 'Start Date', 'date').$field('time', 'Start Time', 'time').$field('days', 'Training Days (e.g. Sat, Mon, Wed)').$field('room', 'Room / Lab', 'text', $labOpts).$field('capacity', 'Seat Capacity', 'number').$field('status', 'Status', 'text', $opts('Upcoming', 'Ongoing', 'Completed')),
        'trainer' => $field('name', 'Trainer Name').$field('specialty', 'Specialty').$field('phone', 'Phone').$field('rate', 'Hourly Rate (BDT)', 'number'),
        // Batch-wise attendance (Take Attendance): batch + date, then a Present / Absent row per student of the batch
        'attendancebatch' => str_replace('<select name="batch" required>', '<select name="batch" required onchange="loadBatchStudents()"><option value="">Select a batch</option>', $field('batch', 'Batch', 'text', $batchOpts))
            .str_replace('<input name="date"', '<input name="date" onchange="loadBatchStudents()"', $field('date', 'Class Date', 'date'))
            .'<div class="field full"><div id="batchStudents" class="attlist"><div class="muted">Select a batch to list its students</div></div></div>',
        'attendance' => $field('student', 'Student', 'text', $studentOpts).$field('batch', 'Batch', 'text', $notAssigned($batchOpts)).$field('date', 'Class Date', 'date').$field('status', 'Attendance', 'text', $opts('Present', 'Absent')),
        'payment' => $field('student', 'Student', 'text', $studentOpts).str_replace('<select name="course" required>', '<select name="course" required data-cert-course>', $field('course', 'Course', 'text', array_merge([['', 'No course for this student']], $courseOpts))).$field('amount', 'Amount Received (BDT)', 'number').$field('date', 'Payment Date', 'date').$field('method', 'Payment Method', 'text', $opts(...App\Http\Controllers\Cms\ErpController::PAYMENT_METHODS)),
        'expense' => $field('category', 'Expense Category', 'text', $opts(...App\Http\Controllers\Cms\ErpController::EXPENSE_CATEGORIES)).$field('description', 'Description').$field('amount', 'Amount (BDT)', 'number').$field('date', 'Expense Date', 'date'),
        'lab' => $field('name', 'Lab / Facility Name').$field('equipment', 'Equipment / Machines').$field('status', 'Status', 'text', $opts('Available', 'Maintenance', 'Unavailable')),
        'category' => str_replace('class="field"', 'class="field full"', $field('label', 'Category Name (course card label)')).$field('filter', 'Website Filter Button (empty = same as name)', 'text', null, false)
            .'<div class="field"><label>Colour</label><input name="color" type="color" value="#287dcc" required></div>',
        'course' => str_replace('class="field"', 'class="field full"', $field('title', 'Course / Program Title')).$field('category', 'Category', 'text', $categoryOpts).$field('duration', 'Duration (e.g. 30 Hours)', 'text', null, false).$field('fee', 'Fee (BDT, empty = On Inquiry)', 'number', null, false)
            .'<div class="field full"><label>Short Description</label><textarea name="description" rows="2" maxlength="1000"></textarea></div>'
            .'<div class="field full"><label>Course Modules (one per line)</label><textarea name="modules" rows="4" maxlength="3000"></textarea></div>'
            .'<div class="field"><label>Course Image (empty = keep current)</label><input name="image" type="file" accept="image/*"></div>'.$field('alt', 'Image Alt Text', 'text', null, false),
        'certificate' => $field('student', 'Student', 'text', $studentOpts).str_replace('<select name="course" required>', '<select name="course" required data-cert-course>', $field('course', 'Course', 'text', array_merge([['', 'No course for this student']], $courseOpts))).$field('issued', 'Issue Date', 'date').$field('grade', 'Result / Grade', 'text', $opts(...App\Models\Erp\Certificate::GRADES))
            .str_replace('min="0"', 'min="0" max="5" step="0.01" placeholder="Optional, e.g. 3.75"', $field('gpa', 'GPA Point (optional)', 'number', null, false))
            .str_replace('min="0"', 'min="0" max="5" step="0.01" placeholder="Optional, e.g. 3.80"', $field('cgpa', 'CGPA Point (optional)', 'number', null, false)),
    ];
@endphp
@foreach($forms as $type => $html)<template id="tpl-{{ $type }}">{!! $html !!}</template>@endforeach
<script>window.CMS={store:@json(route('erp.store', '__TYPE__')),update:@json(route('erp.update', ['__TYPE__', '__ID__'])),students:{{ count($studentOpts) }}}</script>
<script>
// Certificate and Payment forms: the Course list shows only the courses the chosen student is enrolled in
window.CERT_COURSES = @json((object) $studentCourses);
(function () {
    function filterCourses(form) {
        var student = form.elements.student, course = form.querySelector('select[data-cert-course]');
        if (!student || !course) return;
        var allowed = (window.CERT_COURSES[student.value] || []).map(String), first = null;
        Array.prototype.forEach.call(course.options, function (option) {
            var show = option.value === '' ? allowed.length === 0 : allowed.indexOf(option.value) !== -1;
            option.hidden = !show;
            option.disabled = !show || option.value === '';
            if (show && option.value !== '' && !first) first = option;
        });
        if (allowed.indexOf(course.value) === -1) course.value = first ? first.value : '';
    }
    document.addEventListener('change', function (e) {
        if (e.target.name === 'student' && e.target.form) filterCourses(e.target.form);
    });
    // After the Add / Edit pop-up is filled in
    function wrap(name) {
        var original = window[name];
        if (typeof original !== 'function' || original.certWrapped) return;
        window[name] = function () {
            var result = original.apply(this, arguments);
            var form = document.getElementById('modalForm');
            if (form) filterCourses(form);
            return result;
        };
        window[name].certWrapped = true;
    }
    function wrapAll() { wrap('openCreate'); wrap('openEdit'); }
    wrapAll();
    document.addEventListener('DOMContentLoaded', wrapAll);
})();
</script>
<script>
// "Add Course" on a student row: enroll the same student in another course (same name, mobile and website account).
// A student without a course yet gets the course on that same record.
function addStudentCourse(id, record) {
    if (!record.course) {
        openEdit('student', id, record);
        document.getElementById('modalTitle').textContent = 'Add Course • ' + record.name;
        return;
    }
    var form = modalOpen('Add Course • ' + record.name, 'student', CMS.store.replace('__TYPE__', 'student'));
    if (!form) return;
    form.elements.name.value = record.name;
    form.elements.phone.value = record.phone || '';
    form.elements.name.readOnly = form.elements.phone.readOnly = true;
    var from = document.createElement('input');
    from.type = 'hidden'; from.name = 'from'; from.value = id;
    document.getElementById('modalFields').appendChild(from); // replaced when the pop-up opens again
    // Courses this student already has are not offered again
    var taken = (window.CERT_COURSES[id] || []).map(String), first = null;
    Array.prototype.forEach.call(form.elements.course.options, function (option) {
        var show = taken.indexOf(option.value) === -1;
        option.hidden = !show; option.disabled = !show;
        if (show && !first) first = option;
    });
    form.elements.course.value = first ? first.value : '';
    form.elements.batch.value = '';
}
</script>
@include('cms.erp.partials.select')
