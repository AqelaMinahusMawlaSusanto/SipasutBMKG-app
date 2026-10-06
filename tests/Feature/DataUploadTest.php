<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DataUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_pdf_and_parse_predictions()
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@sipasut.id'],
            ['name' => 'Admin SIPASUT', 'password' => bcrypt('admin123')]
        );

        $role = Role::firstOrCreate(['name' => 'admin']);
        if (!$admin->hasRole('admin')) {
            $admin->assignRole($role);
        }

        $location = Location::firstOrCreate(
            ['code' => 'SBYTIM'],
            ['name' => 'Surabaya Timur', 'latitude' => -7.25, 'longitude' => 112.75, 'is_active' => true]
        );

        $samplePdfPath = base_path('data/7.1. SBYTIM juli2026.pdf');
        $this->assertFileExists($samplePdfPath);

        $uploadedFile = new UploadedFile(
            $samplePdfPath,
            '7.1. SBYTIM juli2026.pdf',
            'application/pdf',
            null,
            true
        );

        $response = $this->actingAs($admin)->post(route('admin.data.upload'), [
            'location_id' => $location->id,
            'period_month' => 7,
            'period_year' => 2026,
            'file_data' => $uploadedFile,
        ]);

        $response->assertSessionHas('success');
        
        // Cek bahwa data prediksi bertambah / tersimpan
        $predCount = Prediction::where('location_id', $location->id)->count();
        $this->assertGreaterThanOrEqual(25, $predCount);
    }
}
