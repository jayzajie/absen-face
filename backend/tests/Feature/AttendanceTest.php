<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $this->withBasicAuth('hr', 'testing-password')->get('/')
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
        $this->get('/')->assertUnauthorized();
    }

    public function test_every_navigation_page_is_available_to_hr(): void
    {
        foreach (['history' => 'Riwayat Absensi', 'employees' => 'Data Karyawan', 'devices' => 'Perangkat Kantor'] as $view => $title) {
            $this->withBasicAuth('hr', 'testing-password')
                ->get('/?view='.$view)
                ->assertOk()
                ->assertSee($title);
        }
    }
}
