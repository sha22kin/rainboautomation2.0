<?php

namespace Tests\Feature;

use App\Models\Erp\Batch;
use App\Models\Erp\Payment;
use App\Models\Erp\Student;
use App\Models\Erp\Trainer;
use App\Models\Permission;
use App\Models\PostExtra;
use App\Models\User;
use App\Support\Erp;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

// Runs against the configured (shared) database inside a transaction that is rolled back.
class CmsTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $role = Permission::where('permission', 'like', '%appsSetting%')->first();
        $user = new User(['name' => 'CMS Test Admin', 'email' => 'cms-test-admin@example.com', 'password' => Hash::make('secret-pass')]);
        $user->admin = 1;
        $user->status = 1;
        $user->permission_id = $role?->id;
        $user->save();
        return $user;
    }

    private function course(): PostExtra
    {
        $course = Erp::courses()->first();
        $this->assertNotNull($course, 'The website has no training course');
        return $course;
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin-login');
        $this->get('/admin/website/posts')->assertRedirect('/admin-login');
        $this->get('/admin-login')->assertOk()->assertSee('Sign in');
    }

    public function test_admin_can_sign_in_and_customer_cannot(): void
    {
        $this->admin();
        $this->post('/admin-login', ['username' => 'cms-test-admin@example.com', 'password' => 'secret-pass'])->assertRedirect('/admin');
        $this->assertAuthenticated();

        auth()->logout();
        $customer = new User(['name' => 'CMS Test Customer', 'email' => 'cms-test-customer@example.com', 'password' => Hash::make('secret-pass')]);
        $customer->save();
        $this->post('/admin-login', ['username' => 'cms-test-customer@example.com', 'password' => 'secret-pass'])->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_every_erp_page_renders(): void
    {
        $this->actingAs($this->admin());
        foreach (['/admin', '/admin/inquiries', '/admin/courses', '/admin/students', '/admin/batches', '/admin/trainers', '/admin/attendance', '/admin/fees', '/admin/expenses', '/admin/labs', '/admin/certificates', '/admin/reports', '/admin/settings'] as $uri) {
            $this->get($uri)->assertOk()->assertSee('RAINBOW', false)->assertSee('Website');
        }
    }

    public function test_every_website_page_renders_inside_the_cms(): void
    {
        $this->actingAs($this->admin());
        foreach (['dashboard', 'my-profile', 'medies', 'pages', 'contact-locations', 'industrial-capabilities', 'solar-solution', 'services-page', 'training-courses', 'training-page', 'training-inquiries', 'posts', 'posts/categories', 'posts/tags', 'posts/comments/all', 'services', 'services/categories', 'clients', 'brands', 'sliders', 'galleries', 'theme-setting', 'menus', 'users/admin', 'users/customer', 'users/roles', 'subscribes', 'setting/general', 'setting/mail', 'setting/sms', 'setting/social'] as $page) {
            $this->get('/admin/website/'.$page)->assertOk()->assertSee('cms-website.css', false)->assertSee('id="websiteMenu"', false);
        }
    }

    public function test_student_admission_keeps_the_course_fee_and_tracks_dues(): void
    {
        $this->actingAs($this->admin());
        $course = $this->course();

        $this->post('/admin/records/student', ['name' => 'Test Student', 'phone' => '01700000099', 'course' => $course->id, 'batch' => ''])
            ->assertRedirect(route('erp.listing', 'students'))->assertSessionHas('toast', 'Record saved successfully');
        $student = Student::where('name', 'Test Student')->firstOrFail();
        $this->assertEquals(Erp::courseFee($course), $student->fee);

        $this->post('/admin/records/payment', ['student' => $student->id, 'amount' => 0, 'date' => '2026-10-17', 'method' => 'Cash'])
            ->assertSessionHas('toast', 'Amount must be greater than zero');
        $this->post('/admin/records/payment', ['student' => $student->id, 'amount' => 1000, 'date' => '2026-10-17', 'method' => 'bKash'])
            ->assertRedirect(route('erp.listing', 'fees'));
        $this->assertEquals(max(0, Erp::courseFee($course) - 1000), $student->fresh()->load('payments')->due());

        // A student with receipts is kept
        $this->post('/admin/records/student/'.$student->id.'/delete')->assertSessionHas('toast');
        $this->assertNotNull($student->fresh());

        $payment = Payment::where('student_id', $student->id)->firstOrFail();
        $this->post('/admin/records/payment/'.$payment->id, ['student' => $student->id, 'amount' => 1500, 'date' => '2026-10-17', 'method' => 'Bank']);
        $this->assertEquals(1500, $payment->fresh()->amount);
        $this->post('/admin/records/payment/'.$payment->id.'/delete');
        $this->assertNull($payment->fresh());
    }

    public function test_batches_trainers_and_other_records_can_be_saved(): void
    {
        $this->actingAs($this->admin());
        $course = $this->course();

        $this->post('/admin/records/trainer', ['name' => 'Test Trainer', 'specialty' => 'PLC', 'phone' => '017', 'rate' => 1000])->assertSessionHas('toast', 'Record saved successfully');
        $trainer = Trainer::where('name', 'Test Trainer')->firstOrFail();
        $this->post('/admin/records/lab', ['name' => 'Test Lab', 'equipment' => 'PLC', 'status' => 'Available'])->assertSessionHas('toast', 'Record saved successfully');
        $this->post('/admin/records/batch', ['course' => $course->id, 'trainer' => $trainer->id, 'start' => '2026-11-01', 'time' => '10:00', 'days' => 'Sat, Mon', 'room' => 'Test Lab', 'capacity' => 12, 'status' => 'Upcoming'])
            ->assertSessionHas('toast', 'Record saved successfully');
        $batch = Batch::where('trainer_id', $trainer->id)->firstOrFail();
        $this->get('/admin/batches')->assertSee($batch->code);

        $this->post('/admin/records/expense', ['category' => 'Marketing', 'description' => 'Test ad', 'amount' => 500, 'date' => '2026-10-17'])->assertSessionHas('toast', 'Record saved successfully');
        $this->post('/admin/records/student', ['name' => 'Test Student 2', 'phone' => '018', 'course' => $course->id, 'batch' => $batch->id]);
        $student = Student::where('name', 'Test Student 2')->firstOrFail();
        $this->post('/admin/records/attendance', ['student' => $student->id, 'batch' => $batch->id, 'date' => '2026-10-17', 'status' => 'Present'])->assertSessionHas('toast', 'Record saved successfully');
        $this->post('/admin/records/certificate', ['student' => $student->id, 'course' => $course->id, 'issued' => '2026-10-17', 'grade' => 'A'])->assertSessionHas('toast', 'Record saved successfully');

        // Removing a trainer leaves the batch without a trainer
        $this->post('/admin/records/trainer/'.$trainer->id.'/delete');
        $this->assertNull($batch->fresh()->trainer_id);
    }

    public function test_website_inquiries_follow_up(): void
    {
        $this->actingAs($this->admin());
        $course = $this->course();

        $this->post('/admin/records/inquiry', ['name' => 'Test Lead', 'phone' => '019', 'course' => $course->id, 'status' => 'New'])->assertSessionHas('toast', 'Record saved successfully');
        $inquiry = PostExtra::where('type', 5)->where('name', 'Test Lead')->firstOrFail();
        $this->assertEquals('unread', $inquiry->status);
        $this->assertEquals($course->id, $inquiry->src_id);

        $this->post('/admin/inquiries/'.$inquiry->id.'/status')->assertSessionHas('toast', 'Inquiry status: Follow-up');
        $this->post('/admin/inquiries/'.$inquiry->id.'/status')->assertSessionHas('toast', 'Inquiry status: Converted');
        $this->assertEquals('Converted', $inquiry->fresh()->inquiry('stage'));
        $this->get('/admin/website/training-inquiries')->assertOk()->assertSee('Test Lead');
    }

    public function test_csv_exports(): void
    {
        $this->actingAs($this->admin());
        foreach (['dues', 'inquiries', 'courses', 'students', 'batches', 'trainers', 'attendance', 'fees', 'expenses', 'labs', 'certificates'] as $kind) {
            $this->get('/admin/export/'.$kind)->assertOk()->assertHeader('Content-Disposition', 'attachment; filename="rainbow-'.$kind.'.csv"');
        }
        $this->get('/admin/export/unknown')->assertNotFound();
    }

    public function test_settings_are_saved(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/settings', ['branch' => 'Test Branch'])->assertSessionHas('toast', 'Settings saved');
        $this->get('/admin')->assertSee('Test Branch');
    }

    public function test_home_why_choose_us_items_can_be_added_edited_and_deleted(): void
    {
        $this->actingAs($this->admin());
        $this->get('/')->assertSee('Logo-Matched Theme');
        $this->get('/admin/website/home-why-choose-us')->assertOk()->assertSee('Logo-Matched Theme');
        $this->get('/admin/website/home-why-choose-us/create')->assertOk();

        $this->post('/admin/website/home-why-choose-us/create', ['title' => 'Test Why Item', 'text' => 'Test text', 'color' => '#111111', 'color2' => '#222222'])
            ->assertRedirect('/admin/website/home-why-choose-us');
        $this->get('/')->assertSee('Test Why Item')->assertSee('05');

        $this->get('/admin/website/home-why-choose-us/edit/4')->assertOk()->assertSee('Test Why Item');
        $this->post('/admin/website/home-why-choose-us/edit/4', ['title' => 'Edited Why Item', 'text' => '', 'color' => '#111111', 'color2' => '#222222']);
        $this->get('/admin/website/home-why-choose-us/up/4');
        $this->assertSame('Edited Why Item', homeWhyContent()['items'][3]['title']);

        $this->get('/admin/website/home-why-choose-us/delete/3');
        $this->get('/')->assertDontSee('Edited Why Item');
        $this->assertCount(4, homeWhyContent()['items']);

        $this->post('/admin/website/home-why-choose-us', ['eyebrow' => 'Test Eyebrow', 'title' => 'Test Why Title', 'subtitle' => '']);
        $this->get('/')->assertSee('Test Why Title')->assertSee('Test Eyebrow');
    }

    public function test_new_cms_text_can_be_translated_for_the_bangla_website(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/website/home-why-choose-us/create', ['title' => 'Test Brand New Item', 'text' => '', 'color' => '#111111', 'color2' => '#222222']);
        $this->get('/admin/website/translations')->assertOk()->assertSee('Scan Website');

        // The scan (run in the browser) sends the texts without Bangla; built-in ones are ignored
        $this->postJson('/admin/website/translations/scan', ['texts' => ['Test Brand New Item', 'Home', 'Test Brand New Item']])
            ->assertOk()->assertJsonPath('success', true);
        $row = PostExtra::where('type', 14)->get()->first(fn ($r) => json_decode($r->content, true)['en'] === 'Test Brand New Item');
        $this->assertNotNull($row);
        $this->assertSame('pending', $row->status);
        $this->assertFalse(PostExtra::where('type', 14)->get()->contains(fn ($r) => json_decode($r->content, true)['en'] === 'Home'));
        $this->get('/admin/website/translations')->assertSee('Test Brand New Item');

        $this->post('/admin/website/translations', ['bn' => [$row->id => 'পরীক্ষার নতুন আইটেম']]);
        $this->assertSame('active', $row->fresh()->status);
        $this->get('/')->assertSee('Test Brand New Item')->assertSee(trim(json_encode('Test Brand New Item'), '"').'":"'.trim(json_encode('পরীক্ষার নতুন আইটেম'), '"'), false);

        $this->post('/admin/website/translations/add', ['en' => 'Test Hand Text', 'bn' => 'হাতে লেখা']);
        $this->get('/admin/website/translations?filter=done')->assertSee('Test Hand Text');
        $this->get('/admin/website/translations/delete/'.$row->id);
        $this->assertNull($row->fresh());
    }

    public function test_website_links_point_to_the_public_website(): void
    {
        $this->assertSame(config('cms.website_url').'/contact-us', route('pageView', 'contact-us'));
        $this->assertSame(config('cms.website_url').'/blog/news', route('blogView', 'news'));
    }
}
