<?php

namespace Tests\Feature;

use App\Models\Erp\Certificate;
use App\Models\Erp\Student;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

// Website "Find Certificate": search by certificate number, download when found. Runs inside a rolled back transaction.
class FindCertificateTest extends TestCase
{
    use DatabaseTransactions;

    private Certificate $certificate;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake(['translate.googleapis.com/*' => Http::response([[['অনুবাদ', 'x']]], 200)]);
        $course = \App\Support\Erp::courses()->first();
        $student = Student::create(['name' => 'Find Cert Student', 'phone' => '01777700011', 'course_id' => $course->id, 'fee' => 1000]);
        $this->certificate = Certificate::create(['student_id' => $student->id, 'course_id' => $course->id, 'issued' => '2026-11-09', 'grade' => 'A+']);
    }

    public function test_footer_has_the_find_certificate_link(): void
    {
        $this->get('/')->assertSee(route('certificates.find'))->assertSee('Find Certificate');
        $this->get('/find-certificate')->assertOk()->assertSee('Enter a certificate number or a Student ID')->assertDontSee('No certificate found');
    }

    public function test_every_certificate_gets_a_random_number(): void
    {
        $this->assertMatchesRegularExpression('/^RA-[A-HJ-NP-Z2-9]{4}-[A-HJ-NP-Z2-9]{4}$/', $this->certificate->serial);
        $this->assertSame($this->certificate->serial, $this->certificate->code);
        $other = Certificate::create(['student_id' => $this->certificate->student_id, 'course_id' => $this->certificate->course_id, 'issued' => '2026-11-09', 'grade' => 'A']);
        $this->assertNotSame($this->certificate->serial, $other->serial);
    }

    public function test_found_certificate_shows_details_and_download(): void
    {
        $serial = $this->certificate->serial;
        $this->get('/find-certificate?number='.$serial)->assertOk()
            ->assertSee('Certificate found')->assertSee('Find Cert Student')->assertSee('A+')
            ->assertSee(route('certificates.show', [$serial, 'download' => 1]), false);

        // Small letters, spaces or without "RA" also work
        $this->get('/find-certificate?number='.urlencode(strtolower(str_replace('-', ' ', $serial))))->assertSee('Certificate found');
        $this->get('/find-certificate?number='.substr($serial, 3))->assertSee('Certificate found');

        $this->get('/certificate/'.$serial)->assertOk()->assertSee('Download PDF')->assertSee('Find Cert Student')->assertSee('Find Certificate');
    }

    public function test_unknown_or_old_style_numbers_are_not_found(): void
    {
        $this->get('/find-certificate?number=RA-AAAA-AAAA')->assertOk()->assertSee('No certificate found')->assertDontSee('Find Cert Student');
        // The old sequential number cannot be used to guess certificates
        $this->get('/find-certificate?number=CERT-'.$this->certificate->id)->assertSee('No certificate found');
        $this->get('/find-certificate?number='.$this->certificate->id)->assertSee('No certificate found');
        $this->get('/find-certificate?number='.urlencode('<script>alert(1)</script>'))->assertSee('No certificate found')->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/certificate/RA-AAAA-AAAA')->assertNotFound();
    }

    public function test_search_by_student_id(): void
    {
        $student = $this->certificate->student;
        $this->get('/find-certificate?number='.$student->code)->assertOk()->assertSee('Certificate found')->assertSee($this->certificate->serial)->assertSee('Find Cert Student');
        $this->get('/find-certificate?number='.urlencode(strtolower(str_replace('-', ' ', $student->code))))->assertSee('Certificate found');
        $this->get('/find-certificate?number='.($student->id + 1000))->assertSee('Certificate found');
        $this->get('/find-certificate?number=STU-999999')->assertSee('No certificate found');
    }

    public function test_student_id_lists_every_certificate_of_that_student(): void
    {
        $student = $this->certificate->student;
        $second = \App\Support\Erp::courses()->values()->get(1);
        $record = Student::create(['student_no' => $student->student_no, 'name' => 'Find Cert Student', 'phone' => '+8801777700011', 'course_id' => $second->id, 'fee' => 1000]);
        $other = Certificate::create(['student_id' => $record->id, 'course_id' => $second->id, 'issued' => '2026-11-10', 'grade' => 'A']);

        $this->get('/find-certificate?number='.$student->code)->assertSee('2 certificates found')->assertSee($this->certificate->serial)->assertSee($other->serial);
    }

    public function test_student_without_certificate_is_not_found(): void
    {
        $student = Student::create(['name' => 'No Cert Yet', 'phone' => '01777700099', 'course_id' => \App\Support\Erp::courses()->first()->id, 'fee' => 1000]);
        $this->get('/find-certificate?number='.$student->code)->assertSee('No certificate found')->assertDontSee('No Cert Yet');
    }

    public function test_searches_are_rate_limited(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $this->get('/find-certificate?number=RA-AAAA-AAA'.($i % 9 + 1))->assertOk();
        }
        $this->get('/find-certificate?number=RA-AAAA-AAAB')->assertStatus(429);
    }
}
