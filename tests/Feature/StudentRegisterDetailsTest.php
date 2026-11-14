<?php

namespace Tests\Feature;

use App\Models\StudentAccount;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

// Registration details of the design form (profession, course, education ...). Rolled back after each test.
class StudentRegisterDetailsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake(['translate.googleapis.com/*' => Http::response([[['অনুবাদ', 'x']]], 200)]);
    }

    private function data(array $extra = []): array
    {
        return $extra + [
            'name' => 'Details Student', 'email' => 'details-student@example.com', 'phone' => '01712340881',
            'password' => 'secret123', 'password_confirmation' => 'secret123', 'agree' => '1',
            'dob' => '2001-05-14', 'profession' => 'Engineer', 'organization' => 'BUET', 'education' => 'BSc in Engineering',
            'preferred_course_id' => trainingCourses()->first()->id, 'training_mode' => 'Corporate / Group Training',
            'training_location' => 'Mirpur, Dhaka', 'address' => 'Mirpur 10, Dhaka',
        ];
    }

    public function test_register_page_has_every_design_field(): void
    {
        $page = $this->get('/student/register')->assertOk();
        foreach (['name', 'phone', 'email', 'dob', 'profession', 'organization', 'education', 'preferred_course_id', 'training_mode', 'training_location', 'address', 'password', 'password_confirmation', 'agree'] as $field) {
            $page->assertSee('name="'.$field.'"', false);
        }
        $page->assertSee(trainingCourses()->first()->course('title'));
    }

    public function test_details_are_saved_and_shown_in_profile_and_admin(): void
    {
        $this->post('/student/register', $this->data())->assertRedirect('/student/login');
        $account = StudentAccount::where('email', 'details-student@example.com')->firstOrFail();
        $account->approve();
        $this->assertSame('Engineer', $account->profession);
        $this->assertSame('2001-05-14', $account->dob->toDateString());
        $this->assertSame('Mirpur 10, Dhaka', $account->address);
        $this->assertSame(trainingCourses()->first()->id, $account->preferred_course_id);

        $this->actingAs($account, 'student')->get('/student/profile')->assertOk()
            ->assertSee('My Information')->assertSee('BUET')->assertSee('14 May 2001')->assertSee(trainingCourses()->first()->course('title'));
    }

    public function test_profession_and_course_are_required_and_must_be_from_the_lists(): void
    {
        $this->post('/student/register', $this->data(['profession' => '', 'preferred_course_id' => '']))->assertSessionHasErrors(['profession', 'preferred_course_id']);
        $this->post('/student/register', $this->data(['profession' => 'Hacker', 'preferred_course_id' => '999999', 'education' => 'PhD', 'dob' => '2999-01-01']))
            ->assertSessionHasErrors(['profession', 'preferred_course_id', 'education', 'dob']);
        $this->assertFalse(StudentAccount::where('email', 'details-student@example.com')->exists());
    }

    public function test_details_can_be_changed_on_edit_profile(): void
    {
        $this->post('/student/register', $this->data());
        $account = StudentAccount::where('email', 'details-student@example.com')->firstOrFail();
        $account->approve();
        $this->actingAs($account, 'student')->get('/student/profile/edit')->assertOk()->assertSee('value="BUET"', false);
        $this->actingAs($account, 'student')->post('/student/profile/edit', ['name' => 'Details Student', 'email' => 'details-student@example.com', 'phone' => '01712340881', 'profession' => 'Teacher / Instructor', 'organization' => '', 'address' => 'Uttara, Dhaka'])
            ->assertRedirect('/student/profile/edit')->assertSessionHas('success');
        $account->refresh();
        $this->assertSame('Teacher / Instructor', $account->profession);
        $this->assertNull($account->organization);
        $this->assertSame('Uttara, Dhaka', $account->address);
    }
}
