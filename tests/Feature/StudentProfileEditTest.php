<?php

namespace Tests\Feature;

use App\Models\StudentAccount;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

// Student profile edit: details, photo and password. Runs inside a transaction that is rolled back.
class StudentProfileEditTest extends TestCase
{
    use DatabaseTransactions;

    private StudentAccount $account;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake(['translate.googleapis.com/*' => Http::response([[['অনুবাদ', 'x']]], 200)]);
        $this->account = StudentAccount::create(['name' => 'Edit Test Student', 'email' => 'edit-test@example.com', 'phone' => '01712340001', 'phone_key' => '01712340001', 'password' => 'secret123'])->refresh();
        $this->actingAs($this->account, 'student');
    }

    protected function tearDown(): void
    {
        // Photos written by the test
        foreach (glob(public_path('medies/students/*.jpg')) as $file) {
            if (filemtime($file) >= time() - 120 && StudentAccount::where('photo', 'medies/students/'.basename($file))->doesntExist()) {
                @unlink($file);
            }
        }
        parent::tearDown();
    }

    private function save(array $data = [])
    {
        return $this->from('/student/profile/edit')->post('/student/profile/edit', $data + ['name' => 'Edit Test Student', 'email' => 'edit-test@example.com', 'phone' => '01712340001']);
    }

    public function test_profile_and_edit_pages_show_the_registration_number(): void
    {
        $this->get('/student/profile')->assertOk()->assertSee('Registration No: '.$this->account->code)->assertSee('Edit Profile');
        $this->get('/student/profile/edit')->assertOk()->assertSee('Profile Information')->assertSee('Change Password');
    }

    public function test_name_and_mobile_can_be_changed(): void
    {
        $this->save(['name' => 'Edited Name', 'phone' => '01812340002'])->assertRedirect('/student/profile/edit')->assertSessionHas('success');
        $this->assertSame('Edited Name', $this->account->fresh()->name);
        $this->assertSame('01812340002', $this->account->fresh()->phone_key);
    }

    public function test_new_email_needs_the_current_password(): void
    {
        $this->save(['email' => 'new-mail@example.com'])->assertSessionHasErrors('current_password');
        $this->save(['email' => 'new-mail@example.com', 'current_password' => 'wrong-pass1'])->assertSessionHasErrors('current_password');
        $this->assertSame('edit-test@example.com', $this->account->fresh()->email);
        $this->save(['email' => 'new-mail@example.com', 'current_password' => 'secret123'])->assertSessionHasNoErrors();
        $this->assertSame('new-mail@example.com', $this->account->fresh()->email);
    }

    public function test_email_and_mobile_of_another_account_are_refused(): void
    {
        StudentAccount::create(['name' => 'Other One', 'email' => 'other-one@example.com', 'phone' => '01912340003', 'phone_key' => '01912340003', 'password' => 'secret123']);
        $this->save(['email' => 'other-one@example.com', 'current_password' => 'secret123'])->assertSessionHasErrors('email');
        $this->save(['phone' => '+8801912340003'])->assertSessionHasErrors('phone_key');
    }

    public function test_photo_upload_is_resized_and_old_photo_removed(): void
    {
        $this->save(['photo' => UploadedFile::fake()->image('me.png', 900, 600)])->assertSessionHasNoErrors();
        $photo = $this->account->fresh()->photo;
        $this->assertMatchesRegularExpression('#^medies/students/[A-Za-z0-9]{32}\.jpg$#', $photo);
        [$width, $height] = getimagesize(public_path($photo));
        $this->assertSame([400, 400], [$width, $height]);
        $this->get('/student/profile')->assertSee(asset($photo), false);

        $this->save(['photo' => UploadedFile::fake()->image('me2.jpg', 300, 300)]);
        $this->assertFileDoesNotExist(public_path($photo));

        $this->post('/student/profile/photo/remove');
        $this->assertNull($this->account->fresh()->photo);
    }

    public function test_non_images_and_large_files_are_refused(): void
    {
        $this->save(['photo' => UploadedFile::fake()->create('evil.php', 10, 'application/x-php')])->assertSessionHasErrors('photo');
        $this->save(['photo' => UploadedFile::fake()->create('fake.jpg', 10, 'text/plain')])->assertSessionHasErrors('photo');
        $this->save(['photo' => UploadedFile::fake()->image('big.jpg', 1200, 1200)->size(3000)])->assertSessionHasErrors('photo');
        $this->assertNull($this->account->fresh()->photo);
    }

    public function test_password_change(): void
    {
        $this->from('/student/profile/edit')->post('/student/profile/password', ['current_password' => 'wrong-pass1', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])->assertSessionHasErrors('current_password');
        $this->from('/student/profile/edit')->post('/student/profile/password', ['current_password' => 'secret123', 'password' => 'secret123', 'password_confirmation' => 'secret123'])->assertSessionHasErrors('password');
        $this->from('/student/profile/edit')->post('/student/profile/password', ['current_password' => 'secret123', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('newpass123', $this->account->fresh()->password));
    }

    public function test_guests_cannot_edit(): void
    {
        auth('student')->logout();
        $this->get('/student/profile/edit')->assertRedirect('/student/login');
        $this->post('/student/profile/edit', ['name' => 'Hacker'])->assertRedirect('/student/login');
        $this->assertSame('Edit Test Student', $this->account->fresh()->name);
    }
}
