<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SiteSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SiteSettingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected SiteSetting $siteSetting;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->user = User::where('email', 'rifat@email.com')->first();
        $this->actingAs($this->user);

        $this->siteSetting = SiteSetting::findOrFail(1);
        

        // dd(DB::connection()->getDatabaseName());
    }

    // public function test_site_setting_updates_without_logo()
    // {
    //     $response = $this->patch(route('site.setting'), [
    //         'website_name' => 'New Website Name',
    //         'old_image' => 'old_logo.png',
    //     ]);

    //     $response->assertRedirect();

    //     $this->assertDatabaseHas('site_settings', [
    //         'id' => 1,
    //         'website_name' => 'New Website Name',
    //         'website_logo' => 'old_logo.png',
    //     ]);
    // }

    public function test_site_setting_deletes_old_logo_when_new_one_is_uploaded()
    {
        Storage::fake('public');

        $oldFile = UploadedFile::fake()->image('old_logo.png');
        $oldPath = $oldFile->store('public/sitesetting');
        $oldFileName = basename($oldPath);

        $newFile = UploadedFile::fake()->image('new_logo.jpg');

        $response = $this->patch(route('site.setting'), [
            'website_name' => 'New Website Name',
            'site_logo' => $newFile,
            'old_image' => $oldFileName,
        ]);

        $response->assertRedirect();
        Storage::disk('public')->assertMissing('sitesetting/' . $oldFileName);
    }

    public function test_site_setting_returns_back()
    {

        $response = $this->patch(route('site.setting'), [
            'website_name' => 'New Website Name',
            'old_image' => 'old_logo.png',
        ]);

        $response->assertRedirect();
    }


}
