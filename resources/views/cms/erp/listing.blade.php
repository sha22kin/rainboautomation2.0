@extends('cms.layouts.app', ['erpPage' => $kind])
@section('screen')
@php
    use App\Support\Erp;
    $type = $cfg[2];
    $btn = fn ($label, $onclick, $cls = '') => '<button class="btn '.$cls.'" onclick="'.e($onclick).'">'.$label.'</button>';
    $date = fn ($d) => e($d?->format('Y-m-d'));
    // Small line icons of the row action buttons (the label stays as tooltip / screen reader text)
    $icon = function ($name) {
        $paths = [
            'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
            'delete' => '<path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>',
            'update' => '<path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/>',
            'website' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18a14 14 0 0 1 0-18"/>',
            'batch' => '<rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/>',
            'print' => '<path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>',
            'receipt' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 13h8M8 17h5"/>',
        ];
        return '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$paths[$name].'</svg>';
    };
    $iconBtn = fn ($name, $label, $cls, $attrs = '') => '<button class="btn small iconbtn '.$cls.'" title="'.$label.'" aria-label="'.$label.'" '.$attrs.'>'.$icon($name).'</button>';
    // Edit + Delete buttons of a record (Edit opens the design modal filled with the record)
    $actions = function ($id, $record, $extra = '') use ($type, $iconBtn) {
        $edit = $iconBtn('edit', 'Edit', 'alt', 'onclick="'.e('openEdit('.json_encode($type).','.json_encode($id).','.json_encode($record, JSON_HEX_APOS | JSON_HEX_QUOT).')').'"');
        $delete = '<form method="post" action="'.route('erp.destroy', [$type, $id]).'" onsubmit="return confirm(\'Delete this record?\')">'.csrf_field().$iconBtn('delete', 'Delete', 'danger').'</form>';
        return '<div class="rowactions">'.$extra.$edit.$delete.'</div>';
    };
    $search = fn (...$parts) => mb_strtolower(implode(' ', array_map('strval', $parts)));

    $headers = [];
    $rows = [];
    switch($kind){
        case 'inquiries':
            $headers = ['ID', 'Name', 'Phone', 'Course', 'Status', 'Action'];
            foreach($records as $x){
                $rows[] = ['search' => $search(Erp::inquiryCode($x), $x->name, $x->inquiry('phone'), Erp::courseName($x->src_id), $x->inquiry('stage'), $x->created_at?->format('Y-m-d')), 'cells' => [
                    e(Erp::inquiryCode($x)), e($x->name), e($x->inquiry('phone')), e(Erp::courseName($x->src_id)), Erp::badge($x->inquiry('stage')),
                    $actions($x->id, ['name' => $x->name, 'phone' => $x->inquiry('phone'), 'course' => $x->src_id, 'status' => $x->inquiry('stage')],
                        '<form method="post" action="'.route('erp.inquiryStatus', $x->id).'">'.csrf_field().$iconBtn('update', 'Update status', 'alt').'</form>'),
                ]];
            }
            break;
        case 'courses':
            $headers = ['Course / Program', 'Category', 'Duration', 'Fee', 'Website', 'Course ID', 'Action'];
            foreach($records as $x){
                $color = $x->courseCategory()['color'];
                $live = $x->status == 'active';
                $batchCount = count($courseBatches[$x->id] ?? []);
                $batchBtn = '<button class="btn small iconbtn alt hascount" title="Batches" aria-label="Batches" onclick="'.e('courseBatches('.$x->id.','.json_encode($x->course('title')).')').'">'.$icon('batch').'<span class="iconcount">'.$batchCount.'</span></button>';
                $webBtn = '<form method="post" action="'.route('erp.courseWebsite', $x->id).'">'.csrf_field().$iconBtn('website', $live ? 'Remove from website' : 'Show on website', $live ? 'live' : 'alt').'</form>';
                $rows[] = ['search' => $search($x->course('title'), Erp::courseCategory($x), Erp::courseHours($x), Erp::courseFee($x), Erp::courseCode($x), $x->status), 'cells' => [
                    '<span class="catdot" style="background:'.e($color).'"></span>'.e($x->course('title')), e(Erp::courseCategory($x)), e($x->course('duration') ?: '—'), is_numeric($x->course('fee')) ? Erp::fmt($x->course('fee')) : '<span class="muted">On Inquiry</span>', $live ? '<span class="webstate live">Live</span>' : '<span class="webstate">Not published</span>', e(Erp::courseCode($x)),
                    $actions($x->id, ['title' => $x->course('title'), 'category' => $x->course('category'), 'duration' => $x->course('duration'), 'fee' => $x->course('fee'), 'description' => $x->course('description'), 'modules' => implode("\n", $x->course('modules')), 'alt' => $x->course('alt')], $batchBtn.$webBtn),
                ]];
            }
            break;
        case 'categories':
            $headers = ['Category', 'Website Filter Button', 'Colour', 'Courses', 'Action'];
            foreach($records as $x){
                $edit = ['label' => $x->label, 'filter' => $x->filter, 'color' => $x->color];
                $rows[] = ['search' => $search($x->label, $x->filter, $x->color), 'cells' => [
                    '<span class="catdot" style="background:'.e($x->color).'"></span>'.e($x->label), e($x->filter), '<span class="colorchip" style="background:'.e($x->color).'"></span>'.e($x->color), '<b>'.$x->courses.'</b>',
                    // Delete of a category that still has courses is refused by the server with a message
                    $actions($x->key, $edit),
                ]];
            }
            break;
        case 'students':
            $headers = ['ID', 'Name', 'Phone', 'Enrolled Course', 'Batch', 'Remaining Due', 'Action'];
            foreach($records as $x){
                $rows[] = ['search' => $search($x->code, $x->name, $x->phone, Erp::courseName($x->course_id), $x->batch?->code, $x->admitted_on?->format('Y-m-d')), 'cells' => [
                    e($x->code), e($x->name), e($x->phone), e(Erp::courseName($x->course_id)), e($x->batch?->code ?: 'Not assigned'), '<b>'.Erp::fmt($x->due()).'</b>',
                    $actions($x->id, ['name' => $x->name, 'phone' => $x->phone, 'course' => $x->course_id, 'batch' => $x->batch_id]),
                ]];
            }
            break;
        case 'batches':
            $headers = ['Batch', 'Course', 'Start', 'Days & Time', 'Trainer', 'Room', 'Status', 'Action'];
            foreach($records as $x){
                $rows[] = ['search' => $search($x->code, Erp::courseName($x->course_id), $x->start?->format('Y-m-d'), $x->days, $x->time, $x->trainer?->name, $x->room, $x->status), 'cells' => [
                    e($x->code), e(Erp::courseName($x->course_id)), $date($x->start), e($x->days).' • '.e($x->time), e($x->trainer?->name ?: '—'), e($x->room), Erp::badge($x->status),
                    $actions($x->id, ['course' => $x->course_id, 'trainer' => $x->trainer_id, 'start' => $x->start?->format('Y-m-d'), 'time' => $x->time, 'days' => $x->days, 'room' => $x->room, 'capacity' => $x->capacity, 'status' => $x->status]),
                ]];
            }
            break;
        case 'trainers':
            $headers = ['Name', 'Specialty', 'Phone', 'Hourly Rate', 'ID', 'Action'];
            foreach($records as $x){
                $rows[] = ['search' => $search($x->name, $x->specialty, $x->phone, $x->rate, $x->code), 'cells' => [
                    e($x->name), e($x->specialty), e($x->phone), Erp::fmt($x->rate), e($x->code),
                    $actions($x->id, ['name' => $x->name, 'specialty' => $x->specialty, 'phone' => $x->phone, 'rate' => $x->rate]),
                ]];
            }
            break;
        case 'attendance':
            $headers = ['Student', 'Batch', 'Date', 'Status', 'Action'];
            foreach($records as $x){
                $rows[] = ['search' => $search($x->student?->name, $x->batch?->code, $x->date?->format('Y-m-d'), $x->status), 'cells' => [
                    e($x->student?->name ?: '—'), e($x->batch?->code), $date($x->date), Erp::badge($x->status),
                    $actions($x->id, ['student' => $x->student_id, 'batch' => $x->batch_id, 'date' => $x->date?->format('Y-m-d'), 'status' => $x->status]),
                ]];
            }
            break;
        case 'fees':
            $headers = ['Receipt', 'Student', 'Date', 'Method', 'Amount', 'Action'];
            foreach($records as $x){
                $receipt = $iconBtn('receipt', 'Receipt', 'alt', 'onclick="receipt(this)" data-id="'.e($x->code).'" data-student="'.e($x->student?->name ?: '—').'" data-date="'.$date($x->date).'" data-method="'.e($x->method).'" data-amount="'.e(Erp::fmt($x->amount)).'"');
                $rows[] = ['search' => $search($x->code, $x->student?->name, $x->date?->format('Y-m-d'), $x->method, $x->amount), 'cells' => [
                    e($x->code), e($x->student?->name ?: '—'), $date($x->date), e($x->method), '<b>'.Erp::fmt($x->amount).'</b>',
                    $actions($x->id, ['student' => $x->student_id, 'amount' => $x->amount, 'date' => $x->date?->format('Y-m-d'), 'method' => $x->method], $receipt),
                ]];
            }
            break;
        case 'expenses':
            $headers = ['Voucher', 'Category', 'Description', 'Date', 'Amount', 'Action'];
            foreach($records as $x){
                $rows[] = ['search' => $search($x->code, $x->category, $x->description, $x->date?->format('Y-m-d'), $x->amount), 'cells' => [
                    e($x->code), e($x->category), e($x->description), $date($x->date), Erp::fmt($x->amount),
                    $actions($x->id, ['category' => $x->category, 'description' => $x->description, 'amount' => $x->amount, 'date' => $x->date?->format('Y-m-d')]),
                ]];
            }
            break;
        case 'labs':
            $headers = ['Facility', 'Equipment', 'Status', 'ID', 'Action'];
            foreach($records as $x){
                $rows[] = ['search' => $search($x->name, $x->equipment, $x->status, $x->code), 'cells' => [
                    e($x->name), e($x->equipment), Erp::badge($x->status), e($x->code),
                    $actions($x->id, ['name' => $x->name, 'equipment' => $x->equipment, 'status' => $x->status]),
                ]];
            }
            break;
        case 'certificates':
            $headers = ['Certificate', 'Student', 'Course', 'Grade', 'Issued', 'Action'];
            foreach($records as $x){
                $rows[] = ['search' => $search($x->code, $x->student?->name, Erp::courseName($x->course_id), $x->grade, $x->issued?->format('Y-m-d')), 'cells' => [
                    e($x->code), e($x->student?->name ?: '—'), e(Erp::courseName($x->course_id)), '<b>'.e($x->grade).'</b>'.($x->points() ? ' <span class="muted">· '.e($x->points()).'</span>' : ''), $date($x->issued),
                    $actions($x->id, ['student' => $x->student_id, 'course' => $x->course_id, 'issued' => $x->issued?->format('Y-m-d'), 'grade' => $x->grade, 'gpa' => $x->gpa, 'cgpa' => $x->cgpa],
                        $iconBtn('print', 'Print certificate', 'alt', 'onclick="'.e("window.open('".route('erp.certificatePrint', $x->id)."')").'"')),
                ]];
            }
            break;
    }

    $addLabel = ['courses' => 'Course', 'categories' => 'Category', 'students' => 'Student', 'inquiries' => 'Inquiry', 'batches' => 'Batch', 'trainers' => 'Trainer', 'attendance' => 'Attendance', 'fees' => 'Payment', 'expenses' => 'Expense', 'labs' => 'Lab', 'certificates' => 'Certificate'];
    $export = "location.href='".route('erp.export', $kind)."'";
    $action = $kind == 'attendance' ? $btn('+ Take Attendance', 'openBatchAttendance()') : $btn('+ Add '.$addLabel[$kind], "openCreate('".$type."')");
