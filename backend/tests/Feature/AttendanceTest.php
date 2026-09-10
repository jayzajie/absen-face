<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_records_check_in_and_dashboard_displays_it(): void
    {
        $this->withToken('testing-device-token')->postJson('/api/attendances', [
            'type' => 'masuk',
            'employee_name' => 'Dimas Pratama',
            'device_id' => 'office-hp-001',
            'camera_access_granted' => true,
        ])
            ->assertCreated()
            ->assertJsonPath('type', 'masuk');

        $this->assertDatabaseHas('attendances', [
            'employee_name' => 'Dimas Pratama',
            'device_id' => 'office-hp-001',
            'source' => 'mobile',
        ]);
        $this->withSession(['hr_authenticated' => true])->get('/')
            ->assertOk()
            ->assertSee('office-hp-001')
            ->assertSee('Diizinkan');
    }

    public function test_api_rejects_unknown_devices(): void
    {
        $this->postJson('/api/attendances', [])->assertUnauthorized();
    }

    public function test_dashboard_rejects_unknown_hr_users(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_every_navigation_page_is_available_to_hr(): void
    {
        foreach (['history' => 'Riwayat Absensi', 'employees' => 'Data Karyawan', 'devices' => 'Perangkat Kantor'] as $view => $title) {
            $this->withSession(['hr_authenticated' => true])
                ->get('/?view='.$view)
                ->assertOk()
                ->assertSee($title);
        }
    }

    public function test_device_page_is_read_only_and_has_no_media_controls(): void
    {
        $this->withToken('testing-device-token')->postJson('/api/attendances', [
            'type' => 'masuk',
            'employee_name' => 'Dimas Pratama',
            'device_id' => 'office-hp-001',
            'camera_access_granted' => true,
        ])->assertCreated();

        $this->withSession(['hr_authenticated' => true])
            ->get('/?view=devices')
            ->assertOk()
            ->assertSee('office-hp-001')
            ->assertSee('1')
            ->assertSee('catatan absensi')
            ->assertDontSee('Media aktif')
            ->assertDontSee('Media perangkat');
    }

    public function test_mobile_login_records_the_employee_device_automatically(): void
    {
        $employee = Employee::create([
            'name' => 'Ayu Lestari',
            'username' => 'ayu',
            'password' => Hash::make('rahasia'),
        ]);

        $this->postJson('/api/mobile/login', [
            'username' => 'ayu',
            'password' => 'rahasia',
            'device_id' => 'Samsung SM-A556E',
        ])
            ->assertOk()
            ->assertJsonPath('device_id', 'Samsung SM-A556E')
            ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'device_id' => 'Samsung SM-A556E',
        ]);
    }

    public function test_verified_attendance_rejects_a_wrong_face_and_records_a_matching_face(): void
    {
        Storage::fake('local');
        $referencePath = UploadedFile::fake()->image('reference.jpg')->store('employee-faces', 'local');
        $employee = Employee::create([
            'name' => 'Ayu Lestari',
            'username' => 'ayu',
            'password' => Hash::make('rahasia'),
            'face_photo_path' => $referencePath,
        ]);
        $token = $this->postJson('/api/mobile/login', [
            'username' => 'ayu',
            'password' => 'rahasia',
            'device_id' => 'test-phone',
        ])->json('token');

        Http::fake([
            '*' => Http::sequence()
                ->push(['matched' => false, 'score' => 0.21, 'threshold' => 0.45, 'model_version' => 'test-model'])
                ->push(['matched' => true, 'score' => 0.81, 'threshold' => 0.45, 'model_version' => 'test-model']),
        ]);

        $payload = [
            'type' => 'masuk',
            'device_id' => 'test-phone',
            'selfie' => UploadedFile::fake()->image('selfie.jpg'),
        ];
        $this->withToken($token)->post('/api/attendances/verify', $payload)
            ->assertUnprocessable()
            ->assertJsonPath('face_match_score', 0.21);
        $this->assertDatabaseCount('attendances', 0);

        $payload['selfie'] = UploadedFile::fake()->image('selfie-2.jpg');
        $this->withToken($token)->post('/api/attendances/verify', $payload)
            ->assertCreated()
            ->assertJsonPath('employee_name', $employee->name)
            ->assertJsonPath('face_match_score', 0.81);
        $this->assertDatabaseHas('attendances', [
            'employee_name' => 'Ayu Lestari',
            'face_model_version' => 'test-model',
        ]);
    }

    public function test_hr_can_enroll_and_privately_view_an_employee_face_photo(): void
    {
        Storage::fake('local');

        $this->withSession(['hr_authenticated' => true])->post('/employees', [
            'name' => 'Ayu Lestari',
            'username' => 'ayu',
            'password' => 'rahasia',
            'face_photo' => UploadedFile::fake()->image('ayu.jpg', 300, 300),
        ])->assertRedirect();

        $employee = Employee::where('username', 'ayu')->firstOrFail();
        $this->assertNull($employee->device_id);
        Storage::disk('local')->assertExists($employee->face_photo_path);
        $firstPhotoPath = $employee->face_photo_path;

        $this->withSession(['hr_authenticated' => true])->put(route('employees.face-photo.update', $employee), [
            'face_photo' => UploadedFile::fake()->image('ayu-baru.png', 300, 300),
        ])->assertRedirect();

        $employee->refresh();
        Storage::disk('local')->assertMissing($firstPhotoPath);
        Storage::disk('local')->assertExists($employee->face_photo_path);

        $this->flushSession();
        $this->get(route('employees.face-photo.show', $employee))->assertRedirect('/login');
        $this->withSession(['hr_authenticated' => true])
            ->get(route('employees.face-photo.show', $employee))
            ->assertOk()
            ->assertHeader('cache-control', 'max-age=300, private');
    }

    public function test_gjp_brand_asset_is_available(): void
    {
        $this->get('/brand/gjp.png')
            ->assertOk()
            ->assertHeader('content-type', 'image/png');
    }
}
