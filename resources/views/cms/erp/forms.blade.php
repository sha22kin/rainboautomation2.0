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
        'payment' => $field('student', 'Student', 'text', $studentOpts).$field('amount', 'Amount Received (BDT)', 'number').$field('date', 'Payment Date', 'date').$field('method', 'Payment Method', 'text', $opts(...App\Http\Controllers\Cms\ErpController::PAYMENT_METHODS)),
        'expense' => $field('category', 'Expense Category', 'text', $opts(...App\Http\Controllers\Cms\ErpController::EXPENSE_CATEGORIES)).$field('description', 'Description').$field('amount', 'Amount (BDT)', 'number').$field('date', 'Expense Date', 'date'),
        'lab' => $field('name', 'Lab / Facility Name').$field('equipment', 'Equipment / Machines').$field('status', 'Status', 'text', $opts('Available', 'Maintenance', 'Unavailable')),
        'category' => str_replace('class="field"', 'class="field full"', $field('label', 'Category Name (course card label)')).$field('filter', 'Website Filter Button (empty = same as name)', 'text', null, false)
            .'<div class="field"><label>Colour</label><input name="color" type="color" value="#287dcc" required></div>',
        'course' => str_replace('class="field"', 'class="field full"', $field('title', 'Course / Program Title')).$field('category', 'Category', 'text', $categoryOpts).$field('duration', 'Duration (e.g. 30 Hours)', 'text', null, false).$field('fee', 'Fee (BDT, empty = On Inquiry)', 'number', null, false)
            .'<div class="field full"><label>Short Description</label><textarea name="description" rows="2" maxlength="1000"></textarea></div>'
            .'<div class="field full"><label>Course Modules (one per line)</label><textarea name="modules" rows="4" maxlength="3000"></textarea></div>'
            .'<div class="field"><label>Course Image (empty = keep current)</label><input name="image" type="file" accept="image/*"></div>'.$field('alt', 'Image Alt Text', 'text', null, false),
        'certificate' => $field('student', 'Student', 'text', $studentOpts).$field('course', 'Course', 'text', $courseOpts).$field('issued', 'Issue Date', 'date').$field('grade', 'Result / Grade', 'text', $opts(...App\Models\Erp\Certificate::GRADES))
            .str_replace('min="0"', 'min="0" max="5" step="0.01" placeholder="Optional, e.g. 3.75"', $field('gpa', 'GPA Point (optional)', 'number', null, false))
            .str_replace('min="0"', 'min="0" max="5" step="0.01" placeholder="Optional, e.g. 3.80"', $field('cgpa', 'CGPA Point (optional)', 'number', null, false)),
    ];
@endphp
@foreach($forms as $type => $html)<template id="tpl-{{ $type }}">{!! $html !!}</template>@endforeach
<script>window.CMS={store:@json(route('erp.store', '__TYPE__')),update:@json(route('erp.update', ['__TYPE__', '__ID__'])),students:{{ count($studentOpts) }}}</script>