@endphp
@include('cms.erp.partials.title', ['t' => $cfg[0], 'description' => $cfg[1], 'button' => $action])
@if($kind == 'fees')
<div class="stats">@include('cms.erp.partials.stat', ['name' => 'Collected Fees', 'val' => Erp::fmt($gross), 'icon' => '৳', 'note' => 'Payments received'])@include('cms.erp.partials.stat', ['name' => 'Unpaid Fee Balance', 'val' => Erp::fmt($totalDue), 'icon' => '◉', 'note' => 'All enrolled students'])@include('cms.erp.partials.stat', ['name' => 'Payments Logged', 'val' => $paymentCount, 'icon' => '▤', 'note' => 'Transactions'])@include('cms.erp.partials.stat', ['name' => 'Students', 'val' => $studentCount, 'icon' => '♙', 'note' => 'Enrolled'])</div>
@elseif($kind == 'expenses')
<div class="stats">@include('cms.erp.partials.stat', ['name' => 'Recorded Expenses', 'val' => Erp::fmt($costs), 'icon' => '↗', 'note' => 'Operating cash outflow'])@include('cms.erp.partials.stat', ['name' => 'Fee Income', 'val' => Erp::fmt($gross), 'icon' => '৳', 'note' => 'Total receipts'])@include('cms.erp.partials.stat', ['name' => 'Cash Balance', 'val' => Erp::fmt($gross - $costs), 'icon' => '◫', 'note' => 'Income less expenses'])@include('cms.erp.partials.stat', ['name' => 'Expense Entries', 'val' => $expenseCount, 'icon' => '▤', 'note' => 'Vouchers'])</div>
@endif
<div class="panel"><div class="toolbar"><h2>Records (<span id="recordCount">{{ count($rows) }}</span>)</h2><div class="actions">@if($kind == 'attendance')<select class="filter" id="batchFilter" onchange="filterListing(document.getElementById('listingSearch'))"><option value="">All batches</option>@foreach($batchOpts as [$bid, $btext])<option value="{{ mb_strtolower(explode(' — ', $btext)[0]) }}">{{ $btext }}</option>@endforeach</select>@endif<input class="filter" id="listingSearch" placeholder="Search records..." oninput="filterListing(this)"><button class="btn alt small" onclick="{{ $export }}">↓ Export CSV</button><button class="btn alt small" onclick="location.href='{{ route('erp.exportExcel', $kind) }}'">↓ Excel</button><button class="btn alt small" onclick="window.open('{{ route('erp.print', $kind) }}')">⎙ Print</button></div></div><div id="listingData">@include('cms.erp.partials.table', ['headers' => $headers, 'rows' => $rows])</div></div>
@if($kind == 'batches')
{{-- Weekly Training Plan: + adds a batch on that day, a class opens its batch for editing, × takes the batch off that day --}}
<div class="panel"><h2>Weekly Training Plan</h2><div class="schedule">@foreach(['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $d)@php $short = substr($d, 0, 3); $events = $records->filter(fn ($b) => str_contains(strtolower($b->days), strtolower($short))); @endphp<div class="day"><div class="dayhead"><strong>{{ $d }}</strong><button type="button" class="dayadd" title="Add batch on {{ $d }}" aria-label="Add batch on {{ $d }}" onclick="addBatchOn('{{ $short }}')">+</button></div>@forelse($events as $b)<div class="event"><button type="button" class="event-edit" title="Edit {{ $b->code }}" onclick="openEdit('batch',{{ $b->id }},{{ json_encode(['course' => $b->course_id, 'trainer' => $b->trainer_id, 'start' => $b->start?->format('Y-m-d'), 'time' => $b->time, 'days' => $b->days, 'room' => $b->room, 'capacity' => $b->capacity, 'status' => $b->status], JSON_HEX_APOS | JSON_HEX_QUOT) }})"><b>{{ $b->time }}</b> · {{ $b->code }}<br>{{ Str::substr(Erp::courseName($b->course_id), 0, 45) }}</button><form method="post" action="{{ route('erp.batchRemoveDay', $b->id) }}" onsubmit="return confirm('Remove {{ $b->code }} from {{ $d }}?')">@csrf<input type="hidden" name="day" value="{{ $short }}"><button class="event-del" title="Remove from {{ $d }}" aria-label="Remove from {{ $d }}">×</button></form></div>@empty<span class="muted">No planned class</span>@endforelse</div>@endforeach</div></div>
@endif
@if($kind == 'courses')
{{-- Batches of each course (opened by the batch icon); add / edit / delete come back to this popup --}}
@foreach($records as $x)<template id="cb-{{ $x->id }}"><div class="field full">
<div class="toolbar"><span class="muted">{{ count($courseBatches[$x->id] ?? []) }} batch(es)</span><button type="button" class="btn small" onclick="addCourseBatch({{ $x->id }})">+ Add Batch</button></div>
<div class="tablewrap"><table><thead><tr><th>Batch</th><th>Start</th><th>Days & Time</th><th>Trainer</th><th>Room</th><th>Seats</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($courseBatches[$x->id] ?? [] as $b)
<tr><td>{{ $b->code }}</td><td>{{ $b->start?->format('Y-m-d') }}</td><td>{{ $b->days }} • {{ $b->time }}</td><td>{{ $b->trainer?->name ?: '—' }}</td><td>{{ $b->room }}</td><td>{{ $b->capacity }}</td><td>{!! Erp::badge($b->status) !!}</td>
<td><div class="rowactions">{!! $iconBtn('edit', 'Edit', 'alt', 'type="button" onclick="'.e('editCourseBatch('.$b->id.','.$x->id.','.json_encode(['course' => $b->course_id, 'trainer' => $b->trainer_id, 'start' => $b->start?->format('Y-m-d'), 'time' => $b->time, 'days' => $b->days, 'room' => $b->room, 'capacity' => $b->capacity, 'status' => $b->status], JSON_HEX_APOS | JSON_HEX_QUOT).')').'"') !!}<form method="post" action="{{ route('erp.destroy', ['batch', $b->id]) }}" onsubmit="return confirm('Delete this batch?')">@csrf<input type="hidden" name="back" value="{{ $x->id }}">{!! $iconBtn('delete', 'Delete', 'danger') !!}</form></div></td></tr>
@empty
<tr><td colspan="8" class="empty">No batch for this course yet</td></tr>
@endforelse
</tbody></table></div></div></template>@endforeach
@if(request('batches') && $records->firstWhere('id', request('batches')))<script>document.addEventListener('DOMContentLoaded',()=>courseBatches({{ (int) request('batches') }},{!! json_encode($records->firstWhere('id', request('batches'))->course('title')) !!}))</script>@endif
@endif
@if($kind == 'attendance')<script>window.ATT={students:@json($batchStudents),marks:@json($attendanceMarks)}</script>@endif
@include('cms.erp.forms')
@endsection
