<?php

namespace Tests\Feature;

use App\Filament\Resources\Settings\Pages\EditSetting;
use App\Models\Setting;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function admin(): User
    {
        return User::forceCreate([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_invalid_slot_minutes_below_minimum_is_rejected(): void
    {
        $setting = Setting::create(['key' => 'BOOKING_SLOT_MINUTES', 'value' => '30']);
        $this->actingAs($this->admin(), 'admin');

        Livewire::test(EditSetting::class, ['record' => $setting->getRouteKey()])
            ->fillForm(['value' => '5'])
            ->call('save')
            ->assertHasFormErrors(['value']);
    }

    public function test_valid_slot_minutes_is_accepted(): void
    {
        $setting = Setting::create(['key' => 'BOOKING_SLOT_MINUTES', 'value' => '30']);
        $this->actingAs($this->admin(), 'admin');

        Livewire::test(EditSetting::class, ['record' => $setting->getRouteKey()])
            ->fillForm(['value' => '60'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('60', $setting->fresh()->value);
    }

    public function test_cancellation_fee_above_100_percent_is_rejected(): void
    {
        // updateOrCreate: this row is now guaranteed by migration, so a bare
        // create() would collide with the seeded key.
        $setting = Setting::updateOrCreate(['key' => 'CANCELLATION_FEE_PERCENT'], ['value' => '10']);
        $this->actingAs($this->admin(), 'admin');

        Livewire::test(EditSetting::class, ['record' => $setting->getRouteKey()])
            ->fillForm(['value' => '150'])
            ->call('save')
            ->assertHasFormErrors(['value']);
    }

    public function test_invalid_time_format_for_business_hours_is_rejected(): void
    {
        $setting = Setting::create(['key' => 'BUSINESS_HOURS_START', 'value' => '09:00']);
        $this->actingAs($this->admin(), 'admin');

        Livewire::test(EditSetting::class, ['record' => $setting->getRouteKey()])
            ->fillForm(['value' => '9am'])
            ->call('save')
            ->assertHasFormErrors(['value']);
    }
}
